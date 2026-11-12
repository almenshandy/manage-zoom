@extends('layouts.app')

@php
    $header = 'Backup & Ekspor Database';
@endphp

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <x-page-header title="Cadangan Data Sistem" description="Backup SQL menyertakan struktur dan data tabel. CSV dan ZIP tersedia untuk ekspor data ke spreadsheet.">
        <x-slot:actions>
            <x-btn variant="primary" :href="route('backup.download-sql')">
                <i class="fa-solid fa-database text-[10px]" aria-hidden="true"></i> Backup Database (.SQL)
            </x-btn>
            <x-btn variant="white" :href="route('backup.download-all')">
                <i class="fa-solid fa-file-zipper text-[10px]"></i> Unduh Semua Tabel (.ZIP)
            </x-btn>
        </x-slot:actions>
    </x-page-header>

    <!-- Table List Card -->
    <x-card title="Daftar Tabel Database Aktif">
        <div class="overflow-x-auto">
            <table class="mobile-card-table w-full text-left text-xs">
                <thead class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-200 uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="px-4 py-3">No</th>
                        <th class="px-4 py-3">Nama Tabel Database</th>
                        <th class="px-4 py-3">Jumlah Baris Data</th>
                        <th class="px-4 py-3 text-right">Aksi Ekspor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-700">
                    @foreach($tableList as $index => $table)
                        <tr class="hover:bg-gray-50/75 transition-colors">
                            <td data-label="No" class="px-4 py-3 text-gray-400 font-mono w-12">{{ $index + 1 }}</td>
                            <td data-label="Nama Tabel Database" class="px-4 py-3">
                                <span class="font-mono font-medium text-gray-900 block text-xs">{{ $table }}</span>
                            </td>
                            <td data-label="Jumlah Baris Data" class="px-4 py-3 whitespace-nowrap">
                                <span class="font-semibold text-gray-800">{{ number_format($rowCounts[$table], 0, ',', '.') }}</span>
                                <span class="text-gray-400 text-[11px]">baris</span>
                            </td>
                            <td data-label="Aksi" class="px-4 py-3 text-right whitespace-nowrap">
                                <div class="flex flex-wrap items-center gap-2 md:justify-end">
                                    <a href="{{ route('backup.download-table-sql', $table) }}" class="inline-flex items-center gap-1.5 px-3 py-1 bg-white hover:bg-brand-50 text-brand-900 border border-brand-200 rounded-sm text-[11px] font-medium transition" aria-label="Backup SQL tabel {{ $table }}">
                                        <i class="fa-solid fa-database" aria-hidden="true"></i> Backup SQL
                                    </a>
                                    <a href="{{ route('backup.download-table', $table) }}" class="inline-flex items-center gap-1.5 px-3 py-1 bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 rounded-sm text-[11px] font-medium transition" aria-label="Ekspor CSV tabel {{ $table }}">
                                        <i class="fa-solid fa-file-csv text-emerald-600" aria-hidden="true"></i> Ekspor CSV
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>
</div>
@endsection
