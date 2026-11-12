<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Pdo\Mysql;
use RuntimeException;
use Throwable;

class SqlBackupService
{
    /**
     * @param array{driver: string, database: string, host?: string, port?: int|string, username?: string, password?: string|null, unix_socket?: string, charset?: string, options?: array<int, mixed>} $connection
     */
    public function create(array $connection, ?string $table = null): string
    {
        if (! in_array($connection['driver'], ['mysql', 'mariadb'], true)) {
            throw new RuntimeException('Backup SQL tersedia untuk database MySQL atau MariaDB.');
        }

        $directory = storage_path('app/private/sql-backups');
        File::ensureDirectoryExists($directory, 0700);
        $optionsPath = tempnam($directory, 'client_');
        $dumpPath = tempnam($directory, 'dump_');
        if ($optionsPath === false || $dumpPath === false) {
            if ($optionsPath !== false) {
                File::delete($optionsPath);
            }
            if ($dumpPath !== false) {
                File::delete($dumpPath);
            }

            throw new RuntimeException('Tidak dapat menyiapkan file backup SQL.');
        }

        register_shutdown_function(static function () use ($optionsPath, $dumpPath): void {
            foreach ([$optionsPath, $dumpPath] as $temporaryFile) {
                if (is_file($temporaryFile)) {
                    unlink($temporaryFile);
                }
            }
        });

        try {
            chmod($optionsPath, 0600);
            chmod($dumpPath, 0600);
            File::put($optionsPath, $this->clientOptions($connection));
            $binary = (string) config('backup.dump_binary', 'mysqldump');
            $version = Process::timeout(10)->run([$binary, '--version']);
            if (! $version->successful()) {
                throw new RuntimeException('Program dump MySQL tidak tersedia. Periksa pengaturan DB_DUMP_BINARY pada server.');
            }
            $command = [
                $binary,
                '--defaults-extra-file='.$optionsPath,
                '--single-transaction', '--quick', '--skip-lock-tables', '--skip-add-locks',
                '--hex-blob', '--no-tablespaces', '--default-character-set=utf8mb4',
                '--triggers', '--result-file='.$dumpPath,
            ];
            if (! str_contains(strtolower($version->output()), 'mariadb')) {
                $command[] = '--column-statistics=0';
                $command[] = '--set-gtid-purged=OFF';
            }
            if ($table === null) {
                $command[] = '--routines';
                $command[] = '--events';
            }
            $command[] = '--';
            $command[] = $connection['database'];
            if ($table !== null) {
                $command[] = $table;
            }

            $result = Process::timeout((int) config('backup.timeout', 300))->run($command);
            if (! $result->successful()) {
                Log::error('SQL backup process failed', ['exit_code' => $result->exitCode(), 'error' => $result->errorOutput()]);

                throw new RuntimeException('Backup SQL gagal dibuat. Periksa koneksi dan izin ekspor akun database pada server.');
            }
            clearstatcache(true, $dumpPath);
            if (filesize($dumpPath) === 0) {
                throw new RuntimeException('File backup SQL kosong. Silakan coba kembali.');
            }

            return $dumpPath;
        } catch (Throwable $exception) {
            File::delete($dumpPath);

            throw $exception;
        } finally {
            File::delete($optionsPath);
        }
    }

    /** @param array{host?: string, port?: int|string, username?: string, password?: string|null, unix_socket?: string, options?: array<int, mixed>} $connection */
    private function clientOptions(array $connection): string
    {
        $options = [
            'host' => $connection['host'] ?? '127.0.0.1',
            'port' => $connection['port'] ?? 3306,
            'user' => $connection['username'] ?? '',
            'password' => $connection['password'] ?? '',
        ];
        if (! empty($connection['unix_socket'])) {
            $options['socket'] = $connection['unix_socket'];
        } else {
            $options['protocol'] = 'TCP';
        }
        if (defined(Mysql::class.'::ATTR_SSL_CA') && ! empty($connection['options'][Mysql::ATTR_SSL_CA])) {
            $options['ssl-ca'] = $connection['options'][Mysql::ATTR_SSL_CA];
        }
        $contents = "[client]\n";
        foreach ($options as $name => $value) {
            $escaped = strtr((string) $value, ["\\" => "\\\\", '"' => '\\"', "\n" => '\\n', "\r" => '\\r', "\t" => '\\t']);
            $contents .= $name.'="'.$escaped."\"\n";
        }

        return $contents;
    }
}
