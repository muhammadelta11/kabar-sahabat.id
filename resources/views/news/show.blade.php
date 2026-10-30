@extends('layouts.app')

@section('title', $post->title . ' - Kabar Sahabat')

@section('canonical')
    <link rel="canonical" href="{{ route('news.show', $post->slug) }}">
@endsection

@section('meta')
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    <meta name="description" content="{{ Str::limit(Str::squish(html_entity_decode(strip_tags($post->excerpt ?? $post->content))), 160) }}">
    <meta name="author" content="{{ $post->penulis ?? $post->user->name ?? 'Redaksi' }}">
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="article">
    <meta property="og:url" content="{{ route('news.show', $post->slug) }}">
    <meta property="og:title" content="{{ $post->title }}">
    <meta property="og:description" content="{{ Str::limit(Str::squish(html_entity_decode(strip_tags($post->excerpt ?? $post->content))), 160) }}">
    <meta property="article:published_time" content="{{ $post->published_at?->toIso8601String() }}">
    <meta property="article:author" content="{{ $post->penulis ?? $post->user->name ?? 'Redaksi' }}">
    @foreach($post->categories as $cat)
        <meta property="article:section" content="{{ $cat->name }}">
    @endforeach
    @if($post->image_url)
        <meta property="og:image" content="{{ media_url($post->image_url) }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
    @endif
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ route('news.show', $post->slug) }}">
    <meta name="twitter:title" content="{{ $post->title }}">
    <meta name="twitter:description" content="{{ Str::limit(Str::squish(html_entity_decode(strip_tags($post->excerpt ?? $post->content))), 160) }}">
    @if($post->image_url)
        <meta name="twitter:image" content="{{ media_url($post->image_url) }}">
    @endif
    <!-- JSON-LD: Article structured data (Google News / Rich Results) -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@type": "NewsArticle",
        "headline": {{ Js::from($post->title) }},
        "description": {{ Js::from(Str::limit(Str::squish(html_entity_decode(strip_tags($post->excerpt ?? $post->content))), 160)) }},
        "url": {{ Js::from(route('news.show', $post->slug)) }},
        @if($post->image_url)
        "image": {
            "@type": "ImageObject",
            "url": {{ Js::from(media_url($post->image_url)) }},
            "width": 1200,
            "height": 630
        },
        @endif
        "datePublished": {{ Js::from($post->published_at?->toIso8601String()) }},
        "dateModified": {{ Js::from($post->updated_at?->toIso8601String()) }},
        "author": {
            "@type": "Person",
            "name": {{ Js::from($post->penulis ?? $post->user->name ?? 'Redaksi') }}
        },
        "publisher": {
            "@type": "Organization",
            "name": "Kabar Sahabat",
            "url": {{ Js::from(url('/')) }}
        },
        "mainEntityOfPage": {
            "@type": "WebPage",
            "@id": {{ Js::from(route('news.show', $post->slug)) }}
        }
    }
    </script>
    <!-- JSON-LD: BreadcrumbList -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            {
                "@type": "ListItem",
                "position": 1,
                "name": "Beranda",
                "item": {{ Js::from(url('/')) }}
            }@if($post->categories->count() > 0),
            {
                "@type": "ListItem",
                "position": 2,
                "name": {{ Js::from($post->categories->first()->name) }},
                "item": {{ Js::from(url('/?category=' . $post->categories->first()->slug)) }}
            },
            {
                "@type": "ListItem",
                "position": 3,
                "name": {{ Js::from($post->title) }},
                "item": {{ Js::from(route('news.show', $post->slug)) }}
            }
            @else,
            {
                "@type": "ListItem",
                "position": 2,
                "name": {{ Js::from($post->title) }},
                "item": {{ Js::from(route('news.show', $post->slug)) }}
            }
            @endif
        ]
    }
    </script>
@endsection

@section('content')
<!-- ============================================================
     KOMPAS.COM ~90% MATCH PORTAL BERITA — PMII WALISONGO
     ============================================================ -->

<div class="bg-[#F8F9FA] min-h-screen text-neutral-900 font-sans antialiased overflow-x-hidden w-full" id="pmii-news-wrap">

    <!-- SKIN AD: hanya desktop lebar; default hidden agar tidak geser HP -->
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
            </div>

        </div>
    </div>

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

    <!-- MAIN CONTENT -->
    <main class="max-w-[1080px] mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-6 relative z-10 bg-white w-full min-w-0 overflow-x-hidden">
        
        <!-- Breadcrumb -->
        <nav class="text-[11px] font-semibold text-neutral-500 mb-4 flex items-center gap-2 uppercase tracking-wide">
            <a href="/" class="hover:text-[#003399]">Home</a>
            <span>&rsaquo;</span>
            <a href="/" class="hover:text-[#003399]">Berita</a>
            @if($post->categories->count() > 0)
                <span>&rsaquo;</span>
                <a href="/?category={{ $post->categories->first()->slug }}" class="hover:text-[#003399]">{{ $post->categories->first()->name }}</a>
            @endif
        </nav>

        <div class="flex flex-col lg:flex-row gap-8">
            
            <!-- Left Column: Article Content -->
            <article class="flex-1 lg:max-w-[70%]">
                <h1 class="text-[1.4rem] sm:text-3xl lg:text-4xl font-black font-serif text-neutral-900 leading-snug sm:leading-tight mb-3 sm:mb-4">
                    {{ $post->title }}
                </h1>
                
                <div class="flex items-start justify-between gap-3 border-b border-neutral-200 pb-4 mb-5">
                    <div class="flex flex-col min-w-0">
                        <span class="text-xs font-bold text-[#003399]">Penulis: {{ $post->user->name ?? "Tim Redaksi" }}</span>
                        <span class="text-[11px] text-neutral-500">{{ $post->published_at?->translatedFormat('l, d F Y | H:i') }} WIB</span>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        <button type="button" id="native-share-btn" aria-label="Bagikan artikel" title="Bagikan" class="hidden w-8 h-8 rounded-full bg-neutral-900 text-white items-center justify-center hover:bg-[#003399] transition-colors">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92s2.92-1.31 2.92-2.92-1.31-2.92-2.92-2.92z"/>
                            </svg>
                        </button>
                        <button type="button" id="copy-link-btn" aria-label="Salin tautan" title="Salin tautan" class="w-8 h-8 rounded-full bg-neutral-200 text-neutral-700 flex items-center justify-center hover:bg-[#FFCC00] hover:text-neutral-950 transition-colors">
                            <svg data-copy-icon="link" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/>
                            </svg>
                            <svg data-copy-icon="check" class="hidden w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Featured Image -->
                @if($post->image_url)
                <figure class="mb-6 relative">
                    <x-post-image :src="$post->image_url" :alt="$post->image_caption ?? $post->title" :priority="true" class="w-full h-auto object-cover max-h-[500px]" />
                    @if($post->image_caption)
                    <figcaption>{{ $post->image_caption }}</figcaption>
                    @endif
                </figure>
                @endif

                <!-- Article Content -->
                <div id="article-content" class="max-w-none text-neutral-800 leading-relaxed text-[15px] sm:text-base font-medium [&_p]:mb-5 [&_p:last-child]:mb-0 [&_h1]:text-2xl [&_h1]:font-bold [&_h2]:text-xl [&_h2]:font-bold [&_h3]:text-lg [&_h3]:font-bold [&_ul]:list-disc [&_ul]:pl-6 [&_ol]:list-decimal [&_ol]:pl-6 [&_a]:text-[#003399] [&_a]:underline">
                    {!! \App\Support\Media::rewriteContentUrls($post->content) !!}
                </div>

                <!-- Kredit Penulis & Editor -->
                @if($post->penulis || $post->editor)
                <div class="mt-6 pt-4 border-t border-neutral-200 text-base text-neutral-700">
                    @if($post->penulis)
                    <div><span class="font-bold">Penulis:</span> {{ $post->penulis }}</div>
                    @endif
                    @if($post->editor)
                    <div><span class="font-bold">Editor:</span> {{ $post->editor }}</div>
                    @endif
                </div>
                @endif

                <!-- Tags -->
                @if($post->categories->count() > 0)
                <div class="mt-8 pt-6 border-t border-neutral-200">
                    <h4 class="text-[11px] font-uppercase tracking-widest text-neutral-900 mb-3">Kanal Terkait:</h4>
                    <div class="flex flex-wrap justify-center md:justify-start gap-2">
                        @foreach($post->categories as $category)
                        <a href="/?category={{ $category->slug }}" class="inline-block px-3 py-1 bg-neutral-100 text-neutral-600 text-[11px] font-bold hover:bg-[#003399] hover:text-white transition-colors border border-neutral-200 rounded">
                            #{{ $category->name }}
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif
                
                <!-- Mungkin Anda Melewatkan Ini -->
                <div class="mt-8 pt-8 border-t-2 border-neutral-200">
                    <h3 class="text-base font-uppercase tracking-wider text-neutral-900 mb-4 border-l-4 border-[#CC0000] pl-3">Mungkin Anda Melewatkan Ini</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        @foreach($melewatkanPosts as $mp)
                        <a href="{{ route('news.show', $mp->slug) }}" class="group flex gap-3">
                            <div class="w-24 h-16 bg-neutral-200 flex-shrink-0 overflow-hidden relative">
                                @if($mp->image_url)
                                    <img src="{{ media_url($mp->image_url) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500" alt="{{ $mp->title }}">
                                @else
                                    <div class="w-full h-full bg-neutral-300 flex items-center justify-center text-neutral-400">
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <span class="text-[9px] font-bold uppercase tracking-wider text-[#003399] block">{{ optional($mp->categories->first())->name ?? 'Nasional' }}</span>
                                <h4 class="text-xs font-bold text-neutral-800 leading-snug group-hover:text-[#003399] transition-colors line-clamp-3 font-serif">{{ $mp->title }}</h4>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>

                <!-- Comment Section (Livewire) -->
                <livewire:post-comments :post="$post" />

            </article>

            <!-- Right Column: Sidebar -->
            <aside class="w-full lg:w-[320px] flex-shrink-0">
                @if(isset($ads['sidebar_mrec']))
                <div class="w-full max-w-[300px] h-[250px] mx-auto relative flex items-center justify-center overflow-hidden border border-neutral-200 bg-white rounded-lg mb-8">
                    @if($ads['sidebar_mrec']->type === 'image')
                        <a href="{{ $ads['sidebar_mrec']->link_url }}" target="_blank" class="block w-full h-full">
                            <img src="{{ media_url($ads['sidebar_mrec']->image_url) }}" class="w-full h-full object-cover" alt="{{ $ads['sidebar_mrec']->name }}">
                        </a>
                    @else
                        {!! $ads['sidebar_mrec']->script_code !!}
                    @endif
                </div>
                @endif

                <!-- Terpopuler -->
                <div class="mb-8 border border-neutral-200 bg-white rounded-lg overflow-hidden">
                    <div class="bg-[#CC0000] px-4 py-2 flex items-center gap-2">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        <h3 class="text-xs font-black uppercase tracking-widest text-white">Terpopuler</h3>
                    </div>
                    <div class="p-4 space-y-4">
                        @foreach($popularPosts as $index => $pp)
                        <a href="{{ route('news.show', $pp->slug) }}" class="flex gap-3 group">
                            <span class="text-2xl font-black text-neutral-200 group-hover:text-[#003399] transition-colors leading-none w-6">{{ $index + 1 }}</span>
                            <div class="flex-1">
                                <h4 class="text-sm font-bold text-neutral-800 group-hover:text-[#003399] transition-colors leading-snug line-clamp-3">{{ $pp->title }}</h4>
                                <span class="text-[10px] text-neutral-400 mt-1 block" title="{{ $pp->published_at?->translatedFormat('l, d F Y | H:i') }} WIB">{{ $pp->published_at?->diffForHumans() }}</span>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>

                @if(isset($ads['sidebar_skyscraper']))
                <div class="w-full max-w-[300px] h-[600px] mx-auto relative flex items-center justify-center overflow-hidden border border-neutral-200 bg-white rounded-lg sticky top-24">
                    @if($ads['sidebar_skyscraper']->type === 'image')
                        <a href="{{ $ads['sidebar_skyscraper']->link_url }}" target="_blank" class="block w-full h-full">
                            <img src="{{ media_url($ads['sidebar_skyscraper']->image_url) }}" class="w-full h-full object-cover" alt="{{ $ads['sidebar_skyscraper']->name }}">
                        </a>
                    @else
                        {!! $ads['sidebar_skyscraper']->script_code !!}
                    @endif
                </div>
                @endif
            </aside>
        </div>

        <!-- === Berita Pilihan Untukmu === -->
        <section class="mt-10 pt-10 border-t border-neutral-200">
            <div class="flex items-center justify-between border-b-2 border-[#003399] pb-2 mb-6">
                <h2 class="text-sm font-black uppercase tracking-tight text-[#003399] flex items-center gap-2">
                    <span class="w-2.5 h-4 bg-[#FFCC00] inline-block"></span>BERITA PILIHAN UNTUKMU
                </h2>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach($pilihanPosts as $pp)
                    <article class="group space-y-2">
                        <a href="{{ route('news.show', $pp->slug) }}" class="block aspect-[16/10] bg-neutral-200 overflow-hidden border border-neutral-200 relative">
                            @if($pp->image_url)
                                <img src="{{ media_url($pp->image_url) }}" alt="{{ $pp->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full bg-neutral-300 flex items-center justify-center text-neutral-500 text-xs">KABAR</div>
                            @endif
                        </a>
                        <div class="space-y-0.5">
                            <span class="text-[9px] font-bold uppercase tracking-wider text-[#003399] block">{{ optional($pp->categories->first())->name ?? 'Nasional' }}</span>
                            <a href="{{ route('news.show', $pp->slug) }}" class="text-xs font-bold text-neutral-950 leading-snug group-hover:text-[#003399] line-clamp-2 font-serif block">{{ $pp->title }}</a>
                            <span class="text-[10px] text-neutral-400 block" title="{{ $pp->published_at?->translatedFormat('l, d F Y | H:i') }} WIB">{{ $pp->published_at?->diffForHumans() }}</span>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <!-- === Terkini Lainnya (Paginated) === -->
        <section class="mt-10 pt-10 border-t border-neutral-200">
            <div class="flex items-center justify-between border-b-2 border-[#003399] pb-2 mb-6">
                <h2 class="text-sm font-uppercase tracking-tight text-[#003399] flex items-center gap-2">
                    <span class="w-2.5 h-4 bg-[#CC0000] inline-block"></span>TERKINI LAINNYA
                </h2>
            </div>
            
            <div class="divide-y divide-neutral-200 border border-neutral-200 bg-white rounded-lg overflow-hidden">
                @foreach($terkiniPosts as $index => $tp)
                    <article class="px-4 py-4 hover:bg-neutral-50 transition-colors group">
                        <a href="{{ route('news.show', $tp->slug) }}" class="flex flex-row items-start gap-3">
                            <div class="w-[100px] sm:w-32 h-[70px] sm:h-[80px] bg-neutral-200 flex-shrink-0 overflow-hidden border border-neutral-200 relative">
                                @if($tp->image_url)
                                    <img src="{{ media_url($tp->image_url) }}" alt="{{ $tp->title }}" loading="lazy" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full bg-neutral-300 flex items-center justify-center text-neutral-500 text-xs font-bold">KABAR</div>
                                @endif
                            </div>
                            <div class="space-y-1.5 flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-x-1.5 gap-y-0.5 text-[10px] sm:text-[11px] text-neutral-400">
                                    <span class="font-bold uppercase tracking-wider text-[#003399]">{{ optional($tp->categories->first())->name ?? 'Nasional' }}</span>
                                    <span class="h-3 w-px bg-neutral-300"></span>
                                    <span class="font-semibold text-[#CC0000]">{{ $tp->user->name ?? 'Redaksi' }}</span>
                                    <span>•</span>
                                    <span title="{{ $tp->published_at->translatedFormat('l, d F Y | H:i') }} WIB">{{ $tp->published_at->diffForHumans() }}</span>
                                </div>
                                <h3 class="text-xs sm:text-sm font-bold text-neutral-950 leading-snug group-hover:text-[#003399] transition-colors line-clamp-2 font-serif">{{ $tp->title }}</h3>
                                <p class="text-[11px] text-neutral-600 leading-relaxed line-clamp-1 hidden sm:block">{{ Str::limit(Str::squish(html_entity_decode(strip_tags($tp->content))), 120) }}</p>
                            </div>
                        </a>
                    </article>
                @endforeach

                @if ($terkiniPosts->hasPages())
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-3 sm:px-4 py-3 border-t border-neutral-200 text-xs bg-white">
                        <span class="text-neutral-500 font-medium text-center sm:text-left">Halaman {{ $terkiniPosts->currentPage() }} dari {{ $terkiniPosts->lastPage() }}</span>
                        <div class="flex items-center justify-center gap-1.5 overflow-x-auto max-w-full scrollbar-none">
                            @if ($terkiniPosts->onFirstPage())
                                <button disabled class="px-2.5 sm:px-3 py-1.5 border border-neutral-200 font-bold text-neutral-300 cursor-not-allowed rounded whitespace-nowrap">←</button>
                            @else
                                <a href="{{ $terkiniPosts->previousPageUrl() }}" class="px-2.5 sm:px-3 py-1.5 border border-neutral-300 font-bold text-neutral-700 hover:bg-[#003399] hover:text-white hover:border-[#003399] transition-colors rounded whitespace-nowrap">←</a>
                            @endif

                            @foreach ($terkiniPosts->getUrlRange(max(1, $terkiniPosts->currentPage() - 1), min($terkiniPosts->lastPage(), $terkiniPosts->currentPage() + 1)) as $page => $url)
                                @if ($page == $terkiniPosts->currentPage())
                                    <span class="px-3 py-1.5 bg-[#003399] text-white font-bold border border-[#003399] rounded">{{ $page }}</span>
                                @else
                                    <a href="{{ $url }}" class="px-3 py-1.5 border border-neutral-300 font-bold text-neutral-700 hover:bg-[#003399] hover:text-white hover:border-[#003399] transition-colors rounded">{{ $page }}</a>
                                @endif
                            @endforeach

                            @if ($terkiniPosts->hasMorePages())
                                <a href="{{ $terkiniPosts->nextPageUrl() }}" class="px-2.5 sm:px-3 py-1.5 border border-neutral-300 font-bold text-neutral-700 hover:bg-[#003399] hover:text-white hover:border-[#003399] transition-colors rounded whitespace-nowrap">→</a>
                            @else
                                <button disabled class="px-2.5 sm:px-3 py-1.5 border border-neutral-200 font-bold text-neutral-300 cursor-not-allowed rounded whitespace-nowrap">→</button>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </section>
    </main>

    <!-- BACA JUGA INJECTOR JS -->
    <div id="baca-juga-data" style="display:none;">
        @foreach($bacaJugaPosts as $bp)
        <div class="baca-juga-item">
            <div class="my-6 p-3 bg-neutral-50 border-l-4 border-[#CC0000] shadow-sm flex items-center gap-3 rounded-r-md">
                <div class="w-16 h-10 bg-neutral-200 overflow-hidden flex-shrink-0 relative border border-neutral-200">
                    @if($bp->image_url)
                        <img src="{{ media_url($bp->image_url) }}" class="w-full h-full object-cover" alt="{{ $bp->title }}">
                    @else
                        <div class="w-full h-full bg-neutral-300 flex items-center justify-center text-[8px] font-bold text-neutral-400">KABAR</div>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <span class="text-[9px] font-bold uppercase tracking-wider text-neutral-400 block mb-0.5">BACA JUGA:</span>
                    <a href="{{ route('news.show', $bp->slug) }}" class="text-xs sm:text-sm font-bold text-neutral-900 hover:text-[#003399] leading-snug line-clamp-2 block font-serif">{{ $bp->title }}</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <script>
        // CAPTION GAMBAR RICH TEXT: paragraf tepat di bawah gambar dirender sebagai caption
        document.addEventListener("DOMContentLoaded", function() {
            var article = document.getElementById("article-content");
            if (!article) return;

            // Caption bawaan editor tidak boleh ikut jadi link gambar
            article.querySelectorAll("a > figcaption").forEach(function(caption) {
                var link = caption.parentElement;
                link.parentNode.insertBefore(caption, link.nextSibling);
            });

            article.querySelectorAll("img").forEach(function(img) {
                var node = img;
                while (node.parentElement && node.parentElement !== article && ['FIGURE', 'P'].indexOf(node.parentElement.tagName) !== -1) {
                    node = node.parentElement;
                }
                var next = node.nextElementSibling;
                if (!next || next.tagName !== 'P') return;
                var text = next.textContent.trim();
                if (!text || text.length > 150) return;

                var caption = document.createElement('figcaption');
                caption.textContent = text;
                next.replaceWith(caption);
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var article = document.getElementById("article-content");
            var bacaJugaItems = document.querySelectorAll("#baca-juga-data .baca-juga-item");
            
            if (!article || bacaJugaItems.length === 0) return;
            
            var paragraphs = article.querySelectorAll("p");
            
            // Insert after 2nd paragraph
            if (paragraphs.length >= 2 && bacaJugaItems.length >= 1) {
                paragraphs[1].insertAdjacentHTML('afterend', bacaJugaItems[0].innerHTML);
            }
            
            // Insert after 5th paragraph if exists
            if (paragraphs.length >= 5 && bacaJugaItems.length >= 2) {
                paragraphs[4].insertAdjacentHTML('afterend', bacaJugaItems[1].innerHTML);
            }
        });
    </script>

    @include('partials.footer')

</div>

    <script>
    (function() {
        var shareBtn = document.getElementById('native-share-btn');
        var copyBtn = document.getElementById('copy-link-btn');
        var pageUrl = window.location.href;
        var pageTitle = document.title;

        function copyToClipboard(text) {
            if (navigator.clipboard && window.isSecureContext) {
                return navigator.clipboard.writeText(text);
            }
            var textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.setAttribute('readonly', '');
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            try { document.execCommand('copy'); } catch (e) {}
            document.body.removeChild(textarea);
            return Promise.resolve();
        }

        if (shareBtn && typeof navigator.share === 'function') {
            shareBtn.classList.remove('hidden');
            shareBtn.classList.add('flex');
            shareBtn.addEventListener('click', function() {
                navigator.share({ title: pageTitle, url: pageUrl }).catch(function() {});
            });
        }

        if (copyBtn) {
            var linkIcon = copyBtn.querySelector('[data-copy-icon="link"]');
            var checkIcon = copyBtn.querySelector('[data-copy-icon="check"]');
            var resetTimer = null;

            copyBtn.addEventListener('click', function() {
                copyToClipboard(pageUrl).then(function() {
                    linkIcon.classList.add('hidden');
                    checkIcon.classList.remove('hidden');
                    copyBtn.classList.add('bg-green-600', 'text-white');

                    clearTimeout(resetTimer);
                    resetTimer = setTimeout(function() {
                        linkIcon.classList.remove('hidden');
                        checkIcon.classList.add('hidden');
                        copyBtn.classList.remove('bg-green-600', 'text-white');
                    }, 1600);
                });
            });
        }
    })();
    </script>

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
