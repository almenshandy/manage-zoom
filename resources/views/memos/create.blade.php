@extends('layouts.app')

@php
    $header = 'Buat Memo Kegiatan Zoom';
@endphp

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-200/80 p-8 shadow-sm">
        <div class="mb-6 pb-4 border-b border-slate-100">
            <h3 class="text-lg font-bold text-slate-800">Penerbitan Memo / Lembar Persetujuan</h3>
            <p class="text-xs text-slate-500 mt-1">Cetak dokumen resmi untuk lampiran tugas atau pertanggungjawaban kegiatan.</p>
        </div>

        <form method="POST" action="{{ route('memos.store') }}" class="space-y-4 text-xs">
            @csrf

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Tautkan ke Booking Zoom</label>
                <select name="zoom_booking_id" class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Tanpa Tautan Booking --</option>
                    @foreach($bookings as $b)
                        <option value="{{ $b->id }}" {{ (isset($booking) && $booking->id === $b->id) ? 'selected' : '' }}>
                            #ZM-{{ str_pad($b->id, 5, '0', STR_PAD_LEFT) }} - {{ $b->topic }} ({{ $b->user->name ?? '-' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nomor Surat / Memo *</label>
                    <input type="text" name="memo_number" value="{{ old('memo_number', 'MEMO/ZM/' . date('Y/m') . '/' . rand(100, 999)) }}" required
                        class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Tanggal Surat *</label>
                    <input type="date" name="memo_date" value="{{ old('memo_date', date('Y-m-d')) }}" required
                        class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Isi Memo / Keterangan Penugasan *</label>
                <textarea name="content" rows="6" required
                    class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 leading-relaxed"
                    placeholder="Sehubungan dengan permohonan peminjaman fasilitas Zoom Meeting untuk pelaksanaan kegiatan rapat...">{{ old('content', isset($booking) ? "Diberitahukan bahwa permohonan peminjaman akun Zoom Meeting untuk kegiatan '{$booking->topic}' telah disetujui untuk dilaksanakan pada tanggal {$booking->start_date->format('d F Y')} pukul " . substr($booking->start_time, 0, 5) . " WIB." : '') }}</textarea>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="has_signature" value="1" id="has_signature" checked class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                <label for="has_signature" class="font-semibold text-slate-700 cursor-pointer">Sertakan Kolom Tanda Tangan Pejabat Terkait</label>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('memos.index') }}" class="px-5 py-2.5 bg-slate-100 text-slate-700 font-semibold rounded-xl hover:bg-slate-200 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-lg shadow-blue-600/30 transition">
                    Terbitkan Memo
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
