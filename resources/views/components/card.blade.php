@props([
    'title' => null,
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'bg-white rounded border border-gray-200 overflow-hidden']) }}>
    @if($title || isset($headerActions))
        <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between bg-white">
            <div>
                @if($title)
                    <h4 class="text-xs font-semibold text-gray-900 uppercase tracking-wider">{{ $title }}</h4>
                @endif
                @if($description)
                    <p class="text-xs text-gray-500 mt-0.5">{{ $description }}</p>
                @endif
            </div>
            @if(isset($headerActions))
                <div class="flex items-center gap-2">
                    {{ $headerActions }}
                </div>
            @endif
        </div>
    @endif

    {{ $slot }}

    @if(isset($footer))
        <div class="px-4 py-3 border-t border-gray-200 bg-gray-50">
            {{ $footer }}
        </div>
    @endif
</div>
