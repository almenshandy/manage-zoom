@extends('layouts.app')

@php
    $header = 'Edit Akun Zoom';
@endphp

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-200/80 p-8 shadow-sm">
        <div class="mb-6 pb-4 border-b border-slate-100">
            <h3 class="text-lg font-bold text-slate-800">Edit Akun Zoom</h3>
            <p class="text-xs text-slate-500 mt-1">Perbarui data akun Zoom dan kredensial API terkait.</p>
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

        <form method="POST" action="{{ route('zoom-accounts.update', $zoomAccount->id) }}" class="space-y-4 text-xs">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Nama / Label Akun *</label>
                    <input type="text" name="name" value="{{ old('name', $zoomAccount->name) }}" required
                        class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Email Akun Zoom *</label>
                    <input type="email" name="email" value="{{ old('email', $zoomAccount->email) }}" required
                        class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Kapasitas Peserta *</label>
                    <input type="number" name="capacity" value="{{ old('capacity', $zoomAccount->capacity) }}" required
                        class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Masa Berlaku Expired</label>
                    <input type="date" name="expired_date" value="{{ old('expired_date', $zoomAccount->expired_date ? $zoomAccount->expired_date->format('Y-m-d') : '') }}"
                        class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Status Akun *</label>
                    <select name="status" required class="w-full p-2.5 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                        <option value="active" {{ old('status', $zoomAccount->status) === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ old('status', $zoomAccount->status) === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                        <option value="maintenance" {{ old('status', $zoomAccount->status) === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    </select>
                </div>
            </div>

            <div class="p-4 bg-slate-50 rounded-xl border border-slate-200/80 space-y-3 mt-4">
                <div class="font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-key text-blue-600"></i> Kredensial Server-to-Server OAuth Zoom
                </div>
                <p class="text-[11px] text-slate-500">Kosongkan Client Secret jika tidak ingin mengubahnya.</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Account ID</label>
                        <input type="text" name="account_id" value="{{ old('account_id', $zoomAccount->account_id) }}"
                            class="w-full p-2 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1">Client ID</label>
                        <input type="text" name="client_id" value="{{ old('client_id', $zoomAccount->client_id) }}"
                            class="w-full p-2 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Client Secret (Kosongkan bila tidak diubah)</label>
                    <input type="password" name="client_secret"
                        class="w-full p-2 bg-white border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500" placeholder="••••••••••••">
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('zoom-accounts.index') }}" class="px-5 py-2.5 bg-slate-100 text-slate-700 font-semibold rounded-xl hover:bg-slate-200 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-lg shadow-blue-600/30 transition">
                    Perbarui Akun
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
