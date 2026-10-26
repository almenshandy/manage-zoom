<?php

namespace App\Http\Controllers;

use App\Models\ZoomAccount;
use App\Services\ZoomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ZoomAccountController extends Controller
{
    protected ZoomService $zoomService;

    public function __construct(ZoomService $zoomService)
    {
        $this->zoomService = $zoomService;
    }

    public function index()
    {
        $accounts = ZoomAccount::withCount('bookings')
            ->orderByRaw('expired_date IS NULL ASC')
            ->orderBy('expired_date', 'desc')
            ->latest('id')
            ->paginate(10);

        return view('zoom_accounts.index', compact('accounts'));
    }

    /**
     * Ekspor Laporan Rekap Akun Zoom ke Format PDF
     */
    public function exportPdf(Request $request)
    {
        $query = ZoomAccount::withCount('bookings')
            ->orderByRaw('expired_date IS NULL ASC')
            ->orderBy('expired_date', 'desc')
            ->latest('id');

        if ($request->filled('status')) {
            if ($request->status === 'valid') {
                $query->whereDate('expired_date', '>=', now()->toDateString());
            } elseif ($request->status === 'expired') {
                $query->whereDate('expired_date', '<', now()->toDateString());
            }
        }

        $accounts = $query->get();

        $pdf = Pdf::loadView('zoom_accounts.pdf', compact('accounts'))
            ->setPaper('a4', 'portrait');

        $filename = "rekap-akun-zoom-" . date('Ymd_His') . ".pdf";
        return $pdf->stream($filename);
    }

    /**
     * Preview sinkronisasi akun dari database local 'zoom' (JSON Response untuk modal pop-up)
     */
    public function syncPreview(Request $request)
    {
        try {
            $sourceAccounts = DB::select("SELECT * FROM zoom.zoom_accounts ORDER BY (tanggal_expired IS NULL) ASC, tanggal_expired DESC, id DESC");
        } catch (\Throwable $e) {
            if ($request->wantsJson()) {
                return response()->json(['error' => 'Gagal terhubung ke database `zoom`: ' . $e->getMessage()], 500);
            }
            return redirect()->route('zoom-accounts.index')->with('error', 'Gagal terhubung ke database `zoom`: ' . $e->getMessage());
        }

        $existingEmails = ZoomAccount::pluck('email')->toArray();

        $previewData = [];
        foreach ($sourceAccounts as $item) {
            // Mapping status
            $mappedStatus = match ((string) $item->status) {
                '1'     => 'active',
                '2'     => 'inactive',
                '0'     => 'maintenance',
                default => 'inactive',
            };

            $email = trim($item->email ?? '');
            $name = $email ? 'Akun Zoom (' . explode('@', $email)[0] . ')' : 'Akun Zoom #' . $item->id;
            $exists = in_array(strtolower($email), array_map('strtolower', $existingEmails));

            $previewData[] = [
                'source_id'          => $item->id,
                'email'              => $email,
                'name'               => $name,
                'capacity'           => (int) ($item->kapasitas ?: 100),
                'expired_date'       => $item->tanggal_expired,
                'status'             => $mappedStatus,
                'raw_status'         => $item->status,
                'password'           => $item->password,
                'password_hotmail'   => $item->password_hotmail,
                'has_credentials'    => !empty($item->idakunapi) && !empty($item->clientidapi),
                'is_existing'        => $exists,
            ];
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'data'    => $previewData,
                'summary' => [
                    'total'    => count($previewData),
                    'new'      => collect($previewData)->where('is_existing', false)->count(),
                    'existing' => collect($previewData)->where('is_existing', true)->count(),
                ]
            ]);
        }

        return view('zoom_accounts.sync_preview', compact('previewData'));
    }

    /**
     * Eksekusi sinkronisasi data yang dipilih
     */
    public function syncExecute(Request $request)
    {
        $selectedIds = $request->input('selected_ids', []);
        $mode = $request->input('mode', 'skip'); // 'skip' or 'update'

        if (empty($selectedIds)) {
            return redirect()->route('zoom-accounts.sync-preview')->with('error', 'Pilih minimal satu akun untuk disinkronkan.');
        }

        try {
            $placeholders = implode(',', array_fill(0, count($selectedIds), '?'));
            $sourceAccounts = DB::select("SELECT * FROM zoom.zoom_accounts WHERE id IN ($placeholders)", $selectedIds);
        } catch (\Throwable $e) {
            return redirect()->route('zoom-accounts.index')->with('error', 'Gagal membaca database `zoom`: ' . $e->getMessage());
        }

        $importedCount = 0;
        $updatedCount = 0;

        foreach ($sourceAccounts as $item) {
            $email = trim($item->email ?? '');
            if (!$email) {
                continue;
            }

            $mappedStatus = match ((string) $item->status) {
                '1'     => 'active',
                '2'     => 'inactive',
                '0'     => 'maintenance',
                default => 'inactive',
            };

            $name = 'Akun Zoom (' . explode('@', $email)[0] . ')';

            $data = [
                'name'               => $name,
                'password'           => $item->password,
                'capacity'           => (int) ($item->kapasitas ?: 100),
                'expired_date'       => $item->tanggal_expired,
                'password_hotmail'   => $item->password_hotmail,
                'account_id'         => $item->idakunapi,
                'client_id'          => $item->clientidapi,
                'client_secret'      => $item->secretapi,
                'secret_token'       => $item->secret2api,
                'verification_token' => $item->veriftokenapi,
                'status'             => $mappedStatus,
            ];

            $existing = ZoomAccount::where('email', $email)->first();

            if ($existing) {
                if ($mode === 'update') {
                    $existing->update($data);
                    $updatedCount++;
                }
            } else {
                $data['email'] = $email;
                ZoomAccount::create($data);
                $importedCount++;
            }
        }

        return redirect()->route('zoom-accounts.index')->with(
            'success',
            "Sinkronisasi selesai! Berhasil menambahkan {$importedCount} akun baru dan memperbarui {$updatedCount} akun yang ada."
        );
    }

    public function create()
    {
        return view('zoom_accounts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'               => ['required', 'string', 'max:255'],
            'email'              => ['required', 'email', 'unique:zoom_accounts,email'],
            'password'           => ['nullable', 'string', 'max:255'],
            'capacity'           => ['required', 'integer', 'min:1'],
            'expired_date'       => ['nullable', 'date'],
            'password_hotmail'   => ['nullable', 'string'],
            'account_id'         => ['nullable', 'string'],
            'client_id'          => ['nullable', 'string'],
            'client_secret'      => ['nullable', 'string'],
            'secret_token'       => ['nullable', 'string'],
            'verification_token' => ['nullable', 'string'],
            'status'             => ['required', 'in:active,inactive,maintenance'],
        ]);

        ZoomAccount::create($validated);

        return redirect()->route('zoom-accounts.index')->with('success', 'Akun Zoom berhasil ditambahkan.');
    }

    public function edit(ZoomAccount $zoomAccount)
    {
        return view('zoom_accounts.edit', compact('zoomAccount'));
    }

    public function update(Request $request, ZoomAccount $zoomAccount)
    {
        $validated = $request->validate([
            'name'               => ['required', 'string', 'max:255'],
            'email'              => ['required', 'email', 'unique:zoom_accounts,email,' . $zoomAccount->id],
            'password'           => ['nullable', 'string', 'max:255'],
            'capacity'           => ['required', 'integer', 'min:1'],
            'expired_date'       => ['nullable', 'date'],
            'password_hotmail'   => ['nullable', 'string'],
            'account_id'         => ['nullable', 'string'],
            'client_id'          => ['nullable', 'string'],
            'client_secret'      => ['nullable', 'string'],
            'secret_token'       => ['nullable', 'string'],
            'verification_token' => ['nullable', 'string'],
            'status'             => ['required', 'in:active,inactive,maintenance'],
        ]);

        // Only update secret fields if provided
        if (empty($validated['client_secret'])) {
            unset($validated['client_secret']);
        }
        if (empty($validated['secret_token'])) {
            unset($validated['secret_token']);
        }
        if (empty($validated['verification_token'])) {
            unset($validated['verification_token']);
        }

        $zoomAccount->update($validated);

        return redirect()->route('zoom-accounts.index')->with('success', 'Data Akun Zoom berhasil diperbarui.');
    }

    public function destroy(ZoomAccount $zoomAccount)
    {
        $zoomAccount->delete();
        return redirect()->route('zoom-accounts.index')->with('success', 'Akun Zoom berhasil dihapus.');
    }

    /**
     * Uji coba koneksi API Zoom untuk akun tertentu
     */
    public function testConnection(ZoomAccount $zoomAccount)
    {
        $result = $this->zoomService->testConnection(
            $zoomAccount->account_id,
            $zoomAccount->client_id,
            $zoomAccount->client_secret
        );

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json($result);
        }

        if ($result['success']) {
            return back()->with('success', "Koneksi API Berhasil untuk {$zoomAccount->name}!");
        }

        return back()->with('error', "Koneksi API Gagal: {$result['message']}");
    }
}
