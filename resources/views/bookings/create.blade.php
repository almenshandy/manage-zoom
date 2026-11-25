@extends('layouts.app')

@php
    $header = 'Pengajuan Peminjaman Akun Zoom';
@endphp

@section('content')
<div class="space-y-4">
    <!-- Top Action Bar (Flat Minimalis) -->
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('bookings.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 text-xs font-medium rounded-sm transition">
            <i class="fa-solid fa-arrow-left text-[10px]"></i> <span>Kembali<span class="hidden md:inline"> ke Daftar Booking</span></span>
        </a>

        <div class="hidden md:flex items-center gap-2.5 text-[11px] text-gray-600 bg-white px-3 py-1.5 rounded-sm border border-gray-200">
            <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-blue-100 border border-blue-400"></span> Terisi</span>
            <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-white border border-gray-300 text-gray-400 flex items-center justify-center text-[9px]">+</span> Kosong</span>
            <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-gray-100 border border-gray-300"></span> Expired</span>
        </div>
    </div>

    <!-- Header Indicator & Search Card -->
    <div class="mobile-availability-header bg-white p-4 rounded-sm border border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-xs font-semibold text-gray-900 uppercase tracking-wider">Ketersediaan Akun Zoom (30 Hari Ke Depan)</h3>
            <p class="text-xs text-gray-500 mt-0.5 md:hidden">Pilih akun Zoom untuk melihat kalender peminjaman.</p>
            <p class="hidden md:block text-xs text-gray-500 mt-0.5">Pilih slot tanggal (ikon <span class="font-bold text-gray-700">+</span>) pada akun Zoom yang diinginkan untuk membuka form peminjaman.</p>
        </div>

        <div class="hidden md:block relative shrink-0">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
            <input type="text" id="searchAccount" placeholder="Filter email akun..." class="pl-8 pr-3 py-1.5 bg-white border border-gray-300 rounded-sm text-xs focus:outline-none focus:border-brand-600 w-full sm:w-56">
        </div>
    </div>

    @if($errors->any())
        <div class="p-3 bg-red-50 border border-red-200 rounded-sm text-xs text-red-700">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Timeline Grid Matrix Card -->
    <div class="md:hidden space-y-3">
        @if($accounts->isNotEmpty())
            <div class="mobile-date-picker bg-white rounded-sm border">
                <label for="mobileBookingAccount" class="block mb-2">Akun Zoom</label>
                <select id="mobileBookingAccount" class="w-full px-3 border" aria-describedby="mobileBookingAccountDetails">
                    <option value="">Pilih akun Zoom</option>
                    @foreach($accounts as $account)
                        <option value="{{ $account->id }}" data-email="{{ $account->email }}"
                            data-max-date="{{ $account->expired_date?->format('Y-m-d') }}"
                            data-details="{{ $account->capacity }} peserta · Berlaku s/d {{ $account->expired_date?->format('d/m/Y') }}">
                            {{ $account->name }} · {{ $account->email }}
                        </option>
                    @endforeach
                </select>
                <div class="mobile-account-search">
                    <label for="mobileAccountSearch" class="sr-only">Cari akun Zoom</label>
                    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                    <input id="mobileAccountSearch" type="search" placeholder="Cari nama atau email akun Zoom…" autocomplete="off">
                </div>
                <p id="mobileBookingAccountDetails" class="mt-2 text-xs text-gray-600" aria-live="polite"></p>
                <p id="mobileAccountEmpty" class="hidden mt-2 text-sm text-gray-600" role="status">Tidak ada akun yang sesuai pencarian.</p>
            </div>
            <section id="mobileBookingCalendar" class="hidden space-y-3" aria-label="Kalender peminjaman">
                <div class="mobile-date-picker bg-white rounded-sm border">
                    <div class="flex items-center justify-between gap-2">
                        <button type="button" id="mobileBookingPreviousMonth" aria-label="Bulan sebelumnya" class="h-11 w-11 rounded-sm border border-gray-200 disabled:opacity-40">
                            <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
                        </button>
                        <h4 id="mobileBookingMonth" class="text-sm font-semibold" aria-live="polite"></h4>
                        <button type="button" id="mobileBookingNextMonth" aria-label="Bulan berikutnya" class="h-11 w-11 rounded-sm border border-gray-200 disabled:opacity-40">
                            <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
                        </button>
                    </div>
                    <div class="mt-3 grid grid-cols-7 text-center text-[11px] font-medium text-gray-600" aria-hidden="true">
                        @foreach(['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $weekday)
                            <span>{{ $weekday }}</span>
                        @endforeach
                    </div>
                    <div id="mobileBookingDays" class="mt-1 grid grid-cols-7 gap-y-1"></div>
                    <p class="mt-3 flex items-center gap-2 text-xs text-gray-600">
                        <span class="h-1.5 w-1.5 rounded-full bg-blue-600" aria-hidden="true"></span> Sudah ada booking
                    </p>
                </div>
                <div class="mobile-date-picker bg-white rounded-sm border">
                    <h4 id="mobileBookingScheduleDate" class="text-sm font-semibold"></h4>
                    <div id="mobileBookingSchedule" class="my-3" aria-live="polite"></div>
                    <button type="button" id="mobileBookingSlot" class="mobile-slot-action btn-book-slot w-full px-3 py-2 font-medium">
                        <i class="fa-solid fa-plus mr-1" aria-hidden="true"></i> <span>Buat booking</span>
                    </button>
                </div>
            </section>
        @else
            <p class="p-4 bg-white rounded-sm border border-gray-200 text-sm text-gray-500">Tidak ada akun Zoom aktif yang tersedia saat ini.</p>
        @endif
    </div>
    <div class="hidden md:block bg-white rounded-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-left border-collapse whitespace-nowrap text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-600 font-semibold text-[11px]">
                        <th class="px-2 py-2.5 border-r border-gray-200 sticky left-0 z-20 bg-gray-50 text-center w-10 min-w-[40px] max-w-[40px]" rowspan="2">No</th>
                        <th class="px-3 py-2.5 border-r border-gray-200 sticky left-[40px] z-20 bg-gray-50 min-w-[230px] border-r-2 border-r-gray-300" rowspan="2">Akun Zoom Aktif</th>
                        @foreach($dates as $d)
                            <th class="px-2 py-1.5 text-center border-r border-gray-200 {{ $d['is_today'] ? 'bg-blue-50 text-blue-700 font-bold' : ($d['is_sunday'] ? 'bg-red-50/50 text-red-600' : '') }}" style="min-width: 58px;">
                                <span class="text-xs">{{ $d['day_num'] }}</span>
                                <span class="block text-[9px] uppercase font-normal text-gray-400">{{ $d['month_name'] }}</span>
                            </th>
                        @endforeach
                    </tr>
                    <tr class="bg-gray-50 border-b border-gray-200 text-[10px] text-gray-500">
                        @foreach($dates as $d)
                            <th class="px-1 py-1 text-center border-r border-gray-200 font-medium {{ $d['is_today'] ? 'bg-blue-50 text-blue-700 font-bold' : ($d['is_sunday'] ? 'bg-red-50/50 text-red-600' : '') }}">
                                {{ $d['day_name'] }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($accounts as $idx => $acc)
                        @php
                            $todayDate = \Carbon\Carbon::today();
                            $expDate = $acc->expired_date ? \Carbon\Carbon::parse($acc->expired_date)->startOfDay() : null;
                            $diffDays = $expDate ? $todayDate->diffInDays($expDate, false) : 0;
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition account-row">
                            <td class="px-2 py-2.5 border-r border-gray-200 sticky left-0 z-10 bg-white text-center text-gray-500 font-mono text-[11px] w-10 min-w-[40px] max-w-[40px]">
                                {{ $idx + 1 }}
                            </td>
                            <td class="px-3 py-2.5 border-r border-gray-200 sticky left-[40px] z-10 bg-white min-w-[230px] border-r-2 border-r-gray-300">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-sm bg-gray-100 flex items-center justify-center text-gray-600 shrink-0">
                                        <i class="fa-solid fa-video text-xs"></i>
                                    </div>
                                    <div class="overflow-hidden">
                                        <div class="font-medium text-gray-900 truncate account-email text-xs" title="{{ $acc->email }}">{{ $acc->email }}</div>
                                        <div class="flex items-center gap-1.5 mt-0.5 text-[10px] text-gray-500">
                                            <span><i class="fa-solid fa-users text-[9px]"></i> {{ $acc->capacity }} Pax</span>
                                            <span>&bull;</span>
                                            @if($diffDays <= 0)
                                                <span class="text-red-600 font-semibold">Hari Terakhir</span>
                                            @else
                                                <span class="text-emerald-700 font-medium">Sisa {{ $diffDays }} hr</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            @foreach($dates as $d)
                                @php
                                    $currDate = \Carbon\Carbon::parse($d['date_string']);
                                    $isExpiredOnDate = $expDate && $currDate->greaterThan($expDate);
                                    $dayBookings = $matrixBookings[$acc->id][$d['date_string']] ?? [];
                                @endphp

                                @if($isExpiredOnDate)
                                    <td class="border-r border-gray-200 p-1 text-center bg-gray-100/70" title="Akun sudah kedaluwarsa pada tanggal ini">
                                        <div class="w-full h-9 flex items-center justify-center text-gray-300">
                                            <i class="fa-solid fa-ban text-[10px]"></i>
                                        </div>
                                    </td>
                                @elseif(!empty($dayBookings))
                                    <td class="border-r border-gray-200 p-1 text-center align-middle" style="min-width: 58px;">
                                        <div class="space-y-1">
                                            @foreach($dayBookings as $b)
                                                <a href="{{ route('bookings.show', $b['slug'] ?? $b['id']) }}" 
                                                   class="block w-full py-1 px-1.5 rounded-sm bg-blue-50 border border-blue-200 text-blue-900 hover:bg-blue-100 transition text-[10px] text-left leading-tight"
                                                   title="{{ $b['display_title'] ?? $b['topic'] }} ({{ $b['user_name'] }}) - {{ $b['start_time'] }} s/d {{ $b['end_time'] }} WIB">
                                                    <span class="font-semibold block truncate">{{ $b['start_time'] }}-{{ $b['end_time'] }}</span>
                                                    <span class="text-[9px] text-blue-700 block truncate font-medium">
                                                        {{ !empty($b['project_name']) ? '[' . $b['project_name'] . ']' : $b['user_name'] }}
                                                    </span>
                                                </a>
                                            @endforeach
                                            <button type="button" 
                                                class="btn-book-slot w-full py-0.5 rounded-sm border border-dashed border-gray-300 text-gray-400 hover:text-gray-700 hover:border-gray-400 text-[9px] transition"
                                                data-account-id="{{ $acc->id }}"
                                                data-account-email="{{ $acc->email }}"
                                                data-date="{{ $d['date_string'] }}"
                                                data-date-display="{{ \Carbon\Carbon::parse($d['date_string'])->locale('id')->isoFormat('D MMMM Y') }}"
                                                data-max-date="{{ $acc->expired_date ? $acc->expired_date->format('Y-m-d') : '' }}"
                                                title="Booking jam lain pada tanggal ini">
                                                <i class="fa-solid fa-plus text-[8px]"></i>
                                            </button>
                                        </div>
                                    </td>
                                @else
                                    <td class="border-r border-gray-200 p-1 text-center align-middle">
                                        <button type="button" 
                                            class="btn-book-slot w-full h-9 rounded-sm flex items-center justify-center text-gray-300 hover:text-gray-900 hover:bg-gray-100 transition group"
                                            data-account-id="{{ $acc->id }}"
                                            data-account-email="{{ $acc->email }}"
                                            data-date="{{ $d['date_string'] }}"
                                            data-date-display="{{ \Carbon\Carbon::parse($d['date_string'])->locale('id')->isoFormat('D MMMM Y') }}"
                                            data-max-date="{{ $acc->expired_date ? $acc->expired_date->format('Y-m-d') : '' }}"
                                            title="Pilih slot tanggal {{ $d['day_num'] }} {{ $d['month_name'] }} untuk akun ini">
                                            <i class="fa-solid fa-plus text-[11px] group-hover:scale-110 transition-transform"></i>
                                        </button>
                                    </td>
                                @endif
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($dates) + 2 }}" class="p-8 text-center text-gray-500 text-xs">
                                <i class="fa-solid fa-circle-exclamation text-amber-500 mb-1 text-base block"></i>
                                Tidak ada akun Zoom aktif yang tersedia saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form Booking Zoom (Flat Minimalis, Responsif & Scrollable) -->
<div id="bookingModal" role="dialog" aria-modal="true" aria-labelledby="bookingModalTitle" class="hidden fixed inset-0 bg-gray-900/50 z-50 flex items-center justify-center p-3 sm:p-4 backdrop-blur-xs overflow-y-auto">
    <div class="bg-white rounded-sm border border-gray-200 w-full max-w-xl max-h-[92vh] flex flex-col overflow-hidden shadow-xl animate-fadeIn my-auto">
        <!-- Modal Header (Fixed Top) -->
        <div class="px-5 py-3 border-b border-gray-200 flex items-center justify-between bg-white shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-sm bg-brand-900 text-white flex items-center justify-center text-xs">
                    <i class="fa-solid fa-calendar-plus"></i>
                </div>
                <div>
                    <h4 id="bookingModalTitle" class="text-xs font-semibold text-gray-900 uppercase tracking-wider">Form Pengajuan Peminjaman Zoom</h4>
                    <p class="text-[10px] text-gray-500">Lengkapi formulir untuk slot waktu yang dipilih</p>
                </div>
            </div>
            <button type="button" onclick="closeModal()" aria-label="Tutup form booking" class="w-7 h-7 rounded-sm flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <!-- Form Body (Scrollable bila layar laptop/layar kecil) -->
        <form method="POST" action="{{ route('bookings.store') }}" class="flex flex-col flex-1 overflow-hidden text-xs">
            @csrf
            <input type="hidden" name="zoom_account_id" id="modal_zoom_account_id">

            <div class="p-5 space-y-3.5 overflow-y-auto custom-scrollbar flex-1">
                <!-- Detail Akun & Tanggal Info Pill -->
                <div class="p-2.5 bg-gray-50 rounded-sm border border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-[11px]">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-video text-blue-600"></i>
                        <span class="text-gray-500">Akun:</span>
                        <span id="modal_account_display" class="font-mono font-semibold text-gray-900 truncate max-w-[200px]"></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fa-regular fa-calendar text-emerald-600"></i>
                        <span class="text-gray-500">Mulai:</span>
                        <span id="modal_date_display" class="font-medium text-gray-900"></span>
                    </div>
                </div>

                @if(auth()->user() && auth()->user()->isAdmin())
                    <div class="relative">
                        <label class="block font-medium text-gray-700 mb-1">Nama Pemohon (Karyawan / User) <span class="text-red-500">*</span></label>
                        
                        <!-- Searchable User Select Component (Flat Minimalist) -->
                        <div class="relative">
                            <input type="hidden" name="user_id" id="modal_user_id" value="{{ auth()->id() }}">
                            
                            <div class="relative">
                                <i class="fa-solid fa-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                                <input type="text" id="userSearchInput" 
                                    value="{{ auth()->user()->name }} ({{ auth()->user()->email }})"
                                    placeholder="Ketik untuk mencari nama karyawan / email..." 
                                    autocomplete="off"
                                    class="w-full pl-8 pr-8 py-1.5 bg-white border border-gray-300 rounded-sm focus:outline-none focus:border-brand-600 text-xs cursor-pointer">
                                <button type="button" onclick="toggleUserDropdown()" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <i class="fa-solid fa-chevron-down text-[10px]" id="userDropdownArrow"></i>
                                </button>
                            </div>

                            <!-- Dropdown List Hasil Pencarian -->
                            <div id="userDropdownList" class="hidden absolute left-0 right-0 top-full mt-1 bg-white border border-gray-300 rounded-sm shadow-md max-h-48 overflow-y-auto z-30 divide-y divide-gray-100 text-xs custom-scrollbar">
                                @foreach($users as $u)
                                    <div class="user-option-item px-3 py-2 hover:bg-gray-50 cursor-pointer flex items-center justify-between"
                                        data-id="{{ $u->id }}"
                                        data-name="{{ $u->name }}"
                                        data-email="{{ $u->email }}"
                                        data-dept="{{ $u->department_name ?: 'Umum' }}">
                                        <div>
                                            <span class="font-medium text-gray-900 block user-item-name">{{ $u->name }}</span>
                                            <span class="text-[10px] text-gray-500 user-item-meta">{{ $u->email }} &bull; {{ $u->department_name ?: 'Umum' }}</span>
                                        </div>
                                        <i class="fa-solid fa-check text-emerald-600 text-xs user-check-icon {{ $u->id == auth()->id() ? '' : 'hidden' }}"></i>
                                    </div>
                                @endforeach
                                <div id="userNotFoundMsg" class="hidden px-3 py-4 text-center text-gray-400 text-xs">
                                    Karyawan / user tidak ditemukan.
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Topik / Nama Kegiatan <span class="text-red-500">*</span></label>
                        <input type="text" name="topic" required placeholder="Contoh: Rapat Koordinasi PMB 2026..."
                            class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded-sm focus:outline-none focus:border-brand-600 text-xs">
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">
                            Pilih Proyek / Pelaksanaan <span class="text-gray-400 font-normal">(Opsional)</span>
                        </label>
                        <select name="project_id" class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded-sm focus:outline-none focus:border-brand-600 text-xs">
                            <option value="">-- Tanpa Proyek (Rapat Internal Biasa) --</option>
                            @foreach($projects as $prj)
                                <option value="{{ $prj->id }}" @selected((string) old('project_id') === (string) $prj->id)>
                                    {{ $prj->name }} {{ $prj->code ? "({$prj->code})" : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                        <input type="date" name="start_date" id="modal_start_date" required readonly
                            class="w-full px-3 py-1.5 bg-gray-50 border border-gray-300 rounded-sm text-gray-700 font-medium cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Tanggal Selesai <span class="text-red-500">*</span></label>
                        <input type="date" name="end_date" id="modal_end_date" required
                            class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded-sm focus:outline-none focus:border-brand-600">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Jam Mulai <span class="text-red-500">*</span></label>
                        <input type="time" name="start_time" id="modal_start_time" value="09:00" required
                            class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded-sm focus:outline-none focus:border-brand-600">
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Jam Selesai <span class="text-red-500">*</span></label>
                        <input type="time" name="end_time" id="modal_end_time" value="11:00" required
                            class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded-sm focus:outline-none focus:border-brand-600">
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Durasi (Menit)</label>
                        <input type="number" name="duration_minutes" id="modal_duration" value="120" readonly required
                            class="w-full px-3 py-1.5 bg-gray-50 border border-gray-300 rounded-sm focus:outline-none font-mono text-gray-700 cursor-not-allowed">
                    </div>
                </div>

                <div>
                    <label class="block font-medium text-gray-700 mb-1">Deskripsi / Agenda (Opsional)</label>
                    <textarea name="description" rows="2" placeholder="Catatan ringkas agenda..."
                        class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded-sm focus:outline-none focus:border-brand-600 text-xs"></textarea>
                </div>

                <div>
                    <label class="block font-medium text-gray-700 mb-1">Catatan Pemohon (Opsional)</label>
                    <input type="text" name="user_note" placeholder="Contoh: Butuh breakout room 3 grup..."
                        class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded-sm focus:outline-none focus:border-brand-600 text-xs">
                </div>

                @if(auth()->user() && auth()->user()->isAdmin())
                    <div class="p-2.5 bg-blue-50/70 border border-blue-200 rounded-sm text-[11px] text-blue-800 flex items-start gap-2">
                        <i class="fa-solid fa-circle-check text-blue-600 mt-0.5 shrink-0"></i>
                        <span>Sebagai Administrator, pengajuan ini akan <strong>langsung disetujui (ACC)</strong> dan siap digenerate link Zoom.</span>
                    </div>
                @endif
            </div>

            <!-- Modal Footer (Fixed Bottom) -->
            <div class="px-5 py-3 border-t border-gray-200 bg-gray-50 flex items-center justify-end gap-2 shrink-0">
                <button type="button" onclick="closeModal()" class="px-4 py-1.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-sm hover:bg-gray-100 transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-1.5 bg-brand-900 hover:bg-brand-950 text-white font-medium rounded-sm transition flex items-center gap-1.5">
                    <i class="fa-solid fa-paper-plane text-[10px]"></i> Kirim Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const mobileAccount = document.getElementById('mobileBookingAccount');

    if (mobileAccount) {
        const accounts = Array.from(mobileAccount.options).slice(1);
        const mobileSearch = document.getElementById('mobileAccountSearch');
        const calendar = document.getElementById('mobileBookingCalendar');
        const monthHeading = document.getElementById('mobileBookingMonth');
        const previousMonth = document.getElementById('mobileBookingPreviousMonth');
        const nextMonth = document.getElementById('mobileBookingNextMonth');
        const days = document.getElementById('mobileBookingDays');
        const schedule = document.getElementById('mobileBookingSchedule');
        const scheduleDate = document.getElementById('mobileBookingScheduleDate');
        const slot = document.getElementById('mobileBookingSlot');
        const firstDate = {{ \Illuminate\Support\Js::from($dates[0]['date_string']) }};
        const bookings = {{ \Illuminate\Support\Js::from($mobileBookings) }};
        const bookingUrl = {{ \Illuminate\Support\Js::from(route('bookings.show', '__BOOKING__')) }};
        const monthFormat = new Intl.DateTimeFormat('id-ID', { month: 'long', year: 'numeric', timeZone: 'UTC' });
        const dateFormat = new Intl.DateTimeFormat('id-ID', { dateStyle: 'full', timeZone: 'UTC' });
        let selectedDate = firstDate;
        let visibleMonth = new Date(firstDate.slice(0, 7) + '-01');

        function accountBookings(account, date) {
            return (bookings[account.value] ?? []).filter(booking => booking.start_date <= date && date <= booking.end_date);
        }

        function renderSchedule(account) {
            const dayBookings = [...accountBookings(account, selectedDate)]
                .sort((first, second) => first.start_time.localeCompare(second.start_time));
            const dateLabel = dateFormat.format(new Date(selectedDate));
            scheduleDate.textContent = dateLabel;
            schedule.replaceChildren();

            if (!dayBookings.length) {
                const empty = document.createElement('p');
                empty.className = 'text-sm text-gray-600';
                empty.textContent = 'Belum ada booking pada tanggal ini.';
                schedule.append(empty);
            }

            dayBookings.forEach(booking => {
                const link = document.createElement('a');
                link.href = bookingUrl.replace('__BOOKING__', encodeURIComponent(booking.slug || booking.id));
                link.className = 'mobile-slot-summary';
                const time = document.createElement('span');
                time.className = 'block font-semibold';
                time.textContent = `${booking.start_time} - ${booking.end_time} WIB`;
                const title = document.createElement('span');
                title.className = 'block break-words';
                title.textContent = booking.display_title;
                const status = document.createElement('span');
                status.className = 'block text-xs text-gray-600 mt-1';
                status.textContent = `${booking.user_name} · ${booking.status_label}`;
                link.append(time, title, status);
                schedule.append(link);
            });

            slot.dataset.accountId = account.value;
            slot.dataset.accountEmail = account.dataset.email;
            slot.dataset.maxDate = account.dataset.maxDate;
            slot.dataset.date = selectedDate;
            slot.dataset.dateDisplay = dateLabel;
            slot.querySelector('span').textContent = dayBookings.length ? 'Booking jam lain' : 'Buat booking';
        }

        function renderCalendar() {
            const account = mobileAccount.selectedOptions[0];
            document.getElementById('mobileBookingAccountDetails').textContent = account.dataset.details ?? '';
            calendar.classList.toggle('hidden', !account.value);
            if (!account.value) return;

            const month = visibleMonth.toISOString().slice(0, 7);
            const lastDate = account.dataset.maxDate;
            const firstDayOfMonth = firstDate.startsWith(month) ? Number(firstDate.slice(-2)) : 1;
            const lastDayOfMonth = lastDate.startsWith(month)
                ? Number(lastDate.slice(-2))
                : new Date(Date.UTC(visibleMonth.getUTCFullYear(), visibleMonth.getUTCMonth() + 1, 0)).getUTCDate();
            monthHeading.textContent = monthFormat.format(visibleMonth);
            previousMonth.disabled = firstDate.startsWith(month);
            nextMonth.disabled = lastDate.startsWith(month);
            days.replaceChildren();

            const leadingSpaces = (visibleMonth.getUTCDay() + firstDayOfMonth + 5) % 7;
            for (let offset = 0; offset < leadingSpaces; offset++) {
                days.append(document.createElement('span'));
            }

            for (let day = firstDayOfMonth; day <= lastDayOfMonth; day++) {
                const date = `${month}-${String(day).padStart(2, '0')}`;
                const count = accountBookings(account, date).length;
                const button = document.createElement('button');
                button.type = 'button';
                button.dataset.date = date;
                button.className = 'mobile-booking-day';
                button.textContent = day;
                button.setAttribute('aria-pressed', String(date === selectedDate));
                button.setAttribute('aria-label', dateFormat.format(new Date(date)) + (count ? `, ${count} booking` : ''));
                if (date === firstDate) button.setAttribute('aria-current', 'date');
                if (count) button.dataset.hasBookings = 'true';
                days.append(button);
            }

            renderSchedule(account);
        }

        mobileSearch.addEventListener('input', () => {
            const selectedAccount = mobileAccount.selectedOptions[0];
            const query = mobileSearch.value.trim().toLowerCase();
            const matches = accounts.filter(option => option.textContent.toLowerCase().includes(query));
            const options = selectedAccount.value && !matches.includes(selectedAccount)
                ? [selectedAccount, ...matches]
                : matches;
            mobileAccount.replaceChildren(mobileAccount.options[0], ...options);
            mobileAccount.value = selectedAccount.value;
            document.getElementById('mobileAccountEmpty').classList.toggle('hidden', matches.length > 0);
        });

        mobileAccount.addEventListener('change', () => {
            const account = mobileAccount.selectedOptions[0];
            if (account.value && selectedDate > account.dataset.maxDate) selectedDate = firstDate;
            visibleMonth = new Date(selectedDate.slice(0, 7) + '-01');
            renderCalendar();
        });

        previousMonth.addEventListener('click', () => {
            visibleMonth.setUTCMonth(visibleMonth.getUTCMonth() - 1);
            renderCalendar();
            if (previousMonth.disabled) nextMonth.focus();
        });

        nextMonth.addEventListener('click', () => {
            visibleMonth.setUTCMonth(visibleMonth.getUTCMonth() + 1);
            renderCalendar();
            if (nextMonth.disabled) previousMonth.focus();
        });

        days.addEventListener('click', event => {
            const button = event.target.closest('button[data-date]');
            if (!button || button.disabled) return;
            days.querySelector('[aria-pressed="true"]')?.setAttribute('aria-pressed', 'false');
            selectedDate = button.dataset.date;
            button.setAttribute('aria-pressed', 'true');
            renderSchedule(mobileAccount.selectedOptions[0]);
        });

        renderCalendar();
    }

    const modal = document.getElementById('bookingModal');
    let bookingModalTrigger;
    let previousBookingOverflow;
    const inputAccountId = document.getElementById('modal_zoom_account_id');
    const inputAccountDisplay = document.getElementById('modal_account_display');
    const inputDateDisplay = document.getElementById('modal_date_display');
    const inputStartDate = document.getElementById('modal_start_date');
    const inputEndDate = document.getElementById('modal_end_date');
    const inputStartTime = document.getElementById('modal_start_time');
    const inputEndTime = document.getElementById('modal_end_time');
    const inputDuration = document.getElementById('modal_duration');

    // Auto Duration Calculation
    function calculateModalDuration() {
        if (!inputStartTime.value || !inputEndTime.value) return;
        const [sH, sM] = inputStartTime.value.split(':').map(Number);
        const [eH, eM] = inputEndTime.value.split(':').map(Number);
        let diff = (eH * 60 + eM) - (sH * 60 + sM);
        if (diff > 0) {
            inputDuration.value = diff;
        } else {
            inputDuration.value = 60; // fallback default
        }
    }

    inputStartTime.addEventListener('change', calculateModalDuration);
    inputEndTime.addEventListener('change', calculateModalDuration);

    document.getElementById('searchAccount').addEventListener('input', function () {
        const query = this.value.toLowerCase();
        document.querySelectorAll('.account-row').forEach(row => {
            const email = row.querySelector('.account-email').textContent.toLowerCase();
            row.style.display = email.includes(query) ? '' : 'none';
        });
    });

    // Open Modal on Slot Click
    document.querySelectorAll('.btn-book-slot').forEach(btn => {
        btn.addEventListener('click', function() {
            const accId = this.getAttribute('data-account-id');
            const accEmail = this.getAttribute('data-account-email');
            const dateStr = this.getAttribute('data-date');
            const dateDisplay = this.getAttribute('data-date-display');
            const maxDate = this.getAttribute('data-max-date');

            inputAccountId.value = accId;
            if (inputAccountDisplay) inputAccountDisplay.textContent = accEmail;
            if (inputDateDisplay) inputDateDisplay.textContent = dateDisplay;
            inputStartDate.value = dateStr;
            inputEndDate.value = dateStr;
            inputEndDate.min = dateStr;
            if (maxDate) {
                inputEndDate.max = maxDate;
            }

            calculateModalDuration();
            bookingModalTrigger = this;
            previousBookingOverflow = document.body.style.overflow;
            document.body.style.overflow = 'hidden';
            modal.classList.remove('hidden');
            modal.querySelector('button[aria-label="Tutup form booking"]').focus();
        });
    });

    function closeModal() {
        modal.classList.add('hidden');
        document.body.style.overflow = previousBookingOverflow ?? '';
        bookingModalTrigger?.focus();
        closeUserDropdown();
    }

    modal.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            event.preventDefault();
            closeModal();
        }
    });

    // Searchable User Select Logic
    const userSearchInput = document.getElementById('userSearchInput');
    const userDropdownList = document.getElementById('userDropdownList');
    const modalUserId = document.getElementById('modal_user_id');
    const userNotFoundMsg = document.getElementById('userNotFoundMsg');
    const userOptionItems = document.querySelectorAll('.user-option-item');

    function toggleUserDropdown() {
        if (!userDropdownList) return;
        userDropdownList.classList.toggle('hidden');
    }

    function closeUserDropdown() {
        if (userDropdownList) {
            userDropdownList.classList.add('hidden');
        }
    }

    if (userSearchInput) {
        userSearchInput.addEventListener('focus', function() {
            userDropdownList.classList.remove('hidden');
        });

        userSearchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            let hasVisible = false;

            userOptionItems.forEach(item => {
                const name = item.getAttribute('data-name').toLowerCase();
                const email = item.getAttribute('data-email').toLowerCase();
                const dept = item.getAttribute('data-dept').toLowerCase();

                if (name.includes(query) || email.includes(query) || dept.includes(query)) {
                    item.style.display = 'flex';
                    hasVisible = true;
                } else {
                    item.style.display = 'none';
                }
            });

            if (userNotFoundMsg) {
                userNotFoundMsg.style.display = hasVisible ? 'none' : 'block';
            }
            userDropdownList.classList.remove('hidden');
        });
    }

    userOptionItems.forEach(item => {
        item.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const email = this.getAttribute('data-email');

            if (modalUserId) modalUserId.value = id;
            if (userSearchInput) userSearchInput.value = `${name} (${email})`;

            // Update centang ikon
            document.querySelectorAll('.user-check-icon').forEach(icon => icon.classList.add('hidden'));
            const checkIcon = this.querySelector('.user-check-icon');
            if (checkIcon) checkIcon.classList.remove('hidden');

            closeUserDropdown();
        });
    });

    // Close dropdown on outside click
    document.addEventListener('click', function(e) {
        if (userSearchInput && !userSearchInput.contains(e.target) && userDropdownList && !userDropdownList.contains(e.target)) {
            closeUserDropdown();
        }
    });

    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });
</script>
@endpush
@endsection
