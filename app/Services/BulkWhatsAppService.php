<?php

namespace App\Services;

use App\Jobs\SendBulkWhatsAppMessage;
use App\Models\User;
use App\Models\WhatsAppBulkBatch;
use App\Models\ZoomBooking;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BulkWhatsAppService
{
    public function __construct(private WhatsAppService $whatsApp) {}

    public function invitation(ZoomBooking $booking): string
    {
        $lines = ['Undangan Zoom Meeting', 'Agenda: '.$booking->topic];
        $lines[] = 'Booking atas nama: '.($booking->user?->name ?? '-');
        if ($booking->project_name) {
            $lines[] = 'Proyek: '.$booking->project_name;
        }
        $lines[] = 'Hari/Tanggal: '.$booking->date_range_formatted;
        $lines[] = 'Waktu: '.substr($booking->start_time ?? '', 0, 5).' - '.substr($booking->end_time ?? '', 0, 5).' WIB';
        $lines[] = '';
        if ($booking->join_url) {
            $lines[] = 'Join Zoom Meeting:';
            $lines[] = $booking->join_url;
            $lines[] = 'Meeting ID: '.($booking->zoom_meeting_id ?: '-');
            $lines[] = 'Passcode: '.($booking->passcode ?: '-');
        } else {
            $lines[] = 'Link Zoom belum tersedia. Undangan dengan link akan dibagikan setelah meeting disiapkan.';
        }

        return implode("\n", $lines);
    }

    /** @return list<string> */
    public function recipients(string $input): array
    {
        $numbers = [];
        foreach (preg_split('/[,;\r\n]+/', trim($input), -1, PREG_SPLIT_NO_EMPTY) as $raw) {
            $raw = trim($raw);
            if (! preg_match('/^\+?[0-9\s().-]+$/', $raw)) {
                throw ValidationException::withMessages(['recipients' => 'Nomor tujuan tidak valid. Gunakan angka dan pisahkan nomor dengan koma atau baris baru.']);
            }
            $number = $this->whatsApp->formatTargetNumber($raw);
            if (! preg_match('/^[1-9][0-9]{7,14}$/', $number)) {
                throw ValidationException::withMessages(['recipients' => 'Nomor tujuan harus berisi 8–15 digit setelah kode negara disesuaikan.']);
            }
            $numbers[] = $number;
        }
        $numbers = array_values(array_unique($numbers));
        if (count($numbers) < 1 || count($numbers) > 50) {
            throw ValidationException::withMessages(['recipients' => 'Masukkan 1 sampai 50 nomor tujuan yang berbeda.']);
        }

        return $numbers;
    }

    /**
     * @param  list<int|string>  $bookingIds
     * @return Collection<int, ZoomBooking>
     */
    public function selectedBookings(User $user, array $bookingIds): Collection
    {
        abort_unless($user->is_active, 403);
        $bookings = ZoomBooking::with(['user', 'account'])->whereIn('id', $bookingIds)->orderBy('start_date')->orderBy('start_time')->orderBy('id')->get();
        if ($bookings->count() !== count($bookingIds)) {
            throw ValidationException::withMessages(['booking_ids' => 'Salah satu meeting sudah tidak tersedia. Muat ulang daftar booking.']);
        }
        foreach ($bookings as $booking) {
            abort_unless($user->isAdmin() || $booking->user_id === $user->id, 403);
            if (! $booking->isEligibleForBulkWhatsApp()) {
                throw ValidationException::withMessages(['booking_ids' => 'Hanya booking Disetujui (ACC) atau Link Aktif yang dapat dikirim. Muat ulang daftar booking.']);
            }
        }

        return $bookings;
    }

    public function canIncludeAccount(ZoomBooking $booking): bool
    {
        return $this->accountUnavailableReason($booking) === null;
    }

    public function accountUnavailableReason(ZoomBooking $booking): ?string
    {
        if (! $booking->account) {
            return 'Belum ada akun Zoom yang ditetapkan.';
        }
        if (blank($booking->account->email)) {
            return 'Email akun Zoom belum diisi.';
        }
        if (blank($booking->account->password)) {
            return 'Password akun Zoom belum diisi.';
        }

        return null;
    }

    /** @param array{request_id: string, booking_ids: array, recipients: string, interval_seconds: int|string, include_credentials?: bool} $input */
    public function enqueue(User $user, array $input): WhatsAppBulkBatch
    {
        abort_unless($user->is_active, 403);
        $existing = WhatsAppBulkBatch::where('request_id', $input['request_id'])->first();
        if ($existing) {
            abort_unless($existing->user_id === $user->id, 403);

            return $existing;
        }
        $bookings = $this->selectedBookings($user, $input['booking_ids']);
        $includeCredentials = (bool) ($input['include_credentials'] ?? false);
        $unavailableBooking = $includeCredentials ? $bookings->first(fn (ZoomBooking $booking): bool => ! $this->canIncludeAccount($booking)) : null;
        if ($unavailableBooking) {
            throw ValidationException::withMessages(['include_credentials' => 'Meeting “'.$unavailableBooking->topic.'”: '.$this->accountUnavailableReason($unavailableBooking).' Lengkapi akun atau kirim undangan saja.']);
        }
        $recipients = $this->recipients($input['recipients']);
        $total = $bookings->count() * count($recipients);
        if ($total > 1000) {
            throw ValidationException::withMessages(['recipients' => 'Maksimal 1.000 pesan per antrean. Kurangi jumlah meeting atau nomor tujuan.']);
        }
        $provider = $this->whatsApp->getActiveProvider();
        if (! $this->whatsApp->isEnabled($provider)) {
            throw ValidationException::withMessages(['gateway' => 'Gateway WhatsApp belum aktif atau token belum diatur. Periksa Pengaturan WhatsApp.']);
        }

        return DB::transaction(function () use ($user, $input, $bookings, $recipients, $total, $provider, $includeCredentials): WhatsAppBulkBatch {
            $batch = WhatsAppBulkBatch::firstOrCreate(['request_id' => $input['request_id']], [
                'user_id' => $user->id,
                'provider' => $provider,
                'interval_seconds' => (int) $input['interval_seconds'],
                'total_messages' => $total,
                'include_credentials' => $includeCredentials,
            ]);
            abort_unless($batch->user_id === $user->id, 403);
            if (! $batch->wasRecentlyCreated) {
                return $batch;
            }
            $offset = 0;
            $expiresAt = now()->addDay()->timestamp;
            foreach ($bookings as $booking) {
                foreach ($recipients as $recipient) {
                    $message = $batch->messages()->create([
                        'booking_id' => $booking->id,
                        'topic' => $booking->topic,
                        'recipient' => $recipient,
                        'message' => $this->invitation($booking),
                        'account_credentials' => $includeCredentials ? [
                            'account_id' => $booking->account->id,
                            'name' => $booking->account->name,
                            'email' => $booking->account->email,
                            'password' => $booking->account->password,
                        ] : null,
                    ]);
                    SendBulkWhatsAppMessage::dispatch($message->id, $expiresAt)
                        ->delay(now()->addSeconds($offset))->afterCommit();
                    $offset += $batch->interval_seconds;
                }
            }

            return $batch;
        });
    }
}
