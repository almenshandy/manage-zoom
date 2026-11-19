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
     * Cetak Rekap Keseluruhan Jadwal Kegiatan (Filter by Range / Bulan / Selected IDs)
     */
    public function printAll(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());
        $status = $request->input('status');
        $search = $request->input('search');

        // Dukung booking_ids baik berupa array (booking_ids[]=1&booking_ids[]=2) atau string koma (1,2,3)
        $bookingIds = $request->input('booking_ids');
        if (is_string($bookingIds)) {
            $bookingIds = array_filter(explode(',', $bookingIds));
        }

        $query = ZoomBooking::with(['user', 'account'])
            ->orderBy('start_date', 'asc')
            ->orderBy('start_time', 'asc');

        // Jika user bukan admin, hanya bisa melihat booking miliknya
        if (auth()->user() && ! auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        if (! empty($bookingIds)) {
            // Jika ada kotak centang yang dipilih, rekap HANYA yang dipilih
            $query->whereIn('id', $bookingIds);
            $isCustomSelection = true;
        } else {
            // Jika tidak ada yang dicentang, gunakan filter tanggal default / aktif
            $query->whereDate('start_date', '>=', $startDate)
                ->whereDate('end_date', '<=', $endDate);

            if ($status !== null && $status !== '') {
                $query->where('status', $status);
            }

            if (! empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('topic', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            }

            $isCustomSelection = false;
        }

        $bookings = $query->get();

        $pdf = Pdf::loadView('bookings.print_recap', compact('bookings', 'startDate', 'endDate', 'isCustomSelection'))
            ->setPaper('a4', 'landscape');

        $fileName = ! empty($bookingIds)
            ? 'rekap-jadwal-zoom-pilihan-' . now()->format('YmdHis') . '.pdf'
            : "rekap-jadwal-zoom-{$startDate}-sd-{$endDate}.pdf";

        return $pdf->stream($fileName);
    }
}
