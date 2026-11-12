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
        Schema::table('whatsapp_bulk_batches', function (Blueprint $table): void {
            $table->boolean('include_credentials')->default(false);
        });
        Schema::table('whatsapp_bulk_messages', function (Blueprint $table): void {
            $table->text('account_credentials')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('whatsapp_bulk_messages', function (Blueprint $table): void {
            $table->dropColumn('account_credentials');
        });
        Schema::table('whatsapp_bulk_batches', function (Blueprint $table): void {
            $table->dropColumn('include_credentials');
        });
    }
};
