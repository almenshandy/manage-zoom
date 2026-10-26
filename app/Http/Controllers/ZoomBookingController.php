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
        $accounts = ZoomAccount::where('status', 'active')->get();

        return view('bookings.index', compact('bookings', 'accounts'));
    }

    public function create()
    {
        $accounts = ZoomAccount::where('status', 'active')->get();
        return view('bookings.create', compact('accounts'));
    }

    public function store(StoreBookingRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id() ?? 1;
        $validated['status']  = ZoomBooking::STATUS_PENDING;

        // Hitung jumlah hari
        $start = Carbon::parse($validated['start_date']);
        $end = Carbon::parse($validated['end_date']);
        $validated['day_count'] = $start->diffInDays($end) + 1;

        // Cek potensi bentrok jadwal jika akun sudah dipilih
        if (!empty($validated['zoom_account_id'])) {
            $conflict = ZoomBooking::where('zoom_account_id', $validated['zoom_account_id'])
                ->whereIn('status', [ZoomBooking::STATUS_APPROVED, ZoomBooking::STATUS_EXECUTED])
                ->whereDate('start_date', '<=', $validated['end_date'])
                ->whereDate('end_date', '>=', $validated['start_date'])
                ->where(function ($q) use ($validated) {
                    $q->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                      ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']]);
                })
                ->exists();

            if ($conflict) {
                return back()->withInput()->with('error', 'Peringatan: Akun Zoom yang dipilih sedang digunakan pada tanggal dan jam tersebut!');
            }
        }

        $booking = ZoomBooking::create($validated);

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

        $accounts = ZoomAccount::where('status', 'active')->get();
        return view('bookings.edit', compact('booking', 'accounts'));
    }

    public function update(UpdateBookingRequest $request, ZoomBooking $booking)
    {
        if (!auth()->user()->isAdmin() && $booking->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validated();
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

            $zoomResponse = $this->zoomService->createMeeting([
                'topic'      => $request->topic,
                'start_time' => $startTimeFormatted,
                'duration'   => (int) $request->duration,
                'agenda'     => $booking->description,
                'password'   => $request->passcode,
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

        // Jika ada meeting di Zoom API, hapus jika memungkinkan
        if ($booking->zoom_meeting_id && $booking->account) {
            try {
                $token = $this->zoomService->getAccessToken(
                    $booking->account->account_id,
                    $booking->account->client_id,
                    $booking->account->client_secret
                );
                $this->zoomService->deleteMeeting($booking->zoom_meeting_id, $token);
            } catch (\Throwable $e) {
                // Abaikan error Zoom saat delete
            }
        }

        $booking->delete();
        return redirect()->route('bookings.index')->with('success', 'Booking berhasil dihapus.');
    }
}
