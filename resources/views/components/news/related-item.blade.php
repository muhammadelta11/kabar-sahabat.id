@props([
    'post',
])

@php
    $category = $post->categories->first();
@endphp

<a href="{{ route('news.show', $post->slug) }}" {{ $attributes->class(['group flex gap-3']) }}>
    <div class="w-24 h-16 bg-neutral-200 flex-shrink-0 overflow-hidden relative rounded-xl">
        <x-post-image :src="$post->image_url" :alt="$post->title" class="group-hover:scale-110 transition-transform duration-500" />
    </div>
    <div class="flex-1 min-w-0 space-y-1">
        <x-news.category-badge
            :name="$category?->name ?? 'Nasional'"
            :slug="$category?->slug"
            class="text-[8px] px-1.5"
        />
        <h4 class="text-xs font-bold text-ink leading-snug group-hover:text-brand transition-colors line-clamp-3 font-display">
            {{ $post->title }}
        </h4>
    </div>
</a>
