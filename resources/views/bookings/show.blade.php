@extends('layouts.app')

@php
    $header = 'Detail Peminjaman Zoom';
@endphp

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Top Action Bar -->
    <div class="flex items-center justify-between">
        <a href="{{ route('bookings.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1.5">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar
        </a>

        <div class="flex items-center gap-2">
            @if(auth()->user() && auth()->user()->isAdmin() && $booking->status === 0)
                <button onclick="document.getElementById('accDetailModal').classList.remove('hidden')" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-md shadow-emerald-600/20 transition">
                    <i class="fa-solid fa-check mr-1"></i> ACC Permohonan
                </button>
            @endif

            @if($booking->status === 1)
                <button onclick="document.getElementById('genDetailModal').classList.remove('hidden')" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-xl shadow-md shadow-blue-600/20 transition">
                    <i class="fa-solid fa-bolt mr-1"></i> Buat Link Zoom
                </button>
            @endif

            <a href="{{ route('memos.create', ['booking_id' => $booking->id]) }}" class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-semibold rounded-xl shadow-md shadow-purple-600/20 transition">
                <i class="fa-solid fa-file-pdf mr-1"></i> Buat Memo
            </a>
        </div>
    </div>

    <!-- Main Detail Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-6 sm:p-8 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 pb-6 border-b border-slate-100">
            <div>
                <span class="inline-block px-3 py-1 rounded-full border text-xs font-bold tracking-wide {{ $booking->status_badge }} mb-2">
                    {{ $booking->status_label }}
                </span>
                <h2 class="text-xl font-bold text-slate-800">{{ $booking->topic }}</h2>
                <p class="text-xs text-slate-500 mt-1">Diajukan oleh: <span class="font-semibold text-slate-700">{{ $booking->user->name ?? '-' }}</span> ({{ $booking->user->departemen ?? 'Umum' }})</p>
            </div>
            <div class="text-right">
                <span class="text-xs text-slate-400 block">Nomor Booking</span>
                <span class="font-mono text-sm font-bold text-slate-700">#ZM-{{ str_pad($booking->id, 5, '0', STR_PAD_LEFT) }}</span>
            </div>
        </div>

        <!-- Zoom API Credentials Result Box -->
        @if($booking->status === 3 && $booking->join_url)
            <div class="my-6 p-5 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-2xl">
                <div class="flex items-center gap-2 text-blue-900 font-bold text-sm mb-3">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-base"></i> Link Meeting Zoom Sudah Siap
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                    <div>
                        <span class="text-slate-500 block">Meeting ID:</span>
                        <span class="font-mono font-bold text-slate-800 text-sm select-all">{{ $booking->zoom_meeting_id }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block">Passcode Meeting:</span>
                        <span class="font-mono font-bold text-slate-800 text-sm select-all">{{ $booking->passcode ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-500 block">Link Langsung Peserta:</span>
                        <a href="{{ $booking->join_url }}" target="_blank" class="text-blue-600 hover:underline font-semibold flex items-center gap-1 mt-0.5">
                            Buka Link Meeting <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Details Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 py-6 text-xs text-slate-600 border-b border-slate-100">
            <div>
                <span class="text-slate-400 block mb-1">Tanggal & Durasi Kegiatan</span>
                <p class="font-semibold text-slate-800 text-sm">
                    {{ $booking->start_date->format('d F Y') }} s/d {{ $booking->end_date->format('d F Y') }}
                </p>
                <p class="text-slate-500 mt-0.5">
                    Pukul {{ substr($booking->start_time, 0, 5) }} - {{ substr($booking->end_time, 0, 5) }} WIB ({{ $booking->duration_minutes }} Menit)
                </p>
            </div>

            <div>
                <span class="text-slate-400 block mb-1">Akun Zoom Ditugaskan</span>
                @if($booking->account)
                    <p class="font-semibold text-slate-800 text-sm">{{ $booking->account->name }}</p>
                    <p class="text-slate-500 mt-0.5">Kapasitas: {{ $booking->account->capacity }} Peserta &bull; {{ $booking->account->email }}</p>
                @else
                    <p class="text-amber-600 italic">Belum ada akun yang di-assign oleh Admin</p>
                @endif
            </div>

            <div class="sm:col-span-2">
                <span class="text-slate-400 block mb-1">Deskripsi / Susunan Acara</span>
                <p class="text-slate-800 leading-relaxed bg-slate-50 p-4 rounded-xl border border-slate-100">
                    {{ $booking->description ?: 'Tidak ada keterangan tambahan.' }}
                </p>
            </div>

            @if($booking->user_note)
                <div class="sm:col-span-2">
                    <span class="text-slate-400 block mb-1">Catatan dari Pemohon</span>
                    <p class="text-slate-700 italic bg-amber-50/60 p-3 rounded-xl border border-amber-100">
                        "{{ $booking->user_note }}"
                    </p>
                </div>
            @endif

            @if($booking->admin_note)
                <div class="sm:col-span-2">
                    <span class="text-slate-400 block mb-1">Catatan Administrator</span>
                    <p class="text-slate-700 italic bg-blue-50/60 p-3 rounded-xl border border-blue-100">
                        "{{ $booking->admin_note }}"
                    </p>
                </div>
            @endif
        </div>

        <div class="pt-6 flex items-center justify-between text-xs text-slate-400">
            <span>Dibuat pada: {{ $booking->created_at->format('d M Y, H:i') }}</span>
            @if(auth()->user() && (auth()->user()->isAdmin() || $booking->user_id === auth()->id()))
                <form method="POST" action="{{ route('bookings.destroy', $booking->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus booking ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-500 hover:text-red-700 font-semibold">
                        <i class="fa-regular fa-trash-can mr-1"></i> Hapus Booking
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
