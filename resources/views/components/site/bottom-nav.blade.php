@php
    $isHome = request()->routeIs('news.index') && ! request()->has('category') && ! request()->has('q');
    $isKanal = request()->routeIs('news.index') && request()->filled('category');
    $isSearch = request()->routeIs('news.index') && request()->filled('q');
    $isKirim = request()->routeIs('submissions.*');
    $isAccount = request()->routeIs('account.*') || request()->routeIs('auth.pending');
    $isInfo = request()->routeIs('pages.*');
    $isLoggedIn = auth()->check();
@endphp

<nav
    class="md:hidden fixed inset-x-0 bottom-0 z-[70] bg-white/95 backdrop-blur-md border-t border-neutral-200 shadow-[0_-4px_20px_-8px_rgb(0_0_0_/0.12)]"
    style="padding-bottom: env(safe-area-inset-bottom, 0px);"
    aria-label="Navigasi bawah"
>
    <div class="grid grid-cols-5 h-[3.75rem] max-w-lg mx-auto">
        <a
            href="{{ route('news.index') }}"
            class="flex flex-col items-center justify-center gap-0.5 py-2 {{ $isHome ? 'text-brand' : 'text-ink-muted' }}"
        >
            <svg class="w-5 h-5" fill="{{ $isHome ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1h-2z"/>
            </svg>
            <span class="text-[10px] font-bold">Beranda</span>
        </a>

        <a
            href="{{ url('/#jelajahi-kategori') }}"
            class="flex flex-col items-center justify-center gap-0.5 py-2 {{ $isKanal ? 'text-brand' : 'text-ink-muted' }}"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
            <span class="text-[10px] font-bold">Kanal</span>
        </a>

        <button
            type="button"
            class="flex flex-col items-center justify-center gap-0.5 py-2 {{ $isSearch ? 'text-brand' : 'text-ink-muted' }}"
            id="bottom-nav-search"
            aria-label="Cari berita"
        >
            <span class="w-11 h-11 -mt-5 mb-0.5 rounded-full bg-brand text-white shadow-lg shadow-brand/30 flex items-center justify-center ring-4 ring-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </span>
            <span class="text-[10px] font-bold {{ $isSearch ? 'text-brand' : 'text-ink-muted' }}">Cari</span>
        </button>

        <a
            href="{{ auth()->check() ? route('submissions.create') : route('auth.register') }}"
            class="flex flex-col items-center justify-center gap-0.5 py-2 {{ $isKirim || request()->routeIs('auth.*') ? 'text-brand' : 'text-ink-muted' }}"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            <span class="text-[10px] font-bold">{{ auth()->check() ? 'Kirim' : 'Gabung' }}</span>
        </a>

        @if($isLoggedIn)
            <a
                href="{{ route('account.edit') }}"
                class="flex flex-col items-center justify-center gap-0.5 py-2 {{ $isAccount ? 'text-brand' : 'text-ink-muted' }}"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span class="text-[10px] font-bold">Akun</span>
            </a>
        @else
            <a
                href="{{ route('pages.tentang-kami') }}"
                class="flex flex-col items-center justify-center gap-0.5 py-2 {{ $isInfo ? 'text-brand' : 'text-ink-muted' }}"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-[10px] font-bold">Info</span>
            </a>
        @endif
    </div>
</nav>

<script>
(function () {
    var btn = document.getElementById('bottom-nav-search');
    if (!btn) return;
    btn.addEventListener('click', function () {
        var searchToggle = document.querySelector('[aria-label="Buka pencarian"]');
        if (searchToggle) {
            searchToggle.click();
            return;
        }
        window.location.href = {{ Js::from(route('news.index')) }};
    });
})();
</script>
