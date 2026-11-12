<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Process;
use Tests\TestCase;

class DatabaseBackupSchemaTest extends TestCase
{
    public function test_backup_lists_only_tables_in_the_active_database(): void
    {
        DB::statement('CREATE TABLE backup_main_fixture (id INTEGER PRIMARY KEY)');
        DB::statement("ATTACH DATABASE ':memory:' AS backup_other_schema");
        DB::statement('CREATE TABLE backup_other_schema.foreign_backup_fixture (id INTEGER PRIMARY KEY)');
        $admin = User::factory()->make(['id' => 1, 'role' => 1]);
        Process::fake();

        $response = $this->actingAs($admin)->get(route('backup.index'))->assertOk();

        $this->assertSame(['backup_main_fixture'], $response->viewData('tableList'));
        $response->assertDontSee('foreign_backup_fixture');
        $this->get(route('backup.download-table-sql', 'foreign_backup_fixture'))->assertNotFound();
        Process::assertNothingRan();
    }
}
