@extends('layouts.app')

@php
    $header = 'Edit Pengajuan Peminjaman Akun Zoom';
@endphp

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-200/80 p-8 shadow-sm">
        <div class="mb-6 pb-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-bold text-slate-800">Ubah Data Pengajuan Peminjaman</h3>
                <p class="text-xs text-slate-500 mt-1">Perbarui data kegiatan dan pastikan waktu tidak berbenturan.</p>
            </div>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                @if($booking->status === 0) bg-amber-50 text-amber-700 border border-amber-200
                @elseif($booking->status === 1) bg-blue-50 text-blue-700 border border-blue-200
                @elseif($booking->status === 3) bg-emerald-50 text-emerald-700 border border-emerald-200
                @else bg-red-50 text-red-700 border border-red-200 @endif">
                {{ $booking->status_label }}
            </span>
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

        <form method="POST" action="{{ route('bookings.update', $booking) }}" class="space-y-5 text-xs">
            @csrf
            @method('PUT')

            <div>
                <label class="block font-semibold text-slate-700 mb-1.5">Topik / Nama Kegiatan Rapat *</label>
                <input type="text" name="topic" value="{{ old('topic', $booking->topic) }}" required
                    class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white text-sm"
                    placeholder="Contoh: Rapat Koordinasi Penerimaan Mahasiswa Baru 2026">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1.5">Deskripsi / Agenda Kegiatan</label>
                <textarea name="description" rows="3"
                    class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white"
                    placeholder="Tuliskan gambaran ringkas agenda rapat...">{{ old('description', $booking->description) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Tanggal Mulai *</label>
                    <input type="date" name="start_date" value="{{ old('start_date', $booking->start_date ? \Carbon\Carbon::parse($booking->start_date)->format('Y-m-d') : '') }}" required
                        class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Tanggal Selesai *</label>
                    <input type="date" name="end_date" value="{{ old('end_date', $booking->end_date ? \Carbon\Carbon::parse($booking->end_date)->format('Y-m-d') : '') }}" required
                        class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Jam Mulai *</label>
                    <input type="time" name="start_time" value="{{ old('start_time', $booking->start_time ? substr($booking->start_time, 0, 5) : '09:00') }}" required
                        class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Jam Selesai *</label>
                    <input type="time" name="end_time" value="{{ old('end_time', $booking->end_time ? substr($booking->end_time, 0, 5) : '11:00') }}" required
                        class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Perkiraan Durasi (Menit) *</label>
                    <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $booking->duration_minutes ?? 120) }}" min="15" required
                        class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white">
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1.5">Preferensi Akun Zoom</label>
                <select name="zoom_account_id" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white">
                    <option value="">-- Biarkan Admin Memilihkan Akun Terbaik --</option>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}" {{ old('zoom_account_id', $booking->zoom_account_id) == $acc->id ? 'selected' : '' }}>
                            {{ $acc->name }} (Kapasitas: {{ $acc->capacity }} Peserta)
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1.5">Catatan Pemohon</label>
                <input type="text" name="user_note" value="{{ old('user_note', $booking->user_note) }}"
                    class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:bg-white"
                    placeholder="Contoh: Butuh breakout room atau co-host 3 orang...">
            </div>

            @if(auth()->user()->isAdmin())
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-3">
                    <p class="font-bold text-slate-700">Khusus Administrator:</p>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1.5">Catatan Administrator</label>
                        <input type="text" name="admin_note" value="{{ old('admin_note', $booking->admin_note) }}"
                            class="w-full p-2.5 bg-white border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 text-xs"
                            placeholder="Catatan dari admin untuk pemohon...">
                    </div>
                </div>
            @endif

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('bookings.index') }}" class="px-5 py-2.5 bg-slate-100 text-slate-700 font-semibold rounded-xl hover:bg-slate-200 transition">
                    Kembali
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-lg shadow-blue-600/30 transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
