@extends('layouts.app')

@php
    $activeCategory = request()->filled('category')
        ? ($navCategories->firstWhere('slug', request('category')) ?? null)
        : null;
    $isSearch = request()->filled('q');
    $tagline = $siteSettings->tagline ?? 'Portal berita independen';
    $pageTitle = $isSearch
        ? 'Pencarian: '.request('q').' - Kabar Sahabat'
        : ($activeCategory
            ? 'Kanal '.$activeCategory->name.' - Kabar Sahabat'
            : 'Kabar Sahabat - Portal Berita');
    $pageDescription = $isSearch
        ? 'Hasil pencarian berita untuk "'.request('q').'" di Kabar Sahabat.'
        : ($activeCategory
            ? 'Berita terkini kanal '.$activeCategory->name.' di Kabar Sahabat. '.$tagline
            : 'Kabar Sahabat — '.$tagline.'. Portal berita independen yang menyajikan berita pergerakan, nasional, kaderisasi, opini, dan tokoh terkini.');
    $canonicalUrl = $activeCategory
        ? url('/?category='.$activeCategory->slug)
        : url('/');
    if ($posts->currentPage() > 1) {
        $canonicalUrl .= (str_contains($canonicalUrl, '?') ? '&' : '?').'page='.$posts->currentPage();
    }
    // Homepage share: logo brand, bukan gambar berita terbaru (supaya tidak "random").
    $orgLogo = media_absolute_url($siteSettings->logo_path ?? null);
    $ogImage = $orgLogo ?: url('images/brand/logo-dark.png');
    $ogCard = 'summary';
    $sameAs = collect([
        $siteSettings->instagram_url ?? null,
        $siteSettings->twitter_url ?? null,
        $siteSettings->youtube_url ?? null,
        $siteSettings->facebook_url ?? null,
        $siteSettings->threads_url ?? null,
        $siteSettings->tiktok_url ?? null,
    ])->filter()->values()->all();
@endphp

@section('title', $pageTitle)

@section('canonical')
    <link rel="canonical" href="{{ $canonicalUrl }}">
    @if($posts->currentPage() > 1)
        <link rel="prev" href="{{ $posts->previousPageUrl() }}">
    @endif
    @if($posts->hasMorePages())
        <link rel="next" href="{{ $posts->nextPageUrl() }}">
    @endif
@endsection

@section('meta')
    <meta name="robots" content="{{ $isSearch ? 'noindex, follow' : 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1' }}">
    <meta name="description" content="{{ $pageDescription }}">
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:alt" content="Logo Kabar Sahabat">
    <!-- Twitter / WhatsApp -->
    <meta name="twitter:card" content="{{ $ogCard }}">
    <meta name="twitter:url" content="{{ $canonicalUrl }}">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">
    <!-- JSON-LD: WebSite + Sitelinks SearchAction -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@type": "WebSite",
        "name": "Kabar Sahabat",
        "url": {{ Js::from(url('/')) }},
        "inLanguage": "id-ID",
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
    <!-- JSON-LD: NewsMediaOrganization -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@type": "NewsMediaOrganization",
        "name": "Kabar Sahabat",
        "url": {{ Js::from(url('/')) }},
        "description": {{ Js::from($tagline) }}
        @if($orgLogo)
        ,"logo": {
            "@type": "ImageObject",
            "url": {{ Js::from($orgLogo) }}
        }
        @endif
        @if($sameAs !== [])
        ,"sameAs": {{ Js::from($sameAs) }}
        @endif
    }
    </script>
@endsection

@section('content')
<!-- ============================================================
     KOMPAS.COM ~90% MATCH PORTAL BERITA — PMII WALISONGO
     ============================================================ -->

<div class="bg-surface min-h-screen text-ink font-sans antialiased w-full" id="pmii-news-wrap">

    <!-- SKIN AD: KIRI & KANAN (hanya desktop lebar, default hidden agar tidak geser HP) -->
    <div class="fixed inset-0 pointer-events-none z-[200] hidden lg:block overflow-hidden" style="top: 120px;" id="skin-ads-overlay">
        <div style="position: relative; max-width: var(--site-max); margin: 0 auto; height: 100%;">
            @if(isset($ads['skin_ad_left']))
            <div class="hidden" style="position: absolute; right: 100%; top: 0; padding-right: 8px; pointer-events: auto;" id="skin-ad-left">
                <div class="w-[160px] h-[600px] relative overflow-hidden bg-neutral-200 rounded-xl">
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
                <div class="w-[160px] h-[600px] relative overflow-hidden bg-neutral-200 rounded-xl">
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

    <x-site.header
        :latest-posts="$latestPosts"
        :trending-posts="$trendingPosts"
        :ads="$ads"
    />

    @include('news.partials.home-main')
    <x-site.footer />

</div>

<script>
(function() {
    var leftAd  = document.getElementById('skin-ad-left');
    var rightAd = document.getElementById('skin-ad-right');
    if (!leftAd && !rightAd) return;
    var MIN_WIDTH = 1480;
    var footer = document.querySelector('footer');

    function toggleSkinAds() {
        var wideEnough = window.innerWidth >= MIN_WIDTH;
        // Sembunyikan saat footer masuk viewport supaya tidak menimpa footer
        var footerInView = footer && footer.getBoundingClientRect().top < window.innerHeight - 24;
        var show = wideEnough && !footerInView;

        [leftAd, rightAd].forEach(function (el) {
            if (!el) return;
            el.classList.toggle('hidden', !show);
            el.style.display = show ? 'block' : 'none';
        });
    }
    toggleSkinAds();
    window.addEventListener('resize', toggleSkinAds, { passive: true });
    window.addEventListener('scroll', toggleSkinAds, { passive: true });
})();
</script>
@endsection