@extends('layouts.app')

@php
    $header = 'Log Aktivitas & Audit Trail';
@endphp

@section('content')
<div class="space-y-4">
    <x-page-header title="Catatan Aktivitas Sistem" description="Riwayat otomatis permohonan booking, persetujuan admin, dan eksekusi link meeting." />

    <x-card>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-200 uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="px-4 py-3">Waktu</th>
                        <th class="px-4 py-3">Pengguna</th>
                        <th class="px-4 py-3">Aksi</th>
                        <th class="px-4 py-3">Keterangan Aktivitas</th>
                        <th class="px-4 py-3 text-right">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-gray-700">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50/75 transition-colors">
                            <td class="px-4 py-2.5 font-mono text-gray-500 text-[11px] whitespace-nowrap">
                                {{ $log->created_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="px-4 py-2.5 font-medium text-gray-900 whitespace-nowrap">
                                {{ $log->user->name ?? 'System' }}
                            </td>
                            <td class="px-4 py-2.5 whitespace-nowrap">
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-mono border bg-gray-50 text-gray-700 border-gray-200">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="px-4 py-2.5 text-gray-800">
                                {{ $log->description }}
                            </td>
                            <td class="px-4 py-2.5 text-right font-mono text-gray-400 text-[11px] whitespace-nowrap">
                                {{ $log->ip_address ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-400">
                                <i class="fa-solid fa-clock-rotate-left text-2xl mb-2 text-gray-300 block"></i>
                                Belum ada riwayat aktivitas tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <x-slot:footer>
                {{ $logs->links() }}
            </x-slot:footer>
        @endif
    </x-card>
</div>
@endsection
