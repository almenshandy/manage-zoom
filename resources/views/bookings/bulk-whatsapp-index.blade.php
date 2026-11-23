@extends('layouts.app')

@php($header = 'Antrean Bulk WhatsApp')

@section('content')
<div class="max-w-6xl mx-auto space-y-5">
    <!-- Queue Worker Status / Warning Banner -->
    <div id="queueWorkerAlert" class="hidden p-4 rounded-sm border bg-amber-50 border-amber-300 text-amber-900 text-xs">
        <div class="flex items-start gap-3">
            <i class="fa-solid fa-triangle-exclamation text-amber-600 text-base mt-0.5"></i>
            <div class="flex-1 space-y-1">
                <div class="font-semibold text-sm">Peringatan: Antrean Bulk Belum Berjalan (Queue Worker Mati)</div>
                <p class="text-amber-800">
                    Ada pesan yang masih berstatus antrean (<code class="bg-amber-150 px-1 py-0.5 rounded">queued</code>), namun Queue Worker background belum aktif untuk memprosesnya.
                </p>
                <div class="pt-1.5 flex flex-wrap items-center gap-2">
                    <span class="text-amber-800 font-medium">Jalankan di terminal server:</span>
                    <code class="bg-white border border-amber-300 px-2 py-1 rounded text-amber-950 font-mono select-all">php artisan queue:work --queue=whatsapp-bulk</code>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-gray-600">{{ $bulkBatches->total() }} antrean pengiriman Anda. Buka antrean untuk memantau progres dan rincian pesan.</p>
        <a href="{{ route('bookings.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-700 text-white text-sm font-medium"><i class="fa-solid fa-plus" aria-hidden="true"></i> Buat pengiriman bulk</a>
    </div>
    @include('bookings.bulk-whatsapp')
    @if($bulkBatches->hasPages())
        <div class="bg-white border border-gray-200 p-4">{{ $bulkBatches->links() }}</div>
    @endif
</div>
@endsection
