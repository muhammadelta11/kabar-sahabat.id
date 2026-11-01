@php
    $isHome = ! request()->has('category') && ! request()->has('q');
    $isHomeFirstPage = $isHome && $posts->currentPage() === 1;
    $homePool = $isHomeFirstPage
        ? collect($latestPosts)->concat($posts->getCollection())->unique('id')->values()
        : collect();
    $heroSlides = $homePool->take(5)->values();
    $afterHero = $homePool->slice(5)->values();
    $stripPosts = $afterHero->take(8)->values();
    $latestGrid = $afterHero->slice(8)->take(8)->values();
    $usedIds = $heroSlides->pluck('id')
        ->merge($stripPosts->pluck('id'))
        ->merge($latestGrid->pluck('id'));
    // Page 1: sisa pool setelah hero/strip/grid. Page 2+: seluruh koleksi halaman.
    $feedPosts = $isHome
        ? ($isHomeFirstPage
            ? $posts->getCollection()->reject(fn ($p) => $usedIds->contains($p->id))->values()
            : $posts->getCollection()->values())
        : collect();
@endphp

<main class="w-full min-w-0 {{ $isHome ? 'pb-6 sm:pb-8' : 'site-container py-4 sm:py-5' }}">
    @if($isHome)
        <div class="space-y-3 sm:space-y-3.5">
            @if($isHomeFirstPage)
            {{-- Hero dulu agar fold pertama padat (bukan strip kosong di atas) --}}
            <section class="site-container pt-2.5 sm:pt-3">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-3 sm:gap-3.5 lg:items-stretch">
                    <div class="lg:col-span-8 min-w-0 flex">
                        <x-news.hero-slider :posts="$heroSlides" class="w-full lg:flex-1" />
                    </div>
                    <div class="lg:col-span-4 min-w-0 flex">
                        <x-news.popular-list :posts="$popularPosts" class="w-full lg:flex-1" />
                    </div>
                </div>
            </section>

            @if($stripPosts->isNotEmpty())
                <section class="site-container relative">
                    <div
                        class="flex gap-2.5 overflow-x-auto scrollbar-none snap-x snap-mandatory pb-0.5"
                        id="berita-strip-slider"
                        style="scrollbar-width:none;"
                    >
                        @foreach($stripPosts as $lp)
                            <x-news.card-sm :post="$lp" />
                        @endforeach
                    </div>
                    <button
                        type="button"
                        onclick="document.getElementById('berita-strip-slider').scrollBy({left:360,behavior:'smooth'})"
                        class="absolute right-4 top-[38%] -translate-y-1/2 w-8 h-8 rounded-full bg-white border border-neutral-200 shadow flex items-center justify-center text-brand hover:bg-brand hover:text-white z-10 hidden lg:flex"
                        aria-label="Geser berita"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </section>
            @endif

            <div class="site-container space-y-3 sm:space-y-3.5">
                <x-news.category-chips :categories="$navCategories" />

                @if($latestGrid->isNotEmpty())
                    <section class="grid grid-cols-1 lg:grid-cols-12 gap-3 sm:gap-3.5 items-start">
                        <div class="lg:col-span-8 min-w-0">
                            <x-news.section-heading title="Berita Terbaru" :href="url('/')" class="mb-2.5" />
                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
                                @foreach($latestGrid as $gp)
                                    <x-news.card-md :post="$gp" badge-on-image />
                                @endforeach
                            </div>
                        </div>
                        <aside class="lg:col-span-4 min-w-0 space-y-3">
                            <x-news.widget-newsletter />
                            <x-news.widget-follow />
                            <x-news.widget-epaper />
                            @if(isset($ads['sidebar_mrec']))
                                <x-news.ad-slot :ad="$ads['sidebar_mrec']" class="lg:hidden" />
                            @endif
                        </aside>
                    </section>
                @endif

                @if(isset($ads['in_feed_leaderboard']) || isset($ads['bottom_billboard']))
                    <x-news.ad-slot
                        :ad="$ads['in_feed_leaderboard'] ?? $ads['bottom_billboard'] ?? null"
                        height="h-[90px]"
                        class="max-w-[970px] mx-auto"
                    />
                @endif

                @forelse($homeSections as $homeSection)
                    <section class="bg-white rounded-xl border border-neutral-100 shadow-sm p-3 sm:p-4">
                        <x-news.section-heading
                            :title="$homeSection->name"
                            :href="url('/?category='.$homeSection->slug)"
                            action-label="Selengkapnya"
                            class="mb-2.5"
                        />
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
                            @foreach($homeSection->homepagePosts as $hp)
                                <x-news.card-md :post="$hp" />
                            @endforeach
                        </div>
                    </section>
                @empty
                @endforelse
            </div>
            @endif {{-- isHomeFirstPage --}}

            <div class="site-container space-y-3 sm:space-y-3.5 {{ $isHomeFirstPage ? '' : 'pt-2.5 sm:pt-3' }}">
                @if($feedPosts->isNotEmpty())
                    <section class="bg-white rounded-xl border border-neutral-100 shadow-sm overflow-hidden">
                        <div class="px-4 py-3 border-b border-neutral-100">
                            <x-news.section-heading
                                :title="$isHomeFirstPage ? 'Berita Lainnya' : 'Berita Lainnya · Halaman '.$posts->currentPage()"
                                class="mb-0"
                            />
                        </div>
                        <div class="divide-y divide-neutral-100">
                            @foreach($feedPosts as $fp)
                                <div class="px-4 py-3.5 active:bg-surface/80">
                                    <x-news.feed-item :post="$fp" />
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                @if ($posts->hasPages())
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-1 text-xs">
                        <span class="text-ink-muted font-medium text-center sm:text-left">Halaman {{ $posts->currentPage() }} dari {{ $posts->lastPage() }}</span>
                        <div class="flex items-center justify-center gap-1.5">
                            @if ($posts->onFirstPage())
                                <button disabled class="px-4 py-2.5 border border-neutral-200 font-bold text-neutral-300 cursor-not-allowed rounded-xl">←</button>
                            @else
                                <a href="{{ $posts->previousPageUrl() }}" class="px-4 py-2.5 border border-neutral-200 font-bold text-ink hover:bg-brand hover:text-white hover:border-brand transition-colors rounded-xl">←</a>
                            @endif

                            @foreach ($posts->getUrlRange(max(1, $posts->currentPage() - 1), min($posts->lastPage(), $posts->currentPage() + 1)) as $page => $url)
                                @if ($page == $posts->currentPage())
                                    <span class="px-4 py-2.5 bg-brand text-white font-bold rounded-xl">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="px-4 py-2.5 border border-neutral-200 font-bold text-ink hover:bg-brand hover:text-white hover:border-brand transition-colors rounded-xl">{{ $page }}</a>
                                @endif
                            @endforeach

                            @if ($posts->hasMorePages())
                                <a href="{{ $posts->nextPageUrl() }}" class="px-4 py-2.5 border border-neutral-200 font-bold text-ink hover:bg-brand hover:text-white hover:border-brand transition-colors rounded-xl">→</a>
                            @else
                                <button disabled class="px-4 py-2.5 border border-neutral-200 font-bold text-neutral-300 cursor-not-allowed rounded-xl">→</button>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <div class="lg:col-span-8 min-w-0">
                <div class="bg-white rounded-2xl border border-neutral-100 shadow-sm overflow-hidden">
                    <div class="px-4 py-3 border-b border-neutral-100">
                        <x-news.section-heading
                            :title="$isSearch ? 'Hasil pencarian: '.request('q') : ('Kanal: '.($activeCategory?->name ?? request('category')))"
                            class="mb-0"
                        />
                    </div>
                    <div class="divide-y divide-neutral-100">
                        @forelse($posts as $index => $p)
                            <div class="px-4 py-4 active:bg-surface/80">
                                <x-news.feed-item :post="$p" />
                            </div>
                            @if($index === 4)
                                <div class="px-4 py-3">
                                    <x-news.ad-slot :ad="$ads['in_feed_leaderboard'] ?? null" height="h-[90px]" />
                                </div>
                            @endif
                        @empty
                            <p class="px-4 py-10 text-sm text-ink-muted text-center">Tidak ada berita ditemukan.</p>
                        @endforelse
                    </div>

                    @if ($posts->hasPages())
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 py-3 border-t border-neutral-100 text-xs">
                            <span class="text-ink-muted font-medium text-center sm:text-left">Halaman {{ $posts->currentPage() }} dari {{ $posts->lastPage() }}</span>
                            <div class="flex items-center justify-center gap-1.5">
                                @if ($posts->onFirstPage())
                                    <button disabled class="px-3 py-1.5 border border-neutral-200 font-bold text-neutral-300 cursor-not-allowed rounded-lg">←</button>
                                @else
                                    <a href="{{ $posts->previousPageUrl() }}" class="px-3 py-1.5 border border-neutral-200 font-bold text-ink hover:bg-brand hover:text-white hover:border-brand transition-colors rounded-lg">←</a>
                                @endif

                                @foreach ($posts->getUrlRange(max(1, $posts->currentPage() - 1), min($posts->lastPage(), $posts->currentPage() + 1)) as $page => $url)
                                    @if ($page == $posts->currentPage())
                                        <span class="px-3 py-1.5 bg-brand text-white font-bold rounded-lg">{{ $page }}</span>
                                    @else
                                        <a href="{{ $url }}" class="px-3 py-1.5 border border-neutral-200 font-bold text-ink hover:bg-brand hover:text-white hover:border-brand transition-colors rounded-lg">{{ $page }}</a>
                                    @endif
                                @endforeach

                                @if ($posts->hasMorePages())
                                    <a href="{{ $posts->nextPageUrl() }}" class="px-3 py-1.5 border border-neutral-200 font-bold text-ink hover:bg-brand hover:text-white hover:border-brand transition-colors rounded-lg">→</a>
                                @else
                                    <button disabled class="px-3 py-1.5 border border-neutral-200 font-bold text-neutral-300 cursor-not-allowed rounded-lg">→</button>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <aside class="lg:col-span-4 min-w-0">
                <x-news.sidebar
                    :popular-posts="$popularPosts"
                    :ads="$ads"
                />
            </aside>
        </div>
    @endif
</main>
