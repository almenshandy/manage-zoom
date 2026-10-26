<?php

namespace App\Http\Controllers;

use App\Models\ZoomBooking;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class BookingReportController extends Controller
{
    /**
     * Cetak Lembar Bukti ACC / Peminjaman Satuan
     */
    public function printBooking(ZoomBooking $booking)
    {
        $booking->load(['user', 'account', 'memo']);

        $pdf = Pdf::loadView('bookings.print_receipt', compact('booking'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream("bukti-booking-zoom-{$booking->id}.pdf");
    }

    /**
     * Cetak Rekap Keseluruhan Jadwal Kegiatan (Filter by Range / Bulan)
     */
    public function printAll(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());
        $status = $request->input('status');

        $query = ZoomBooking::with(['user', 'account'])
            ->whereDate('start_date', '>=', $startDate)
            ->whereDate('end_date', '<=', $endDate)
            ->orderBy('start_date', 'asc')
            ->orderBy('start_time', 'asc');

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        $bookings = $query->get();

        $pdf = Pdf::loadView('bookings.print_recap', compact('bookings', 'startDate', 'endDate'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream("rekap-jadwal-zoom-{$startDate}-sd-{$endDate}.pdf");
    }
}
