@props([
    'posts' => null,
])

@php
    $posts = collect($posts ?? [])->values();
    $loopPosts = $posts->isNotEmpty() ? $posts->concat($posts) : collect();
@endphp

@if($posts->isNotEmpty())
    <div {{ $attributes->class(['bg-white border-b border-neutral-100']) }}>
        <div class="site-container flex items-center h-11 gap-3">
            <span class="text-[11px] font-black uppercase tracking-widest text-danger flex-shrink-0 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 animate-pulse" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                </svg>
                Trending
            </span>
            <div class="h-4 w-px bg-neutral-200 flex-shrink-0"></div>
            <div class="trending-marquee flex-1 min-w-0" aria-label="Berita trending">
                <div class="trending-track">
                    @foreach($loopPosts as $index => $tp)
                        <a
                            href="{{ route('news.show', $tp->slug) }}"
                            class="trending-item text-[12px] font-semibold text-ink hover:text-brand transition-colors"
                            tabindex="{{ $index >= $posts->count() ? '-1' : '0' }}"
                            @if($index >= $posts->count()) aria-hidden="true" @endif
                        >
                            <span class="text-accent font-black mr-1">{{ ($index % $posts->count()) + 1 }}.</span>
                            {{ \Illuminate\Support\Str::limit($tp->title, 56) }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif
