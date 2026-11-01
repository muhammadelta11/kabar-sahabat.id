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
<div class="bg-surface min-h-screen text-ink font-sans antialiased w-full">

    <x-site.header :show-trending="false" />

    <main class="site-container py-6 sm:py-8 space-y-5">
        <x-news.breadcrumb :items="[
            ['label' => 'Beranda', 'href' => route('news.index')],
            ['label' => trim($__env->yieldContent('page-title'))],
        ]" />

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 lg:gap-6">
            <aside class="lg:col-span-3">
                <nav class="bg-white border border-neutral-100 shadow-sm rounded-2xl overflow-hidden lg:sticky lg:top-16" aria-label="Informasi">
                    <div class="px-4 py-3 border-b border-neutral-100">
                        <h2 class="text-sm font-black text-brand font-display flex items-center gap-2">
                            <span class="w-1 h-4 rounded-full bg-accent"></span>
                            Informasi
                        </h2>
                    </div>
                    <div class="flex lg:flex-col overflow-x-auto divide-x lg:divide-x-0 lg:divide-y divide-neutral-50 scrollbar-none">
                        @foreach($infoPages as $routeName => $label)
                            <a
                                href="{{ route($routeName) }}"
                                class="px-4 py-3 text-sm whitespace-nowrap transition-colors {{ request()->routeIs($routeName) ? 'font-bold text-brand bg-brand/5 lg:border-l-4 lg:border-l-accent' : 'text-ink-muted hover:text-brand hover:bg-surface/80' }}"
                            >
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>
                </nav>
            </aside>

            <article class="lg:col-span-9 bg-white border border-neutral-100 shadow-sm rounded-2xl overflow-hidden">
                <div class="px-5 sm:px-6 py-3.5 border-b border-neutral-100">
                    <h1 class="text-sm sm:text-base font-black tracking-tight text-ink font-display flex items-center gap-2">
                        <span class="w-1 h-5 rounded-full bg-accent flex-shrink-0"></span>
                        @yield('page-title')
                    </h1>
                </div>
                <div class="px-5 sm:px-8 py-6 md:px-10 md:py-8 text-ink-muted leading-relaxed [&_a]:text-brand [&_a]:font-medium hover:[&_a]:underline">
                    @yield('page-content')
                </div>
            </article>
        </div>
    </main>

    <x-site.footer />
</div>
@endsection
