@props([
    'categories' => null,
])

@php
    $categories = collect($categories ?? []);
    if ($categories->isEmpty()) {
        $categories = \App\Support\NewsCache::navCategories();
    }
    $settings = $siteSettings ?? \App\Support\NewsCache::siteSettings();
    $isHome = ! request()->has('category') && ! request()->has('q');
@endphp

<nav {{ $attributes->class(['relative z-50']) }} id="main-nav" aria-label="Navigasi utama">
    {{--
      Mobile: full-bleed (tanpa padding kiri/kanan).
      Desktop: sejajar site-container (termasuk saat scroll/compact).
    --}}
    <div
        id="main-nav-container"
        class="w-full max-w-none px-0 pt-0 pb-0 md:mx-auto md:max-w-[var(--site-max)] md:px-6 lg:px-8 md:pt-2"
    >
        <div id="main-nav-bar" class="bg-brand rounded-none md:rounded-t-2xl shadow-md border-b-[3px] border-accent">
            <div class="flex items-center justify-between h-14 md:h-16 px-2 sm:px-2.5 md:px-1.5">
                <a
                    href="{{ route('news.index') }}"
                    class="flex-shrink-0 items-center gap-1.5 mr-3 hidden pl-0.5"
                    id="nav-sticky-logo"
                >
                    <img
                        src="{{ asset('images/brand/logo-dark.png') }}"
                        class="h-12 md:h-14 w-auto max-w-[240px] sm:max-w-[280px] md:max-w-[340px] object-contain object-left"
                        alt="Kabar Sahabat"
                    >
                </a>

                <div class="relative flex-1 min-w-0 h-full">
                    <div class="flex items-center overflow-x-auto h-full scrollbar-none pr-8" style="scrollbar-width:none;">
                        <a
                            href="{{ route('news.index') }}"
                            class="h-full flex items-center gap-1.5 px-3 sm:px-4 text-[11px] font-bold uppercase tracking-wider whitespace-nowrap transition-colors {{ $isHome ? 'text-accent' : 'text-white/90 hover:text-white hover:bg-brand-dark/60' }}"
                        >
                            <svg class="w-3.5 h-3.5 {{ $isHome ? 'text-accent' : 'text-white/80' }}" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7A1 1 0 003 11h1v6a1 1 0 001 1h4v-4h2v4h4a1 1 0 001-1v-6h1a1 1 0 00.707-1.707l-7-7z"/>
                            </svg>
                            <span class="hidden xs:inline sm:inline">Beranda</span>
                        </a>

                        @foreach($categories as $navCategory)
                            <a
                                href="{{ url('/?category='.$navCategory->slug) }}"
                                class="h-full flex items-center px-3 sm:px-4 text-[11px] font-bold uppercase tracking-wider whitespace-nowrap transition-colors {{ request('category') === $navCategory->slug ? 'text-accent' : 'text-white/90 hover:text-white hover:bg-brand-dark/60' }}"
                            >
                                {{ $navCategory->name }}
                            </a>
                        @endforeach
                    </div>
                    <div class="pointer-events-none absolute inset-y-0 right-0 w-10" style="background: linear-gradient(to left, var(--color-brand) 20%, transparent)"></div>
                </div>

                <div class="flex-shrink-0 pl-1">
                    <livewire:search-navbar />
                </div>
            </div>
        </div>
    </div>
</nav>
