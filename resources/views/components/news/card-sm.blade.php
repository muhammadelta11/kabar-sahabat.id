@props([
    'post',
])

@php
    $category = $post->categories->first();
@endphp

<article {{ $attributes->class(['group flex-shrink-0 w-[118px] sm:w-[132px] snap-start']) }}>
    <a href="{{ route('news.show', $post->slug) }}" class="block space-y-1.5">
        <div class="aspect-[4/3] overflow-hidden rounded-xl bg-neutral-200 shadow-sm">
            <x-post-image :src="$post->image_url" :alt="$post->title" class="group-hover:scale-105 transition-transform duration-500" />
        </div>
        <div class="space-y-0.5 px-0.5">
            <span class="block text-[9px] font-bold uppercase tracking-wide text-accent truncate">
                {{ $category?->name ?? 'Nasional' }}
            </span>
            <h3 class="text-[11px] sm:text-[12px] font-bold text-ink leading-snug line-clamp-2 group-hover:text-brand transition-colors font-display">
                {{ $post->title }}
            </h3>
            <time class="block text-[10px] text-ink-muted" datetime="{{ $post->published_at?->toIso8601String() }}">
                {{ $post->published_at?->translatedFormat('d M Y') }}
            </time>
        </div>
    </a>
</article>
