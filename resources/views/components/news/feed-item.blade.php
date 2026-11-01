@props([
    'post',
])

@php
    $category = request()->filled('category')
        ? ($post->categories->firstWhere('slug', request('category')) ?? $post->categories->first())
        : $post->categories->first();
@endphp

<article {{ $attributes->class(['group']) }}>
    <a href="{{ route('news.show', $post->slug) }}" class="flex flex-row items-start gap-3">
        <div class="w-[100px] sm:w-44 h-[72px] sm:h-[110px] bg-neutral-200 flex-shrink-0 overflow-hidden relative rounded-xl">
            <x-post-image :src="$post->image_url" :alt="$post->title" class="group-hover:scale-105 transition-transform duration-500" />
        </div>
        <div class="space-y-1 sm:space-y-1.5 flex-1 min-w-0">
            <div class="flex flex-wrap items-center gap-x-1.5 gap-y-0.5 text-[10px] sm:text-[11px] text-ink-muted">
                <x-news.category-badge
                    :name="$category?->name ?? 'Nasional'"
                    :slug="$category?->slug"
                />
                <span class="font-semibold text-danger truncate max-w-[40%]">{{ $post->displayAuthorName() }}</span>
                <span>•</span>
                <time datetime="{{ $post->published_at?->toIso8601String() }}">{{ $post->published_at?->translatedFormat('d M Y, H:i') }} WIB</time>
            </div>
            <h3 class="text-[13px] sm:text-base font-bold text-ink leading-snug group-hover:text-brand transition-colors line-clamp-3 font-display">
                {{ $post->title }}
            </h3>
            <p class="text-[11px] sm:text-xs text-ink-muted leading-relaxed line-clamp-2 hidden sm:block">
                {{ Str::limit(Str::squish(html_entity_decode(strip_tags($post->excerpt ?: $post->content))), 150) }}
            </p>
        </div>
    </a>
</article>
