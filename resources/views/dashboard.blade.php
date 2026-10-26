@extends('layouts.app')

@php
    $header = 'Dashboard Ikhtisar';
@endphp

@section('content')
<div class="space-y-6">
    <!-- Stat Cards (Flat Style) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Akun Aktif -->
        <div class="bg-white p-4 rounded border border-gray-200 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Akun Zoom Aktif</p>
                <h3 class="text-xl font-bold text-gray-900 mt-1">{{ $stats['active_accounts'] }} <span class="text-xs text-gray-400 font-normal">/ {{ $stats['total_accounts'] }} akun</span></h3>
            </div>
            <div class="w-10 h-10 rounded bg-gray-50 border border-gray-200 text-gray-700 flex items-center justify-center">
                <i class="fa-solid fa-server text-sm"></i>
            </div>
        </div>

        <!-- Card 2: Menunggu ACC -->
        <div class="bg-white p-4 rounded border border-gray-200 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Menunggu Persetujuan</p>
                <h3 class="text-xl font-bold text-amber-700 mt-1">{{ $stats['pending_bookings'] }}</h3>
            </div>
            <div class="w-10 h-10 rounded bg-amber-50 border border-amber-200 text-amber-700 flex items-center justify-center">
                <i class="fa-regular fa-clock text-sm"></i>
            </div>
        </div>

        <!-- Card 3: Disetujui (ACC) -->
        <div class="bg-white p-4 rounded border border-gray-200 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Disetujui (ACC)</p>
                <h3 class="text-xl font-bold text-blue-700 mt-1">{{ $stats['approved_bookings'] }}</h3>
            </div>
            <div class="w-10 h-10 rounded bg-blue-50 border border-blue-200 text-blue-700 flex items-center justify-center">
                <i class="fa-solid fa-clipboard-check text-sm"></i>
            </div>
        </div>

        <!-- Card 4: Meeting Hari Ini -->
        <div class="bg-white p-4 rounded border border-gray-200 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Meeting Hari Ini</p>
                <h3 class="text-xl font-bold text-emerald-700 mt-1">{{ $stats['today_meetings'] }}</h3>
            </div>
            <div class="w-10 h-10 rounded bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center justify-center">
                <i class="fa-solid fa-video text-sm"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Upcoming Approved Meetings -->
        <div class="bg-white p-5 rounded border border-gray-200">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-100">
                <h3 class="text-xs font-semibold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-calendar-check text-gray-400"></i> Meeting Mendatang (Telah Disetujui)
                </h3>
                <a href="{{ route('bookings.index') }}" class="text-xs text-gray-500 hover:text-gray-900 font-medium">Lihat Semua &rarr;</a>
            </div>

            @if($upcomingBookings->isEmpty())
                <div class="py-8 text-center text-gray-400 text-xs">
                    <i class="fa-regular fa-calendar-xmark text-2xl mb-2 text-gray-300 block"></i>
                    Belum ada jadwal meeting disetujui dalam waktu dekat.
                </div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach($upcomingBookings as $booking)
                        <div class="py-3 first:pt-0 last:pb-0 flex items-start justify-between gap-4">
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('bookings.show', $booking) }}" class="font-semibold text-xs text-gray-900 hover:text-blue-600 block truncate">
                                    {{ $booking->topic }}
                                </a>
                                <div class="text-[11px] text-gray-500 mt-0.5 flex flex-wrap items-center gap-2">
                                    <span><i class="fa-regular fa-clock mr-1 text-gray-400"></i>{{ $booking->time_range_formatted }}</span>
                                    <span>&bull;</span>
                                    <span><i class="fa-regular fa-user mr-1 text-gray-400"></i>{{ $booking->user->name ?? '-' }}</span>
                                    <span>&bull;</span>
                                    <span class="text-gray-700 font-medium">{{ $booking->account->name ?? 'Belum ditentukan' }}</span>
                                </div>
                            </div>
                            <div class="shrink-0 text-right">
                                <span class="inline-block text-[10px] font-semibold px-2 py-0.5 rounded border 
                                    @if($booking->status === 3) bg-emerald-50 text-emerald-700 border-emerald-200
                                    @else bg-blue-50 text-blue-700 border-blue-200 @endif">
                                    {{ $booking->status_label }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Latest Bookings Waiting for Review -->
        <div class="bg-white p-5 rounded border border-gray-200">
            <div class="flex items-center justify-between pb-3 mb-4 border-b border-gray-100">
                <h3 class="text-xs font-semibold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-regular fa-hourglass-half text-gray-400"></i> Pengajuan Perlu Ditinjau
                </h3>
                <a href="{{ route('bookings.index', ['status' => 0]) }}" class="text-xs text-gray-500 hover:text-gray-900 font-medium">Tinjau Semua &rarr;</a>
            </div>

            @if($recentPending->isEmpty())
                <div class="py-8 text-center text-gray-400 text-xs">
                    <i class="fa-regular fa-circle-check text-2xl mb-2 text-gray-300 block"></i>
                    Semua pengajuan booking telah diproses.
                </div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach($recentPending as $booking)
                        <div class="py-3 first:pt-0 last:pb-0 flex items-start justify-between gap-4">
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('bookings.show', $booking) }}" class="font-semibold text-xs text-gray-900 hover:text-blue-600 block truncate">
                                    {{ $booking->topic }}
                                </a>
                                <div class="text-[11px] text-gray-500 mt-0.5 flex flex-wrap items-center gap-2">
                                    <span>{{ $booking->date_range_formatted }} ({{ substr($booking->start_time, 0, 5) }} - {{ substr($booking->end_time, 0, 5) }})</span>
                                    <span>&bull;</span>
                                    <span>{{ $booking->user->name ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="shrink-0">
                                <a href="{{ route('bookings.show', $booking) }}" class="text-[11px] text-gray-700 hover:text-black font-semibold border border-gray-300 px-2.5 py-1 rounded bg-white hover:bg-gray-50 transition">
                                    Periksa
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
