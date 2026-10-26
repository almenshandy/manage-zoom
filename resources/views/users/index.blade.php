@extends('layouts.app')

@php
    $header = 'Manajemen Pengguna';
@endphp

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <x-page-header title="Daftar Pengguna Sistem" description="Kelola akun pengguna, peran hak akses (Admin / Pegawai), dan unit departemen.">
        <x-slot:actions>
            <x-btn variant="primary" :href="route('users.create')">
                <i class="fa-solid fa-user-plus text-[10px]"></i> Tambah Pengguna
            </x-btn>
        </x-slot:actions>
    </x-page-header>

    <!-- Filter & Search (Flat) -->
    <div class="bg-white p-3 rounded border border-gray-200">
        <form method="GET" action="{{ route('users.index') }}" class="flex flex-wrap items-center gap-2">
            <div class="relative flex-1 min-w-[200px]">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, atau departemen..."
                    class="w-full pl-8 pr-3 py-1.5 text-xs bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500">
            </div>

            <select name="role" onchange="this.form.submit()" class="py-1.5 px-3 text-xs bg-white border border-gray-300 rounded focus:outline-none focus:border-gray-500">
                <option value="">Semua Peran</option>
                <option value="1" {{ request('role') === '1' ? 'selected' : '' }}>Administrator</option>
                <option value="2" {{ request('role') === '2' ? 'selected' : '' }}>Pegawai / Staf</option>
            </select>

            <button type="submit" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-800 text-xs font-semibold rounded border border-gray-300 transition">
                Filter
            </button>
            @if(request()->hasAny(['search', 'role']))
                <a href="{{ route('users.index') }}" class="px-2 py-1.5 text-xs text-gray-500 hover:text-gray-800">Reset</a>
            @endif
        </form>
    </div>

    <!-- Table Users (Flat) -->
    <x-card>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-200 uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="px-4 py-3">Nama & Username</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Jabatan & Departemen</th>
                        <th class="px-4 py-3">No. HP</th>
                        <th class="px-4 py-3 text-center">Peran (Role)</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-700">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50/75 transition-colors">
                            <td class="px-4 py-3">
                                <span class="font-semibold text-gray-900 block">{{ $user->name }}</span>
                                <span class="text-gray-400 text-[11px] font-mono">@<!-- -->{{ $user->username }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap font-mono text-gray-600">
                                {{ $user->email }}
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-gray-800 font-medium block">{{ $user->jabatan ?: '-' }}</span>
                                <span class="text-gray-500 text-[11px]">{{ $user->departemen ?: 'Umum' }}</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-gray-600 font-mono">
                                {{ $user->no_hp ?: '-' }}
                            </td>
                            <td class="px-4 py-3 text-center whitespace-nowrap">
                                <form method="POST" action="{{ route('users.update-role', $user->id) }}" class="inline">
                                    @csrf
                                    <select name="role" onchange="this.form.submit()" class="text-[10px] font-semibold px-2 py-0.5 rounded border focus:ring-0 cursor-pointer
                                        {{ $user->role == 1 ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-gray-50 text-gray-700 border-gray-300' }}">
                                        <option value="1" {{ $user->role == 1 ? 'selected' : '' }}>Administrator</option>
                                        <option value="2" {{ $user->role == 2 ? 'selected' : '' }}>Pegawai / Staf</option>
                                    </select>
                                </form>
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('users.edit', $user->id) }}" class="p-1.5 text-gray-500 hover:text-gray-900 hover:bg-gray-100 rounded border border-transparent hover:border-gray-200 transition" title="Edit Pengguna">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </a>
                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('users.destroy', $user->id) }}" class="inline" onsubmit="return confirm('Hapus pengguna {{ $user->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded border border-transparent hover:border-red-200 transition" title="Hapus">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                                <i class="fa-solid fa-users text-2xl mb-2 text-gray-300 block"></i>
                                Tidak ada data pengguna ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <x-slot:footer>
                {{ $users->links() }}
            </x-slot:footer>
        @endif
    </x-card>
</div>
@endsection
