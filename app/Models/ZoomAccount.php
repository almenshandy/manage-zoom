<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ZoomAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'capacity',
        'expired_date',
        'password_hotmail',
        'account_id',
        'client_id',
        'client_secret',
        'secret_token',
        'verification_token',
        'status',
    ];

    protected $hidden = [
        'password',
        'password_hotmail',
        'client_secret',
        'secret_token',
        'verification_token',
    ];

    protected function casts(): array
    {
        return [
            'expired_date' => 'date',
            'capacity' => 'integer',
        ];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(ZoomBooking::class);
    }

    public function renewals(): HasMany
    {
        return $this->hasMany(ZoomRenewal::class);
    }

    public function meetings(): HasMany
    {
        return $this->hasMany(ZoomMeeting::class);
    }
}
