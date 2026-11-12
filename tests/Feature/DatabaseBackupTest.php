<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\SqlBackupService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use PHPUnit\Framework\Attributes\TestWith;
use RuntimeException;
use Tests\TestCase;
use ZipArchive;

class DatabaseBackupTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_sees_database_sql_and_sql_csv_actions_for_every_table(): void
    {
        $admin = User::factory()->create(['role' => 1]);
        $response = $this->actingAs($admin)->get(route('backup.index'))->assertOk();

        $response->assertSee('Backup Database (.SQL)')->assertSee(route('backup.download-sql'))->assertSee(route('backup.download-all'));
        foreach ($response->viewData('tableList') as $table) {
            $response->assertSee(route('backup.download-table-sql', $table))->assertSee(route('backup.download-table', $table));
        }
        $this->assertSame(1, $response->viewData('rowCounts')['users']);
    }

    #[TestWith([null])]
    #[TestWith(['users'])]
    public function test_admin_downloads_sql_as_an_attachment_and_temporary_file_is_deleted(?string $table): void
    {
        $this->freezeSecond();
        $admin = User::factory()->create(['role' => 1]);
        $path = tempnam(storage_path('framework'), 'sql_test_');
        $sql = "CREATE TABLE example (id INT);\nINSERT INTO example VALUES (1);\n";
        File::put($path, $sql);
        $this->mock(SqlBackupService::class)->shouldReceive('create')->once()
            ->withArgs(fn (array $connection, ?string $selected): bool => $connection['driver'] === 'sqlite' && $selected === $table)
            ->andReturn($path);
        $route = $table === null ? route('backup.download-sql') : route('backup.download-table-sql', $table);

        $response = $this->actingAs($admin)->get($route)->assertOk()->assertDownload()->assertHeader('Content-Type', 'application/sql');

        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString(now()->format('Ymd_His').'.sql', $response->headers->get('Content-Disposition'));
        ob_start();
        try {
            $response->baseResponse->sendContent();
            $contents = ob_get_contents();
            $deletedAfterDownload = ! is_file($path);
        } finally {
            ob_end_clean();
            File::delete($path);
        }
        $this->assertSame($sql, $contents);
        $this->assertTrue($deletedAfterDownload);
    }

    #[TestWith(['missing_table'])]
    #[TestWith(['users; DROP TABLE users'])]
    #[TestWith(['--all-databases'])]
    public function test_invalid_table_is_rejected_before_starting_a_dump(string $table): void
    {
        $admin = User::factory()->create(['role' => 1]);
        Process::fake();

        $this->actingAs($admin)->get(route('backup.download-table-sql', $table))->assertNotFound();

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
        Process::assertNothingRan();
    }

    #[TestWith([2])]
    #[TestWith([3])]
    #[TestWith([4])]
    public function test_non_admin_cannot_download_sql_or_view_backup_actions(int $role): void
    {
        $user = User::factory()->create(['role' => $role]);
        Process::fake();

        $this->actingAs($user)->get(route('backup.index'))->assertForbidden();
        $this->get(route('backup.download-sql'))->assertForbidden();
        $this->get(route('backup.download-table-sql', 'users'))->assertForbidden();

        Process::assertNothingRan();
    }

    public function test_guests_are_redirected_to_login(): void
    {
        Process::fake();
        $this->get(route('backup.download-sql'))->assertRedirect(route('login'));
        $this->get(route('backup.download-table-sql', 'users'))->assertRedirect(route('login'));
        Process::assertNothingRan();
    }

    public function test_dump_failure_returns_a_readable_error_without_internal_details(): void
    {
        $admin = User::factory()->create(['role' => 1]);
        Exceptions::fake();
        $this->mock(SqlBackupService::class)->shouldReceive('create')->once()->andThrow(new RuntimeException('private connection details'));

        $this->actingAs($admin)->get(route('backup.download-sql'))->assertRedirect(route('backup.index'))
            ->assertSessionHas('error', 'Backup SQL gagal dibuat. Periksa pengaturan DB_DUMP_BINARY, koneksi dan izin database pada server.');

        Exceptions::assertReported(RuntimeException::class);
    }

    public function test_csv_export_still_downloads_existing_data(): void
    {
        $admin = User::factory()->create(['role' => 1, 'name' => 'Pengguna; "CSV"']);

        $response = $this->actingAs($admin)->get(route('backup.download-table', 'users'))->assertOk()->assertDownload();

        $contents = $response->streamedContent();
        $this->assertStringStartsWith("\xEF\xBB\xBF", $contents);
        $this->assertStringContainsString('"Pengguna; ""CSV"""', $contents);
    }

    public function test_zip_export_still_contains_each_tables_csv(): void
    {
        $admin = User::factory()->create(['role' => 1, 'name' => 'Admin ZIP Export']);

        $response = $this->actingAs($admin)->get(route('backup.download-all'))->assertOk()->assertDownload();

        $path = $response->baseResponse->getFile()->getPathname();
        $archive = new ZipArchive;
        try {
            $this->assertTrue($archive->open($path));
            $this->assertStringContainsString('Admin ZIP Export', $archive->getFromName('users.csv'));
            $this->assertNotFalse($archive->getFromName('zoom_bookings.csv'));
        } finally {
            $archive->close();
            File::delete($path);
        }
    }
}
