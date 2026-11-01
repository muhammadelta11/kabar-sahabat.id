@php
    $settings = $siteSettings ?? \App\Support\NewsCache::siteSettings();
    $email = $settings->email ?? null;
@endphp

<section {{ $attributes->class(['rounded-2xl bg-brand text-white p-4 sm:p-5 shadow-sm']) }}>
    <h2 class="text-sm font-black font-display">Newsletter</h2>
    <p class="mt-1 text-[12px] text-white/80 leading-relaxed">
        Dapatkan ringkasan berita pilihan langsung ke email Anda.
    </p>
    <form
        class="mt-3 flex items-stretch gap-2"
        data-newsletter-form
        @if(filled($email))
            data-newsletter-email="{{ $email }}"
        @endif
        action="{{ route('pages.kontak') }}"
        method="get"
    >
        <label class="sr-only" for="sidebar-newsletter-email">Email</label>
        <input
            id="sidebar-newsletter-email"
            type="email"
            name="email"
            required
            placeholder="email@anda.com"
            class="min-w-0 flex-1 rounded-xl border-0 bg-white/95 px-3 py-2.5 text-sm text-ink placeholder:text-ink-muted focus:outline-none focus:ring-2 focus:ring-accent"
        >
        <button
            type="submit"
            class="inline-flex items-center justify-center rounded-xl bg-accent px-3.5 text-white hover:bg-accent-soft hover:text-ink transition-colors"
            aria-label="Kirim"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7-7 7M5 12h16"/></svg>
        </button>
    </form>
</section>

<script>
(function () {
    document.querySelectorAll('[data-newsletter-form]').forEach(function (form) {
        if (form.dataset.ready) return;
        form.dataset.ready = '1';
        form.addEventListener('submit', function (e) {
            var to = form.getAttribute('data-newsletter-email');
            if (!to) return;
            e.preventDefault();
            var value = (form.querySelector('input[type="email"]') || {}).value || '';
            window.location.href = 'mailto:' + encodeURIComponent(to)
                + '?subject=' + encodeURIComponent('Langganan Newsletter Kabar Sahabat')
                + '&body=' + encodeURIComponent('Mohon daftarkan email ini untuk newsletter: ' + value);
        });
    });
})();
</script>
