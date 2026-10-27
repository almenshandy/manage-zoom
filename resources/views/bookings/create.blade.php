@extends('layouts.app')

@php
    $header = 'Pengajuan Peminjaman Akun Zoom';
@endphp

@section('content')
<div class="space-y-4">
    <!-- Header Indicator & Search -->
    <div class="bg-white p-4 rounded border border-gray-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h3 class="text-xs font-semibold text-gray-900 uppercase tracking-wider">Matriks Ketersediaan Akun Zoom (30 Hari Ke Depan)</h3>
            <p class="text-xs text-gray-500 mt-0.5">Pilih slot tanggal kosong (ikon <span class="font-bold text-gray-700">+</span>) pada akun Zoom yang diinginkan untuk membuka form peminjaman.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" id="searchAccount" placeholder="Filter akun..." class="pl-8 pr-3 py-1.5 bg-white border border-gray-300 rounded text-xs focus:outline-none focus:border-gray-500 w-48">
            </div>

            <div class="flex items-center gap-2.5 text-[11px] text-gray-600 border-l border-gray-200 pl-3">
                <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded bg-blue-100 border border-blue-400"></span> Terisi</span>
                <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded bg-white border border-gray-300 text-gray-400 flex items-center justify-center text-[9px]">+</span> Kosong</span>
                <span class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded bg-gray-100 border border-gray-300"></span> Expired</span>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="p-3 bg-red-50 border border-red-200 rounded text-xs text-red-700">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Timeline Grid Matrix Card -->
    <div class="bg-white rounded border border-gray-200 overflow-hidden">
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
                                    <div class="w-7 h-7 rounded bg-gray-100 flex items-center justify-center text-gray-600 shrink-0">
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
                                                   class="block w-full py-1 px-1.5 rounded bg-blue-50 border border-blue-200 text-blue-900 hover:bg-blue-100 transition text-[10px] text-left leading-tight"
                                                   title="{{ $b['display_title'] ?? $b['topic'] }} ({{ $b['user_name'] }}) - {{ $b['start_time'] }} s/d {{ $b['end_time'] }} WIB">
                                                    <span class="font-semibold block truncate">{{ $b['start_time'] }}-{{ $b['end_time'] }}</span>
                                                    <span class="text-[9px] text-blue-700 block truncate font-medium">
                                                        {{ !empty($b['project_name']) ? '[' . $b['project_name'] . ']' : $b['user_name'] }}
                                                    </span>
                                                </a>
                                            @endforeach
                                            <button type="button" 
                                                class="btn-book-slot w-full py-0.5 rounded border border-dashed border-gray-300 text-gray-400 hover:text-gray-700 hover:border-gray-400 text-[9px] transition"
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
                                            class="btn-book-slot w-full h-9 rounded flex items-center justify-center text-gray-300 hover:text-gray-900 hover:bg-gray-100 transition group"
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
<div id="bookingModal" class="hidden fixed inset-0 bg-gray-900/50 z-50 flex items-center justify-center p-3 sm:p-4 backdrop-blur-xs overflow-y-auto">
    <div class="bg-white rounded-lg border border-gray-200 w-full max-w-xl max-h-[92vh] flex flex-col overflow-hidden shadow-xl animate-fadeIn my-auto">
        <!-- Modal Header (Fixed Top) -->
        <div class="px-5 py-3 border-b border-gray-200 flex items-center justify-between bg-white shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded bg-gray-900 text-white flex items-center justify-center text-xs">
                    <i class="fa-solid fa-calendar-plus"></i>
                </div>
                <div>
                    <h4 class="text-xs font-semibold text-gray-900 uppercase tracking-wider">Form Pengajuan Peminjaman Zoom</h4>
                    <p class="text-[10px] text-gray-500">Lengkapi formulir untuk slot waktu yang dipilih</p>
                </div>
            </div>
            <button type="button" onclick="closeModal()" class="w-7 h-7 rounded flex items-center justify-center text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <!-- Form Body (Scrollable bila layar laptop/layar kecil) -->
        <form method="POST" action="{{ route('bookings.store') }}" class="flex flex-col flex-1 overflow-hidden text-xs">
            @csrf
            <input type="hidden" name="zoom_account_id" id="modal_zoom_account_id">

            <div class="p-5 space-y-3.5 overflow-y-auto custom-scrollbar flex-1">
                <!-- Detail Akun & Tanggal Info Pill -->
                <div class="p-2.5 bg-gray-50 rounded border border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-[11px]">
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
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Nama Pemohon (Karyawan / User) <span class="text-red-500">*</span></label>
                        <select name="user_id" class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500 text-xs">
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" {{ $u->id == auth()->id() ? 'selected' : '' }}>
                                    {{ $u->name }} &bull; {{ $u->email }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Topik / Nama Kegiatan <span class="text-red-500">*</span></label>
                        <input type="text" name="topic" required placeholder="Contoh: Rapat Koordinasi PMB 2026..."
                            class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500 text-xs">
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">
                            Nama Proyek / Pelaksanaan <span class="text-gray-400 font-normal">(Opsional)</span>
                        </label>
                        <input type="text" name="project_name" placeholder="Contoh: Proyek Asesmen Mandiri..."
                            class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500 text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                        <input type="date" name="start_date" id="modal_start_date" required readonly
                            class="w-full px-3 py-1.5 bg-gray-50 border border-gray-300 rounded text-gray-700 font-medium cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Tanggal Selesai <span class="text-red-500">*</span></label>
                        <input type="date" name="end_date" id="modal_end_date" required
                            class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Jam Mulai <span class="text-red-500">*</span></label>
                        <input type="time" name="start_time" id="modal_start_time" value="09:00" required
                            class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500">
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Jam Selesai <span class="text-red-500">*</span></label>
                        <input type="time" name="end_time" id="modal_end_time" value="11:00" required
                            class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500">
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Durasi (Menit)</label>
                        <input type="number" name="duration_minutes" id="modal_duration" value="120" readonly required
                            class="w-full px-3 py-1.5 bg-gray-50 border border-gray-300 rounded focus:outline-none font-mono text-gray-700 cursor-not-allowed">
                    </div>
                </div>

                <div>
                    <label class="block font-medium text-gray-700 mb-1">Deskripsi / Agenda (Opsional)</label>
                    <textarea name="description" rows="2" placeholder="Catatan ringkas agenda..."
                        class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500 text-xs"></textarea>
                </div>

                <div>
                    <label class="block font-medium text-gray-700 mb-1">Catatan Pemohon (Opsional)</label>
                    <input type="text" name="user_note" placeholder="Contoh: Butuh breakout room 3 grup..."
                        class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500 text-xs">
                </div>

                @if(auth()->user() && auth()->user()->isAdmin())
                    <div class="p-2.5 bg-blue-50/70 border border-blue-200 rounded text-[11px] text-blue-800 flex items-start gap-2">
                        <i class="fa-solid fa-circle-check text-blue-600 mt-0.5 shrink-0"></i>
                        <span>Sebagai Administrator, pengajuan ini akan <strong>langsung disetujui (ACC)</strong> dan siap digenerate link Zoom.</span>
                    </div>
                @endif
            </div>

            <!-- Modal Footer (Fixed Bottom) -->
            <div class="px-5 py-3 border-t border-gray-200 bg-gray-50 flex items-center justify-end gap-2 shrink-0">
                <button type="button" onclick="closeModal()" class="px-4 py-1.5 bg-white border border-gray-300 text-gray-700 font-medium rounded hover:bg-gray-100 transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-1.5 bg-gray-900 hover:bg-black text-white font-medium rounded transition flex items-center gap-1.5">
                    <i class="fa-solid fa-paper-plane text-[10px]"></i> Kirim Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const modal = document.getElementById('bookingModal');
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

    // Filter Akun
    document.getElementById('searchAccount')?.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        document.querySelectorAll('.account-row').forEach(row => {
            const email = row.querySelector('.account-email')?.textContent.toLowerCase() || '';
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
            modal.classList.remove('hidden');
        });
    });

    function closeModal() {
        modal.classList.add('hidden');
    }

    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });
</script>
@endpush
@endsection
