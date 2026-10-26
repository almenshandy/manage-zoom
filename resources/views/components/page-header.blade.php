@props([
    'title' => '',
    'description' => '',
    'backUrl' => null,
    'backText' => 'Kembali',
])

<div class="bg-white p-4 rounded border border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
    <div>
        @if($backUrl)
            <div class="mb-1">
                <a href="{{ $backUrl }}" class="text-xs text-gray-500 hover:text-gray-900 inline-flex items-center gap-1 font-medium">
                    <i class="fa-solid fa-arrow-left text-[10px]"></i> {{ $backText }}
                </a>
            </div>
        @endif
        @if($title)
            <h3 class="text-xs font-semibold text-gray-900 uppercase tracking-wider">{{ $title }}</h3>
        @endif
        @if($description)
            <p class="text-xs text-gray-500 mt-0.5">{{ $description }}</p>
        @endif
    </div>

    @if(isset($actions))
        <div class="flex items-center gap-2">
            {{ $actions }}
        </div>
    @endif
</div>
