@extends('layouts.app')

@php
    $header = 'Master Akun Zoom';
@endphp

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-base font-bold text-slate-800">Daftar Inventaris Akun Zoom</h3>
            <p class="text-xs text-slate-500">Kelola akun Zoom, kredensial Server-to-Server OAuth, dan masa aktif langganan.</p>
        </div>
        <a href="{{ route('zoom-accounts.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow-md shadow-blue-600/20 transition">
            <i class="fa-solid fa-plus"></i> Tambah Akun Baru
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200 uppercase tracking-wider text-[11px]">
                <tr>
                    <th class="px-5 py-3.5">Nama & Identitas Akun</th>
                    <th class="px-5 py-3.5">Kapasitas</th>
                    <th class="px-5 py-3.5">Status API OAuth</th>
                    <th class="px-5 py-3.5">Masa Berlaku</th>
                    <th class="px-5 py-3.5 text-center">Status</th>
                    <th class="px-5 py-3.5 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700">
                @forelse($accounts as $acc)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-5 py-4">
                            <span class="font-bold text-slate-800 text-sm block">{{ $acc->name }}</span>
                            <span class="text-slate-400 text-xs">{{ $acc->email }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="font-semibold text-slate-800">{{ $acc->capacity }}</span>
                            <span class="text-slate-400 text-[11px]">Peserta</span>
                        </td>
                        <td class="px-5 py-4">
                            @if($acc->account_id && $acc->client_id)
                                <span class="inline-flex items-center gap-1.5 text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full text-[11px] font-semibold border border-emerald-200">
                                    <i class="fa-solid fa-circle-check text-[10px]"></i> S2S Configured
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-slate-400 bg-slate-100 px-2.5 py-1 rounded-full text-[11px] font-semibold">
                                    <i class="fa-solid fa-circle-exclamation text-[10px]"></i> Default Env
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            @if($acc->expired_date)
                                <span class="font-medium text-slate-800">{{ $acc->expired_date->format('d M Y') }}</span>
                                @if($acc->expired_date->isPast())
                                    <span class="text-red-500 font-bold text-[10px] block">Kadaluarsa</span>
                                @endif
                            @else
                                <span class="text-slate-400 italic">Tidak diset</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-center">
                            @if($acc->status === 'active')
                                <span class="px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold">Aktif</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-[10px] font-bold">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right space-x-1">
                            <a href="{{ route('zoom-accounts.edit', $acc->id) }}" class="p-2 text-slate-500 hover:text-blue-600 rounded-lg hover:bg-slate-100 transition inline-block">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <form method="POST" action="{{ route('zoom-accounts.destroy', $acc->id) }}" class="inline-block" onsubmit="return confirm('Hapus akun ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-slate-500 hover:text-red-600 rounded-lg hover:bg-slate-100 transition">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-slate-400">Belum ada akun Zoom yang ditambahkan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($accounts->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $accounts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
