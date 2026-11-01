@props([
    'title',
    'href' => null,
    'actionLabel' => 'Lihat Semua',
])

<div {{ $attributes->class(['flex items-center justify-between gap-3']) }}>
    <h2 class="text-sm sm:text-base font-black tracking-tight text-ink font-display flex items-center gap-2 min-w-0">
        <span class="w-1 h-5 rounded-full bg-accent flex-shrink-0"></span>
        <span class="truncate">{{ $title }}</span>
    </h2>
    @if($href)
        <a href="{{ $href }}" class="text-[11px] font-bold text-brand hover:text-accent transition-colors whitespace-nowrap">
            {{ $actionLabel }} →
        </a>
    @endif
</div>
