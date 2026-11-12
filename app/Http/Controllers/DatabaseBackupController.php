<?php

namespace App\Http\Controllers;

use App\Services\SqlBackupService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;
use ZipArchive;

class DatabaseBackupController extends Controller
{
    public function index(): View
    {
        $tableList = $this->tableNames();
        $rowCounts = [];
        foreach ($tableList as $table) {
            $rowCounts[$table] = DB::table($table)->count();
        }

        return view('backup.index', compact('tableList', 'rowCounts'));
    }

    /**
     * Download tabel spesifik dalam format CSV/Excel
     */
    public function downloadTable(string $table): StreamedResponse|RedirectResponse
    {
        // Validasi nama tabel agar aman dari injeksi
        $validTables = $this->tableNames();

        if (! in_array($table, $validTables, true)) {
            return back()->with('error', 'Tabel tidak ditemukan.');
        }

        $rows = DB::table($table)->get();

        $response = new StreamedResponse(function () use ($rows) {
            $handle = fopen('php://output', 'w');

            // UTF-8 BOM untuk Excel
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            if ($rows->isNotEmpty()) {
                // Header CSV
                fputcsv($handle, array_keys((array) $rows->first()), ';', escape: '');

                foreach ($rows as $row) {
                    fputcsv($handle, (array) $row, ';', escape: '');
                }
            } else {
                fputcsv($handle, ['Tabel Kosong'], escape: '');
            }

            fclose($handle);
        });

        $filename = "export_{$table}_".date('Ymd_His').'.csv';

        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', "attachment; filename=\"{$filename}\"");

        return $response;
    }

    /**
     * Download seluruh tabel dalam arsip ZIP
     */
    public function downloadAll(): BinaryFileResponse|RedirectResponse
    {
        $tables = $this->tableNames();
        $dbName = DB::connection()->getDatabaseName();

        $zip = new ZipArchive;
        $zipFilename = "backup_all_{$dbName}_".date('Ymd_His').'.zip';
        $tempFile = tempnam(sys_get_temp_dir(), 'zip');

        if ($zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Gagal membuat file arsip ZIP.');
        }

        foreach ($tables as $table) {
            $rows = DB::table($table)->get();

            $csvBuffer = fopen('php://memory', 'r+');
            fprintf($csvBuffer, chr(0xEF).chr(0xBB).chr(0xBF));

            if ($rows->isNotEmpty()) {
                fputcsv($csvBuffer, array_keys((array) $rows->first()), ';', escape: '');
                foreach ($rows as $r) {
                    fputcsv($csvBuffer, (array) $r, ';', escape: '');
                }
            }
            rewind($csvBuffer);
            $csvContent = stream_get_contents($csvBuffer);
            fclose($csvBuffer);

            $zip->addFromString("{$table}.csv", $csvContent);
        }

        $zip->close();

        return response()->download($tempFile, $zipFilename)->deleteFileAfterSend(true);
    }

    public function downloadSql(SqlBackupService $backup): BinaryFileResponse|RedirectResponse
    {
        return $this->sqlResponse($backup);
    }

    public function downloadTableSql(string $table, SqlBackupService $backup): BinaryFileResponse|RedirectResponse
    {
        abort_unless(in_array($table, $this->tableNames(), true), 404);

        return $this->sqlResponse($backup, $table);
    }

    /** @return list<string> */
    private function tableNames(): array
    {
        $schema = DB::connection()->getSchemaBuilder();

        return $schema->getTableListing($schema->getCurrentSchemaListing(), schemaQualified: false);
    }

    private function sqlResponse(SqlBackupService $backup, ?string $table = null): BinaryFileResponse|RedirectResponse
    {
        $path = null;
        try {
            $connection = DB::connection();
            $path = $backup->create($connection->getConfig(), $table);
            $name = preg_replace('/[^A-Za-z0-9_-]+/', '_', $table ?? $connection->getDatabaseName());
            $filename = 'backup_'.($table === null ? 'database_' : 'table_').$name.'_'.now()->format('Ymd_His').'.sql';

            return response()->download($path, $filename, [
                'Content-Type' => 'application/sql',
                'Cache-Control' => 'private, no-store, max-age=0',
                'X-Content-Type-Options' => 'nosniff',
            ])->deleteFileAfterSend(true);
        } catch (Throwable $exception) {
            if ($path !== null) {
                File::delete($path);
            }
            report($exception);

            return to_route('backup.index')->with('error', 'Backup SQL gagal dibuat. Periksa pengaturan DB_DUMP_BINARY, koneksi dan izin database pada server.');
        }
    }
}
