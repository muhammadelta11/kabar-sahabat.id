@extends('layouts.app')

@section('title', $__env->yieldContent('page-title') . ' - Kabar Sahabat')

@section('canonical')
    <link rel="canonical" href="{{ url()->current() }}">
@endsection

@section('meta')
    <meta name="description" content="@yield('page-description')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('page-title') - Kabar Sahabat">
    <meta property="og:description" content="@yield('page-description')">
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="@yield('page-title') - Kabar Sahabat">
    <meta name="twitter:description" content="@yield('page-description')">
@endsection

@section('content')
@php
    $infoPages = [
        'pages.redaksi' => 'Redaksi',
        'pages.tentang-kami' => 'Tentang Kami',
        'pages.kontak' => 'Kontak',
        'pages.pedoman-media-siber' => 'Pedoman Media Siber',
    ];
@endphp
<div class="bg-[#F8F9FA] min-h-screen text-neutral-900 font-sans antialiased overflow-x-hidden w-full">

    <!-- TOP BAR -->
    <div class="bg-neutral-900 text-white text-[11px] border-b border-neutral-800">
        <div class="max-w-[1080px] mx-auto px-4 sm:px-6 lg:px-8 h-8 flex items-center">
            <span class="text-neutral-400 font-medium whitespace-nowrap">{{ now()->translatedFormat('l, d F Y') }}</span>
        </div>
    </div>

    <!-- BRANDING -->
    <div class="bg-white border-b border-neutral-200 py-4">
        <div class="max-w-[1080px] mx-auto px-4 sm:px-6 lg:px-8 flex items-center">
            <a href="/" class="flex items-center gap-3 flex-shrink-0">
                @if(isset($siteSettings) && $siteSettings->logo_path)
                    <img src="{{ asset('storage/' . $siteSettings->logo_path) }}" class="h-14 w-auto max-w-[300px] object-contain object-left" alt="Logo">
                @else
                    <div class="w-11 h-11 bg-[#003399] flex items-center justify-center text-white font-black text-2xl border-2 border-[#FFCC00]">K</div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-1 leading-none">
                            <span class="text-3xl font-black tracking-tighter text-[#003399] font-serif">KABAR</span>
                            <span class="text-3xl font-black tracking-tighter text-[#FFCC00] font-serif">SAHABAT</span>
                        </div>
                        <span class="text-[9px] text-neutral-500 font-bold uppercase tracking-widest mt-1">{{ $siteSettings->tagline ?? 'Jernih Melihat Pergerakan' }}</span>
                    </div>
                @endif
            </a>
        </div>
    </div>

    <!-- NAVBAR KANAL -->
    <nav class="sticky top-0 z-50 bg-[#003399] border-b-4 border-[#FFCC00] shadow-md">
        <div class="max-w-[1080px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-11">
                <div class="relative flex-1 min-w-0 h-full">
                    <div class="flex items-center overflow-x-auto h-full pr-6 scrollbar-none" style="scrollbar-width:none;">
                        <a href="/" class="h-full flex items-center px-4 text-[11px] font-bold uppercase tracking-wider text-white/90 hover:text-white hover:bg-[#002b80] transition-colors whitespace-nowrap">Beranda</a>
                        @foreach($navCategories as $navCategory)
                        <a href="/?category={{ $navCategory->slug }}" class="h-full flex items-center px-4 text-[11px] font-bold uppercase tracking-wider text-white/90 hover:text-white hover:bg-[#002b80] transition-colors whitespace-nowrap">{{ $navCategory->name }}</a>
                        @endforeach
                    </div>
                    <div class="pointer-events-none absolute inset-y-0 right-0 w-12" style="background: linear-gradient(to left, #003399 20%, rgba(0,51,153,0))"></div>
                </div>

                <livewire:search-navbar />
            </div>
        </div>
    </nav>

    <!-- BREADCRUMB -->
    <div class="bg-white border-b border-neutral-200">
        <div class="max-w-[1080px] mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center gap-2 text-xs text-neutral-500">
            <a href="/" class="hover:text-[#003399] transition-colors">Beranda</a>
            <span>›</span>
            <span class="text-neutral-900 font-medium">@yield('page-title')</span>
        </div>
    </div>

    <main class="max-w-[1080px] mx-auto px-4 sm:px-6 lg:px-8 py-8 grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- MENU INFORMASI -->
        <aside class="lg:col-span-3">
            <div class="bg-white border border-neutral-200 shadow-sm rounded-lg overflow-hidden lg:sticky lg:top-16">
                <div class="border-b-2 border-[#003399] px-4 py-2.5">
                    <h2 class="text-sm font-black uppercase tracking-tight text-[#003399]">Informasi</h2>
                </div>
                <div class="flex lg:flex-col overflow-x-auto divide-x lg:divide-x-0 lg:divide-y divide-neutral-100" style="scrollbar-width:none;">
                    @foreach($infoPages as $routeName => $label)
                    <a href="{{ route($routeName) }}" class="px-4 py-3 text-sm whitespace-nowrap transition-colors {{ request()->routeIs($routeName) ? 'font-bold text-[#003399] bg-[#003399]/5 lg:border-l-4 lg:border-l-[#FFCC00]' : 'text-neutral-700 hover:text-[#003399] hover:bg-neutral-50' }}">{{ $label }}</a>
                    @endforeach
                </div>
            </div>
        </aside>

        <!-- KONTEN -->
        <article class="lg:col-span-9 bg-white border border-neutral-200 shadow-sm rounded-lg overflow-hidden">
            <div class="border-b-2 border-[#003399] px-6 py-3 flex items-center gap-2">
                <span class="w-2.5 h-4 bg-[#FFCC00] inline-block"></span>
                <h1 class="text-sm font-black uppercase tracking-tight text-[#003399]">@yield('page-title')</h1>
            </div>
            <div class="px-6 py-6 md:px-10 md:py-8 text-neutral-700 leading-relaxed">
                @yield('page-content')
            </div>
        </article>
    </main>

    @include('partials.footer')
</div>
@endsection
