<?php

namespace App\Http\Controllers;

use App\Models\Memo;
use App\Models\ZoomBooking;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class MemoController extends Controller
{
    public function index()
    {
        $memos = Memo::with(['booking', 'user'])->latest()->paginate(15);
        return view('memos.index', compact('memos'));
    }

    public function create(Request $request)
    {
        $booking = null;
        if ($request->has('booking_id')) {
            $booking = ZoomBooking::with(['user', 'account'])->findOrFail($request->booking_id);
        }

        $bookings = ZoomBooking::with('user')->whereIn('status', [ZoomBooking::STATUS_APPROVED, ZoomBooking::STATUS_EXECUTED])->latest()->get();
        return view('memos.create', compact('booking', 'bookings'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'zoom_booking_id' => ['nullable', 'exists:zoom_bookings,id'],
            'memo_number'     => ['required', 'string', 'max:100'],
            'memo_date'       => ['required', 'date'],
            'content'         => ['required', 'string'],
            'has_signature'   => ['nullable', 'boolean'],
        ]);

        $validated['user_id'] = auth()->id() ?? 1;
        $validated['has_signature'] = $request->has('has_signature');

        $memo = Memo::create($validated);

        return redirect()->route('memos.index')->with('success', 'Memo kegiatan berhasil diterbitkan.');
    }

    public function show(Memo $memo)
    {
        $memo->load(['booking.user', 'booking.account', 'user']);
        return view('memos.show', compact('memo'));
    }

    public function printPdf(Memo $memo)
    {
        $memo->load(['booking.user', 'booking.account', 'user']);

        $pdf = Pdf::loadView('memos.pdf', compact('memo'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream("memo-peminjaman-zoom-{$memo->id}.pdf");
    }
}
