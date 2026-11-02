<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'is_active',
        'jabatan',
        'departemen',
        'departemen2',
        'no_hp',
        'foto',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 1;
    }

    public function isUser(): bool
    {
        return $this->role !== 1;
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(ZoomBooking::class);
    }

    public function memos(): HasMany
    {
        return $this->hasMany(Memo::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function departmentRel()
    {
        return $this->belongsTo(Department::class, 'departemen', 'kode_departemen');
    }

    public function positionRel()
    {
        return $this->belongsTo(Position::class, 'jabatan', 'kode_jabatan');
    }

    /**
     * Accessor nama departemen yang human-readable
     */
    public function getDepartmentNameAttribute(): string
    {
        if (empty($this->departemen)) {
            return '-';
        }

        // Cek jika relasi department ada berdasarkan kode (misal: DP-001)
        if ($this->relationLoaded('departmentRel') && $this->departmentRel) {
            return $this->departmentRel->nama_departemen;
        }

        $dept = Department::where('kode_departemen', $this->departemen)->first();
        if ($dept) {
            return $dept->nama_departemen;
        }

        return $this->departemen;
    }

    /**
     * Accessor nama jabatan yang human-readable
     */
    public function getPositionNameAttribute(): string
    {
        if (empty($this->jabatan)) {
            return '-';
        }

        // Cek jika relasi position ada berdasarkan kode (misal: JBT-01)
        if ($this->relationLoaded('positionRel') && $this->positionRel) {
            return $this->positionRel->nama_jabatan;
        }

        $pos = Position::where('kode_jabatan', $this->jabatan)->first();
        if ($pos) {
            return $pos->nama_jabatan;
        }

        return $this->jabatan;
    }
}
