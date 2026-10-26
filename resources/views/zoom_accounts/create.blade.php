@extends('layouts.app')

@php
    $header = 'Tambah Akun Zoom';
@endphp

@section('content')
<div class="max-w-2xl mx-auto space-y-4">
    <x-page-header title="Form Pendaftaran Akun Zoom" description="Masukkan identitas lisensi dan kredensial OAuth Server-to-Server." :backUrl="route('zoom-accounts.index')" backText="Kembali ke Master Akun" />

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

            <form method="POST" action="{{ route('zoom-accounts.store') }}" class="space-y-4 text-xs">
                @csrf

                <div>
                    <label class="block font-medium text-gray-700 mb-1">Nama Ruang / Label Akun *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500"
                        placeholder="Contoh: Zoom Pro Ruang A (300 Peserta)">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Email Akun Zoom *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500 font-mono"
                            placeholder="nama@institusi.ac.id">
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Kapasitas Maksimal (Peserta) *</label>
                        <input type="number" name="capacity" value="{{ old('capacity', 300) }}" required min="1"
                            class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Masa Berlaku Lisensi</label>
                        <input type="date" name="expired_date" value="{{ old('expired_date') }}"
                            class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500">
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Status Akun *</label>
                        <select name="status" required class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500">
                            <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                            <option value="maintenance" {{ old('status') === 'maintenance' ? 'selected' : '' }}>Perbaikan (Maintenance)</option>
                        </select>
                    </div>
                </div>

                <!-- API Credentials Accordion / Fieldset -->
                <div class="p-4 bg-gray-50 border border-gray-200 rounded space-y-3">
                    <p class="font-semibold text-gray-900 text-[11px] uppercase tracking-wider">Kredensial Zoom Server-to-Server OAuth (Opsional)</p>
                    <p class="text-[11px] text-gray-500">Kosongkan jika akun ini menggunakan kredensial global dari file .env.</p>

                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Zoom Account ID</label>
                        <input type="text" name="account_id" value="{{ old('account_id') }}"
                            class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded font-mono text-xs">
                    </div>

                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Zoom Client ID</label>
                        <input type="text" name="client_id" value="{{ old('client_id') }}"
                            class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded font-mono text-xs">
                    </div>

                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Zoom Client Secret</label>
                        <input type="password" name="client_secret"
                            class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded font-mono text-xs">
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-200 flex items-center justify-end gap-2">
                    <x-btn variant="white" :href="route('zoom-accounts.index')">Batal</x-btn>
                    <x-btn variant="primary" type="submit">Simpan Akun</x-btn>
                </div>
            </form>
        </div>
    </x-card>
</div>
@endsection
