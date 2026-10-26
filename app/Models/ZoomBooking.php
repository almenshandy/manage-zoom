<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ZoomBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'zoom_account_id',
        'topic',
        'description',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'duration_minutes',
        'day_count',
        'days_detail',
        'status',
        'admin_note',
        'user_note',
        'zoom_meeting_id',
        'join_url',
        'start_url',
        'passcode',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'duration_minutes' => 'integer',
            'day_count' => 'integer',
            'status' => 'integer',
        ];
    }

    // Status Constants
    public const STATUS_PENDING = 0;
    public const STATUS_APPROVED = 1; // ACC
    public const STATUS_REJECTED = 2;
    public const STATUS_EXECUTED = 3; // Link Ready
    public const STATUS_COMPLETED = 4;
    public const STATUS_CANCELLED = 5;

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Menunggu Persetujuan',
            self::STATUS_APPROVED => 'Disetujui (ACC)',
            self::STATUS_REJECTED => 'Ditolak',
            self::STATUS_EXECUTED => 'Link Aktif / Siap',
            self::STATUS_COMPLETED => 'Selesai',
            self::STATUS_CANCELLED => 'Dibatalkan',
            default => 'Unknown',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'bg-amber-100 text-amber-800 border-amber-300',
            self::STATUS_APPROVED => 'bg-blue-100 text-blue-800 border-blue-300',
            self::STATUS_REJECTED => 'bg-red-100 text-red-800 border-red-300',
            self::STATUS_EXECUTED => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            self::STATUS_COMPLETED => 'bg-gray-100 text-gray-800 border-gray-300',
            self::STATUS_CANCELLED => 'bg-zinc-100 text-zinc-800 border-zinc-300',
            default => 'bg-slate-100 text-slate-800 border-slate-300',
        };
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(ZoomAccount::class, 'zoom_account_id');
    }

    public function meetings(): HasMany
    {
        return $this->hasMany(ZoomMeeting::class);
    }

    public function memo(): HasOne
    {
        return $this->hasOne(Memo::class);
    }
}
