@props([
    'status' => 0,
    'label' => null,
])

@php
    $class = match ((int) $status) {
        1 => 'bg-blue-50 text-blue-700 border-blue-200',
        3 => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        2 => 'bg-red-50 text-red-700 border-red-200',
        default => 'bg-amber-50 text-amber-700 border-amber-200',
    };

    $defaultLabel = match ((int) $status) {
        1 => 'Disetujui (ACC)',
        3 => 'Link Aktif',
        2 => 'Ditolak',
        default => 'Menunggu ACC',
    };
@endphp

<span {{ $attributes->merge(['class' => 'inline-block px-2 py-0.5 rounded text-[10px] font-semibold border ' . $class]) }}>
    {{ $label ?? $defaultLabel }}
</span>
