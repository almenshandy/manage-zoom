<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class DatabaseBackupController extends Controller
{
    public function index()
    {
        $tables = DB::select('SHOW TABLES');
        $dbName = config('database.connections.mysql.database');
        $tableKey = "Tables_in_{$dbName}";

        $tableList = array_map(function ($row) use ($tableKey) {
            return $row->$tableKey ?? current((array)$row);
        }, $tables);

        return view('backup.index', compact('tableList'));
    }

    /**
     * Download tabel spesifik dalam format CSV/Excel
     */
    public function downloadTable(string $table)
    {
        // Validasi nama tabel agar aman dari injeksi
        $tables = DB::select('SHOW TABLES');
        $dbName = config('database.connections.mysql.database');
        $tableKey = "Tables_in_{$dbName}";
        $validTables = array_map(function ($row) use ($tableKey) {
            return $row->$tableKey ?? current((array)$row);
        }, $tables);

        if (!in_array($table, $validTables)) {
            return back()->with('error', 'Tabel tidak ditemukan.');
        }

        $rows = DB::table($table)->get();

        $response = new StreamedResponse(function () use ($rows, $table) {
            $handle = fopen('php://output', 'w');
            
            // UTF-8 BOM untuk Excel
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            if ($rows->isNotEmpty()) {
                // Header CSV
                fputcsv($handle, array_keys((array)$rows->first()), ';');

                foreach ($rows as $row) {
                    fputcsv($handle, (array)$row, ';');
                }
            } else {
                fputcsv($handle, ['Tabel Kosong']);
            }

            fclose($handle);
        });

        $filename = "export_{$table}_" . date('Ymd_His') . ".csv";

        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', "attachment; filename=\"{$filename}\"");

        return $response;
    }

    /**
     * Download seluruh tabel dalam arsip ZIP
     */
    public function downloadAll()
    {
        $tables = DB::select('SHOW TABLES');
        $dbName = config('database.connections.mysql.database');
        $tableKey = "Tables_in_{$dbName}";

        $zip = new ZipArchive();
        $zipFilename = "backup_all_{$dbName}_" . date('Ymd_His') . ".zip";
        $tempFile = tempnam(sys_get_temp_dir(), 'zip');

        if ($zip->open($tempFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'Gagal membuat file arsip ZIP.');
        }

        foreach ($tables as $row) {
            $table = $row->$tableKey ?? current((array)$row);
            $rows = DB::table($table)->get();

            $csvBuffer = fopen('php://memory', 'r+');
            fprintf($csvBuffer, chr(0xEF).chr(0xBB).chr(0xBF));

            if ($rows->isNotEmpty()) {
                fputcsv($csvBuffer, array_keys((array)$rows->first()), ';');
                foreach ($rows as $r) {
                    fputcsv($csvBuffer, (array)$r, ';');
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
}
