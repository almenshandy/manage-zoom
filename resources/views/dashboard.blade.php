@extends('layouts.app')

@php
    $header = 'Dashboard Ikhtisar';
@endphp

@section('content')
<div class="space-y-6">
    <!-- Stat Cards (5 Kolom: Total Meeting, Akun Aktif, Menunggu ACC, Disetujui, Meeting Hari Ini) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Card 1: Total Pengajuan Meeting -->
        <div class="bg-white p-4 rounded border border-gray-200 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Total Meeting</p>
                <h3 class="text-xl font-bold text-gray-900 mt-1">{{ $stats['total_bookings'] }} <span class="text-xs text-gray-400 font-normal">kegiatan</span></h3>
            </div>
            <div class="w-10 h-10 rounded bg-gray-50 border border-gray-200 text-gray-700 flex items-center justify-center">
                <i class="fa-solid fa-calendar-days text-sm"></i>
            </div>
        </div>

        <!-- Card 2: Akun Zoom Aktif -->
        <div class="bg-white p-4 rounded border border-gray-200 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Akun Zoom Aktif</p>
                <h3 class="text-xl font-bold text-gray-900 mt-1">{{ $stats['active_accounts'] }} <span class="text-xs text-gray-400 font-normal">/ {{ $stats['total_accounts'] }} akun</span></h3>
            </div>
            <div class="w-10 h-10 rounded bg-indigo-50 border border-indigo-200 text-indigo-700 flex items-center justify-center">
                <i class="fa-solid fa-server text-sm"></i>
            </div>
        </div>

        <!-- Card 3: Menunggu ACC -->
        <div class="bg-white p-4 rounded border border-gray-200 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Menunggu ACC</p>
                <h3 class="text-xl font-bold text-amber-700 mt-1">{{ $stats['pending_bookings'] }}</h3>
            </div>
            <div class="w-10 h-10 rounded bg-amber-50 border border-amber-200 text-amber-700 flex items-center justify-center">
                <i class="fa-regular fa-clock text-sm"></i>
            </div>
        </div>

        <!-- Card 4: Disetujui (ACC) -->
        <div class="bg-white p-4 rounded border border-gray-200 flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Disetujui (ACC)</p>
                <h3 class="text-xl font-bold text-blue-700 mt-1">{{ $stats['approved_bookings'] }}</h3>
            </div>
            <div class="w-10 h-10 rounded bg-blue-50 border border-blue-200 text-blue-700 flex items-center justify-center">
                <i class="fa-solid fa-clipboard-check text-sm"></i>
            </div>
        </div>

        <!-- Card 5: Meeting Hari Ini -->
        <div class="bg-white p-4 rounded border border-gray-200 flex items-center justify-between sm:col-span-2 lg:col-span-1">
            <div>
                <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-wider">Meeting Hari Ini</p>
                <h3 class="text-xl font-bold text-emerald-700 mt-1">{{ $stats['today_meetings'] }}</h3>
            </div>
            <div class="w-10 h-10 rounded bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center justify-center">
                <i class="fa-solid fa-video text-sm"></i>
            </div>
        </div>
    </div>

    <!-- Chart Perbandingan Pengajuan Meeting per Departemen -->
    <div class="bg-white p-5 rounded border border-gray-200">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 mb-4 border-b border-gray-100 gap-2">
            <div>
                <h3 class="text-xs font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-chart-column text-blue-600"></i> Perbandingan Pengajuan Meeting per Departemen
                </h3>
                <p class="text-[11px] text-gray-500 mt-0.5">Distribusi frekuensi aktivitas meeting berdasarkan unit kerja/departemen pemohon</p>
            </div>
            <div class="flex items-center gap-2 self-start sm:self-auto">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-100 text-gray-700 border border-gray-200">
                    <i class="fa-solid fa-sitemap mr-1 text-[9px] text-gray-400"></i> {{ count($chartLabels) }} Departemen Aktif
                </span>
            </div>
        </div>

        @if(empty($chartData) || array_sum($chartData) === 0)
            <div class="py-12 text-center text-gray-400 text-xs">
                <i class="fa-solid fa-chart-pie text-3xl mb-2 text-gray-300 block"></i>
                Belum ada data meeting yang diajukan untuk divisualisasikan.
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-center">
                <!-- Bar Chart Canvas -->
                <div class="lg:col-span-2 h-64 sm:h-72">
                    <canvas id="departmentChart"></canvas>
                </div>

                <!-- Legend & Ranking List -->
                <div class="border-t lg:border-t-0 lg:border-l border-gray-100 pt-4 lg:pt-0 lg:pl-6 space-y-2.5">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-500 mb-2">Peringkat Penggunaan</p>
                    <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                        @foreach($departmentMeetings as $dept => $total)
                            @php
                                $percent = $stats['total_bookings'] > 0 ? round(($total / $stats['total_bookings']) * 100, 1) : 0;
                            @endphp
                            <div class="p-2 rounded bg-gray-50/70 border border-gray-100 hover:bg-gray-100/70 transition">
                                <div class="flex items-center justify-between text-xs mb-1">
                                    <span class="font-semibold text-gray-800 truncate mr-2" title="{{ $dept }}">{{ $dept }}</span>
                                    <span class="font-mono text-gray-900 font-bold shrink-0">{{ $total }} <span class="text-[10px] text-gray-500 font-normal">({{ $percent }}%)</span></span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ $percent }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- 2 Kolom: Meeting Mendatang & Pengajuan Perlu Ditinjau -->
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

@push('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('departmentChart');
        if (!ctx) return;

        const labels = @json($chartLabels);
        const dataValues = @json($chartData);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Meeting Diajukan',
                    data: dataValues,
                    backgroundColor: 'rgba(37, 99, 235, 0.85)', // Tailwind blue-600
                    hoverBackgroundColor: 'rgba(29, 78, 216, 1)', // Tailwind blue-700
                    borderRadius: 4,
                    borderSkipped: false,
                    barPercentage: 0.55,
                    categoryPercentage: 0.7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#111827',
                        titleFont: { size: 12, family: 'Inter' },
                        bodyFont: { size: 11, family: 'Inter' },
                        padding: 10,
                        cornerRadius: 4,
                        callbacks: {
                            label: function (context) {
                                return ` ${context.parsed.y} Meeting`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            font: { size: 10, family: 'Inter' },
                            color: '#6b7280'
                        },
                        grid: {
                            color: '#f3f4f6',
                            drawBorder: false
                        }
                    },
                    x: {
                        ticks: {
                            font: { size: 10, family: 'Inter' },
                            color: '#374151',
                            maxRotation: 30,
                            minRotation: 0,
                            callback: function (val, index) {
                                const label = this.getLabelForValue(val);
                                return label.length > 18 ? label.substring(0, 16) + '...' : label;
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
