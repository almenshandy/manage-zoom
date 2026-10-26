@extends('layouts.app')

@php
    $header = 'Detail Memo';
@endphp

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('memos.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar
        </a>

        <a href="{{ route('memos.pdf', $memo->id) }}" target="_blank" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded-xl shadow-md shadow-red-600/20 transition flex items-center gap-1.5">
            <i class="fa-solid fa-file-pdf"></i> Unduh / Cetak PDF
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 p-8 shadow-sm">
        <div class="text-center pb-6 border-b-2 border-slate-800 mb-6">
            <h2 class="text-lg font-bold uppercase tracking-wider text-slate-900">MEMO PERSETUJUAN PEMINJAMAN FASILITAS ZOOM</h2>
            <p class="font-mono text-xs text-slate-600 mt-1">Nomor: {{ $memo->memo_number }}</p>
        </div>

        <div class="space-y-4 text-xs text-slate-700 leading-relaxed">
            <p><strong>Tanggal Surat:</strong> {{ $memo->memo_date ? $memo->memo_date->translatedFormat('d F Y') : '-' }}</p>
            @if($memo->booking)
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 space-y-1.5">
                    <p><strong>Topik Rapat:</strong> {{ $memo->booking->topic }}</p>
                    <p><strong>Pemohon:</strong> {{ $memo->booking->user->name ?? '-' }} ({{ $memo->booking->user->departemen ?? 'Umum' }})</p>
                    <p><strong>Jadwal:</strong> {{ $memo->booking->start_date->format('d M Y') }} ({{ substr($memo->booking->start_time, 0, 5) }} - {{ substr($memo->booking->end_time, 0, 5) }} WIB)</p>
                    @if($memo->booking->join_url)
                        <p><strong>Link Meeting:</strong> <a href="{{ $memo->booking->join_url }}" target="_blank" class="text-blue-600 underline">{{ $memo->booking->join_url }}</a></p>
                        <p><strong>Meeting ID:</strong> {{ $memo->booking->zoom_meeting_id }} &bull; <strong>Passcode:</strong> {{ $memo->booking->passcode ?? '-' }}</p>
                    @endif
                </div>
            @endif

            <div class="py-4 whitespace-pre-line text-justify">
                {{ $memo->content }}
            </div>

            @if($memo->has_signature)
                <div class="pt-8 flex justify-end">
                    <div class="text-center min-w-[200px]">
                        <p class="text-xs text-slate-500">Mengetahui & Menyetujui,</p>
                        <p class="text-xs font-semibold text-slate-700 mb-16">Pengelola Zoom Meeting</p>
                        <p class="text-xs font-bold underline text-slate-900">{{ auth()->user()->name ?? 'Administrator' }}</p>
                        <p class="text-[10px] text-slate-500">Unit Pengelola Sistem Informasi</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
