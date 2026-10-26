@extends('layouts.app')

@php
    $header = 'Buat Memo Kegiatan';
@endphp

@section('content')
<div class="max-w-2xl mx-auto space-y-4">
    <x-page-header title="Penerbitan Memo Resmi" description="Isi formulir berikut untuk menerbitkan lembar persetujuan/memo kegiatan." :backUrl="route('memos.index')" backText="Kembali ke Daftar Memo" />

    <x-card>
        <div class="p-6">
            @if($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 text-xs rounded">
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('memos.store') }}" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-medium text-gray-700 mb-1">Pilih Booking Terkait (Opsional)</label>
                    <select name="zoom_booking_id" class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500">
                        <option value="">-- Memo Umum (Tanpa Tautan Booking) --</option>
                        @foreach($bookings as $b)
                            <option value="{{ $b->id }}" {{ (old('zoom_booking_id', $booking->id ?? '') == $b->id) ? 'selected' : '' }}>
                                #ZM-{{ str_pad($b->id, 5, '0', STR_PAD_LEFT) }} - {{ $b->topic }} ({{ $b->user->name ?? 'User' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Nomor Memo / Surat *</label>
                        <input type="text" name="memo_number" value="{{ old('memo_number', 'MEMO/' . date('Y/m') . '/' . rand(100, 999)) }}" required
                            class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500 font-mono">
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Tanggal Surat *</label>
                        <input type="date" name="memo_date" value="{{ old('memo_date', date('Y-m-d')) }}" required
                            class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500">
                    </div>
                </div>

                <div>
                    <label class="block font-medium text-gray-700 mb-1">Isi / Keterangan Memo *</label>
                    <textarea name="content" rows="5" required
                        class="w-full px-3 py-2 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500 leading-relaxed"
                        placeholder="Tuliskan isi memo persetujuan atau catatan dinas...">{{ old('content', $booking ? "Sehubungan dengan permohonan penggunaan fasilitas Zoom Meeting untuk agenda '{$booking->topic}', dengan ini permohonan disetujui dengan rincian jadwal yang terlampir." : '') }}</textarea>
                </div>

                <div>
                    <label class="flex items-center gap-2 cursor-pointer font-medium text-gray-700">
                        <input type="checkbox" name="has_signature" value="1" {{ old('has_signature', true) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-gray-900 focus:ring-0">
                        <span>Sertakan kolom tanda tangan pejabat yang berwenang</span>
                    </label>
                </div>

                <div class="pt-4 border-t border-gray-200 flex items-center justify-end gap-2">
                    <x-btn variant="white" :href="route('memos.index')">Batal</x-btn>
                    <x-btn variant="primary" type="submit">Terbitkan Memo</x-btn>
                </div>
            </form>
        </div>
    </x-card>
</div>
@endsection
