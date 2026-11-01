@props([
    'popularPosts' => null,
    'latestPosts' => null,
    'ads' => null,
    'excludePostId' => null,
    'showPopular' => true,
    'showLatest' => true,
    'showCategories' => true,
    'showNewsletter' => true,
    'showFollow' => true,
    'showEpaper' => true,
])

@php
    $popularPosts = collect($popularPosts ?? \App\Support\NewsCache::popularPosts(5));
    $latestPosts = collect($latestPosts ?? \App\Support\NewsCache::latestPosts(10));
    $ads = $ads instanceof \Illuminate\Support\Collection ? $ads : collect($ads ?? \App\Support\NewsCache::activeAds());
    $categories = \App\Support\NewsCache::navCategories()->take(8);
@endphp

<div {{ $attributes->class(['space-y-4 lg:sticky lg:top-28 lg:self-start']) }}>
    @if($showPopular && $popularPosts->isNotEmpty())
        <x-news.popular-list :posts="$popularPosts" />
    @endif

    <x-news.ad-slot :ad="$ads->get('sidebar_mrec')" />

    @if($showLatest)
        <x-news.latest-list :posts="$latestPosts" :exclude-id="$excludePostId" :limit="5" />
    @endif

    @if($showCategories && $categories->isNotEmpty())
        <section class="bg-white rounded-2xl border border-neutral-100 shadow-sm p-4">
            <h2 class="text-sm font-black text-brand font-display flex items-center gap-2 mb-3">
                <span class="w-1 h-4 rounded-full bg-accent"></span>
                Kanal
            </h2>
            <div class="flex flex-wrap gap-2">
                @foreach($categories as $cat)
                    <a
                        href="{{ url('/?category='.$cat->slug) }}"
                        class="inline-flex items-center rounded-full border border-neutral-200 bg-surface px-2.5 py-1 text-[11px] font-bold text-ink hover:border-brand hover:text-brand transition-colors"
                    >
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    @if($showNewsletter)
        <x-news.widget-newsletter />
    @endif

    @if($showFollow)
        <x-news.widget-follow />
    @endif

    @if($showEpaper)
        <x-news.widget-epaper />
    @endif

    <x-news.ad-slot :ad="$ads->get('sidebar_sponsor_mrec')" />
    <x-news.ad-slot
        :ad="$ads->get('sidebar_skyscraper')"
        height="h-[250px] sm:h-[400px] lg:h-[600px]"
        class="max-w-[300px] mx-auto"
    />
</div>
