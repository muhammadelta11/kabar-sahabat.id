<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#003399">
        <meta name="color-scheme" content="light">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
        <title>@yield('title', 'Kabar Sahabat')</title>
        <meta property="og:site_name" content="Kabar Sahabat">
        <meta property="og:locale" content="id_ID">
        <link rel="alternate" type="application/rss+xml" title="Kabar Sahabat - RSS Feed" href="{{ url('/feed.xml') }}">
        @yield('canonical')
        @yield('meta')

<!-- Fonts non-blocking agar First Contentful Paint lebih cepat -->
<meta name="google-site-verification" content="8wzdnD6Xk1071f1tHkgNEPHnoKYY0S8bhahuHf9Stwc" />
@php
    $gaMeasurementId = filled($siteSettings->ga_measurement_id ?? null)
        ? $siteSettings->ga_measurement_id
        : 'G-W85WZ4589W';
@endphp
@if(filled($gaMeasurementId))
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaMeasurementId }}"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '{{ $gaMeasurementId }}');
</script>
@endif
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
<noscript>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
</noscript>
{{-- Design tokens: brand / accent / surface — lihat resources/css/app.css @theme --}}

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @if(isset($siteSettings) && $siteSettings->analytics_code)
            {!! $siteSettings->analytics_code !!}
        @endif
    </head>
    <body class="bg-white text-neutral-950 min-h-screen flex flex-col antialiased">

        <main class="flex-grow w-full min-w-0">
            @yield('content')
        </main>

        <x-site.bottom-nav />

        <!-- Lenis: defer + hanya desktop -->
        <script src="https://cdn.jsdelivr.net/npm/lenis@1.1.13/dist/lenis.min.js" defer></script>
        <script defer>
            document.addEventListener('DOMContentLoaded', function () {
                if (typeof Lenis === 'undefined') return;
                if (window.matchMedia('(pointer: coarse)').matches || window.innerWidth < 1024) return;

                var lenis = new Lenis({
                    duration: 1.2,
                    easing: function (t) { return Math.min(1, 1.001 - Math.pow(2, -10 * t)); },
                    smoothWheel: true
                });
                window.lenis = lenis;

                function raf(time) {
                    lenis.raf(time);
                    requestAnimationFrame(raf);
                }
                requestAnimationFrame(raf);
            });
        </script>

        @if(isset($siteSettings) && $siteSettings->whatsapp_enabled && $siteSettings->whatsapp_number)
            @php
                $waLink = 'https://wa.me/'.preg_replace('/\D/', '', $siteSettings->whatsapp_number);
                if ($siteSettings->whatsapp_message) {
                    $waLink .= '?text='.rawurlencode($siteSettings->whatsapp_message);
                }
            @endphp
            <a
                href="{{ $waLink }}"
                target="_blank"
                rel="noopener noreferrer"
                aria-label="Chat WhatsApp"
                class="fixed bottom-20 right-4 md:bottom-6 md:right-6 z-50 flex h-12 w-12 sm:h-14 sm:w-14 items-center justify-center rounded-full bg-[#25D366] text-white shadow-lg transition hover:scale-110 hover:shadow-xl"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-7 w-7">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                </svg>
            </a>
        @endif
    </body>
</html>
