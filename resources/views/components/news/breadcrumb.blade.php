@props([
    'items' => [],
])

@php
    $items = collect($items)->filter(fn ($item) => filled($item['label'] ?? null));
@endphp

@if($items->isNotEmpty())
    <nav {{ $attributes->class(['text-[11px] font-semibold text-ink-muted flex flex-wrap items-center gap-x-2 gap-y-1']) }} aria-label="Breadcrumb">
        @foreach($items as $item)
            @if(! $loop->first)
                <span class="text-neutral-300" aria-hidden="true">&rsaquo;</span>
            @endif
            @if(! empty($item['href']) && ! $loop->last)
                <a href="{{ $item['href'] }}" class="hover:text-brand transition-colors uppercase tracking-wide">{{ $item['label'] }}</a>
            @else
                <span class="{{ $loop->last ? 'text-ink truncate max-w-[60%]' : 'uppercase tracking-wide' }}">{{ $item['label'] }}</span>
            @endif
        @endforeach
    </nav>
@endif
