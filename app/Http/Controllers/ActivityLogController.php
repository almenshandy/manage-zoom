<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ActivityLogController extends Controller
{
    /**
     * Tampilkan daftar riwayat aktivitas sistem dengan fitur pencarian dan filter
     */
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->latest();

        // 1. Filter Pencarian Teks (Deskripsi, Topik, atau Nama User)
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // 2. Filter Tipe Aksi
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // 3. Filter Rentang Tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $logs = $query->paginate(25)->withQueryString();

        // Daftar aksi yang tersedia untuk opsi filter
        $availableActions = [
            'create_booking' => 'Pengajuan Booking Baru',
            'update_status'  => 'Perubahan Status Booking',
            'delete_booking' => 'Penghapusan Booking',
        ];

        // Statistik singkat
        $totalLogsCount = ActivityLog::count();
        $todayLogsCount = ActivityLog::whereDate('created_at', Carbon::today())->count();

        return view('activity_logs.index', compact('logs', 'availableActions', 'totalLogsCount', 'todayLogsCount'));
    }

    /**
     * Bersihkan riwayat log (Log Pruning)
     */
    public function clear(Request $request)
    {
        $request->validate([
            'retention' => 'required|string|in:all,30_days,60_days,90_days',
        ]);

        $retention = $request->input('retention');
        $deletedCount = 0;

        if ($retention === 'all') {
            $deletedCount = ActivityLog::count();
            ActivityLog::truncate();
            $msg = "Seluruh riwayat log aktivitas ({$deletedCount} rekaman) berhasil dibersihkan.";
        } elseif ($retention === '30_days') {
            $date = Carbon::now()->subDays(30);
            $deletedCount = ActivityLog::where('created_at', '<', $date)->delete();
            $msg = "Riwayat log yang lebih lama dari 30 hari ({$deletedCount} rekaman) berhasil dibersihkan.";
        } elseif ($retention === '60_days') {
            $date = Carbon::now()->subDays(60);
            $deletedCount = ActivityLog::where('created_at', '<', $date)->delete();
            $msg = "Riwayat log yang lebih lama dari 60 hari ({$deletedCount} rekaman) berhasil dibersihkan.";
        } elseif ($retention === '90_days') {
            $date = Carbon::now()->subDays(90);
            $deletedCount = ActivityLog::where('created_at', '<', $date)->delete();
            $msg = "Riwayat log yang lebih lama dari 90 hari ({$deletedCount} rekaman) berhasil dibersihkan.";
        }

        return redirect()->route('activity-logs.index')->with('success', $msg);
    }
}
