@extends('layouts.app')

@php($header = 'Antrean Bulk WhatsApp')

@section('content')
<div class="max-w-6xl mx-auto space-y-5">
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
