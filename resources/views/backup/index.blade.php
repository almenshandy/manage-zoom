@extends('layouts.app')

@php
    $header = 'Backup & Ekspor Database';
@endphp

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <x-page-header title="Cadangan Data Sistem" description="Ekspor tabel database dalam format CSV/Excel atau unduh seluruh basis data dalam arsip ZIP.">
        <x-slot:actions>
            <x-btn variant="primary" :href="route('backup.download-all')">
                <i class="fa-solid fa-file-zipper text-[10px]"></i> Unduh Semua Tabel (.ZIP)
            </x-btn>
        </x-slot:actions>
    </x-page-header>

    <!-- Table List Card -->
    <x-card title="Daftar Tabel Database Aktif">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
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
                        @php
                            $rowCount = \Illuminate\Support\Facades\DB::table($table)->count();
                        @endphp
                        <tr class="hover:bg-gray-50/75 transition-colors">
                            <td class="px-4 py-3 text-gray-400 font-mono w-12">{{ $index + 1 }}</td>
                            <td class="px-4 py-3">
                                <span class="font-mono font-medium text-gray-900 block text-xs">{{ $table }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="font-semibold text-gray-800">{{ number_format($rowCount, 0, ',', '.') }}</span>
                                <span class="text-gray-400 text-[11px]">baris</span>
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('backup.download-table', $table) }}" class="inline-flex items-center gap-1.5 px-3 py-1 bg-white hover:bg-gray-50 text-gray-700 border border-gray-300 rounded text-[11px] font-medium transition">
                                    <i class="fa-solid fa-file-csv text-emerald-600"></i> Ekspor CSV
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>
</div>
@endsection
