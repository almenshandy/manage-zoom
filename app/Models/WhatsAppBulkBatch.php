<?php

namespace App\Models;

use Database\Factories\WhatsAppBulkBatchFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhatsAppBulkBatch extends Model
{
    /** @use HasFactory<WhatsAppBulkBatchFactory> */
    use HasFactory;

    protected $table = 'whatsapp_bulk_batches';

    protected $fillable = ['request_id', 'user_id', 'provider', 'interval_seconds', 'total_messages', 'include_credentials'];

    protected function casts(): array
    {
        return ['interval_seconds' => 'integer', 'total_messages' => 'integer', 'include_credentials' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(WhatsAppBulkMessage::class, 'batch_id');
    }
}
