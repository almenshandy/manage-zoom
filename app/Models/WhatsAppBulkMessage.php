<?php

namespace App\Models;

use Database\Factories\WhatsAppBulkMessageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppBulkMessage extends Model
{
    /** @use HasFactory<WhatsAppBulkMessageFactory> */
    use HasFactory;

    protected $table = 'whatsapp_bulk_messages';

    protected $fillable = ['batch_id', 'booking_id', 'topic', 'recipient', 'message', 'account_credentials', 'status', 'error', 'attempted_at', 'completed_at'];

    protected $hidden = ['account_credentials'];

    protected function casts(): array
    {
        return ['attempted_at' => 'datetime', 'completed_at' => 'datetime', 'account_credentials' => 'encrypted:array'];
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(WhatsAppBulkBatch::class, 'batch_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(ZoomBooking::class, 'booking_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'queued' => 'Menunggu',
            'sending' => 'Sedang dikirim',
            'sent' => 'Diterima gateway',
            'unknown' => 'Perlu diperiksa',
            default => 'Gagal',
        };
    }
}
