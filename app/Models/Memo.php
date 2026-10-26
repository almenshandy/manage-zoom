<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Memo extends Model
{
    use HasFactory;

    protected $fillable = [
        'zoom_booking_id',
        'user_id',
        'memo_number',
        'content',
        'memo_date',
        'has_signature',
    ];

    protected function casts(): array
    {
        return [
            'memo_date' => 'date',
            'has_signature' => 'boolean',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(ZoomBooking::class, 'zoom_booking_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
