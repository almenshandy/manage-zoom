<?php

namespace App\Http\Controllers;

use App\Models\ZoomBooking;
use App\Models\ZoomAccount;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        $stats = [
            'total_accounts'     => ZoomAccount::count(),
            'active_accounts'    => ZoomAccount::whereDate('expired_date', '>=', $today)->count(),
            'pending_bookings'   => ZoomBooking::where('status', ZoomBooking::STATUS_PENDING)->count(),
            'approved_bookings'  => ZoomBooking::where('status', ZoomBooking::STATUS_APPROVED)->count(),
            'executed_bookings'  => ZoomBooking::where('status', ZoomBooking::STATUS_EXECUTED)->count(),
            'today_meetings'     => ZoomBooking::where('status', ZoomBooking::STATUS_EXECUTED)
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->count(),
        ];

        $upcomingBookings = ZoomBooking::with(['user', 'account'])
            ->whereIn('status', [ZoomBooking::STATUS_APPROVED, ZoomBooking::STATUS_EXECUTED])
            ->whereDate('end_date', '>=', $today)
            ->orderBy('start_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->take(5)
            ->get();

        $recentPending = ZoomBooking::with(['user', 'account'])
            ->where('status', ZoomBooking::STATUS_PENDING)
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'upcomingBookings', 'recentPending'));
    }
}
