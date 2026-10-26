<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ZoomRenewal extends Model
{
    use HasFactory;

    protected $fillable = [
        'zoom_account_id',
        'invoice',
        'renewal_date',
        'entry_date',
    ];

    protected function casts(): array
    {
        return [
            'renewal_date' => 'date',
            'entry_date' => 'datetime',
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(ZoomAccount::class, 'zoom_account_id');
    }
}
