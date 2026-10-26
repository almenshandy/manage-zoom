<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ZoomAccount;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Admin Default
        User::firstOrCreate(
            ['email' => 'admin@zoom.test'],
            [
                'name'       => 'Administrator Zoom',
                'username'   => 'admin',
                'password'   => Hash::make('password'),
                'role'       => 1, // Admin
                'jabatan'    => 'Super Administrator',
                'departemen' => 'TI / Pusat Komputer',
                'no_hp'      => '081234567890',
            ]
        );

        // 2. Akun Pegawai / User Contoh
        User::firstOrCreate(
            ['email' => 'user@zoom.test'],
            [
                'name'       => 'Staf Humas & Kerjasama',
                'username'   => 'stafhumas',
                'password'   => Hash::make('password'),
                'role'       => 2, // User
                'jabatan'    => 'Pranata Humas',
                'departemen' => 'Biro Humas & Protokol',
                'no_hp'      => '089876543210',
            ]
        );

        // 3. Akun Zoom Master Contoh
        ZoomAccount::firstOrCreate(
            ['email' => 'zoom1@institusi.ac.id'],
            [
                'name'         => 'Zoom Pro Ruang A (300 Peserta)',
                'capacity'     => 300,
                'expired_date' => now()->addMonths(12),
                'status'       => 'active',
            ]
        );

        ZoomAccount::firstOrCreate(
            ['email' => 'zoom2@institusi.ac.id'],
            [
                'name'         => 'Zoom Enterprise Ruang B (500 Peserta)',
                'capacity'     => 500,
                'expired_date' => now()->addMonths(6),
                'status'       => 'active',
            ]
        );
    }
}
