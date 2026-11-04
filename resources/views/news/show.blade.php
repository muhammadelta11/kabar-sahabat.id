@extends('layouts.app')

@section('title', $post->title . ' - Kabar Sahabat')

@section('canonical')
    <link rel="canonical" href="{{ route('news.show', $post->slug) }}">
@endsection

@section('meta')
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    <meta name="description" content="{{ Str::limit(Str::squish(html_entity_decode(strip_tags($post->excerpt ?? $post->content))), 160) }}">
    <meta name="author" content="{{ $post->penulis ?? $post->user->name ?? 'Redaksi' }}">
    @php
        $ogTitle = $post->title;
        $ogDescription = Str::limit(Str::squish(html_entity_decode(strip_tags($post->excerpt ?: $post->content))), 160);
        $ogUrl = route('news.show', $post->slug);
        $ogImage = $post->image_url ? media_absolute_url($post->image_url) : null;
    @endphp
    <!-- Open Graph / Facebook / WhatsApp -->
    <meta property="og:type" content="article">
    <meta property="og:site_name" content="Kabar Sahabat">
    <meta property="og:url" content="{{ $ogUrl }}">
    <meta property="og:title" content="{{ $ogTitle }}">
    <meta property="og:description" content="{{ $ogDescription }}">
    <meta property="article:published_time" content="{{ $post->published_at?->toIso8601String() }}">
    <meta property="article:author" content="{{ $post->penulis ?? $post->user->name ?? 'Redaksi' }}">
    @foreach($post->categories as $cat)
        <meta property="article:section" content="{{ $cat->name }}">
    @endforeach
    <meta property="og:locale" content="id_ID">
    <meta property="article:modified_time" content="{{ $post->updated_at?->toIso8601String() }}">
    @if($ogImage)
        <meta property="og:image" content="{{ $ogImage }}">
        <meta property="og:image:url" content="{{ $ogImage }}">
        @if(str_starts_with($ogImage, 'https://'))
            <meta property="og:image:secure_url" content="{{ $ogImage }}">
        @endif
        <meta property="og:image:type" content="image/jpeg">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:alt" content="{{ $post->image_caption ?? $ogTitle }}">
    @endif
    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ $ogUrl }}">
    <meta name="twitter:title" content="{{ $ogTitle }}">
    <meta name="twitter:description" content="{{ $ogDescription }}">
    @if($ogImage)
        <meta name="twitter:image" content="{{ $ogImage }}">
        <meta name="twitter:image:alt" content="{{ $post->image_caption ?? $ogTitle }}">
    @endif
    @php
        $publisherLogo = media_absolute_url($siteSettings->logo_path ?? null);
        $articleSections = $post->categories->pluck('name')->values()->all();
    @endphp
    <!-- JSON-LD: Article structured data (Google News / Rich Results) -->
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@type": "NewsArticle",
        "headline": {{ Js::from($post->title) }},
        "description": {{ Js::from(Str::limit(Str::squish(html_entity_decode(strip_tags($post->excerpt ?? $post->content))), 160)) }},
        "url": {{ Js::from(route('news.show', $post->slug)) }},
        "inLanguage": "id-ID",
        "isAccessibleForFree": true,
        @if($articleSections !== [])
        "articleSection": {{ Js::from(count($articleSections) === 1 ? $articleSections[0] : $articleSections) }},
        @endif
        @if($post->image_url)
        "image": {
            "@type": "ImageObject",
            "url": {{ Js::from(media_absolute_url($post->image_url)) }},
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
            "@type": "NewsMediaOrganization",
            "name": "Kabar Sahabat",
            "url": {{ Js::from(url('/')) }}
            @if($publisherLogo)
            ,"logo": {
                "@type": "ImageObject",
                "url": {{ Js::from($publisherLogo) }}
            }
            @endif
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
<div class="bg-surface min-h-screen text-ink font-sans antialiased w-full" id="pmii-news-wrap">

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

    <x-site.header :ads="$ads" />

    @include('news.partials.article-main')

    <div id="baca-juga-data" class="hidden" aria-hidden="true">
        @foreach($bacaJugaPosts as $bp)
            <div class="baca-juga-item">
                <x-news.baca-juga :post="$bp" />
            </div>
        @endforeach
    </div>

    <x-site.footer />
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var article = document.getElementById('article-content');
    if (!article) return;

    article.querySelectorAll('a > figcaption').forEach(function (caption) {
        var link = caption.parentElement;
        link.parentNode.insertBefore(caption, link.nextSibling);
    });

    article.querySelectorAll('img').forEach(function (img) {
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
document.addEventListener('DOMContentLoaded', function () {
    var article = document.getElementById('article-content');
    var bacaJugaItems = document.querySelectorAll('#baca-juga-data .baca-juga-item');
    if (!article || bacaJugaItems.length === 0) return;

    var paragraphs = Array.from(article.querySelectorAll('p'));
    var count = bacaJugaItems.length;

    // Artikel pendek: sisipkan semua di akhir isi agar tetap tampil minimal 3.
    if (paragraphs.length < 2) {
        for (var i = 0; i < count; i++) {
            article.insertAdjacentHTML('beforeend', bacaJugaItems[i].innerHTML);
        }
        return;
    }

    var used = Object.create(null);
    var plan = [];

    for (var i = 0; i < count; i++) {
        // Sebar rata: posisi ke-(i+1) dari (count+1) segmen artikel.
        var idx = Math.round((i + 1) * paragraphs.length / (count + 1)) - 1;
        idx = Math.max(0, Math.min(paragraphs.length - 1, idx));

        while (used[idx] && idx < paragraphs.length - 1) {
            idx++;
        }

        if (used[idx]) {
            plan.push({ mode: 'end', html: bacaJugaItems[i].innerHTML });
            continue;
        }

        used[idx] = true;
        plan.push({ mode: 'after', index: idx, html: bacaJugaItems[i].innerHTML });
    }

    plan
        .filter(function (slot) { return slot.mode === 'after'; })
        .sort(function (a, b) { return b.index - a.index; })
        .forEach(function (slot) {
            paragraphs[slot.index].insertAdjacentHTML('afterend', slot.html);
        });

    plan
        .filter(function (slot) { return slot.mode === 'end'; })
        .forEach(function (slot) {
            article.insertAdjacentHTML('beforeend', slot.html);
        });
});
</script>

<script>
(function () {
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
        shareBtn.addEventListener('click', function () {
            navigator.share({ title: pageTitle, url: pageUrl }).catch(function () {});
        });
    }

    if (copyBtn) {
        var linkIcon = copyBtn.querySelector('[data-copy-icon="link"]');
        var checkIcon = copyBtn.querySelector('[data-copy-icon="check"]');
        var resetTimer = null;

        copyBtn.addEventListener('click', function () {
            copyToClipboard(pageUrl).then(function () {
                linkIcon.classList.add('hidden');
                checkIcon.classList.remove('hidden');
                copyBtn.classList.add('bg-green-600', 'text-white');

                clearTimeout(resetTimer);
                resetTimer = setTimeout(function () {
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
(function () {
    var leftAd = document.getElementById('skin-ad-left');
    var rightAd = document.getElementById('skin-ad-right');
    if (!leftAd && !rightAd) return;
    var MIN_WIDTH = 1480;
    var footer = document.querySelector('footer');

    function toggleSkinAds() {
        var wideEnough = window.innerWidth >= MIN_WIDTH;
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
