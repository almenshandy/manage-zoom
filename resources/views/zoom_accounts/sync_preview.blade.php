@extends('layouts.app')

@php
    $header = 'Preview Sinkronisasi Akun Zoom';
@endphp

@section('content')
<div class="space-y-4">
    <!-- Back & Title (Flat) -->
    <div class="bg-white p-4 rounded border border-gray-200 flex flex-col md:flex-row md:items-center justify-between gap-3">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('zoom-accounts.index') }}" class="text-xs text-gray-500 hover:text-gray-800">
                    &larr; Kembali ke Master Akun
                </a>
            </div>
            <h3 class="text-xs font-semibold text-gray-900 uppercase tracking-wider">Preview Data Akun dari Database `zoom`</h3>
            <p class="text-xs text-gray-500 mt-0.5">Ditemukan <strong>{{ count($previewData) }}</strong> baris akun di tabel <span class="font-mono text-gray-700">zoom.zoom_accounts</span>. Silakan pilih data yang ingin disinkronkan.</p>
        </div>

        <div class="flex items-center gap-2 text-xs">
            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded border border-emerald-200 bg-emerald-50 text-emerald-800">
                <i class="fa-solid fa-plus text-[10px]"></i> Siap Tambah: {{ collect($previewData)->where('is_existing', false)->count() }}
            </span>
            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded border border-amber-200 bg-amber-50 text-amber-800">
                <i class="fa-solid fa-arrows-rotate text-[10px]"></i> Sudah Ada: {{ collect($previewData)->where('is_existing', true)->count() }}
            </span>
        </div>
    </div>

    <form method="POST" action="{{ route('zoom-accounts.sync-execute') }}" id="syncForm">
        @csrf

        <!-- Options Bar -->
        <div class="bg-gray-50 p-3.5 rounded border border-gray-200 mb-3 flex flex-wrap items-center justify-between gap-3 text-xs">
            <div class="flex items-center gap-4">
                <label class="flex items-center gap-2 font-medium text-gray-700 cursor-pointer">
                    <input type="checkbox" id="checkAll" class="rounded border-gray-300 text-gray-900 focus:ring-0" checked>
                    <span>Pilih Semua Akun</span>
                </label>
                <div class="h-4 w-px bg-gray-300"></div>
                <div class="flex items-center gap-2">
                    <span class="text-gray-500 font-medium">Jika Akun Sudah Ada:</span>
                    <label class="flex items-center gap-1 text-gray-700 cursor-pointer">
                        <input type="radio" name="mode" value="skip" checked class="text-gray-900 focus:ring-0">
                        <span>Lewati (Skip)</span>
                    </label>
                    <label class="flex items-center gap-1 text-gray-700 cursor-pointer ml-2">
                        <input type="radio" name="mode" value="update" class="text-gray-900 focus:ring-0">
                        <span>Perbarui Kredensial (Update)</span>
                    </label>
                </div>
            </div>

            <button type="submit" class="px-4 py-1.5 bg-gray-900 hover:bg-black text-white font-medium rounded transition flex items-center gap-1.5">
                <i class="fa-solid fa-download text-[11px]"></i> Proses Sinkronisasi Terpilih
            </button>
        </div>

        <!-- Preview Table (Flat) -->
        <div class="bg-white rounded border border-gray-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-200 uppercase tracking-wider text-[11px]">
                        <tr>
                            <th class="p-3 w-10 text-center">
                                <span class="sr-only">Pilih</span>
                            </th>
                            <th class="px-4 py-3">ID Asli</th>
                            <th class="px-4 py-3">Email Akun</th>
                            <th class="px-4 py-3">Kapasitas</th>
                            <th class="px-4 py-3">Kedaluwarsa</th>
                            <th class="px-4 py-3">API OAuth (idakunapi / clientid)</th>
                            <th class="px-4 py-3 text-center">Status Asli</th>
                            <th class="px-4 py-3 text-center">Status di Sistem</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-gray-700">
                        @foreach($previewData as $item)
                            <tr class="hover:bg-gray-50/75 transition-colors {{ $item['is_existing'] ? 'bg-amber-50/20' : '' }}">
                                <td class="p-3 text-center">
                                    <input type="checkbox" name="selected_ids[]" value="{{ $item['source_id'] }}"
                                        class="item-checkbox rounded border-gray-300 text-gray-900 focus:ring-0" checked>
                                </td>
                                <td class="px-4 py-2.5 font-mono text-gray-500">
                                    #{{ $item['source_id'] }}
                                </td>
                                <td class="px-4 py-2.5">
                                    <span class="font-medium text-gray-900 font-mono">{{ $item['email'] }}</span>
                                </td>
                                <td class="px-4 py-2.5 whitespace-nowrap">
                                    <span class="font-medium text-gray-900">{{ $item['capacity'] }}</span>
                                    <span class="text-gray-400 text-[10px]">Pax</span>
                                </td>
                                <td class="px-4 py-2.5 whitespace-nowrap">
                                    <span class="font-mono text-gray-600">{{ $item['expired_date'] ?: '-' }}</span>
                                </td>
                                <td class="px-4 py-2.5">
                                    @if($item['account_id'] && $item['client_id'])
                                        <span class="inline-flex items-center gap-1 text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200 text-[10px] font-mono font-medium">
                                            <i class="fa-solid fa-key text-[9px]"></i> Kredensial Ada
                                        </span>
                                    @else
                                        <span class="text-gray-400 italic text-[11px]">Kosong</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2.5 text-center">
                                    <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-mono border
                                        @if($item['status'] === 'active') bg-emerald-50 text-emerald-700 border-emerald-200
                                        @elseif($item['status'] === 'maintenance') bg-amber-50 text-amber-700 border-amber-200
                                        @else bg-gray-100 text-gray-600 border-gray-200 @endif">
                                        {{ $item['status'] }} ({{ $item['raw_status'] }})
                                    </span>
                                </td>
                                <td class="px-4 py-2.5 text-center">
                                    @if($item['is_existing'])
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                                            Sudah Ada
                                        </span>
                                    @else
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Baru
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkAll = document.getElementById('checkAll');
        const checkboxes = document.querySelectorAll('.item-checkbox');

        checkAll.addEventListener('change', function () {
            checkboxes.forEach(cb => cb.checked = checkAll.checked);
        });

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function () {
                const allChecked = Array.from(checkboxes).every(c => c.checked);
                checkAll.checked = allChecked;
            });
        });
    });
</script>
@endpush
@endsection
