@extends('layouts.app')

@php
    $header = 'Memo & Persetujuan Kegiatan';
@endphp

@section('content')
<div class="space-y-4">
    <!-- Header (Flat) -->
    <x-page-header title="Daftar Memo & Surat Tugas Zoom" description="Arsip dokumen formal persetujuan peminjaman akun Zoom.">
        <x-slot:actions>
            <x-btn variant="primary" :href="route('memos.create')">
                <i class="fa-solid fa-plus text-[10px]"></i> Buat Memo Baru
            </x-btn>
        </x-slot:actions>
    </x-page-header>

    <!-- Table Card (Flat) -->
    <x-card>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-200 uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="px-4 py-3">Nomor Memo / Surat</th>
                        <th class="px-4 py-3">Kegiatan Terkait</th>
                        <th class="px-4 py-3">Tanggal Surat</th>
                        <th class="px-4 py-3">Pembuat Memo</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-700">
                    @forelse($memos as $m)
                        <tr class="hover:bg-gray-50/75 transition-colors">
                            <td class="px-4 py-3 font-mono font-semibold text-gray-900 text-xs">
                                {{ $m->memo_number }}
                            </td>
                            <td class="px-4 py-3">
                                @if($m->booking)
                                    <a href="{{ route('bookings.show', $m->booking->id) }}" class="font-semibold text-gray-900 hover:text-blue-600 block">
                                        {{ $m->booking->topic }}
                                    </a>
                                    <span class="text-gray-400 text-[11px] font-mono">#ZM-{{ str_pad($m->booking->id, 5, '0', STR_PAD_LEFT) }}</span>
                                @else
                                    <span class="text-gray-400 italic">Umum / Tanpa Booking</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-700 whitespace-nowrap">
                                {{ $m->memo_date ? $m->memo_date->format('d M Y') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-gray-700 whitespace-nowrap">
                                {{ $m->user->name ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('memos.show', $m->id) }}" class="p-1.5 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded border border-transparent hover:border-gray-200 transition" title="Lihat">
                                        <i class="fa-regular fa-eye"></i>
                                    </a>
                                    <a href="{{ route('memos.pdf', $m->id) }}" target="_blank" class="px-2.5 py-1 bg-white hover:bg-gray-50 text-red-700 border border-gray-300 font-semibold rounded text-[11px] inline-flex items-center gap-1 transition">
                                        <i class="fa-solid fa-file-pdf text-red-600"></i> PDF
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-400">
                                <i class="fa-solid fa-file-lines text-2xl mb-2 text-gray-300 block"></i>
                                Belum ada memo yang diterbitkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($memos->hasPages())
            <x-slot:footer>
                {{ $memos->links() }}
            </x-slot:footer>
        @endif
    </x-card>
</div>
@endsection
