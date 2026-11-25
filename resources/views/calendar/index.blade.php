@extends('layouts.app')

@php
    $header = 'Kalender Jadwal Zoom Meeting';
@endphp

@push('styles')
<style>
    .fc { font-size: 0.8rem; }
    .fc-theme-standard td, .fc-theme-standard th { border-color: var(--color-gray-200); }
    .fc-toolbar-title { font-size: 1rem !important; font-weight: 600; color: var(--color-gray-900); }
    .fc-button-primary {
        background-color: #ffffff !important;
        border-color: var(--color-gray-300) !important;
        color: var(--color-gray-700) !important;
        border-radius: var(--radius-sm) !important;
        min-height: 32px;
        box-shadow: none !important;
        font-size: 0.75rem !important;
        padding: 0.25rem 0.6rem !important;
        text-transform: capitalize !important;
    }
    .fc-button-primary:hover { background-color: var(--color-brand-50) !important; }
    .fc-button-primary.fc-button-active {
        background-color: var(--color-brand-900) !important;
        border-color: var(--color-brand-900) !important;
        color: #ffffff !important;
    }
    .fc-event {
        cursor: pointer;
        border-radius: var(--radius-sm);
        padding: 1px 3px;
        font-size: 0.7rem;
        border: none !important;
    }
    .fc-daygrid-day, .fc-timegrid-col { cursor: pointer; }
    .fc .calendar-date-trigger {
        color: var(--color-brand-900);
        cursor: pointer;
        font-weight: 600;
    }
    .fc .calendar-date-trigger:hover,
    .fc .calendar-date-trigger:focus-visible { text-decoration: underline; }
    #calendarDayDialog {
        position: fixed;
        inset: 0;
        width: 100%;
        height: 100%;
        max-width: none;
        max-height: none;
        margin: 0;
        padding: max(1rem, env(safe-area-inset-top)) max(1rem, env(safe-area-inset-right))
            max(1rem, env(safe-area-inset-bottom)) max(1rem, env(safe-area-inset-left));
        overflow: hidden;
        background: transparent;
    }
    #calendarDayDialog[open] { display: grid; place-items: center; }
    #calendarDayDialog::backdrop { background: rgb(17 24 39 / 55%); }
    .calendar-day-panel {
        display: grid;
        grid-template-rows: auto minmax(0, 1fr);
        width: min(100%, 32rem);
        max-height: 100%;
        min-height: 0;
        overflow: hidden;
    }

    @media (max-width: 767px) {
        .fc .fc-toolbar {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 0.5rem;
        }
        .fc .fc-toolbar-chunk:nth-child(2) {
            grid-column: 1 / -1;
            grid-row: 1;
        }
        .fc .fc-toolbar .fc-button-primary {
            min-height: 44px;
            padding-inline: 0.4rem !important;
        }
        .fc .fc-daygrid-day-top { justify-content: center; }
        .fc .fc-daygrid-day-number { padding-block: 0.45rem; }
        .fc .fc-daygrid-day-frame { min-height: 64px; }
        .fc .fc-daygrid .fc-event { min-height: 12px; }
        .fc .fc-daygrid .fc-event-title,
        .fc .fc-daygrid .fc-event-time {
            position: absolute;
            width: 1px;
            height: 1px;
            overflow: hidden;
            clip-path: inset(50%);
        }
        .fc .fc-daygrid .fc-daygrid-dot-event {
            display: flex;
            justify-content: center;
        }
        .fc .fc-daygrid-more-link { font-size: 0.65rem; }
    }
</style>
@endpush

@section('content')
<div class="space-y-4">
    <!-- Header Indicator (Flat) -->
    <div class="bg-white p-4 rounded-sm border border-gray-200 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h3 class="text-xs font-semibold text-gray-900 uppercase tracking-wider">Ketersediaan & Jadwal Penggunaan Zoom</h3>
            <p class="text-xs text-gray-500 mt-0.5">Pilih tanggal untuk melihat kegiatan yang dijadwalkan pada hari itu.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3 text-xs">
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span> Link Siap</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span> Disetujui (ACC)</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Pending</span>
        </div>
    </div>

    <!-- Calendar Container (Flat Bordered) -->
    <div class="bg-white p-2 sm:p-5 rounded-sm border border-gray-200">
        <div id="calendar"></div>
    </div>
</div>

<dialog id="calendarDayDialog" aria-labelledby="calendarDayTitle">
    <div class="calendar-day-panel rounded-sm border border-gray-200 bg-white text-gray-900 shadow-xl">
        <div class="flex items-center justify-between gap-3 border-b border-gray-200 px-4 py-2">
            <h3 id="calendarDayTitle" class="text-sm font-semibold"></h3>
            <form method="dialog">
                <button type="submit" autofocus aria-label="Tutup jadwal harian" class="h-11 w-11 rounded-sm border border-gray-200 text-gray-600 hover:bg-gray-50">
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                </button>
            </form>
        </div>
        <div id="calendarDayContent" class="min-h-0 overflow-y-auto overscroll-contain p-4" aria-live="polite"></div>
    </div>
    <template id="calendarDayEvent">
        <li>
            <a class="block rounded-sm border border-gray-200 p-3 hover:border-brand-600">
                <span class="block break-words text-sm font-semibold" data-title></span>
                <span class="mt-1 block text-xs text-gray-700" data-time></span>
                <span class="mt-1 block text-xs text-gray-600" data-status></span>
            </a>
        </li>
    </template>
</dialog>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.10/locales/id.global.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const calendarEl = document.getElementById("calendar");
        const mobileCalendar = window.matchMedia('(max-width: 767px)');
        const dialog = document.getElementById('calendarDayDialog');
        const dialogTitle = document.getElementById('calendarDayTitle');
        const dialogContent = document.getElementById('calendarDayContent');
        const eventTemplate = document.getElementById('calendarDayEvent');
        const dayFormat = new Intl.DateTimeFormat('id-ID', { dateStyle: 'full', timeZone: 'UTC' });
        const shortDateFormat = new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'short', timeZone: 'UTC' });
        let selectedDate;
        let loadingEvents = false;
        let eventsFailed = false;

        dialog.addEventListener('click', event => {
            if (event.target === dialog) dialog.close();
        });

        function renderDay() {
            if (!dialog.open) return;

            dialogContent.replaceChildren();
            const dayEvents = calendar.getEvents()
                .filter(event => {
                    const startDate = event.startStr.slice(0, 10);
                    const endDate = (event.endStr || event.startStr).slice(0, 10);
                    return startDate <= selectedDate && selectedDate <= endDate;
                })
                .sort((first, second) => first.startStr.localeCompare(second.startStr));

            if (loadingEvents || eventsFailed || !dayEvents.length) {
                const message = document.createElement('p');
                message.className = 'text-sm text-gray-600';
                if (loadingEvents) {
                    message.textContent = 'Memuat jadwal…';
                } else if (eventsFailed) {
                    message.textContent = 'Jadwal gagal dimuat. Muat ulang halaman untuk mencoba lagi.';
                } else {
                    message.textContent = 'Tidak ada kegiatan yang dijadwalkan pada hari ini.';
                }
                dialogContent.append(message);
                return;
            }

            const list = document.createElement('ul');
            list.className = 'space-y-2';
            dayEvents.forEach(event => {
                const row = eventTemplate.content.cloneNode(true);
                const link = row.querySelector('a');
                const account = event.extendedProps.account;
                const accountPrefix = `[${account}] `;
                const startDate = event.startStr.slice(0, 10);
                const endDate = (event.endStr || event.startStr).slice(0, 10);
                const startTime = event.startStr.slice(11, 16);
                const endTime = event.endStr.slice(11, 16);

                link.href = event.url;
                row.querySelector('[data-title]').textContent = event.title.startsWith(accountPrefix)
                    ? event.title.slice(accountPrefix.length)
                    : event.title;
                row.querySelector('[data-time]').textContent = startDate === endDate
                    ? `${startTime}–${endTime} WIB`
                    : `${shortDateFormat.format(new Date(startDate))}, ${startTime} – ${shortDateFormat.format(new Date(endDate))}, ${endTime} WIB`;
                row.querySelector('[data-status]').textContent = `${account} · ${event.extendedProps.status}`;
                list.append(row);
            });
            dialogContent.append(list);
        }

        function openDay(date) {
            selectedDate = date;
            dialogTitle.textContent = dayFormat.format(new Date(date));
            if (!dialog.open) dialog.showModal();
            renderDay();
        }

        function makeDateTrigger(element, date) {
            if (!element) return;

            element.classList.add('calendar-date-trigger');
            element.setAttribute('role', 'button');
            element.setAttribute('aria-haspopup', 'dialog');
            element.setAttribute('aria-label', `Lihat jadwal ${dayFormat.format(new Date(date))}`);
            element.tabIndex = 0;
            element.addEventListener('click', event => {
                event.preventDefault();
                openDay(date);
            });
            element.addEventListener('keydown', event => {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    openDay(date);
                }
            });
        }

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'id',
            firstDay: 1,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay',
            },
            buttonText: { today: 'Hari ini', month: 'Bulan', week: 'Minggu', day: 'Hari' },
            dayMaxEvents: mobileCalendar.matches ? 1 : 3,
            moreLinkContent: info => `+${info.num}`,
            height: 'auto',
            events: '{{ route("calendar.events") }}',
            loading: function (loading) {
                loadingEvents = loading;
                if (loading) eventsFailed = false;
                renderDay();
            },
            eventSourceFailure: function () {
                eventsFailed = true;
                renderDay();
            },
            eventsSet: renderDay,
            dateClick: function (info) {
                if (!info.jsEvent.target.closest('.calendar-date-trigger')) {
                    openDay(info.dateStr.slice(0, 10));
                }
            },
            dayCellDidMount: function (info) {
                makeDateTrigger(info.el.querySelector('.fc-daygrid-day-number'), calendar.formatIso(info.date, true));
            },
            dayHeaderDidMount: function (info) {
                if (info.view.type !== 'dayGridMonth') {
                    makeDateTrigger(info.el.querySelector('.fc-col-header-cell-cushion'), calendar.formatIso(info.date, true));
                }
            },
            moreLinkClick: function (info) {
                openDay(calendar.formatIso(info.date, true));
            },
            eventTimeFormat: {
                hour: '2-digit',
                minute: '2-digit',
                meridiem: false,
                hour12: false
            }
        });

        calendar.render();
        mobileCalendar.addEventListener('change', event => {
            calendar.setOption('dayMaxEvents', event.matches ? 1 : 3);
        });
    });
</script>
@endpush
