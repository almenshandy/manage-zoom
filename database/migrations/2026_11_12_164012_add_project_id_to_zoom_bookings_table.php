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
        Schema::table('zoom_bookings', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
        });

        DB::table('projects')->select('name')->selectRaw('MIN(id) AS id')
            ->where('name', '!=', '')->groupBy('name')->havingRaw('COUNT(*) = 1')->orderBy('name')
            ->chunk(200, function ($projects): void {
                foreach ($projects as $project) {
                    DB::table('zoom_bookings')->whereNull('project_id')->where('project_name', $project->name)
                        ->update(['project_id' => $project->id]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zoom_bookings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('project_id');
        });
    }
};
