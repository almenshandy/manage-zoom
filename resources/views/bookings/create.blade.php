@extends('layouts.app')

@php
    $header = 'Pengajuan Peminjaman Akun Zoom';
@endphp

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded border border-gray-200 p-6">
        <div class="mb-5 pb-3 border-b border-gray-200">
            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider">Form Pengajuan Peminjaman Zoom</h3>
            <p class="text-xs text-gray-500 mt-0.5">Isi detail kegiatan berikut untuk pengajuan slot meeting.</p>
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

        <form method="POST" action="{{ route('bookings.store') }}" class="space-y-4 text-xs">
            @csrf

            <div>
                <label class="block font-medium text-gray-700 mb-1">Topik / Nama Kegiatan Rapat <span class="text-red-500">*</span></label>
                <input type="text" name="topic" value="{{ old('topic') }}" required
                    class="w-full px-3 py-2 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500 text-xs"
                    placeholder="Contoh: Rapat Koordinasi Penerimaan Mahasiswa Baru 2026">
            </div>

            <div>
                <label class="block font-medium text-gray-700 mb-1">Deskripsi / Agenda Kegiatan</label>
                <textarea name="description" rows="3"
                    class="w-full px-3 py-2 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500"
                    placeholder="Tuliskan gambaran ringkas agenda rapat...">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block font-medium text-gray-700 mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required
                        class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500">
                </div>
                <div>
                    <label class="block font-medium text-gray-700 mb-1">Tanggal Selesai <span class="text-red-500">*</span></label>
                    <input type="date" name="end_date" value="{{ old('end_date', date('Y-m-d')) }}" required
                        class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block font-medium text-gray-700 mb-1">Jam Mulai <span class="text-red-500">*</span></label>
                    <input type="time" name="start_time" id="start_time" value="{{ old('start_time', '09:00') }}" required
                        class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500">
                </div>
                <div>
                    <label class="block font-medium text-gray-700 mb-1">Jam Selesai <span class="text-red-500">*</span></label>
                    <input type="time" name="end_time" id="end_time" value="{{ old('end_time', '11:00') }}" required
                        class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500">
                </div>
                <div>
                    <label class="block font-medium text-gray-700 mb-1">Durasi Otomatis (Menit) <span class="text-red-500">*</span></label>
                    <input type="number" name="duration_minutes" id="duration_minutes" value="{{ old('duration_minutes', 120) }}" min="15" required readonly
                        class="w-full px-3 py-1.5 bg-gray-50 border border-gray-300 rounded focus:outline-none text-gray-700 font-mono font-medium">
                </div>
            </div>

            <div>
                <label class="block font-medium text-gray-700 mb-1">
                    {{ auth()->user() && auth()->user()->isAdmin() ? 'Tugaskan Akun Zoom (Otomatis ACC jika dipilih)' : 'Preferensi Akun Zoom (Opsional)' }}
                </label>
                <select name="zoom_account_id" class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500 font-mono text-xs">
                    <option value="">{{ auth()->user() && auth()->user()->isAdmin() ? '-- Pilih Akun untuk Langsung ACC --' : '-- Biarkan Admin Memilihkan Akun Terbaik --' }}</option>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}" {{ old('zoom_account_id') == $acc->id ? 'selected' : '' }}>
                            {{ $acc->email }} ({{ $acc->capacity }} Pax) &bull; Masa Berlaku s/d {{ $acc->expired_date ? $acc->expired_date->format('d/m/Y') : '-' }}
                        </option>
                    @endforeach
                </select>
                @if(auth()->user() && auth()->user()->isAdmin())
                    <p class="text-[11px] text-blue-600 mt-1">
                        <i class="fa-solid fa-circle-info"></i> Karena Anda Administrator, jika memilih akun Zoom di sini, permohonan akan <strong>langsung disetujui (ACC)</strong> tanpa perlu menunggu approval lagi.
                    </p>
                @endif
            </div>

            <div>
                <label class="block font-medium text-gray-700 mb-1">Catatan Pemohon (Opsional)</label>
                <input type="text" name="user_note" value="{{ old('user_note') }}"
                    class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500"
                    placeholder="Contoh: Butuh breakout room atau co-host 3 orang...">
            </div>

            <div class="pt-4 flex items-center justify-end gap-2 border-t border-gray-200">
                <a href="{{ route('bookings.index') }}" class="px-4 py-1.5 bg-white border border-gray-300 text-gray-700 font-medium rounded hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-4 py-1.5 bg-gray-900 hover:bg-black text-white font-medium rounded transition">
                    Kirim Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');
        const durationInput = document.getElementById('duration_minutes');

        function calculateDuration() {
            if (!startTimeInput.value || !endTimeInput.value) return;

            const [startH, startM] = startTimeInput.value.split(':').map(Number);
            const [endH, endM] = endTimeInput.value.split(':').map(Number);

            const startMinutes = startH * 60 + startM;
            const endMinutes = endH * 60 + endM;

            let diff = endMinutes - startMinutes;
            if (diff > 0) {
                durationInput.value = diff;
            }
        }

        startTimeInput.addEventListener('change', calculateDuration);
        endTimeInput.addEventListener('change', calculateDuration);
        calculateDuration();
    });
</script>
@endpush
@endsection
