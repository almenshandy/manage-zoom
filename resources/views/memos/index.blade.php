@extends('layouts.app')

@php
    $header = 'Memo & Persetujuan Kegiatan';
@endphp

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-base font-bold text-slate-800">Daftar Memo & Surat Tugas Zoom</h3>
            <p class="text-xs text-slate-500">Arsip dan dokumen formal persetujuan peminjaman akun Zoom.</p>
        </div>
        <a href="{{ route('memos.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow-md shadow-blue-600/20 transition">
            <i class="fa-solid fa-plus"></i> Buat Memo Baru
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200 uppercase tracking-wider text-[11px]">
                <tr>
                    <th class="px-5 py-3.5">Nomor Memo / Surat</th>
                    <th class="px-5 py-3.5">Kegiatan Terkait</th>
                    <th class="px-5 py-3.5">Tanggal Surat</th>
                    <th class="px-5 py-3.5">Pembuat Memo</th>
                    <th class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700">
                @forelse($memos as $m)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-5 py-4 font-mono font-bold text-slate-800 text-xs">
                            {{ $m->memo_number }}
                        </td>
                        <td class="px-5 py-4">
                            @if($m->booking)
                                <a href="{{ route('bookings.show', $m->booking->id) }}" class="font-semibold text-slate-800 hover:text-blue-600 block">
                                    {{ $m->booking->topic }}
                                </a>
                                <span class="text-slate-400 text-[11px]">ID: #ZM-{{ str_pad($m->booking->id, 5, '0', STR_PAD_LEFT) }}</span>
                            @else
                                <span class="text-slate-400 italic">Umum / Tanpa Booking</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-slate-600">
                            {{ $m->memo_date ? $m->memo_date->format('d M Y') : '-' }}
                        </td>
                        <td class="px-5 py-4 text-slate-600">
                            {{ $m->user->name ?? '-' }}
                        </td>
                        <td class="px-5 py-4 text-right space-x-1">
                            <a href="{{ route('memos.show', $m->id) }}" class="p-2 text-slate-500 hover:text-blue-600 rounded-lg hover:bg-slate-100 transition inline-block" title="Lihat">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ route('memos.pdf', $m->id) }}" target="_blank" class="px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 border border-red-200 font-semibold rounded-lg text-[11px] inline-flex items-center gap-1.5 transition">
                                <i class="fa-solid fa-file-pdf"></i> PDF
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-slate-400">Belum ada memo yang diterbitkan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($memos->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $memos->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
