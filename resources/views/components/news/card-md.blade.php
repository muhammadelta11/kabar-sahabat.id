@props([
    'post',
    'badgeOnImage' => false,
])

@php
    $category = $post->categories->first();
@endphp

<article {{ $attributes->class(['group']) }}>
    <a href="{{ route('news.show', $post->slug) }}" class="block space-y-2">
        <div class="relative aspect-[16/10] overflow-hidden rounded-xl bg-neutral-200 shadow-sm">
            <x-post-image :src="$post->image_url" :alt="$post->title" class="group-hover:scale-105 transition-transform duration-500" />
            @if($badgeOnImage)
                <div class="absolute top-2 left-2 z-10">
                    <x-news.category-badge
                        :name="$category?->name ?? 'Nasional'"
                        :slug="$category?->slug"
                        class="shadow-sm"
                    />
                </div>
            @endif
        </div>
        <div class="space-y-1">
            @unless($badgeOnImage)
                <x-news.category-badge
                    :name="$category?->name ?? 'Nasional'"
                    :slug="$category?->slug"
                />
            @endunless
            <h3 class="text-sm font-bold text-ink leading-snug line-clamp-2 group-hover:text-brand transition-colors font-display">
                {{ $post->title }}
            </h3>
            <time class="block text-[11px] text-ink-muted" datetime="{{ $post->published_at?->toIso8601String() }}">
                {{ $post->published_at?->translatedFormat('d M Y, H:i') }} WIB
            </time>
        </div>
    </a>
</article>
