<?php

namespace App\Http\Controllers;

use App\Models\ZoomBooking;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index()
    {
        return view('calendar.index');
    }

    public function events(Request $request)
    {
        $start = $request->query('start');
        $end   = $request->query('end');

        $query = ZoomBooking::with('account')
            ->whereNotIn('status', [ZoomBooking::STATUS_REJECTED, ZoomBooking::STATUS_CANCELLED]);

        if ($start && $end) {
            $query->whereDate('start_date', '<=', $end)
                  ->whereDate('end_date', '>=', $start);
        }

        $bookings = $query->get();

        $events = $bookings->map(function ($item) {
            $startDateTime = $item->start_date->format('Y-m-d') . 'T' . ($item->start_time ?? '08:00:00');
            $endDateTime = $item->end_date->format('Y-m-d') . 'T' . ($item->end_time ?? '17:00:00');
            $accountName = $item->account ? $item->account->name : 'Belum Ditentukan';

            $color = match ($item->status) {
                ZoomBooking::STATUS_EXECUTED => '#10B981', // Hijau
                ZoomBooking::STATUS_APPROVED => '#3B82F6', // Biru
                default => '#F59E0B',                      // Oranye (Pending)
            };

            return [
                'id'          => $item->id,
                'title'       => "[{$accountName}] {$item->topic}",
                'start'       => $startDateTime,
                'end'         => $endDateTime,
                'url'         => route('bookings.show', $item->id),
                'backgroundColor' => $color,
                'borderColor'     => $color,
                'extendedProps'   => [
                    'account' => $accountName,
                    'status'  => $item->status_label,
                ],
            ];
        });

        return response()->json($events);
    }
}
