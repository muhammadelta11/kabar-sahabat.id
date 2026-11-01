@props([
    'latestPosts' => null,
    'trendingPosts' => null,
    'ads' => null,
    'showTrending' => true,
])

@php
    $latestPosts = $latestPosts ?? \App\Support\NewsCache::latestPosts(24);
    $trendingPosts = $trendingPosts ?? \App\Support\NewsCache::trendingPosts(10);
    $ads = $ads ?? \App\Support\NewsCache::activeAds();
    $settings = $siteSettings ?? \App\Support\NewsCache::siteSettings();
@endphp

<header {{ $attributes->class(['relative']) }}>
    {{-- Chrome tetap di atas (fixed) — anti Lenis/overflow merusak sticky --}}
    <div id="site-header-fixed" class="fixed top-0 inset-x-0 z-50 bg-surface shadow-sm transition-colors" data-header-shell>
        <x-site.top-bar :latest-posts="$latestPosts" />

        <div class="md:hidden bg-white border-b border-neutral-100" data-mobile-brand>
            <div class="site-container h-12 flex items-center justify-between gap-3">
                <a href="{{ route('news.index') }}" class="flex items-center gap-2 min-w-0">
                    @if(filled($settings->logo_path ?? null))
                        <img src="{{ media_url($settings->logo_path) }}" class="h-8 w-auto max-w-[160px] object-contain object-left" alt="Kabar Sahabat">
                    @else
                        <span class="w-8 h-8 rounded-lg bg-brand text-white flex items-center justify-center font-black text-sm flex-shrink-0">K</span>
                        <span class="font-display font-black text-base text-brand truncate">
                            kabarsahabat<span class="text-accent">.id</span>
                        </span>
                    @endif
                </a>
                @auth
                    @php
                        $mobileUser = auth()->user();
                        $mobileIsPanel = $mobileUser?->role instanceof \App\UserRole && $mobileUser->role->canAccessPanel();
                    @endphp
                    @if($mobileIsPanel)
                        <a href="{{ url('/admin') }}" class="flex-shrink-0 text-[10px] font-bold uppercase tracking-wide px-2.5 py-1.5 rounded-full bg-brand text-white">Panel</a>
                    @else
                        <a href="{{ route('account.edit') }}" class="flex-shrink-0 text-[10px] font-bold uppercase tracking-wide px-2.5 py-1.5 rounded-full bg-brand text-white">Akun</a>
                    @endif
                @else
                    <a href="{{ route('auth.register') }}" class="flex-shrink-0 text-[10px] font-bold uppercase tracking-wide px-2.5 py-1.5 rounded-full bg-brand text-white">Bergabung</a>
                @endauth
            </div>
        </div>

        {{-- Logo di atas menu (desktop); menyusut saat scroll --}}
        <div class="max-md:hidden" id="header-branding" data-site-branding>
            <x-site.branding :ads="$ads" />
        </div>

        <x-site.nav />
    </div>

    <div id="site-header-spacer" aria-hidden="true"></div>

    @if($showTrending)
        <x-site.trending :posts="$trendingPosts" />
    @endif
</header>

<script>
(function () {
    var fixed = document.getElementById('site-header-fixed');
    var spacer = document.getElementById('site-header-spacer');
    var branding = document.getElementById('header-branding');
    var stickyLogo = document.getElementById('nav-sticky-logo');
    var navContainer = document.getElementById('main-nav-container');
    var navBar = document.getElementById('main-nav-bar');
    if (!fixed || !spacer) return;

    function syncSpacer() {
        spacer.style.height = fixed.offsetHeight + 'px';
    }

    function onScroll() {
        var isDesktop = window.matchMedia('(min-width: 768px)').matches;
        var compact = isDesktop && window.scrollY > 24;

        if (branding) {
            branding.classList.toggle('hidden', compact);
        }

        if (stickyLogo) {
            stickyLogo.classList.toggle('hidden', !compact);
            stickyLogo.classList.toggle('flex', compact);
        }

        // Compact desktop: chrome biru, menu tetap sejajar site-container (jangan full-bleed)
        fixed.classList.toggle('bg-surface', !compact);
        fixed.classList.toggle('bg-brand', compact);
        fixed.classList.toggle('shadow-sm', !compact);
        fixed.classList.toggle('shadow-md', compact);

        if (navContainer) {
            navContainer.classList.toggle('md:pt-2', !compact);
        }

        if (navBar) {
            navBar.classList.toggle('md:rounded-t-2xl', !compact);
            navBar.classList.toggle('shadow-md', !compact);
        }

        syncSpacer();
    }

    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onScroll, { passive: true });
    if (typeof ResizeObserver !== 'undefined') {
        new ResizeObserver(syncSpacer).observe(fixed);
    }
})();
</script>
