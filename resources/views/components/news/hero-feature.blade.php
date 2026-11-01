@props([
    'post',
])

@php
    $category = $post->categories->first();
    $excerpt = Str::limit(Str::squish(html_entity_decode(strip_tags($post->excerpt ?: $post->content))), 180);
@endphp

<article {{ $attributes->class(['group relative overflow-hidden rounded-2xl bg-brand-deep shadow-card min-h-[300px] sm:min-h-[360px] h-full']) }}>
    <a href="{{ route('news.show', $post->slug) }}" class="block absolute inset-0">
        <div class="absolute inset-0">
            <x-post-image
                :src="$post->image_url"
                :alt="$post->title"
                :priority="true"
                class="opacity-90 group-hover:scale-105 transition-transform duration-700"
            />
            <div class="absolute inset-0" style="background: linear-gradient(to top, var(--color-brand-deep) 12%, rgb(0 26 77 / 0.5) 55%, transparent)"></div>
        </div>

        <div class="relative z-10 flex h-full min-h-[300px] sm:min-h-[360px] flex-col justify-end p-4 sm:p-7 gap-2.5 sm:gap-3">
            <x-news.category-badge
                :name="$category?->name ?? 'Nasional'"
                :slug="$category?->slug"
                class="w-fit shadow"
            />
            <h1 class="text-xl sm:text-3xl lg:text-[2rem] font-black text-white leading-snug font-display max-w-3xl">
                {{ $post->title }}
            </h1>
            @if($excerpt)
                <p class="text-sm text-white/80 leading-relaxed line-clamp-2 max-w-2xl hidden sm:block">{{ $excerpt }}</p>
            @endif
            <div class="flex flex-wrap items-center gap-3 pt-1">
                <time class="text-[11px] text-white/70" datetime="{{ $post->published_at?->toIso8601String() }}">
                    {{ $post->published_at?->translatedFormat('l, d F Y | H:i') }} WIB
                </time>
                <span class="inline-flex items-center gap-1.5 rounded-full bg-accent px-3.5 py-1.5 text-[11px] font-bold text-white group-hover:bg-accent-soft group-hover:text-ink transition-colors">
                    Baca Selengkapnya
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </span>
            </div>
        </div>
    </a>
</article>
