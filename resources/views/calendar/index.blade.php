@extends('layouts.app')

@php
    $header = 'Kalender Jadwal Zoom Meeting';
@endphp

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css" rel="stylesheet" />
<style>
    .fc-event {
        cursor: pointer;
        border-radius: 6px;
        padding: 2px 4px;
        font-size: 0.75rem;
    }
</style>
@endpush

@section('content')
<div class="space-y-4">
    <div class="bg-white p-4 sm:p-6 rounded-2xl border border-slate-200/80 shadow-sm flex flex-wrap items-center justify-between gap-4">
        <div>
            <h3 class="text-base font-bold text-slate-800">Kalender Kegiatan & Ketersediaan Zoom</h3>
            <p class="text-xs text-slate-500">Pantau seluruh agenda meeting agar tidak terjadi bentrok pemesanan.</p>
        </div>

        <div class="flex items-center gap-3 text-xs">
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-emerald-500"></span> Link Siap</span>
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-blue-500"></span> Disetujui (ACC)</span>
            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-amber-500"></span> Pending</span>
        </div>
    </div>

    <!-- Calendar Container -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
        <div id="calendar" class="text-xs"></div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const calendarEl = document.getElementById("calendar");
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: "dayGridMonth",
            locale: "id",
            headerToolbar: {
                left: "prev,next today",
                center: "title",
                right: "dayGridMonth,timeGridWeek,timeGridDay",
            },
            events: '{{ route("calendar.events") }}',
            eventClick: function (info) {
                if (info.event.url) {
                    window.location.href = info.event.url;
                    info.jsEvent.preventDefault();
                }
            },
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                meridiem: false,
                hour12: false
            }
        });
        calendar.render();
    });
</script>
@endpush
