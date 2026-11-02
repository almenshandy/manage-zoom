<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $fonnteDefaults = [
            [
                'key' => 'fonnte_token',
                'value' => null,
                'group' => 'fonnte',
                'type' => 'password',
                'label' => 'Fonnte Device / Account Token',
                'description' => 'Token API dari akun Fonnte Anda (menu Device di fonnte.com). Token ini digunakan untuk otentikasi pengiriman WhatsApp API.',
            ],
            [
                'key' => 'fonnte_enabled',
                'value' => '1',
                'group' => 'fonnte',
                'type' => 'boolean',
                'label' => 'Aktifkan Pengiriman WhatsApp via Fonnte',
                'description' => 'Jika diaktifkan, tombol kirim WhatsApp di halaman detail peminjaman akan otomatis mengirim pesan langsung via API Fonnte ke nomor tujuan.',
            ],
            [
                'key' => 'fonnte_country_code',
                'value' => '62',
                'group' => 'fonnte',
                'type' => 'string',
                'label' => 'Kode Negara Default',
                'description' => 'Kode negara default jika nomor tujuan diawali angka 0 (misal: 62 untuk Indonesia).',
            ],
        ];

        foreach ($fonnteDefaults as $d) {
            DB::table('system_settings')->updateOrInsert(
                ['key' => $d['key']],
                array_merge($d, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('system_settings')->where('group', 'fonnte')->delete();
    }
};
