@php
    $siteSettings = $siteSettings ?? \App\Support\NewsCache::siteSettings();
    $settings = $siteSettings;
    $categories = isset($navCategories) ? collect($navCategories) : \App\Support\NewsCache::navCategories();
    if ($categories->isEmpty()) {
        $categories = \App\Support\NewsCache::navCategories();
    }
    $footerEmail = $settings->email ?? 'kabarsahabat@gmail.com';
    $footerAddress = $settings->address ?? 'Gedung Rektorat Kampus 3 UIN Walisongo Semarang, Jl. Prof. Hamka, Tambakaji, Ngaliyan, Kota Semarang, Jawa Tengah 50185';
    $footerHeading = 'text-xs font-black uppercase tracking-widest text-white mb-5 pb-2 border-b border-white/10 relative after:absolute after:left-0 after:-bottom-px after:h-0.5 after:w-10 after:bg-accent';
@endphp

<footer class="bg-brand-deep text-white/70 mt-10 border-t-4 border-brand mb-0">
    <div class="h-1 bg-accent"></div>

    <div class="site-container pt-12 pb-10 md:pb-10">
        <div class="grid grid-cols-2 lg:grid-cols-12 gap-x-6 gap-y-10">
            <div class="col-span-2 lg:col-span-4 min-w-0 lg:pr-4">
                <a href="{{ route('news.index') }}" class="block max-w-full overflow-hidden">
                    <img
                        src="{{ asset('images/brand/logo-dark.png') }}"
                        class="h-16 md:h-20 w-auto max-w-full object-contain object-left"
                        alt="Kabar Sahabat"
                    >
                </a>
                <p class="mt-2 text-[11px] font-bold uppercase tracking-widest text-accent/90 break-words">{{ $settings->tagline ?? 'Jernih Melihat Pergerakan' }}</p>
                <p class="mt-4 text-[13px] leading-relaxed text-white/60">
                    Ruang informasi, publikasi, dokumentasi, dan pertukaran gagasan yang tumbuh dari ekosistem PMII UIN Walisongo Semarang dan terbuka untuk berkolaborasi dengan berbagai pihak.
                </p>

                <p class="mt-6 text-xs font-black uppercase tracking-widest text-white">Ikuti Kami</p>
                <div class="-mt-3 [&>div]:justify-start [&_a]:bg-white/10 [&_a]:hover:bg-accent">
                    @include('partials.social-links')
                </div>
            </div>

            <nav class="col-span-2 lg:col-span-3 min-w-0" aria-label="Kanal">
                <h4 class="{{ $footerHeading }}">Kanal</h4>
                <ul class="grid grid-cols-2 gap-x-4 sm:gap-x-8 gap-y-2.5 text-[13px]">
                    @foreach($categories as $footerCategory)
                        <li class="min-w-0">
                            <a href="{{ url('/?category='.$footerCategory->slug) }}" class="hover:text-accent transition-colors break-words">{{ $footerCategory->name }}</a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            <nav class="col-span-2 sm:col-span-1 lg:col-span-2" aria-label="Informasi">
                <h4 class="{{ $footerHeading }}">Informasi</h4>
                <ul class="space-y-2.5 text-[13px]">
                    <li><a href="{{ route('pages.redaksi') }}" class="hover:text-accent transition-colors">Redaksi</a></li>
                    <li><a href="{{ route('pages.tentang-kami') }}" class="hover:text-accent transition-colors">Tentang Kami</a></li>
                    <li><a href="{{ route('pages.kontak') }}" class="hover:text-accent transition-colors">Kontak</a></li>
                    <li><a href="{{ route('pages.pedoman-media-siber') }}" class="hover:text-accent transition-colors">Pedoman Media Siber</a></li>
                </ul>
            </nav>

            <div class="col-span-2 sm:col-span-1 lg:col-span-3">
                <h4 class="{{ $footerHeading }}">Hubungi Redaksi</h4>
                <ul class="space-y-3 text-[13px]">
                    <li class="flex gap-2.5">
                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        <a href="mailto:{{ $footerEmail }}" class="break-all hover:text-accent transition-colors">{{ $footerEmail }}</a>
                    </li>
                    @if(filled($settings->phone ?? null))
                        <li class="flex gap-2.5">
                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <a href="tel:{{ $settings->phone }}" class="hover:text-accent transition-colors">{{ $settings->phone }}</a>
                        </li>
                    @endif
                    <li class="flex gap-2.5">
                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="leading-relaxed">{{ $footerAddress }}</span>
                    </li>
                </ul>
                <a href="{{ route('submissions.create') }}" class="mt-5 inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-accent text-white text-xs font-black uppercase tracking-wider hover:bg-accent-soft hover:text-ink transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Kirim Tulisan
                </a>
            </div>
        </div>
    </div>

    <div class="bg-black/40 border-t border-white/10">
        <div class="site-container py-4 flex flex-col md:flex-row items-center justify-between gap-3 text-[11px] text-white/50">
            <span class="text-center md:text-left">{!! $settings->copyright ?? 'Copyright &copy; ' . now()->format('Y') . ' Kabar Sahabat. All Rights Reserved' !!}</span>
            <div class="flex flex-wrap items-center justify-center gap-3 sm:gap-4">
                <span id="footer-clock" class="tabular-nums text-center">{{ now()->translatedFormat('l, d F Y | H:i') }} WIB</span>
                <a href="{{ url('/feed.xml') }}" class="hover:text-accent transition-colors">RSS</a>
                <button type="button" id="back-to-top" aria-label="Kembali ke atas" title="Kembali ke atas" class="w-8 h-8 rounded-lg flex items-center justify-center border border-white/20 text-white/60 hover:bg-accent hover:border-accent hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/></svg>
                </button>
            </div>
        </div>
    </div>
</footer>

<script>
(function() {
    var backToTop = document.getElementById('back-to-top');
    if (backToTop) {
        backToTop.addEventListener('click', function() {
            if (typeof lenis !== 'undefined') {
                lenis.scrollTo(0);
            } else {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
    }

    var clock = document.getElementById('footer-clock');
    if (!clock) return;
    var formatter = new Intl.DateTimeFormat('id-ID', {
        timeZone: 'Asia/Jakarta', weekday: 'long', day: '2-digit', month: 'long', year: 'numeric',
        hour: '2-digit', minute: '2-digit', hourCycle: 'h23'
    });

    function tick() {
        var parts = {};
        formatter.formatToParts(new Date()).forEach(function(part) { parts[part.type] = part.value; });
        clock.textContent = parts.weekday + ', ' + parts.day + ' ' + parts.month + ' ' + parts.year + ' | ' + parts.hour + ':' + parts.minute + ' WIB';
    }
    tick();
    setInterval(tick, 30000);
})();
</script>
