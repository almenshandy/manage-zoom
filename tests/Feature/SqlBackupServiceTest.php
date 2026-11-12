<?php

namespace Tests\Feature;

use App\Services\SqlBackupService;
use Illuminate\Process\PendingProcess;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use PHPUnit\Framework\Attributes\TestWith;
use RuntimeException;
use Tests\TestCase;

class SqlBackupServiceTest extends TestCase
{
    #[TestWith([null, 'mysqldump Ver 8.0.30'])]
    #[TestWith(['odd table', 'mysqldump Ver 8.0.30'])]
    #[TestWith([null, 'mariadb-dump Ver 10.19 Distrib 10.11-MariaDB'])]
    public function test_dump_uses_private_credentials_and_includes_structure_data_and_binary_support(?string $table, string $version): void
    {
        config(['backup.dump_binary' => 'mysqldump']);
        $connection = $this->connection();
        $optionsPath = null;
        $dumpCommand = [];
        Process::preventStrayProcesses();
        Process::fake(function (PendingProcess $process) use ($table, $version, &$optionsPath, &$dumpCommand) {
            if (in_array('--version', $process->command, true)) {
                return Process::result(output: $version);
            }
            $dumpCommand = $process->command;
            $optionsPath = substr($dumpCommand[1], strlen('--defaults-extra-file='));
            $options = File::get($optionsPath);
            $this->assertStringContainsString('password="p\\"a\\\\ss\\n[client]\\nuser=attacker"', $options);
            $this->assertStringContainsString('host="db.example.test"', $options);
            $this->assertStringContainsString('port="3307"', $options);
            $this->assertStringNotContainsString("\nuser=attacker", $options);
            $path = substr(array_values(array_filter($dumpCommand, fn (string $argument): bool => str_starts_with($argument, '--result-file=')))[0], strlen('--result-file='));
            File::put($path, "CREATE TABLE sample (id INT);\nINSERT INTO sample VALUES (1);\n");

            return Process::result();
        });

        $path = app(SqlBackupService::class)->create($connection, $table);

        try {
            $this->assertFileExists($path);
            $this->assertFileDoesNotExist($optionsPath);
            $this->assertStringNotContainsString('--password', implode(' ', $dumpCommand));
            $this->assertContains('--single-transaction', $dumpCommand);
            $this->assertContains('--hex-blob', $dumpCommand);
            $this->assertNotContains('--no-data', $dumpCommand);
            $this->assertSame($table === null ? ['--', 'example_db'] : ['--', 'example_db', $table], array_slice($dumpCommand, $table === null ? -2 : -3));
            if ($table === null) {
                $this->assertContains('--routines', $dumpCommand);
                $this->assertContains('--events', $dumpCommand);
            } else {
                $this->assertNotContains('--routines', $dumpCommand);
                $this->assertNotContains('--events', $dumpCommand);
            }
            if (str_contains($version, 'MariaDB')) {
                $this->assertNotContains('--column-statistics=0', $dumpCommand);
                $this->assertNotContains('--set-gtid-purged=OFF', $dumpCommand);
            }
            Process::assertRanTimes('*', 2);
        } finally {
            File::delete($path);
        }
    }

    #[TestWith([1, 'partial dump'])]
    #[TestWith([0, ''])]
    public function test_failed_or_empty_dump_is_removed_with_the_client_options(int $exitCode, string $output): void
    {
        $optionsPath = null;
        $dumpPath = null;
        Process::preventStrayProcesses();
        Process::fake(function (PendingProcess $process) use ($exitCode, $output, &$optionsPath, &$dumpPath) {
            if (in_array('--version', $process->command, true)) {
                return Process::result(output: 'mysqldump Ver 8.0.30');
            }
            $optionsPath = substr($process->command[1], strlen('--defaults-extra-file='));
            $dumpPath = substr(array_values(array_filter($process->command, fn (string $argument): bool => str_starts_with($argument, '--result-file=')))[0], strlen('--result-file='));
            File::put($dumpPath, $output);

            return Process::result(exitCode: $exitCode, errorOutput: $exitCode ? 'Dump failed' : '');
        });

        try {
            app(SqlBackupService::class)->create($this->connection());
            $this->fail('A failed or empty dump must not be downloaded.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString($exitCode ? 'gagal dibuat' : 'kosong', $exception->getMessage());
        }

        $this->assertFileDoesNotExist($optionsPath);
        $this->assertFileDoesNotExist($dumpPath);
        Process::assertRanTimes('*', 2);
    }

    public function test_unavailable_binary_returns_an_actionable_error(): void
    {
        Process::preventStrayProcesses();
        Process::fake(fn () => Process::result(exitCode: 127));
        try {
            app(SqlBackupService::class)->create($this->connection());
            $this->fail('An unavailable dump binary must produce an error.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('DB_DUMP_BINARY', $exception->getMessage());
        }
        Process::assertRanTimes('*', 1);
    }

    public function test_unsupported_database_driver_does_not_start_an_external_process(): void
    {
        Process::fake();
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('MySQL atau MariaDB');
        try {
            app(SqlBackupService::class)->create(['driver' => 'sqlite', 'database' => ':memory:']);
        } finally {
            Process::assertNothingRan();
        }
    }

    private function connection(): array
    {
        return ['driver' => 'mysql', 'database' => 'example_db', 'host' => 'db.example.test', 'port' => 3307, 'username' => 'backup-user', 'password' => "p\"a\\ss\n[client]\nuser=attacker"];
    }
}
