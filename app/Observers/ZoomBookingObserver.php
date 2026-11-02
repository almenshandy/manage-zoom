<?php

namespace App\Observers;

use App\Models\ZoomBooking;
use App\Models\ActivityLog;

class ZoomBookingObserver
{
    public function created(ZoomBooking $booking): void
    {
        // Pastikan relasi user (pemohon) termuat
        $applicantName = $booking->user?->name;
        if (!$applicantName && $booking->user_id) {
            $applicant = \App\Models\User::find($booking->user_id);
            $applicantName = $applicant?->name;
        }

        $applicantStr = $applicantName ? " atas nama {$applicantName}" : '';

        ActivityLog::create([
            'user_id'     => auth()->id() ?? $booking->user_id,
            'action'      => 'create_booking',
            'description' => "Pengajuan booking baru: '{$booking->topic}' (ID: {$booking->id}){$applicantStr}",
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
        ]);
    }

    public function updated(ZoomBooking $booking): void
    {
        if ($booking->wasChanged('status')) {
            $applicantName = $booking->user?->name;
            $applicantStr = $applicantName ? " (Pemohon: {$applicantName})" : '';

            ActivityLog::create([
                'user_id'     => auth()->id() ?? $booking->user_id,
                'action'      => 'update_status',
                'description' => "Status booking '{$booking->topic}' diubah menjadi: {$booking->status_label}{$applicantStr}",
                'ip_address'  => request()->ip(),
                'user_agent'  => request()->userAgent(),
            ]);
        }
    }

    public function deleted(ZoomBooking $booking): void
    {
        $applicantName = $booking->user?->name;
        $applicantStr = $applicantName ? " (Pemohon: {$applicantName})" : '';

        ActivityLog::create([
            'user_id'     => auth()->id() ?? $booking->user_id,
            'action'      => 'delete_booking',
            'description' => "Menghapus booking '{$booking->topic}' (ID: {$booking->id}){$applicantStr}",
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
        ]);
    }
}
