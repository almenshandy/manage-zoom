<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_jabatan',
        'nama_jabatan',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'jabatan', 'kode_jabatan');
    }
}
