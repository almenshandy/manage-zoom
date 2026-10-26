@extends('layouts.app')

@php
    $header = 'Kalender Jadwal Zoom Meeting';
@endphp

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/main.min.css" rel="stylesheet" />
<style>
    .fc { font-size: 0.8rem; }
    .fc-theme-standard td, .fc-theme-standard th { border-color: #e5e7eb; }
    .fc-toolbar-title { font-size: 1rem !important; font-weight: 600; color: #111827; }
    .fc-button-primary {
        background-color: #ffffff !important;
        border-color: #d1d5db !important;
        color: #374151 !important;
        box-shadow: none !important;
        font-size: 0.75rem !important;
        padding: 0.25rem 0.6rem !important;
        text-transform: capitalize !important;
    }
    .fc-button-primary:hover { background-color: #f3f4f6 !important; }
    .fc-button-primary.fc-button-active {
        background-color: #111827 !important;
        border-color: #111827 !important;
        color: #ffffff !important;
    }
    .fc-event {
        cursor: pointer;
        border-radius: 3px;
        padding: 1px 3px;
        font-size: 0.7rem;
        border: none !important;
    }
</style>
@endpush

@section('content')
<div class="space-y-4">
    <!-- Header Indicator (Flat) -->
    <div class="bg-white p-4 rounded border border-gray-200 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h3 class="text-xs font-semibold text-gray-900 uppercase tracking-wider">Ketersediaan & Jadwal Penggunaan Zoom</h3>
            <p class="text-xs text-gray-500 mt-0.5">Semua agenda terdaftar dapat dipantau di kalender berikut.</p>
        </div>

        <div class="flex items-center gap-3 text-xs">
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span> Link Siap</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span> Disetujui (ACC)</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Pending</span>
        </div>
    </div>

    <!-- Calendar Container (Flat Bordered) -->
    <div class="bg-white p-5 rounded border border-gray-200">
        <div id="calendar"></div>
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
