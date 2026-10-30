@props([
    'src' => null,
    'alt' => '',
    'priority' => false,
])

@php
    $url = media_url($src);
@endphp

@if($url)
    <img
        src="{{ $url }}"
        alt="{{ $alt }}"
        @if($priority)
            fetchpriority="high"
            loading="eager"
            decoding="async"
        @else
            loading="lazy"
            decoding="async"
        @endif
        {{ $attributes->class(['w-full h-full object-cover']) }}
    >
@else
    <div {{ $attributes->class(['w-full h-full bg-neutral-300 flex items-center justify-center text-neutral-500 text-xs font-bold']) }}>
        KABAR
    </div>
@endif
