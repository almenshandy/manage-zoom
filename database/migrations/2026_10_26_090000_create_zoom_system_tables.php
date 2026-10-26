<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. zoom_accounts (Master Akun Zoom & Kredensial API Server-to-Server)
        Schema::create('zoom_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Label/Nama Akun Zoom, misal: Akun 01 - 500 Peserta');
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->integer('capacity')->default(100)->comment('Kapasitas peserta');
            $table->date('expired_date')->nullable();
            $table->string('password_hotmail')->nullable();
            // Server-to-Server OAuth Credentials
            $table->string('account_id')->nullable();
            $table->string('client_id')->nullable();
            $table->text('client_secret')->nullable();
            $table->text('secret_token')->nullable();
            $table->text('verification_token')->nullable();
            $table->enum('status', ['active', 'inactive', 'maintenance'])->default('active');
            $table->timestamps();
        });

        // 2. zoom_renewals (History Perpanjangan Akun)
        Schema::create('zoom_renewals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zoom_account_id')->constrained('zoom_accounts')->cascadeOnDelete();
            $table->string('invoice')->nullable();
            $table->date('renewal_date')->nullable();
            $table->dateTime('entry_date')->nullable();
            $table->timestamps();
        });

        // 3. zoom_bookings (Pengajuan Peminjaman Zoom)
        Schema::create('zoom_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('zoom_account_id')->nullable()->constrained('zoom_accounts')->nullOnDelete();
            $table->string('topic');
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('duration_minutes')->default(60);
            $table->integer('day_count')->default(1);
            $table->text('days_detail')->nullable()->comment('JSON / list hari kegiatan');
            
            // Status flow: 0=pending, 1=approved (ACC), 2=rejected, 3=executed (link ready), 4=completed, 5=cancelled
            $table->tinyInteger('status')->default(0)->comment('0: Pending, 1: Approved/ACC, 2: Rejected, 3: Executed, 4: Completed, 5: Cancelled');
            
            $table->text('admin_note')->nullable();
            $table->text('user_note')->nullable();
            
            // Zoom Meeting Execution details (bisa diisi saat execute atau manual)
            $table->string('zoom_meeting_id')->nullable();
            $table->text('join_url')->nullable();
            $table->text('start_url')->nullable();
            $table->string('passcode')->nullable();
            
            $table->timestamps();
        });

        // 4. zoom_meetings (Detail Jadwal Pertemuan / Multiday Link)
        Schema::create('zoom_meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zoom_booking_id')->constrained('zoom_bookings')->cascadeOnDelete();
            $table->foreignId('zoom_account_id')->nullable()->constrained('zoom_accounts')->nullOnDelete();
            $table->date('meeting_date');
            $table->time('meeting_time')->nullable();
            $table->string('activity_name');
            $table->string('zoom_meeting_id')->nullable();
            $table->text('join_url')->nullable();
            $table->string('passcode')->nullable();
            $table->timestamps();
        });

        // 5. memos (Surat & Memo Kegiatan)
        Schema::create('memos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zoom_booking_id')->nullable()->constrained('zoom_bookings')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('memo_number')->nullable();
            $table->longText('content')->nullable();
            $table->date('memo_date')->nullable();
            $table->boolean('has_signature')->default(false);
            $table->timestamps();
        });

        // 6. activity_logs (Audit Trail Otomatis)
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action'); // create_booking, approve_booking, reject_booking, generate_meeting, etc.
            $table->text('description');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('memos');
        Schema::dropIfExists('zoom_meetings');
        Schema::dropIfExists('zoom_bookings');
        Schema::dropIfExists('zoom_renewals');
        Schema::dropIfExists('zoom_accounts');
    }
};
