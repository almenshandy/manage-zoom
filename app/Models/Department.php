<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_departemen',
        'nama_departemen',
        'rumpun_direksi',
        'pejabat',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'departemen', 'kode_departemen');
    }
}
