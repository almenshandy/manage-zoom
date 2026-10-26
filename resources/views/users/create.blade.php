@extends('layouts.app')

@php
    $header = 'Tambah Pengguna';
@endphp

@section('content')
<div class="max-w-2xl mx-auto space-y-4">
    <x-page-header title="Form Tambah Pengguna" description="Buat akun pegawai atau administrator baru untuk sistem portal." :backUrl="route('users.index')" backText="Kembali ke Daftar Pengguna" />

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

            <form method="POST" action="{{ route('users.store') }}" class="space-y-4 text-xs">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Nama Lengkap *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500"
                            placeholder="Contoh: Budi Santoso">
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Username *</label>
                        <input type="text" name="username" value="{{ old('username') }}" required
                            class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500 font-mono"
                            placeholder="budisantoso">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Alamat Email *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500 font-mono"
                            placeholder="budi@institusi.ac.id">
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Password *</label>
                        <input type="password" name="password" required minlength="6"
                            class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500"
                            placeholder="Minimal 6 karakter">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Peran Akses (Role) *</label>
                        <select name="role" required class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500">
                            <option value="2" {{ old('role') == '2' ? 'selected' : '' }}>Pegawai / Staf</option>
                            <option value="1" {{ old('role') == '1' ? 'selected' : '' }}>Administrator</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Jabatan</label>
                        <input type="text" name="jabatan" value="{{ old('jabatan') }}"
                            class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500"
                            placeholder="Contoh: Pranata Komputer">
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Departemen / Unit</label>
                        <input type="text" name="departemen" value="{{ old('departemen') }}"
                            class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500"
                            placeholder="Biro Humas">
                    </div>
                </div>

                <div>
                    <label class="block font-medium text-gray-700 mb-1">Nomor WhatsApp / HP</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp') }}"
                        class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500 font-mono"
                        placeholder="08123456789">
                </div>

                <div class="pt-4 border-t border-gray-200 flex items-center justify-end gap-2">
                    <x-btn variant="white" :href="route('users.index')">Batal</x-btn>
                    <x-btn variant="primary" type="submit">Simpan Pengguna</x-btn>
                </div>
            </form>
        </div>
    </x-card>
</div>
@endsection
