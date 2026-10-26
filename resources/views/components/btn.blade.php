@props([
    'variant' => 'primary', // 'primary', 'secondary', 'danger', 'white'
    'type' => 'button',
    'href' => null,
])

@php
    $baseClass = 'inline-flex items-center justify-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded transition-colors';
    
    $variantClass = match ($variant) {
        'secondary', 'white' => 'bg-white hover:bg-gray-50 text-gray-700 border border-gray-300',
        'danger' => 'bg-red-600 hover:bg-red-700 text-white',
        default => 'bg-gray-900 hover:bg-black text-white',
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
