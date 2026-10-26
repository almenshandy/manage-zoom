<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ZoomMeeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'zoom_booking_id',
        'zoom_account_id',
        'meeting_date',
        'meeting_time',
        'activity_name',
        'zoom_meeting_id',
        'join_url',
        'passcode',
    ];

    protected function casts(): array
    {
        return [
            'meeting_date' => 'date',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(ZoomBooking::class, 'zoom_booking_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(ZoomAccount::class, 'zoom_account_id');
    }
}
