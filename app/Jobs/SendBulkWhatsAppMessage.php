<?php

namespace App\Jobs;

use App\Models\WhatsAppBulkMessage;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class SendBulkWhatsAppMessage implements ShouldQueue
{
    use Queueable;

    public int $tries = 0;

    public int $timeout = 45;

    public bool $failOnTimeout = true;

    public function __construct(public int $messageId, public int $expiresAt)
    {
        $this->onConnection('database')->onQueue('whatsapp-bulk');
    }

    public function retryUntil(): DateTimeInterface
    {
        return Carbon::createFromTimestamp($this->expiresAt);
    }

    public function handle(WhatsAppService $whatsApp): void
    {
        $message = WhatsAppBulkMessage::with(['batch.user', 'booking.account'])->find($this->messageId);
        if ($message && $message->status === 'sending' && $message->attempted_at?->lte(now()->subSeconds(75))) {
            $message->update(['status' => 'unknown', 'error' => 'Worker sebelumnya terhenti. Periksa penerimaan pesan di gateway sebelum mengirim ulang.', 'completed_at' => now()]);

            return;
        }
        if (! $message || $message->status !== 'queued') {
            return;
        }
        $user = $message->batch->user;
        if (! $message->booking || ! $message->booking->isEligibleForBulkWhatsApp() || ! $user->is_active || (! $user->isAdmin() && $message->booking->user_id !== $user->id)) {
            $message->update(['status' => 'failed', 'error' => 'Booking tidak lagi tersedia, belum disetujui, atau akses pemohon berubah.', 'completed_at' => now()]);

            return;
        }
        if (! $whatsApp->isEnabled($message->batch->provider)) {
            $message->update(['status' => 'failed', 'error' => 'Gateway WhatsApp dinonaktifkan atau token belum tersedia.', 'completed_at' => now()]);

            return;
        }

        $outgoingMessage = $message->message;
        if ($message->batch->include_credentials) {
            $credentials = $message->account_credentials;
            $account = $message->booking->account;
            if (! $account || ! $credentials || $account->status !== 'active'
                || $account->id !== ($credentials['account_id'] ?? null)
                || $account->email !== ($credentials['email'] ?? null)
                || $account->password !== ($credentials['password'] ?? null)) {
                $message->update(['status' => 'failed', 'error' => 'Akun Zoom berubah atau tidak lagi tersedia. Buat antrean baru dengan akun terkini.', 'completed_at' => now()]);

                return;
            }
            $outgoingMessage .= "\n\nAKUN HOST ZOOM:\nNama: ".$credentials['name']."\nEmail: ".$credentials['email']."\nPassword: ".$credentials['password'];
        }

        /** Reserve the shared sending lane for longer than the gateway timeout. */
        $token = (string) Str::uuid();
        $nowMs = now()->getTimestampMs();
        $reserved = DB::table('whatsapp_bulk_dispatch_state')->where('id', 1)
            ->where('next_allowed_at_ms', '<=', $nowMs)
            ->update(['next_allowed_at_ms' => $nowMs + 75000, 'reservation_token' => $token]);
        if (! $reserved) {
            $next = (int) DB::table('whatsapp_bulk_dispatch_state')->where('id', 1)->value('next_allowed_at_ms');
            $this->release(min(15, max(1, (int) ceil(($next - $nowMs) / 1000))));

            return;
        }

        $attempted = false;
        try {
            $claimed = WhatsAppBulkMessage::whereKey($message->id)->where('status', 'queued')
                ->update(['status' => 'sending', 'attempted_at' => now(), 'updated_at' => now()]);
            if (! $claimed) {
                return;
            }
            $attempted = true;
            $result = $whatsApp->sendMessage($message->recipient, $outgoingMessage, $message->batch->provider);
            $message->update([
                'status' => ($result['success'] ?? false) ? 'sent' : 'failed',
                'error' => ($result['success'] ?? false) ? null : Str::limit((string) ($result['message'] ?? 'Gateway menolak pesan.'), 1000),
                'completed_at' => now(),
            ]);
        } catch (Throwable $exception) {
            $message->update(['status' => 'unknown', 'error' => 'Penerimaan gateway belum dapat dipastikan. Periksa gateway sebelum mengirim ulang.', 'completed_at' => now()]);
            report($exception);
        } finally {
            DB::table('whatsapp_bulk_dispatch_state')->where('id', 1)->where('reservation_token', $token)->update([
                'next_allowed_at_ms' => now()->getTimestampMs() + ($attempted ? $message->batch->interval_seconds * 1000 : 0),
                'reservation_token' => null,
            ]);
        }
    }

    public function failed(?Throwable $exception): void
    {
        $message = WhatsAppBulkMessage::find($this->messageId);
        if ($message && in_array($message->status, ['queued', 'sending'], true)) {
            $message->update([
                'status' => $message->status === 'sending' ? 'unknown' : 'failed',
                'error' => 'Proses antrean berhenti atau melewati batas waktu. Periksa gateway sebelum mengirim ulang.',
                'completed_at' => now(),
            ]);
        }
    }
}
