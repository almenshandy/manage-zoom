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
        $settings = [
            // Provider Aktif (default / switcher)
            [
                'key' => 'wa_default_provider',
                'value' => 'flowkirim', // 'flowkirim' or 'fonnte'
                'group' => 'whatsapp_gateway',
                'type' => 'select',
                'label' => 'Default WhatsApp Gateway Provider',
                'description' => 'Pilih gateway WhatsApp utama yang digunakan sistem untuk mengirimkan pesan/notifikasi secara otomatis.',
            ],
            [
                'key' => 'wa_gateway_enabled',
                'value' => '1',
                'group' => 'whatsapp_gateway',
                'type' => 'boolean',
                'label' => 'Aktifkan Gateway WhatsApp Otomatis',
                'description' => 'Jika dinonaktifkan, seluruh aksi kirim pesan akan dialihkan manual ke WhatsApp Web.',
            ],
            [
                'key' => 'wa_country_code',
                'value' => '62',
                'group' => 'whatsapp_gateway',
                'type' => 'string',
                'label' => 'Kode Negara Default',
                'description' => 'Kode negara standar untuk nomor telepon lokal berawalan 0 (contoh: 62).',
            ],

            // FlowKirim Settings
            [
                'key' => 'flowkirim_token',
                'value' => null,
                'group' => 'whatsapp_gateway',
                'type' => 'password',
                'label' => 'FlowKirim API / Bearer Token',
                'description' => 'API Token dari dashboard FlowKirim (Authorization: Bearer token).',
            ],
            [
                'key' => 'flowkirim_endpoint',
                'value' => 'https://api.flowkirim.com/v1/messages',
                'group' => 'whatsapp_gateway',
                'type' => 'string',
                'label' => 'FlowKirim Send Endpoint',
                'description' => 'URL endpoint pengiriman pesan FlowKirim API (default: https://api.flowkirim.com/v1/messages).',
            ],

            // Fonnte Settings
            [
                'key' => 'fonnte_token',
                'value' => null,
                'group' => 'whatsapp_gateway',
                'type' => 'password',
                'label' => 'Fonnte Device Token',
                'description' => 'Token API dari akun Fonnte Anda (menu Device di fonnte.com).',
            ],
            [
                'key' => 'fonnte_endpoint',
                'value' => 'https://api.fonnte.com/send',
                'group' => 'whatsapp_gateway',
                'type' => 'string',
                'label' => 'Fonnte Send Endpoint',
                'description' => 'URL endpoint pengiriman pesan Fonnte API (default: https://api.fonnte.com/send).',
            ],
        ];

        foreach ($settings as $s) {
            DB::table('system_settings')->updateOrInsert(
                ['key' => $s['key']],
                array_merge($s, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        // Hapus grup lama fonnte jika ada agar tergabung rapi di whatsapp_gateway
        DB::table('system_settings')->where('group', 'fonnte')->update(['group' => 'whatsapp_gateway']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('system_settings')->where('group', 'whatsapp_gateway')->delete();
    }
};
