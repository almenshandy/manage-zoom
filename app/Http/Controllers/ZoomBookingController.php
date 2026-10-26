<?php

namespace App\Http\Controllers;

use App\Models\ZoomBooking;
use App\Models\ZoomAccount;
use App\Services\ZoomService;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;

class ZoomBookingController extends Controller
{
    protected ZoomService $zoomService;

    public function __construct(ZoomService $zoomService)
    {
        $this->zoomService = $zoomService;
    }

    public function index(Request $request)
    {
        $query = ZoomBooking::with(['user', 'account'])->latest();

        // If not admin, only show user's own bookings
        if (auth()->user() && !auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('topic', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $bookings = $query->paginate(15)->withQueryString();
        $accounts = ZoomAccount::whereDate('expired_date', '>=', now()->toDateString())
            ->orderBy('expired_date', 'asc')
            ->get();

        return view('bookings.index', compact('bookings', 'accounts'));
    }

    public function create()
    {
        $accounts = ZoomAccount::whereDate('expired_date', '>=', now()->toDateString())
            ->orderBy('expired_date', 'asc')
            ->get();

        return view('bookings.create', compact('accounts'));
    }

    public function store(StoreBookingRequest $request)
    {
        $validated = $request->validated();
        $user = auth()->user();
        $validated['user_id'] = $user ? $user->id : 1;

        // Jika pengaju adalah Admin dan sudah memilih akun Zoom, otomatis disetujui (ACC / STATUS_APPROVED)
        $isAdmin = $user && $user->isAdmin();
        if ($isAdmin && !empty($validated['zoom_account_id'])) {
            $validated['status'] = ZoomBooking::STATUS_APPROVED;
            $validated['admin_note'] = 'Disetujui otomatis oleh Administrator saat pengajuan.';
        } else {
            $validated['status'] = ZoomBooking::STATUS_PENDING;
        }

        // Hitung jumlah hari
        $start = Carbon::parse($validated['start_date']);
        $end = Carbon::parse($validated['end_date']);
        $validated['day_count'] = $start->diffInDays($end) + 1;

        // Hitung otomatis durasi menit dari start_time dan end_time agar selalu akurat
        if (!empty($validated['start_time']) && !empty($validated['end_time'])) {
            $timeStart = Carbon::parse($validated['start_date'] . ' ' . $validated['start_time']);
            $timeEnd = Carbon::parse($validated['start_date'] . ' ' . $validated['end_time']);
            if ($timeEnd->greaterThan($timeStart)) {
                $validated['duration_minutes'] = $timeStart->diffInMinutes($timeEnd);
            }
        }

        // Cek potensi bentrok jadwal jika akun sudah dipilih
        if (!empty($validated['zoom_account_id'])) {
            $conflict = $this->hasScheduleConflict(
                $validated['zoom_account_id'],
                $validated['start_date'],
                $validated['end_date'],
                $validated['start_time'],
                $validated['end_time']
            );

            if ($conflict) {
                return back()->withInput()->with('error', 'Peringatan: Akun Zoom yang dipilih sedang digunakan pada tanggal dan jam tersebut!');
            }
        }

        $booking = ZoomBooking::create($validated);

        if ($isAdmin && !empty($validated['zoom_account_id'])) {
            return redirect()->route('bookings.show', $booking->id)->with('success', 'Booking berhasil dibuat dan otomatis disetujui (ACC). Anda dapat langsung membuat Link Zoom!');
        }

        return redirect()->route('bookings.index')->with('success', 'Permohonan booking Zoom berhasil diajukan.');
    }

    public function show(ZoomBooking $booking)
    {
        $booking->load(['user', 'account', 'memo', 'meetings']);
        return view('bookings.show', compact('booking'));
    }

    public function edit(ZoomBooking $booking)
    {
        // Hanya bisa edit jika admin atau status masih pending
        if (!auth()->user()->isAdmin() && $booking->user_id !== auth()->id()) {
            abort(403);
        }

        $accounts = ZoomAccount::whereDate('expired_date', '>=', now()->toDateString())
            ->orderBy('expired_date', 'asc')
            ->get();
        return view('bookings.edit', compact('booking', 'accounts'));
    }

    public function update(UpdateBookingRequest $request, ZoomBooking $booking)
    {
        if (!auth()->user()->isAdmin() && $booking->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validated();

        if (!empty($validated['zoom_account_id'])) {
            $conflict = $this->hasScheduleConflict(
                $validated['zoom_account_id'],
                $validated['start_date'],
                $validated['end_date'],
                $validated['start_time'],
                $validated['end_time'],
                $booking->id
            );

            if ($conflict) {
                return back()->withInput()->with('error', 'Peringatan: Akun Zoom yang dipilih sedang digunakan pada tanggal dan jam tersebut!');
            }
        }

        if (isset($validated['start_date']) && isset($validated['end_date'])) {
            $start = Carbon::parse($validated['start_date']);
            $end = Carbon::parse($validated['end_date']);
            $validated['day_count'] = $start->diffInDays($end) + 1;
        }

        // Hitung otomatis durasi menit dari start_time dan end_time
        if (!empty($validated['start_time']) && !empty($validated['end_time'])) {
            $refDate = $validated['start_date'] ?? $booking->start_date->format('Y-m-d');
            $timeStart = Carbon::parse($refDate . ' ' . $validated['start_time']);
            $timeEnd = Carbon::parse($refDate . ' ' . $validated['end_time']);
            if ($timeEnd->greaterThan($timeStart)) {
                $validated['duration_minutes'] = $timeStart->diffInMinutes($timeEnd);
            }
        }

        $booking->update($validated);

        return redirect()->route('bookings.index')->with('success', 'Data booking berhasil diperbarui.');
    }

    /**
     * Admin Persetujuan (ACC Slot)
     */
    public function approve(Request $request, ZoomBooking $booking)
    {
        $request->validate([
            'zoom_account_id' => ['required', 'exists:zoom_accounts,id'],
            'admin_note'      => ['nullable', 'string', 'max:500'],
        ]);

        // Cek potensi bentrok jadwal saat disetujui
        $conflict = $this->hasScheduleConflict(
            $request->zoom_account_id,
            $booking->start_date,
            $booking->end_date,
            $booking->start_time,
            $booking->end_time,
            $booking->id
        );

        if ($conflict) {
            return back()->with('error', 'Gagal ACC: Akun Zoom yang dipilih sudah memiliki jadwal kegiatan lain yang disetujui pada jam tersebut.');
        }

        $booking->update([
            'zoom_account_id' => $request->zoom_account_id,
            'admin_note'      => $request->admin_note,
            'status'          => ZoomBooking::STATUS_APPROVED, // Status 1: ACC
        ]);

        return back()->with('success', 'Permohonan peminjaman berhasil disetujui (ACC). Silakan generate Link Zoom jika sudah siap.');
    }

    /**
     * Admin Penolakan Booking
     */
    public function reject(Request $request, ZoomBooking $booking)
    {
        $request->validate([
            'admin_note' => ['required', 'string', 'max:500'],
        ]);

        $booking->update([
            'admin_note' => $request->admin_note,
            'status'     => ZoomBooking::STATUS_REJECTED,
        ]);

        return back()->with('success', 'Permohonan peminjaman berhasil ditolak.');
    }

    /**
     * Eksekusi Pembuatan Link Meeting via Zoom API
     */
    public function generateMeeting(Request $request, ZoomBooking $booking)
    {
        $request->validate([
            'topic'      => ['required', 'string', 'max:255'],
            'start_time' => ['required', 'date'],
            'duration'   => ['required', 'integer', 'min:15'],
            'passcode'   => ['nullable', 'string', 'max:10'],
        ]);

        $account = $booking->account;
        if (!$account) {
            return back()->with('error', 'Akun Zoom belum ditentukan untuk booking ini.');
        }

        try {
            // Dapatkan token menggunakan kredensial spesifik akun (atau fallback env)
            $token = $this->zoomService->getAccessToken(
                $account->account_id,
                $account->client_id,
                $account->client_secret
            );

            $startTimeFormatted = Carbon::parse($request->start_time)->format('Y-m-d\TH:i:s');

            $customSettings = [];
            if ($request->has('waiting_room')) {
                $customSettings['waiting_room'] = (bool) $request->waiting_room;
            }
            if ($request->has('join_before_host')) {
                $customSettings['join_before_host'] = (bool) $request->join_before_host;
            }
            if ($request->has('auto_recording')) {
                $customSettings['auto_recording'] = $request->auto_recording;
            }
            if ($request->has('mute_upon_entry')) {
                $customSettings['mute_upon_entry'] = (bool) $request->mute_upon_entry;
            }

            $zoomResponse = $this->zoomService->createMeeting([
                'topic'      => $request->topic,
                'start_time' => $startTimeFormatted,
                'duration'   => (int) $request->duration,
                'agenda'     => $booking->description,
                'password'   => $request->passcode,
                'settings'   => $customSettings,
            ], $token);

            $booking->update([
                'zoom_meeting_id' => $zoomResponse['id'],
                'join_url'        => $zoomResponse['join_url'],
                'start_url'       => $zoomResponse['start_url'] ?? null,
                'passcode'        => $zoomResponse['password'] ?? $request->passcode,
                'status'          => ZoomBooking::STATUS_EXECUTED, // Status 3: Executed
            ]);

            return back()->with('success', 'Link Zoom Meeting berhasil dibuat dan aktif via API!');
        } catch (Exception $e) {
            return back()->with('error', 'Gagal memproses API Zoom: ' . $e->getMessage());
        }
    }

    public function destroy(ZoomBooking $booking)
    {
        if (!auth()->user()->isAdmin() && $booking->user_id !== auth()->id()) {
            abort(403);
        }

        $zoomDeleted = false;
        $zoomError = null;

        // Jika meeting sudah digenerate di Zoom API, hapus juga langsung dari Cloud Zoom
        if ($booking->zoom_meeting_id && $booking->account) {
            try {
                $token = $this->zoomService->getAccessToken(
                    $booking->account->account_id,
                    $booking->account->client_id,
                    $booking->account->client_secret
                );
                
                $zoomDeleted = $this->zoomService->deleteMeeting($booking->zoom_meeting_id, $token);
            } catch (\Throwable $e) {
                $zoomError = $e->getMessage();
                \Illuminate\Support\Facades\Log::warning("Gagal menghapus meeting di Zoom API ID {$booking->zoom_meeting_id}: " . $zoomError);
            }
        }

        $booking->delete();

        if ($zoomDeleted) {
            return redirect()->route('bookings.index')->with('success', 'Data booking dan jadwal meeting di cloud Zoom berhasil dihapus.');
        }

        if ($zoomError) {
            return redirect()->route('bookings.index')->with('info', 'Booking di portal berhasil dihapus, namun terjadi kendala saat menghapus di Zoom API: ' . $zoomError);
        }

        return redirect()->route('bookings.index')->with('success', 'Booking berhasil dihapus.');
    }

    /**
     * Helper untuk mendeteksi apakah terjadi overlap/bentrok waktu pada akun yang sama
     */
    private function hasScheduleConflict($accountId, $startDate, $endDate, $startTime, $endTime, $ignoreBookingId = null): bool
    {
        $query = ZoomBooking::where('zoom_account_id', $accountId)
            ->whereIn('status', [ZoomBooking::STATUS_APPROVED, ZoomBooking::STATUS_EXECUTED])
            ->whereDate('start_date', '<=', $endDate)
            ->whereDate('end_date', '>=', $startDate)
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where(function ($sub) use ($startTime, $endTime) {
                    // Mulai atau Selesai berada di antara jadwal yang sudah ada
                    $sub->whereBetween('start_time', [$startTime, $endTime])
                        ->orWhereBetween('end_time', [$startTime, $endTime]);
                })->orWhere(function ($sub) use ($startTime, $endTime) {
                    // Atau jadwal baru mencakup penuh jadwal yang sudah ada
                    $sub->where('start_time', '>=', $startTime)
                        ->where('end_time', '<=', $endTime);
                })->orWhere(function ($sub) use ($startTime, $endTime) {
                    // Atau jadwal baru berada di dalam rentang jadwal yang sudah ada
                    $sub->where('start_time', '<=', $startTime)
                        ->where('end_time', '>=', $endTime);
                });
            });

        if ($ignoreBookingId) {
            $query->where('id', '!=', $ignoreBookingId);
        }

        return $query->exists();
    }
}
