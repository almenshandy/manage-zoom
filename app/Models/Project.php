<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id',
        'name',
        'code',
        'description',
        'start_date',
        'end_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    /**
     * Relasi ke Departemen penanggung jawab proyek
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * Relasi ke seluruh booking Zoom yang terkait dengan proyek ini
     */
    public function bookings()
    {
        return $this->hasMany(ZoomBooking::class, 'project_name', 'name');
    }
}
