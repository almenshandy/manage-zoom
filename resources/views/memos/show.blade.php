@extends('layouts.app')

@php
    $header = 'Detail Memo Kegiatan';
@endphp

@section('content')
<div class="max-w-2xl mx-auto space-y-4">
    <x-page-header title="Memo #{{ $memo->memo_number }}" description="Diterbitkan pada {{ $memo->memo_date ? $memo->memo_date->format('d F Y') : '-' }}" :backUrl="route('memos.index')" backText="Kembali ke Daftar Memo">
        <x-slot:actions>
            <x-btn variant="white" :href="route('memos.pdf', $memo->id)" target="_blank">
                <i class="fa-solid fa-print text-gray-500"></i> Cetak Dokumen PDF
            </x-btn>
        </x-slot:actions>
    </x-page-header>

    <x-card>
        <div class="p-6 space-y-5 text-xs text-gray-700">
            <div class="border-b border-gray-200 pb-4 flex items-center justify-between">
                <div>
                    <span class="text-[11px] text-gray-400 uppercase tracking-wider block">Nomor Memo</span>
                    <span class="font-mono text-sm font-bold text-gray-900">{{ $memo->memo_number }}</span>
                </div>
                <div class="text-right">
                    <span class="text-[11px] text-gray-400 uppercase tracking-wider block">Dibuat Oleh</span>
                    <span class="font-medium text-gray-900">{{ $memo->user->name ?? '-' }}</span>
                </div>
            </div>

            @if($memo->booking)
                <div class="p-3 bg-gray-50 border border-gray-200 rounded flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-gray-400 uppercase tracking-wider block">Terkait Agenda Booking</span>
                        <a href="{{ route('bookings.show', $memo->booking->id) }}" class="font-semibold text-gray-900 hover:text-blue-600">
                            {{ $memo->booking->topic }}
                        </a>
                    </div>
                    <span class="text-xs font-mono text-gray-500">#ZM-{{ str_pad($memo->booking->id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
            @endif

            <div>
                <span class="text-[11px] text-gray-400 uppercase tracking-wider block mb-1.5">Isi Memo / Keterangan</span>
                <div class="p-4 bg-gray-50 border border-gray-200 rounded leading-relaxed text-gray-800 whitespace-pre-line">
                    {{ $memo->content }}
                </div>
            </div>

            @if($memo->has_signature)
                <div class="pt-6 flex justify-end">
                    <div class="text-center w-48">
                        <p class="text-gray-500 text-[11px]">Mengetahui,</p>
                        <div class="h-16"></div>
                        <p class="font-bold text-gray-900 underline">{{ $memo->user->name ?? 'Administrator' }}</p>
                        <p class="text-gray-500 text-[10px]">{{ $memo->user->jabatan ?? 'Pejabat Berwenang' }}</p>
                    </div>
                </div>
            @endif
        </div>
    </x-card>
</div>
@endsection
