<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->default('zoom');
            $table->string('type')->default('string'); // boolean, integer, string, select
            $table->string('label');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Seed default Zoom Meeting settings
        $defaults = [
            [
                'key' => 'zoom_waiting_room',
                'value' => '1',
                'group' => 'zoom_meeting',
                'type' => 'boolean',
                'label' => 'Ruang Tunggu (Waiting Room)',
                'description' => 'Tahan peserta di ruang tunggu sebelum diizinkan masuk oleh Host.',
            ],
            [
                'key' => 'zoom_mute_upon_entry',
                'value' => '1',
                'group' => 'zoom_meeting',
                'type' => 'boolean',
                'label' => 'Mute Peserta Saat Masuk (Mute Upon Entry)',
                'description' => 'Otomatis matikan mikrofon peserta saat baru bergabung.',
            ],
            [
                'key' => 'zoom_join_before_host',
                'value' => '0',
                'group' => 'zoom_meeting',
                'type' => 'boolean',
                'label' => 'Izinkan Masuk Sebelum Host (Join Before Host)',
                'description' => 'Peserta diperbolehkan masuk ke room sebelum Host memulai rapat.',
            ],
            [
                'key' => 'zoom_jbh_time',
                'value' => '5',
                'group' => 'zoom_meeting',
                'type' => 'select',
                'label' => 'Waktu Sebelum Host (Menit)',
                'description' => 'Batas menit peserta boleh masuk sebelum jam mulai (0 = kapan saja, 5, 10, 15 menit).',
            ],
            [
                'key' => 'zoom_host_video',
                'value' => '1',
                'group' => 'zoom_meeting',
                'type' => 'boolean',
                'label' => 'Video Host Aktif Saat Masuk',
                'description' => 'Kamera Host otomatis menyala saat bergabung ke pertemuan.',
            ],
            [
                'key' => 'zoom_participant_video',
                'value' => '1',
                'group' => 'zoom_meeting',
                'type' => 'boolean',
                'label' => 'Video Peserta Aktif Saat Masuk',
                'description' => 'Kamera peserta otomatis menyala saat bergabung ke pertemuan.',
            ],
            [
                'key' => 'zoom_auto_recording',
                'value' => 'none',
                'group' => 'zoom_meeting',
                'type' => 'select',
                'label' => 'Perekaman Otomatis (Auto Recording)',
                'description' => 'Pilihan: none (tidak merekam), cloud (rekam ke Cloud Zoom), local (rekam di komputer host).',
            ],
            [
                'key' => 'zoom_meeting_authentication',
                'value' => '0',
                'group' => 'zoom_meeting',
                'type' => 'boolean',
                'label' => 'Wajib Login Akun Zoom (Authentication Required)',
                'description' => 'Hanya pengguna yang login ke aplikasi Zoom yang dapat masuk room.',
            ],
            [
                'key' => 'zoom_watermark',
                'value' => '0',
                'group' => 'zoom_meeting',
                'type' => 'boolean',
                'label' => 'Tampilkan Watermark Layar',
                'description' => 'Menempelkan watermark nama/email peserta pada layar share-screen (keamanan dokumen).',
            ],
        ];

        foreach ($defaults as $d) {
            DB::table('system_settings')->insert(array_merge($d, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
