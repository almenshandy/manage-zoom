<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('whatsapp_bulk_batches', function (Blueprint $table) {
            $table->id();
            $table->uuid('request_id')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('provider', 20);
            $table->unsignedTinyInteger('interval_seconds');
            $table->unsignedInteger('total_messages');
            $table->timestamps();
            $table->index(['user_id', 'created_at']);
        });
        Schema::create('whatsapp_bulk_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('whatsapp_bulk_batches')->cascadeOnDelete();
            $table->foreignId('booking_id')->nullable()->constrained('zoom_bookings')->nullOnDelete();
            $table->string('topic');
            $table->string('recipient', 20);
            $table->text('message');
            $table->string('status', 20)->default('queued');
            $table->text('error')->nullable();
            $table->timestamp('attempted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->unique(['batch_id', 'booking_id', 'recipient']);
            $table->index(['batch_id', 'status']);
        });
        Schema::create('whatsapp_bulk_dispatch_state', function (Blueprint $table) {
            $table->unsignedTinyInteger('id')->primary();
            $table->unsignedBigInteger('next_allowed_at_ms')->default(0);
            $table->uuid('reservation_token')->nullable();
        });
        DB::table('whatsapp_bulk_dispatch_state')->insert(['id' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_bulk_messages');
        Schema::dropIfExists('whatsapp_bulk_batches');
        Schema::dropIfExists('whatsapp_bulk_dispatch_state');
    }
};
