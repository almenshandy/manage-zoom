@extends('layouts.app')

@php
    $header = 'Edit Pengajuan Peminjaman Akun Zoom';
@endphp

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded border border-gray-200 p-6">
        <div class="mb-5 pb-3 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Ubah Pengajuan Booking</h3>
                <p class="text-xs text-gray-500 mt-0.5">Perbarui data kegiatan dan pastikan jadwal akurat.</p>
            </div>
            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-semibold border
                @if($booking->status === 0) bg-amber-50 text-amber-700 border-amber-200
                @elseif($booking->status === 1) bg-blue-50 text-blue-700 border-blue-200
                @elseif($booking->status === 3) bg-emerald-50 text-emerald-700 border-emerald-200
                @else bg-red-50 text-red-700 border-red-200 @endif">
                {{ $booking->status_label }}
            </span>
        </div>

        @if($errors->any())
            <div class="mb-5 p-3 bg-red-50 border border-red-200 rounded text-xs text-red-700">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('bookings.update', $booking) }}" class="space-y-4 text-xs">
            @csrf
            @method('PUT')

            <!-- Pemilihan Pemohon (Khusus Admin) atau Info Pemohon -->
            @if(auth()->user()->isAdmin())
                <div class="relative">
                    <label class="block font-medium text-gray-700 mb-1">
                        Nama Pemohon (Karyawan / User) <span class="text-red-500">*</span>
                    </label>
                    
                    <div class="relative">
                        <input type="hidden" name="user_id" id="edit_user_id" value="{{ old('user_id', $booking->user_id) }}">
                        
                        <div class="relative">
                            <i class="fa-solid fa-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                            <input type="text" id="userSearchInput" 
                                value="{{ $booking->user ? $booking->user->name . ' (' . $booking->user->email . ')' : '' }}"
                                placeholder="Ketik untuk mencari nama karyawan / email..." 
                                autocomplete="off"
                                class="w-full pl-8 pr-8 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500 text-xs cursor-pointer">
                            <button type="button" onclick="toggleUserDropdown()" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fa-solid fa-chevron-down text-[10px]" id="userDropdownArrow"></i>
                            </button>
                        </div>

                        <!-- Dropdown List Hasil Pencarian User -->
                        <div id="userDropdownList" class="hidden absolute left-0 right-0 top-full mt-1 bg-white border border-gray-300 rounded shadow-md max-h-48 overflow-y-auto z-30 divide-y divide-gray-100 text-xs custom-scrollbar">
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
                                    <i class="fa-solid fa-check text-emerald-600 text-xs user-check-icon {{ $u->id == old('user_id', $booking->user_id) ? '' : 'hidden' }}"></i>
                                </div>
                            @endforeach
                            <div id="userNotFoundMsg" class="hidden px-3 py-4 text-center text-gray-400 text-xs">
                                Karyawan / user tidak ditemukan.
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="p-3 bg-gray-50 rounded border border-gray-200 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-gray-400 uppercase tracking-wider block">Pemohon Kegiatan</span>
                        <span class="font-medium text-gray-900">{{ $booking->user->name ?? '-' }} ({{ $booking->user->department_name ?? 'Umum' }})</span>
                    </div>
                    <span class="text-[11px] text-gray-500 font-mono">{{ $booking->user->email ?? '' }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-medium text-gray-700 mb-1">Topik / Nama Kegiatan <span class="text-red-500">*</span></label>
                    <input type="text" name="topic" value="{{ old('topic', $booking->topic) }}" required
                        class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500 text-xs"
                        placeholder="Contoh: Rapat Koordinasi PMB 2026">
                </div>
                <div>
                    <label class="block font-medium text-gray-700 mb-1">
                        Pilih Proyek / Pelaksanaan <span class="text-gray-400 font-normal">(Opsional)</span>
                    </label>
                    <select name="project_name" class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500 text-xs">
                        <option value="">-- Tanpa Proyek (Rapat Internal Biasa) --</option>
                        @foreach($projects as $prj)
                            <option value="{{ $prj->name }}" {{ old('project_name', $booking->project_name) === $prj->name ? 'selected' : '' }}>
                                {{ $prj->name }} {{ $prj->code ? "({$prj->code})" : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block font-medium text-gray-700 mb-1">Deskripsi / Agenda Kegiatan</label>
                <textarea name="description" rows="3"
                    class="w-full px-3 py-2 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500"
                    placeholder="Tuliskan gambaran ringkas agenda rapat...">{{ old('description', $booking->description) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-medium text-gray-700 mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" value="{{ old('start_date', $booking->start_date ? \Carbon\Carbon::parse($booking->start_date)->format('Y-m-d') : '') }}" required
                        class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500">
                </div>
                <div>
                    <label class="block font-medium text-gray-700 mb-1">Tanggal Selesai <span class="text-red-500">*</span></label>
                    <input type="date" name="end_date" value="{{ old('end_date', $booking->end_date ? \Carbon\Carbon::parse($booking->end_date)->format('Y-m-d') : '') }}" required
                        class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block font-medium text-gray-700 mb-1">Jam Mulai <span class="text-red-500">*</span></label>
                    <input type="time" name="start_time" id="start_time" value="{{ old('start_time', $booking->start_time ? substr($booking->start_time, 0, 5) : '09:00') }}" required
                        class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500">
                </div>
                <div>
                    <label class="block font-medium text-gray-700 mb-1">Jam Selesai <span class="text-red-500">*</span></label>
                    <input type="time" name="end_time" id="end_time" value="{{ old('end_time', $booking->end_time ? substr($booking->end_time, 0, 5) : '11:00') }}" required
                        class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500">
                </div>
                <div>
                    <label class="block font-medium text-gray-700 mb-1">Durasi (Menit)</label>
                    <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', $booking->duration_minutes ?? 120) }}" min="1" readonly
                        class="w-full px-3 py-1.5 bg-gray-50 border border-gray-300 rounded focus:outline-none font-semibold text-gray-800 cursor-not-allowed">
                </div>
            </div>

            <div>
                <label class="block font-medium text-gray-700 mb-1">Preferensi Akun Zoom</label>
                <select name="zoom_account_id" class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500 font-mono text-xs">
                    <option value="">-- Biarkan Admin Memilihkan Akun Terbaik --</option>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}" {{ old('zoom_account_id', $booking->zoom_account_id) == $acc->id ? 'selected' : '' }}>
                            {{ $acc->email }} ({{ $acc->capacity }} Pax) &bull; Masa Berlaku s/d {{ $acc->expired_date ? $acc->expired_date->format('d/m/Y') : '-' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-medium text-gray-700 mb-1">Catatan Pemohon</label>
                <input type="text" name="user_note" value="{{ old('user_note', $booking->user_note) }}"
                    class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500"
                    placeholder="Contoh: Butuh breakout room atau co-host 3 orang...">
            </div>

            @if(auth()->user()->isAdmin())
                <div class="p-3 bg-gray-50 rounded border border-gray-200 space-y-2">
                    <p class="font-semibold text-gray-800 text-[11px] uppercase tracking-wider">Catatan Administrator</p>
                    <input type="text" name="admin_note" value="{{ old('admin_note', $booking->admin_note) }}"
                        class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500 text-xs"
                        placeholder="Catatan dari admin untuk pemohon...">
                </div>
            @endif

            <div class="pt-4 flex items-center justify-end gap-2 border-t border-gray-200">
                <a href="{{ route('bookings.index') }}" class="px-4 py-1.5 bg-white border border-gray-300 text-gray-700 font-medium rounded hover:bg-gray-50 transition">
                    Kembali
                </a>
                <button type="submit" class="px-4 py-1.5 bg-gray-900 hover:bg-black text-white font-medium rounded transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');
        const durationInput = document.getElementById('duration_minutes');

        function calculateDuration() {
            if (!startTimeInput.value || !endTimeInput.value) return;

            const [startH, startM] = startTimeInput.value.split(':').map(Number);
            const [endH, endM] = endTimeInput.value.split(':').map(Number);

            let startTotal = startH * 60 + startM;
            let endTotal = endH * 60 + endM;

            if (endTotal > startTotal) {
                durationInput.value = endTotal - startTotal;
            } else if (endTotal < startTotal) {
                durationInput.value = (1440 - startTotal) + endTotal;
            }
        }

        startTimeInput.addEventListener('change', calculateDuration);
        endTimeInput.addEventListener('change', calculateDuration);
        calculateDuration();
    });

    // Searchable User Select Logic untuk Halaman Edit
    const userSearchInput = document.getElementById('userSearchInput');
    const userDropdownList = document.getElementById('userDropdownList');
    const editUserId = document.getElementById('edit_user_id');
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

            if (editUserId) editUserId.value = id;
            if (userSearchInput) userSearchInput.value = `${name} (${email})`;

            document.querySelectorAll('.user-check-icon').forEach(icon => icon.classList.add('hidden'));
            const checkIcon = this.querySelector('.user-check-icon');
            if (checkIcon) checkIcon.classList.remove('hidden');

            closeUserDropdown();
        });
    });

    document.addEventListener('click', function(e) {
        if (userSearchInput && !userSearchInput.contains(e.target) && userDropdownList && !userDropdownList.contains(e.target)) {
            closeUserDropdown();
        }
    });
</script>
@endpush
@endsection
