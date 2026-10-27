@extends('layouts.app')

@php
    $header = 'Edit Pengguna';
@endphp

@section('content')
<div class="max-w-2xl mx-auto space-y-4">
    <x-page-header title="Ubah Data Pengguna" description="Perbarui informasi profil atau reset kata sandi pengguna." :backUrl="route('users.index')" backText="Kembali ke Daftar Pengguna" />

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

            <form method="POST" action="{{ route('users.update', $user->id) }}" class="space-y-4 text-xs">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Nama Lengkap *</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                            class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500">
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Username *</label>
                        <input type="text" name="username" value="{{ old('username', $user->username) }}" required
                            class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500 font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Alamat Email *</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                            class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500 font-mono">
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Password Baru (Kosongkan jika tidak diubah)</label>
                        <input type="password" name="password" minlength="6"
                            class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500"
                            placeholder="••••••••">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Peran Akses (Role) *</label>
                        <select name="role" required class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500">
                            <option value="2" {{ old('role', $user->role) == '2' ? 'selected' : '' }}>Pegawai / Staf</option>
                            <option value="1" {{ old('role', $user->role) == '1' ? 'selected' : '' }}>Administrator</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Status Akun *</label>
                        <select name="is_active" required class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500">
                            <option value="1" {{ old('is_active', $user->is_active) ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ !old('is_active', $user->is_active) ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Jabatan</label>
                        <input type="text" name="jabatan" value="{{ old('jabatan', $user->jabatan) }}"
                            class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500">
                    </div>
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Departemen / Unit</label>
                        <input type="text" name="departemen" value="{{ old('departemen', $user->departemen) }}"
                            class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500">
                    </div>
                </div>

                <div>
                    <label class="block font-medium text-gray-700 mb-1">Nomor WhatsApp / HP</label>
                    <input type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}"
                        class="w-full px-3 py-1.5 bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500 font-mono">
                </div>

                <div class="pt-4 border-t border-gray-200 flex items-center justify-end gap-2">
                    <x-btn variant="white" :href="route('users.index')">Batal</x-btn>
                    <x-btn variant="primary" type="submit">Simpan Perubahan</x-btn>
                </div>
            </form>
        </div>
    </x-card>
</div>
@endsection
