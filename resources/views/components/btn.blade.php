@props([
    'variant' => 'primary', // 'primary', 'secondary', 'danger', 'white'
    'type' => 'button',
    'href' => null,
])

@php
    $baseClass = 'inline-flex items-center justify-center gap-1.5 text-xs font-medium px-3.5 py-2 min-h-9 rounded-sm border transition-colors disabled:opacity-50 disabled:cursor-not-allowed';
    
    $variantClass = match ($variant) {
        'secondary', 'white' => 'bg-white hover:bg-gray-50 text-gray-700 border-gray-300',
        'danger' => 'bg-red-700 hover:bg-red-800 border-red-700 text-white',
        default => 'bg-brand-900 hover:bg-brand-950 border-brand-900 text-white',
    };

    $classes = $baseClass . ' ' . $variantClass;
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
