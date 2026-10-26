@extends('layouts.app')

@php
    $header = 'Dashboard Ikhtisar';
@endphp

@section('content')
<div class="space-y-6">
    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Akun Aktif -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Akun Zoom Aktif</p>
                <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $stats['active_accounts'] }} <span class="text-xs text-slate-400 font-normal">/ {{ $stats['total_accounts'] }} akun</span></h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-server"></i>
            </div>
        </div>

        <!-- Card 2: Menunggu ACC -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Menunggu Persetujuan</p>
                <h3 class="text-2xl font-bold text-amber-600 mt-1">{{ $stats['pending_bookings'] }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-clock"></i>
            </div>
        </div>

        <!-- Card 3: Disetujui (ACC) -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Disetujui (ACC)</p>
                <h3 class="text-2xl font-bold text-blue-600 mt-1">{{ $stats['approved_bookings'] }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-clipboard-check"></i>
            </div>
        </div>

        <!-- Card 4: Meeting Berjalan Hari Ini -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Meeting Hari Ini</p>
                <h3 class="text-2xl font-bold text-emerald-600 mt-1">{{ $stats['today_meetings'] }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                <i class="fa-solid fa-video"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Upcoming Approved Meetings -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-calendar-check text-blue-600"></i> Agenda Meeting Terdekat
                </h3>
                <a href="{{ route('bookings.index') }}" class="text-xs font-semibold text-blue-600 hover:underline">Lihat Semua</a>
            </div>

            @if($upcomingBookings->isEmpty())
                <div class="text-center py-8 text-slate-400 text-sm">
                    <i class="fa-regular fa-calendar-xmark text-3xl mb-2"></i>
                    <p>Belum ada jadwal meeting terkonfirmasi terdekat.</p>
                </div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($upcomingBookings as $item)
                        <div class="py-3 flex items-start justify-between gap-4">
                            <div>
                                <h4 class="font-semibold text-sm text-slate-800">{{ $item->topic }}</h4>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    <i class="fa-solid fa-user text-[10px] mr-1"></i> {{ $item->user->name ?? '-' }} &bull;
                                    <span class="text-blue-600 font-medium">{{ $item->account->name ?? 'Belum ada akun' }}</span>
                                </p>
                                <p class="text-xs text-slate-500 mt-1">
                                    <i class="fa-regular fa-clock text-[10px] mr-1"></i> {{ $item->start_date->format('d M Y') }} ({{ substr($item->start_time, 0, 5) }} - {{ substr($item->end_time, 0, 5) }} WIB)
                                </p>
                            </div>
                            <div class="shrink-0 text-right">
                                <span class="inline-block text-[11px] font-semibold px-2.5 py-1 rounded-full border {{ $item->status_badge }}">
                                    {{ $item->status_label }}
                                </span>
                                @if($item->join_url)
                                    <div class="mt-2">
                                        <a href="{{ $item->join_url }}" target="_blank" class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-800 font-semibold">
                                            Join Zoom <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Pending Approvals -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-hourglass-half text-amber-500"></i> Pengajuan Perlu Di-ACC
                </h3>
                <a href="{{ route('bookings.index', ['status' => 0]) }}" class="text-xs font-semibold text-blue-600 hover:underline">Lihat Semua</a>
            </div>

            @if($recentPending->isEmpty())
                <div class="text-center py-8 text-slate-400 text-sm">
                    <i class="fa-regular fa-circle-check text-3xl mb-2 text-emerald-400"></i>
                    <p>Semua permohonan sudah diproses!</p>
                </div>
            @else
                <div class="divide-y divide-slate-100">
                    @foreach($recentPending as $item)
                        <div class="py-3 flex items-start justify-between gap-4">
                            <div>
                                <h4 class="font-semibold text-sm text-slate-800">{{ $item->topic }}</h4>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Diajukan oleh: <span class="font-medium text-slate-700">{{ $item->user->name ?? '-' }}</span> ({{ $item->user->departemen ?? 'Umum' }})
                                </p>
                                <p class="text-xs text-slate-500 mt-1">
                                    Tanggal: {{ $item->start_date->format('d M Y') }} &bull; Durasi: {{ $item->duration_minutes }} menit
                                </p>
                            </div>
                            <div class="shrink-0">
                                <a href="{{ route('bookings.show', $item->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-100 hover:bg-blue-50 hover:text-blue-600 text-slate-700 text-xs font-semibold rounded-lg transition">
                                    Review <i class="fa-solid fa-arrow-right text-[10px]"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
