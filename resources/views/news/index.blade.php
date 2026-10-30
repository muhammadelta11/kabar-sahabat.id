@extends('layouts.app')

@section('title', request()->has('category') ? 'Kanal ' . ($navCategories->firstWhere('slug', request('category'))?->name ?? request('category')) . ' - Kabar Sahabat' : (request()->has('q') ? 'Pencarian: ' . request('q') . ' - Kabar Sahabat' : 'Kabar Sahabat - Portal Berita'))

@section('canonical')
    @php
        $canonicalUrl = request()->has('category')
            ? url('/?category=' . request('category'))
            : url('/');
        if ($posts->currentPage() > 1) {
            $canonicalUrl .= (str_contains($canonicalUrl, '?') ? '&' : '?') . 'page=' . $posts->currentPage();
        }
    @endphp
    <link rel="canonical" href="{{ $canonicalUrl }}">
    @if($posts->currentPage() > 1)
        <link rel="prev" href="{{ $posts->previousPageUrl() }}">
    @endif
    @if($posts->hasMorePages())
        <link rel="next" href="{{ $posts->nextPageUrl() }}">
    @endif
@endsection

@section('meta')
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    <meta name="description" content="Kabar Sahabat - Portal berita independen yang menyajikan berita pergerakan, nasional, kaderisasi, opini, dan tokoh terkini.">
    <meta name="keywords" content="portal berita, pmii, pergerakan, kabar sahabat, nasional, kaderisasi, opini, tokoh">
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="Kabar Sahabat - Portal Berita">
    <meta property="og:description" content="Kabar Sahabat - Portal berita independen yang menyajikan berita pergerakan, nasional, kaderisasi, opini, dan tokoh terkini.">
    @if(isset($latestPosts) && $latestPosts->first()?->image_url)
        <meta property="og:image" content="{{ media_url($latestPosts->first()->image_url) }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
    @endif
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ url('/') }}">
    <meta name="twitter:title" content="Kabar Sahabat - Portal Berita">
    <meta name="twitter:description" content="Kabar Sahabat - Portal berita independen yang menyajikan berita pergerakan, nasional, kaderisasi, opini, dan tokoh terkini.">
    @if(isset($latestPosts) && $latestPosts->first()?->image_url)
        <meta name="twitter:image" content="{{ media_url($latestPosts->first()->image_url) }}">
    @endif
    <!-- JSON-LD: WebSite + Sitelinks SearchAction -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@type": "WebSite",
        "name": "Kabar Sahabat",
        "url": {{ Js::from(url('/')) }},
        "potentialAction": {
            "@type": "SearchAction",
            "target": {
                "@type": "EntryPoint",
                "urlTemplate": {{ Js::from(url('/') . '?q={search_term_string}') }}
            },
            "query-input": "required name=search_term_string"
        }
    }
    </script>
@endsection

@section('content')
<!-- ============================================================
     KOMPAS.COM ~90% MATCH PORTAL BERITA — PMII WALISONGO
     ============================================================ -->

<div class="bg-[#F8F9FA] min-h-screen text-neutral-900 font-sans antialiased overflow-x-hidden w-full" id="pmii-news-wrap">

    <!-- SKIN AD: KIRI & KANAN (hanya desktop lebar, default hidden agar tidak geser HP) -->
    <div class="fixed inset-0 pointer-events-none z-[200] hidden lg:block overflow-hidden" style="top: 120px;" id="skin-ads-overlay">
        <div style="position: relative; max-width: 1080px; margin: 0 auto; height: 100%;">
            @if(isset($ads['skin_ad_left']))
            <div class="hidden" style="position: absolute; right: 100%; top: 0; padding-right: 8px; pointer-events: auto;" id="skin-ad-left">
                <div class="w-[160px] h-[600px] relative overflow-hidden bg-neutral-200">
                    @if($ads['skin_ad_left']->type === 'image')
                        <a href="{{ $ads['skin_ad_left']->link_url }}" target="_blank" class="block w-full h-full">
                            <img src="{{ media_url($ads['skin_ad_left']->image_url) }}" class="w-full h-full object-cover" alt="{{ $ads['skin_ad_left']->name }}">
                        </a>
                    @else
                        {!! $ads['skin_ad_left']->script_code !!}
                    @endif
                </div>
            </div>
            @endif
            @if(isset($ads['skin_ad_right']))
            <div class="hidden" style="position: absolute; left: 100%; top: 0; padding-left: 8px; pointer-events: auto;" id="skin-ad-right">
                <div class="w-[160px] h-[600px] relative overflow-hidden bg-neutral-200">
                    @if($ads['skin_ad_right']->type === 'image')
                        <a href="{{ $ads['skin_ad_right']->link_url }}" target="_blank" class="block w-full h-full">
                            <img src="{{ media_url($ads['skin_ad_right']->image_url) }}" class="w-full h-full object-cover" alt="{{ $ads['skin_ad_right']->name }}">
                        </a>
                    @else
                        {!! $ads['skin_ad_right']->script_code !!}
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- 1. TOP UTILITY BAR -->
    <div class="bg-neutral-900 text-white text-[11px] border-b border-neutral-800">
        <div class="max-w-[1080px] mx-auto px-4 sm:px-6 lg:px-8 h-8 flex items-center justify-between">
            <div class="flex items-center gap-3 overflow-hidden">
                <span class="text-neutral-400 font-medium whitespace-nowrap">{{ now()->translatedFormat('l, d F Y') }}</span>
                <span class="hidden md:inline-block w-px h-3 bg-neutral-700"></span>
                @if($latestPosts->count() > 0)
                <div class="hidden md:flex items-center gap-2 text-neutral-300 overflow-hidden flex-1 min-w-0">
                    <span class="bg-[#FFCC00] text-neutral-950 px-1.5 font-bold uppercase tracking-wider text-[9px]">SOROTAN</span>
                    @if($latestPosts->count() === 1)
                        <a href="{{ route('news.show', $latestPosts->first()->slug) }}" class="hover:text-[#FFCC00] transition-colors truncate font-medium">{{ $latestPosts->first()->title }}</a>
                    @else
                        {{-- Ticker vertikal: 3 artikel utama berputar otomatis, pause saat hover --}}
                        <div class="sorotan-ticker">
                            <ul class="sorotan-track">
                                @foreach($latestPosts->take(3)->concat($latestPosts->take(3)) as $sorotanPost)
                                <li>
                                    <a href="{{ route('news.show', $sorotanPost->slug) }}">{{ $sorotanPost->title }}</a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
                @endif
            </div>

        </div>
    </div>

    <style>
        /* Ticker SOROTAN: rotasi vertikal 3 artikel, murni CSS */
        .sorotan-ticker {
            flex: 1;
            min-width: 0;
            height: 16px;
            overflow: hidden;
        }
        .sorotan-track {
            list-style: none;
            margin: 0;
            padding: 0;
            animation: sorotan-rotate 12s linear infinite;
        }
        .sorotan-track li {
            height: 16px;
            line-height: 16px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .sorotan-track a {
            font-size: 11px;
            font-weight: 500;
            color: #d4d4d4;
        }
        .sorotan-track a:hover { color: #FFCC00; }
        .sorotan-ticker:hover .sorotan-track { animation-play-state: paused; }
        @keyframes sorotan-rotate {
            0%, 28%   { transform: translateY(0); }
            33%, 61%  { transform: translateY(-16px); }
            66%, 94%  { transform: translateY(-32px); }
            100%      { transform: translateY(-48px); }
        }
    </style>

    <!-- 2. BRANDING & LEADERBOARD AD -->
    <div class="bg-white border-b border-neutral-200 py-4">
        <div class="max-w-[1080px] mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <a href="/" class="flex items-center gap-3 group flex-shrink-0">
                @if(isset($siteSettings) && $siteSettings->logo_path)
                    <img src="{{ media_url($siteSettings->logo_path) }}" class="h-10 sm:h-14 w-auto max-w-[220px] sm:max-w-[300px] object-contain object-left" alt="Logo">
                @else
                    <div class="w-9 h-9 sm:w-11 sm:h-11 bg-[#003399] flex items-center justify-center text-white font-black text-xl sm:text-2xl border-2 border-[#FFCC00]">K</div>
                    <div class="flex flex-col min-w-0">
                        <div class="flex items-center gap-1 leading-none">
                            <span class="text-2xl sm:text-3xl font-black tracking-tighter text-[#003399] font-serif">KABAR</span>
                            <span class="text-2xl sm:text-3xl font-black tracking-tighter text-[#FFCC00] font-serif">SAHABAT</span>
                        </div>
                        <span class="text-[8px] sm:text-[9px] text-neutral-500 font-bold uppercase tracking-widest mt-1 truncate">{{ $siteSettings->tagline ?? 'Jernih Melihat Pergerakan' }}</span>
                    </div>
                @endif
            </a>
            @if(isset($ads['header_leaderboard']))
            <div class="w-full max-w-full md:max-w-[728px] h-[50px] sm:h-[90px] relative flex-shrink-0 flex items-center justify-center overflow-hidden border border-neutral-200">
                @if($ads['header_leaderboard']->type === 'image')
                    <a href="{{ $ads['header_leaderboard']->link_url }}" target="_blank" class="block w-full h-full">
                        <img src="{{ media_url($ads['header_leaderboard']->image_url) }}" class="w-full h-full object-cover" alt="{{ $ads['header_leaderboard']->name }}">
                    </a>
                @else
                    {!! $ads['header_leaderboard']->script_code !!}
                @endif
            </div>
            @endif
        </div>
    </div>

    <!-- 3. STICKY NAVBAR -->
    <nav class="sticky top-0 z-50 bg-[#003399] border-b-4 border-[#FFCC00] shadow-md" id="main-nav">
        <div class="max-w-[1080px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-11">
                <a href="/" class="flex-shrink-0 items-center gap-1.5 mr-3 hidden" id="nav-sticky-logo">
                    @if(isset($siteSettings) && $siteSettings->logo_path)
                        <img src="{{ media_url($siteSettings->logo_path) }}" class="h-9 w-auto max-w-[200px] object-contain object-left" alt="Logo">
                    @else
                        <div class="w-7 h-7 bg-white flex items-center justify-center font-black text-[#003399] text-sm border border-[#FFCC00]">K</div>
                        <span class="text-sm font-black text-white font-serif hidden sm:block">KABAR SAHABAT</span>
                    @endif
                </a>

                <div class="relative flex-1 min-w-0 h-full">
                    <div class="flex items-center overflow-x-auto h-full scrollbar-none pr-6" style="scrollbar-width:none;">
                        <!-- Beranda -->
                        <a href="/" class="h-full flex items-center px-4 text-[11px] font-bold uppercase tracking-wider {{ !request()->has('category') && !request()->has('q') ? 'text-[#FFCC00]' : 'text-white/90 hover:text-white hover:bg-[#002b80]' }} transition-colors whitespace-nowrap">
                            Beranda
                        </a>

                        @foreach($navCategories as $navCategory)
                        <a href="/?category={{ $navCategory->slug }}" class="h-full flex items-center px-4 text-[11px] font-bold uppercase tracking-wider {{ request('category') === $navCategory->slug ? 'text-[#FFCC00]' : 'text-white/90 hover:text-white hover:bg-[#002b80]' }} transition-colors whitespace-nowrap">
                            {{ $navCategory->name }}
                        </a>
                        @endforeach
                    </div>

                    <!-- Fade kanan: kategori yang terpotong memudar, bukan terpotong kaku -->
                    <div class="pointer-events-none absolute inset-y-0 right-0 w-12" style="background: linear-gradient(to left, #003399 20%, rgba(0,51,153,0))"></div>
                </div>

                <livewire:search-navbar />
            </div>
        </div>
    </nav>

    <!-- 4. TRENDING BAR -->
    <div class="bg-white border-b border-neutral-200">
        <div class="max-w-[1080px] mx-auto px-4 sm:px-6 lg:px-8 flex items-center h-10 gap-3">
            <span class="text-[11px] font-black uppercase tracking-widest text-neutral-500 flex-shrink-0 flex items-center gap-1.5">
                <svg class="w-3 h-3 text-[#CC0000]" fill="currentColor" viewBox="0 0 20 20"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/></svg>
                Trending:
            </span>
            <div class="flex items-center gap-2 overflow-x-auto scrollbar-none flex-1 pb-0" style="scrollbar-width:none;">
                @foreach($trendingPosts as $tp)
                    <a href="{{ route('news.show', $tp->slug) }}" class="flex-shrink-0 px-3 py-1 bg-neutral-100 hover:bg-[#003399] hover:text-white text-neutral-700 text-[11px] font-semibold transition-colors whitespace-nowrap border border-neutral-200 hover:border-[#003399] rounded-full">
                        {{ $tp->title }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    @if(!request()->has('category') && !request()->has('q'))
    <!-- 5. BERITA TERBARU SLIDER -->
    <div class="bg-white border-b border-neutral-200">
        <div class="max-w-[1080px] mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <div class="flex items-center gap-3 mb-2.5">
                <span class="text-xs font-uppercase tracking-tight text-neutral-950 flex items-center gap-1.5">
                    <span class="w-1.5 h-4 bg-[#CC0000] inline-block"></span>
                    Berita Terbaru
                </span>
            </div>
            <div class="relative" id="berita-terbaru-wrap">
                <div class="flex gap-3 overflow-x-auto scrollbar-none pb-1" id="berita-terbaru-slider" style="scrollbar-width:none;">
                    @foreach($latestPosts as $lp)
                        <a href="{{ route('news.show', $lp->slug) }}" class="flex-shrink-0 w-[140px] sm:w-[120px] group block space-y-1.5">
                            <div class="w-full h-[90px] bg-neutral-200 overflow-hidden relative border border-neutral-200">
                                @if($lp->image_url)
                                    <img src="{{ media_url($lp->image_url) }}" alt="{{ $lp->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full bg-neutral-300 flex items-center justify-center text-neutral-500 text-[10px] font-bold">KABAR</div>
                                @endif
                                <span class="absolute bottom-0 left-0 right-0 bg-[#003399] text-white text-[8px] font-bold uppercase px-1.5 py-0.5 leading-none">{{ optional($lp->categories->first())->name ?? 'Nasional' }}</span>
                            </div>
                            <p class="text-[11px] font-serif text-neutral-900 leading-snug line-clamp-2 group-hover:text-[#003399] transition-colors">{{ $lp->title }}</p>
                            <span class="text-[10px] text-neutral-400 block">{{ $lp->published_at->translatedFormat('l, d M Y | H:i') }} WIB</span>
                        </a>
                    @endforeach
                </div>
                <button onclick="document.getElementById('berita-terbaru-slider').scrollBy({left:320,behavior:'smooth'})" class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-0 w-8 h-8 bg-white border border-neutral-300 shadow flex items-center justify-center text-neutral-600 hover:text-[#003399] z-10 hidden lg:flex">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- 6. BREAKING NEWS TICKER -->
    <div class="bg-white border-b border-neutral-200 overflow-hidden">
        <div class="max-w-[1080px] mx-auto px-4 sm:px-6 lg:px-8 flex items-center h-9">
            <span class="bg-[#CC0000] text-white text-[10px] font-black uppercase tracking-widest px-3 py-1 flex-shrink-0 mr-3">TERKINI</span>
            <div class="overflow-hidden flex-1 relative">
                <div class="flex animate-marquee-ticker whitespace-nowrap">
                    @foreach($latestPosts as $lp)
                        <span class="text-xs text-neutral-700 font-medium px-6"><span class="text-[#003399] font-bold mr-1">●</span>{{ $lp->title }}</span>
                    @endforeach
                    @foreach($latestPosts as $lp)
                        <span class="text-xs text-neutral-700 font-medium px-6"><span class="text-[#003399] font-bold mr-1">●</span>{{ $lp->title }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- 7. MAIN BODY -->
    <main class="max-w-[1080px] mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-5 space-y-5 sm:space-y-6 w-full min-w-0">

        <!-- === HEADLINE GRID === -->
        <section class="bg-white p-3 sm:p-4 border border-neutral-200 shadow-sm rounded-lg space-y-4">
            <div class="flex items-center justify-between gap-2 border-b-2 border-[#003399] pb-2">
                <h2 class="text-sm font-black uppercase tracking-tight text-[#003399] flex items-center gap-2 min-w-0">
                    <span class="w-2.5 h-4 bg-[#FFCC00] inline-block flex-shrink-0"></span>HEADLINE UTAMA
                </h2>
                <span class="text-[10px] text-neutral-400 font-medium hidden sm:inline flex-shrink-0">Kabar Terkini Hari Ini</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
                @if($latestPosts->count() > 0)
                    @php $headline = $latestPosts->first(); @endphp
                    <article class="lg:col-span-8 group">
                        <a href="{{ route('news.show', $headline->slug) }}" class="block space-y-3">
                            <div class="relative aspect-[16/9] overflow-hidden bg-neutral-900 border border-neutral-200">
                                @if($headline->image_url)
                                    <x-post-image :src="$headline->image_url" :alt="$headline->title" :priority="true" class="group-hover:scale-105 transition-transform duration-500" />
                                @else
                                    <div class="w-full h-full bg-neutral-800 flex items-center justify-center text-white/30 text-2xl font-black font-serif">KABAR SAHABAT</div>
                                @endif
                                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/60 to-transparent p-3 hidden lg:block">
                                    <span class="bg-[#003399] text-white px-2 py-0.5 text-[10px] font-bold uppercase tracking-widest border-l-2 border-[#FFCC00]">{{ optional($headline->categories->first())->name ?? 'Nasional' }}</span>
                                </div>
                            </div>
                            <div class="space-y-1.5">
                                <div class="text-[11px] text-neutral-500 font-medium flex items-center gap-2 flex-wrap">
                                    <span class="bg-[#003399] text-white px-2 py-0.5 text-[10px] font-bold uppercase border-l-2 border-[#FFCC00] lg:hidden">{{ optional($headline->categories->first())->name ?? 'Nasional' }}</span>
                                    <span>{{ $headline->published_at->translatedFormat('l, d F Y | H:i') }} WIB</span>
                                </div>
                                <h1 class="text-lg sm:text-2xl lg:text-[26px] font-black text-neutral-950 leading-snug sm:leading-tight group-hover:text-[#003399] transition-colors font-serif">{{ $headline->title }}</h1>
                                <p class="text-sm text-neutral-600 leading-relaxed line-clamp-2">{{ Str::limit(Str::squish(html_entity_decode(strip_tags($headline->content))), 200) }}</p>
                            </div>
                        </a>
                    </article>
                @endif

                <!-- SECONDARY HEADLINES -->
                <div class="lg:col-span-4">
                    <div class="flex gap-3 overflow-x-auto pb-2 lg:hidden scrollbar-none">
                        @foreach($latestPosts->skip(1)->take(4) as $subPost)
                            <article class="flex-shrink-0 w-40 group">
                                <a href="{{ route('news.show', $subPost->slug) }}" class="block space-y-1.5">
                                    <div class="w-40 h-24 bg-neutral-200 overflow-hidden border border-neutral-200 relative">
                                        @if($subPost->image_url)
                                            <img src="{{ media_url($subPost->image_url) }}" alt="{{ $subPost->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                        @else
                                            <div class="w-full h-full bg-neutral-300 flex items-center justify-center text-neutral-500 text-[10px]">KABAR</div>
                                        @endif
                                    </div>
                                    <h3 class="text-xs font-black text-neutral-950 leading-snug group-hover:text-[#003399] line-clamp-2 font-serif">{{ $subPost->title }}</h3>
                                    <span class="text-[10px] text-neutral-400 block">{{ $subPost->published_at->translatedFormat('l, d M Y | H:i') }} WIB</span>
                                </a>
                            </article>
                        @endforeach
                    </div>

                    <div class="hidden lg:flex flex-col divide-y divide-neutral-200 h-full">
                        @foreach($latestPosts->skip(1)->take(4) as $subPost)
                            <article class="py-3 first:pt-0 flex items-start gap-3 group flex-1">
                                <a href="{{ route('news.show', $subPost->slug) }}" class="flex items-start gap-3 w-full">
                                    <div class="w-24 h-[70px] bg-neutral-200 flex-shrink-0 overflow-hidden border border-neutral-200 relative">
                                        @if($subPost->image_url)
                                            <img src="{{ media_url($subPost->image_url) }}" alt="{{ $subPost->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                        @else
                                            <div class="w-full h-full bg-neutral-300 flex items-center justify-center text-neutral-500 text-[10px]">KABAR</div>
                                        @endif
                                    </div>
                                    <div class="space-y-0.5 flex-1 min-w-0">
                                        <span class="text-[9px] font-bold text-[#CC0000] uppercase tracking-wider block">{{ optional($subPost->categories->first())->name ?? 'Nasional' }}</span>
                                        <h3 class="text-xs font-bold text-neutral-950 leading-snug group-hover:text-[#003399] line-clamp-3 font-serif">{{ $subPost->title }}</h3>
                                        <span class="text-[10px] text-neutral-400 block">{{ $subPost->published_at->translatedFormat('l, d M Y | H:i') }} WIB</span>
                                    </div>
                                </a>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

        <!-- === DUAL COLUMN: FEED + SIDEBAR === -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            <!-- MAIN FEED -->
            <div class="lg:col-span-8">
                <div class="bg-white px-3 sm:px-4 py-3 border border-neutral-200 border-b-2 border-b-[#003399] flex items-center justify-between gap-2">
                    <h2 class="text-sm font-black uppercase tracking-tight text-neutral-950 flex items-center gap-2 min-w-0">
                        <span class="w-2 h-4 bg-[#003399] inline-block flex-shrink-0"></span>
                        <span class="truncate">
                        @if(request()->has('category'))
                            KANAL: {{ strtoupper($navCategories->firstWhere('slug', request('category'))?->name ?? request('category')) }}
                        @elseif(request()->has('q'))
                            HASIL PENCARIAN: "{{ request('q') }}"
                        @else
                            BERITA TERKINI
                        @endif
                        </span>
                    </h2>
                    <span class="text-[10px] text-neutral-400 font-medium hidden sm:inline flex-shrink-0">Diurutkan Berdasarkan Waktu</span>
                </div>

                <div class="divide-y divide-neutral-200 border border-neutral-200 border-t-0 bg-white">
                    @foreach($posts as $index => $p)
                        <article class="px-3 sm:px-4 py-3.5 sm:py-4 hover:bg-neutral-50 transition-colors group">
                            <a href="{{ route('news.show', $p->slug) }}" class="flex flex-row items-start gap-2.5 sm:gap-3">
                                <div class="w-[100px] sm:w-44 h-[72px] sm:h-[110px] bg-neutral-200 flex-shrink-0 overflow-hidden border border-neutral-200 relative rounded-sm">
                                    <x-post-image :src="$p->image_url" :alt="$p->title" class="group-hover:scale-105 transition-transform duration-500" />
                                </div>
                                <div class="space-y-1 sm:space-y-1.5 flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-x-1.5 gap-y-0.5 text-[10px] sm:text-[11px] text-neutral-400">
                                        <span class="font-bold uppercase tracking-wider text-[#003399]">{{ optional($p->categories->firstWhere('slug', request('category')) ?? $p->categories->first())->name ?? 'Nasional' }}</span>
                                        <span class="h-3 w-px bg-neutral-300 hidden xs:inline sm:inline"></span>
                                        <span class="font-semibold text-[#CC0000] truncate max-w-[40%]">{{ $p->user->name ?? 'Redaksi' }}</span>
                                        <span>•</span>
                                        <span class="truncate">{{ $p->published_at->translatedFormat('d M Y, H:i') }} WIB</span>
                                    </div>
                                    <h3 class="text-[13px] sm:text-base font-bold text-neutral-950 leading-snug group-hover:text-[#003399] transition-colors line-clamp-3 font-serif">{{ $p->title }}</h3>
                                    <p class="text-[11px] sm:text-xs text-neutral-600 leading-relaxed line-clamp-2 hidden sm:block">{{ Str::limit(Str::squish(html_entity_decode(strip_tags($p->content))), 150) }}</p>
                                </div>
                            </a>
                        </article>

                        @if($index == 4 && isset($ads['in_feed_leaderboard']))
                            <div class="w-full h-[90px] relative flex items-center justify-center overflow-hidden border-y border-neutral-200 bg-white">
                                @if($ads['in_feed_leaderboard']->type === 'image')
                                    <a href="{{ $ads['in_feed_leaderboard']->link_url }}" target="_blank" class="block w-full h-full">
                                        <img src="{{ media_url($ads['in_feed_leaderboard']->image_url) }}" class="w-full h-full object-cover" alt="{{ $ads['in_feed_leaderboard']->name }}">
                                    </a>
                                @else
                                    {!! $ads['in_feed_leaderboard']->script_code !!}
                                @endif
                            </div>
                        @endif
                        @if($index == 9 && isset($ads['sponsor_leaderboard']))
                            <div class="w-full h-[90px] relative flex items-center justify-center overflow-hidden border-y border-neutral-200 bg-white">
                                @if($ads['sponsor_leaderboard']->type === 'image')
                                    <a href="{{ $ads['sponsor_leaderboard']->link_url }}" target="_blank" class="block w-full h-full">
                                        <img src="{{ media_url($ads['sponsor_leaderboard']->image_url) }}" class="w-full h-full object-cover" alt="{{ $ads['sponsor_leaderboard']->name }}">
                                    </a>
                                @else
                                    {!! $ads['sponsor_leaderboard']->script_code !!}
                                @endif
                            </div>
                        @endif
                    @endforeach

                    @if ($posts->hasPages())
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-3 sm:px-4 py-3 border border-neutral-200 border-t-0 text-xs bg-white rounded-b-lg">
                            <span class="text-neutral-500 font-medium text-center sm:text-left">Halaman {{ $posts->currentPage() }} dari {{ $posts->lastPage() }}</span>
                            <div class="flex items-center justify-center gap-1.5 overflow-x-auto max-w-full scrollbar-none">
                                @if ($posts->onFirstPage())
                                    <button disabled class="px-2.5 sm:px-3 py-1.5 border border-neutral-200 font-bold text-neutral-300 cursor-not-allowed rounded whitespace-nowrap">←</button>
                                @else
                                    <a href="{{ $posts->previousPageUrl() }}" class="px-2.5 sm:px-3 py-1.5 border border-neutral-300 font-bold text-neutral-700 hover:bg-[#003399] hover:text-white hover:border-[#003399] transition-colors rounded whitespace-nowrap">←</a>
                                @endif

                                @foreach ($posts->getUrlRange(max(1, $posts->currentPage() - 1), min($posts->lastPage(), $posts->currentPage() + 1)) as $page => $url)
                                    @if ($page == $posts->currentPage())
                                        <span class="px-3 py-1.5 bg-[#003399] text-white font-bold border border-[#003399] rounded">{{ $page }}</span>
                                    @else
                                        <a href="{{ $url }}" class="px-3 py-1.5 border border-neutral-300 font-bold text-neutral-700 hover:bg-[#003399] hover:text-white hover:border-[#003399] transition-colors rounded">{{ $page }}</a>
                                    @endif
                                @endforeach

                                @if ($posts->hasMorePages())
                                    <a href="{{ $posts->nextPageUrl() }}" class="px-2.5 sm:px-3 py-1.5 border border-neutral-300 font-bold text-neutral-700 hover:bg-[#003399] hover:text-white hover:border-[#003399] transition-colors rounded whitespace-nowrap">→</a>
                                @else
                                    <button disabled class="px-2.5 sm:px-3 py-1.5 border border-neutral-200 font-bold text-neutral-300 cursor-not-allowed rounded whitespace-nowrap">→</button>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- SIDEBAR -->
            <aside class="lg:col-span-4 space-y-5">
                @if(isset($ads['sidebar_mrec']))
                <div class="w-full h-[250px] relative flex items-center justify-center overflow-hidden border border-neutral-200 bg-white rounded-lg">
                    @if($ads['sidebar_mrec']->type === 'image')
                        <a href="{{ $ads['sidebar_mrec']->link_url }}" target="_blank" class="block w-full h-full">
                            <img src="{{ media_url($ads['sidebar_mrec']->image_url) }}" class="w-full h-full object-cover" alt="{{ $ads['sidebar_mrec']->name }}">
                        </a>
                    @else
                        {!! $ads['sidebar_mrec']->script_code !!}
                    @endif
                </div>
                @endif

                <div class="bg-white border border-neutral-200 shadow-sm rounded-lg overflow-hidden">
                    <div class="border-b-2 border-[#003399] px-4 py-2.5 flex items-center justify-between">
                        <h3 class="text-sm font-black uppercase tracking-tight text-[#003399] flex items-center gap-2">
                            <span class="w-2 h-4 bg-[#FFCC00]"></span>TERPOPULER
                        </h3>
                        <span class="text-[10px] text-neutral-400 font-bold uppercase">Minggu Ini</span>
                    </div>
                    <div class="divide-y divide-neutral-100">
                        @foreach($popularPosts as $index => $pp)
                            <article class="px-4 py-3 flex items-start gap-3 group hover:bg-neutral-50 transition-colors">
                                <div class="text-xl font-black text-neutral-300 font-serif w-5 flex-shrink-0 text-center leading-none pt-0.5">{{ $index + 1 }}</div>
                                <div class="space-y-0.5 flex-1 min-w-0">
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-[#CC0000] block">{{ optional($pp->categories->first())->name ?? 'Nasional' }}</span>
                                    <a href="{{ route('news.show', $pp->slug) }}" class="text-xs font-bold text-neutral-950 leading-snug group-hover:text-[#003399] line-clamp-2 font-serif block">{{ $pp->title }}</a>
                                    <span class="text-[10px] text-neutral-400 block">{{ $pp->published_at->translatedFormat('l, d M Y | H:i') }} WIB</span>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>

                @if(isset($ads['sidebar_sponsor_mrec']))
                <div class="w-full h-[250px] relative flex items-center justify-center overflow-hidden border border-neutral-200 bg-white rounded-lg">
                    @if($ads['sidebar_sponsor_mrec']->type === 'image')
                        <a href="{{ $ads['sidebar_sponsor_mrec']->link_url }}" target="_blank" class="block w-full h-full">
                            <img src="{{ media_url($ads['sidebar_sponsor_mrec']->image_url) }}" class="w-full h-full object-cover" alt="{{ $ads['sidebar_sponsor_mrec']->name }}">
                        </a>
                    @else
                        {!! $ads['sidebar_sponsor_mrec']->script_code !!}
                    @endif
                </div>
                @endif

                @if(isset($ads['sidebar_skyscraper']))
                <div class="sticky top-[60px]">
                    <div class="w-full h-[600px] relative flex items-center justify-center overflow-hidden border border-neutral-200 bg-white rounded-lg">
                        @if($ads['sidebar_skyscraper']->type === 'image')
                            <a href="{{ $ads['sidebar_skyscraper']->link_url }}" target="_blank" class="block w-full h-full">
                                <img src="{{ media_url($ads['sidebar_skyscraper']->image_url) }}" class="w-full h-full object-cover" alt="{{ $ads['sidebar_skyscraper']->name }}">
                            </a>
                        @else
                            {!! $ads['sidebar_skyscraper']->script_code !!}
                        @endif
                    </div>
                </div>
                @endif
            </aside>
        </div>

        @if(!request()->has('category') && !request()->has('q'))
        @foreach($homeSections as $homeSection)
        <!-- === SEKSI KATEGORI: {{ strtoupper($homeSection->name) }} === -->
        <section class="bg-white p-4 border border-neutral-200 shadow-sm rounded-lg">
            <div class="flex items-center justify-between border-b-2 border-[#003399] pb-2 mb-4">
                <h2 class="text-sm font-black uppercase tracking-tight text-[#003399] flex items-center gap-2">
                    <span class="w-2.5 h-4 {{ $loop->index % 2 === 0 ? 'bg-[#CC0000]' : 'bg-[#FFCC00]' }} inline-block"></span>{{ $homeSection->name }}
                </h2>
                <a href="/?category={{ $homeSection->slug }}" class="text-[10px] text-[#003399] font-bold uppercase hover:underline">Selengkapnya →</a>
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($homeSection->homepagePosts as $hp)
                    <article class="group space-y-2">
                        <a href="{{ route('news.show', $hp->slug) }}" class="block aspect-[16/10] bg-neutral-200 overflow-hidden border border-neutral-200 relative">
                            @if($hp->image_url)
                                <img src="{{ media_url($hp->image_url) }}" alt="{{ $hp->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full bg-neutral-300 flex items-center justify-center text-neutral-500 text-xs">KABAR</div>
                            @endif
                        </a>

                        <div class="space-y-0.5">
                            <span class="text-[9px] font-bold uppercase tracking-wider text-[#003399] block">{{ optional($hp->categories->first())->name }}</span>
                            <a href="{{ route('news.show', $hp->slug) }}" class="text-xs font-bold text-neutral-950 leading-snug group-hover:text-[#003399] line-clamp-2 font-serif block">{{ $hp->title }}</a>
                            <span class="text-[10px] text-neutral-400 block">{{ $hp->published_at?->translatedFormat('l, d M Y | H:i') }} WIB</span>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <!-- BILLBOARD AD -->
        @if(isset($ads['bottom_billboard']) && $loop->first)
        <div class="w-full max-w-[970px] h-[90px] relative flex items-center justify-center overflow-hidden border border-neutral-200 bg-white rounded-lg mx-auto my-6">
            @if($ads['bottom_billboard']->type === 'image')
                <a href="{{ $ads['bottom_billboard']->link_url }}" target="_blank" class="block w-full h-full">
                    <img src="{{ media_url($ads['bottom_billboard']->image_url) }}" class="w-full h-full object-cover" alt="{{ $ads['bottom_billboard']->name }}">
                </a>
            @else
                {!! $ads['bottom_billboard']->script_code !!}
            @endif
        </div>
        @endif
        @endforeach
        @endif

    </main>

    @include('partials.footer')

</div>

<style>
/* Ticker marquee */
@keyframes marquee-ticker {
    0%   { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}
.animate-marquee-ticker {
    animation: marquee-ticker 90s linear infinite;
    display: inline-flex;
}
.animate-marquee-ticker:hover { animation-play-state: paused; }

/* Dropdown submenu smooth */
.group:hover > div { display: block !important; }
</style>

<script>
// SKIN ADS: Tampilkan hanya jika ada ruang di kiri & kanan konten
(function() {
    var leftAd  = document.getElementById('skin-ad-left');
    var rightAd = document.getElementById('skin-ad-right');
    if (!leftAd || !rightAd) return;
    var MIN_WIDTH = 1416; // max-w-[1080px] + 2 skin ads 160px + padding

    function toggleSkinAds() {
        var show = window.innerWidth >= MIN_WIDTH;
        leftAd.classList.toggle('hidden', !show);
        leftAd.style.display = show ? 'block' : 'none';
        rightAd.classList.toggle('hidden', !show);
        rightAd.style.display = show ? 'block' : 'none';
    }
    toggleSkinAds();
    window.addEventListener('resize', toggleSkinAds, { passive: true });
})();

// STICKY LOGO: Muncul setelah scroll melewati branding
(function() {
    var stickyLogo = document.getElementById('nav-sticky-logo');
    var brandingSection = document.querySelector('.bg-white.border-b.border-neutral-200.py-4');
    if (!stickyLogo || !brandingSection) return;

    function onScroll() {
        var brandingBottom = brandingSection.getBoundingClientRect().bottom;
        if (brandingBottom < 0) {
            stickyLogo.classList.remove('hidden');
            stickyLogo.classList.add('flex');
        } else {
            stickyLogo.classList.add('hidden');
            stickyLogo.classList.remove('flex');
        }
    }
    window.addEventListener('scroll', onScroll, { passive: true });
})();
</script>
@endsection