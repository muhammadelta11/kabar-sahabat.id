@php
    $settings = $siteSettings ?? \App\Support\NewsCache::siteSettings();
@endphp

<section {{ $attributes->class(['relative overflow-hidden rounded-2xl bg-brand-deep text-white p-4 sm:p-5 shadow-sm']) }}>
    <div class="absolute inset-0 opacity-30" style="background: radial-gradient(circle at 85% 20%, rgb(255 122 0 / 0.45), transparent 55%)"></div>
    <div class="relative">
        <p class="text-[10px] font-bold uppercase tracking-widest text-accent">E-Paper</p>
        <h2 class="mt-1 text-base font-black font-display leading-snug">
            Baca edisi digital Kabar Sahabat
        </h2>
        <p class="mt-1.5 text-[12px] text-white/75 leading-relaxed">
            {{ $settings->tagline ?? 'Jernih Melihat Pergerakan' }}
        </p>
        <a
            href="{{ route('pages.kontak') }}"
            class="mt-3 inline-flex items-center gap-1.5 rounded-full bg-accent px-3.5 py-2 text-[11px] font-bold text-white hover:bg-accent-soft hover:text-ink transition-colors"
        >
            Download E-Paper
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/></svg>
        </a>
    </div>
</section>
