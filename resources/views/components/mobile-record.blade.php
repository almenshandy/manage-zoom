@props(['title', 'href' => null, 'badge' => null, 'badgeClass' => 'bg-gray-100 text-gray-700'])

<article {{ $attributes->merge(['class' => 'mobile-record']) }}>
    <div class="mobile-record-header">
        @isset($selection)<div class="mobile-record-selection">{{ $selection }}</div>@endisset
        <h2 class="mobile-record-title">
            @if($href)
                <a href="{{ $href }}">{{ $title }}</a>
            @else
                {{ $title }}
            @endif
        </h2>
        @if($badge)
            <span class="mobile-record-badge {{ $badgeClass }}">{{ $badge }}</span>
        @endif
    </div>
    <div class="mobile-record-body">{{ $slot }}</div>
    @isset($footer)
        <div class="mobile-record-footer">{{ $footer }}</div>
    @endisset
</article>
