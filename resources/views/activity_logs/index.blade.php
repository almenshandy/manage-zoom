@extends('layouts.app')

@php
    $header = 'Log Aktivitas & Audit Trail';
@endphp

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-base font-bold text-slate-800">Catatan Aktivitas Sistem</h3>
            <p class="text-xs text-slate-500">Mencatat otomatis riwayat pemesanan, approval, modifikasi status, dan eksekusi meeting.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200 uppercase tracking-wider text-[11px]">
                <tr>
                    <th class="px-5 py-3.5">Waktu</th>
                    <th class="px-5 py-3.5">User Pelaku</th>
                    <th class="px-5 py-3.5">Aksi</th>
                    <th class="px-5 py-3.5">Keterangan Aktivitas</th>
                    <th class="px-5 py-3.5 text-right">IP Address</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700">
                @forelse($logs as $log)
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-5 py-3.5 font-mono text-[11px] text-slate-500 whitespace-nowrap">
                            {{ $log->created_at->format('d/m/Y H:i:s') }}
                        </td>
                        <td class="px-5 py-3.5 font-semibold text-slate-800">
                            {{ $log->user->name ?? 'System' }}
                        </td>
                        <td class="px-5 py-3.5">
                            <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-100 text-slate-700">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-slate-800">
                            {{ $log->description }}
                        </td>
                        <td class="px-5 py-3.5 text-right font-mono text-slate-400 text-[11px]">
                            {{ $log->ip_address ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-slate-400">Belum ada aktivitas tercatat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($logs->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
