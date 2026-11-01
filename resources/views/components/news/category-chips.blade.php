@props([
    'categories' => null,
])

@php
    $categories = collect($categories ?? []);
    if ($categories->isEmpty()) {
        $categories = \App\Support\NewsCache::navCategories();
    }

    $icons = [
        'book', 'chart', 'scale', 'spark', 'chat', 'chip', 'map', 'more',
    ];
@endphp

@if($categories->isNotEmpty())
<section id="jelajahi-kategori" {{ $attributes->class(['bg-white rounded-2xl border border-neutral-100 shadow-sm px-3 sm:px-4 py-3 sm:py-4 scroll-mt-28']) }}>
    <div class="flex items-center gap-3 mb-3 sm:mb-4">
        <span class="inline-flex items-center rounded-lg bg-brand px-2.5 py-1 text-[10px] font-black uppercase tracking-wider text-white">
            Jelajahi Kategori
        </span>
    </div>

    <div class="flex sm:grid sm:grid-cols-4 md:grid-cols-8 gap-3 overflow-x-auto sm:overflow-visible scrollbar-none snap-x snap-mandatory pb-1 sm:pb-0" style="scrollbar-width:none;">
        @foreach($categories->take(8) as $index => $category)
            <a
                href="{{ url('/?category='.$category->slug) }}"
                class="flex flex-col items-center gap-2 group text-center flex-shrink-0 w-[72px] sm:w-auto snap-start"
            >
                <span class="w-14 h-14 sm:w-14 sm:h-14 rounded-2xl bg-surface border border-neutral-100 flex items-center justify-center text-brand group-hover:bg-brand group-hover:text-white group-hover:border-brand active:scale-95 transition-all shadow-sm">
                    @switch($icons[$index % count($icons)])
                        @case('book')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                            @break
                        @case('chart')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            @break
                        @case('scale')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                            @break
                        @case('spark')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                            @break
                        @case('chat')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            @break
                        @case('chip')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                            @break
                        @case('map')
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            @break
                        @default
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    @endswitch
                </span>
                <span class="text-[10px] sm:text-[11px] font-semibold text-ink leading-tight line-clamp-2 group-hover:text-brand transition-colors">
                    {{ $category->name }}
                </span>
            </a>
        @endforeach
    </div>
</section>
@endif
