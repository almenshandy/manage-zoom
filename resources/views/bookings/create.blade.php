@extends('layouts.app')

@php
    $header = 'Pengajuan Peminjaman Akun Zoom';
@endphp

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-200/80 p-8 shadow-sm">
        <div class="mb-6 pb-4 border-b border-slate-100">
            <h3 class="text-lg font-bold text-slate-800">Form Pengajuan Peminjaman Zoom</h3>
            <p class="text-xs text-slate-500 mt-1">Isi formulir berikut dengan teliti agar jadwal tidak bentrok dengan kegiatan lain.</p>
        </div>

        @if($errors->any())
            <div class="mb-5 p-4 bg-red-50 border border-red-200 rounded-xl text-xs text-red-700">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('bookings.store') }}" class="space-y-5 text-xs">
            @csrf

            <div>
                <label class="block font-semibold text-slate-700 mb-1.5">Topik / Nama Kegiatan Rapat *</label>
                <input type="text" name="topic" value="{{ old('topic') }}" required
                    class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white text-sm"
                    placeholder="Contoh: Rapat Koordinasi Penerimaan Mahasiswa Baru 2026">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1.5">Deskripsi / Agenda Kegiatan</label>
                <textarea name="description" rows="3"
                    class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white"
                    placeholder="Tuliskan gambaran ringkas agenda rapat...">{{ old('description') }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Tanggal Mulai *</label>
                    <input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required
                        class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Tanggal Selesai *</label>
                    <input type="date" name="end_date" value="{{ old('end_date', date('Y-m-d')) }}" required
                        class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Jam Mulai *</label>
                    <input type="time" name="start_time" value="{{ old('start_time', '09:00') }}" required
                        class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Jam Selesai *</label>
                    <input type="time" name="end_time" value="{{ old('end_time', '11:00') }}" required
                        class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Perkiraan Durasi (Menit) *</label>
                    <input type="number" name="duration_minutes" value="{{ old('duration_minutes', 120)" min="15" required
                        class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white">
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1.5">Preferensi Akun Zoom (Opsional)</label>
                <select name="zoom_account_id" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white">
                    <option value="">-- Biarkan Admin Memilihkan Akun Terbaik --</option>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}" {{ old('zoom_account_id') == $acc->id ? 'selected' : '' }}>
                            {{ $acc->name }} (Kapasitas: {{ $acc->capacity }} Peserta)
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1.5">Catatan Pemohon (Opsional)</label>
                <input type="text" name="user_note" value="{{ old('user_note') }}"
                    class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white"
                    placeholder="Contoh: Butuh breakout room atau co-host 3 orang...">
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('bookings.index') }}" class="px-5 py-2.5 bg-slate-100 text-slate-700 font-semibold rounded-xl hover:bg-slate-200 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-lg shadow-blue-600/30 transition">
                    Ajukan Permohonan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
