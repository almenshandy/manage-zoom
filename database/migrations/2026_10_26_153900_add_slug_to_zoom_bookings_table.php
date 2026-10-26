<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('zoom_bookings', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique()->after('topic');
        });

        // Generate slug untuk data booking yang sudah ada
        $bookings = DB::table('zoom_bookings')->get();
        foreach ($bookings as $b) {
            $baseSlug = Str::slug($b->topic);
            $slug = $baseSlug ?: 'booking';
            $uniqueSlug = $slug . '-' . $b->id;

            DB::table('zoom_bookings')->where('id', $b->id)->update([
                'slug' => $uniqueSlug,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('zoom_bookings', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
