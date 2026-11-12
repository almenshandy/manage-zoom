<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use App\Models\ZoomBooking;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BookingProjectMigrationTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_existing_bookings_link_only_to_unambiguous_names_without_losing_legacy_labels(): void
    {
        $this->freezeTime();
        $user = User::factory()->create();
        $unique = Project::factory()->create(['name' => 'Proyek Unik']);
        Project::factory()->count(2)->create(['name' => 'Nama Duplikat']);
        $bookings = collect(['Proyek Unik', 'Nama Duplikat', 'Proyek Lama Tidak Ada', null, ''])->map(
            fn (?string $name): ZoomBooking => ZoomBooking::factory()->create(['user_id' => $user->id, 'project_name' => $name])
        );
        $migration = require database_path('migrations/2026_11_12_164012_add_project_id_to_zoom_bookings_table.php');
        $migration->down();

        $migration->up();

        $this->assertSame($unique->id, (int) DB::table('zoom_bookings')->where('id', $bookings[0]->id)->value('project_id'));
        foreach ($bookings as $index => $booking) {
            $row = DB::table('zoom_bookings')->where('id', $booking->id)->first();
            $this->assertSame($booking->project_name, $row->project_name);
            $this->assertSame($booking->updated_at->toDateTimeString(), $row->updated_at);
            if ($index > 0) {
                $this->assertNull($row->project_id);
            }
        }
    }
}
