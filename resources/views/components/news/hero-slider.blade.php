@props([
    'posts',
])

@php
    $slides = collect($posts)->take(5)->values();
@endphp

@if($slides->isNotEmpty())
<section
    {{ $attributes->class(['relative overflow-hidden rounded-xl bg-neutral-900 shadow-sm lg:h-full lg:min-h-[20rem]']) }}
    id="hero-slider"
    data-hero-slider
    aria-roledescription="carousel"
    aria-label="Berita utama"
>
    <div class="relative aspect-[16/10] sm:aspect-[2/1] lg:aspect-auto lg:h-full lg:min-h-[20rem]">
        @foreach($slides as $index => $slide)
            @php
                $category = $slide->categories->first();
                $rawExcerpt = Str::squish(html_entity_decode(strip_tags($slide->excerpt ?: $slide->content)));
                $excerpt = filled($rawExcerpt) && ! str_starts_with($rawExcerpt, Str::limit($slide->title, 40, ''))
                    ? Str::limit($rawExcerpt, 110)
                    : null;
            @endphp
            <article
                class="hero-slide absolute inset-0 transition-opacity duration-500 ease-out {{ $index === 0 ? 'opacity-100 z-[1]' : 'opacity-0 z-0 pointer-events-none' }}"
                data-hero-slide
                data-index="{{ $index }}"
                aria-hidden="{{ $index === 0 ? 'false' : 'true' }}"
            >
                <a href="{{ route('news.show', $slide->slug) }}" class="group block h-full">
                    <div class="absolute inset-0">
                        <x-post-image
                            :src="$slide->image_url"
                            :alt="$slide->title"
                            :priority="$index === 0"
                            class="object-cover group-hover:scale-[1.02] transition-transform duration-700"
                        />
                        <div
                            class="absolute inset-0"
                            style="background: linear-gradient(to top, rgb(0 0 0 / 0.78) 0%, rgb(0 0 0 / 0.35) 42%, transparent 70%)"
                        ></div>
                    </div>

                    <div class="absolute inset-x-0 bottom-0 z-10 p-4 sm:p-5 lg:px-6 lg:pt-5 lg:pb-10 text-white">
                        <x-news.category-badge
                            :name="$category?->name ?? 'Nasional'"
                            :slug="$category?->slug"
                            class="w-fit mb-2 shadow-sm"
                        />
                        <h2 class="text-base sm:text-xl lg:text-2xl font-black leading-snug font-display max-w-3xl line-clamp-2">
                            {{ $slide->title }}
                        </h2>
                        @if($excerpt)
                            <p class="mt-1.5 text-sm text-white/85 leading-relaxed line-clamp-1 max-w-2xl hidden md:block">
                                {{ $excerpt }}
                            </p>
                        @endif
                        <div class="mt-2.5 flex flex-wrap items-center gap-2.5">
                            <time class="text-[11px] text-white/70" datetime="{{ $slide->published_at?->toIso8601String() }}">
                                {{ $slide->published_at?->translatedFormat('d M Y, H:i') }} WIB
                            </time>
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-accent px-3 py-1.5 text-[11px] font-bold text-white">
                                Baca Selengkapnya
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </span>
                        </div>
                    </div>
                </a>
            </article>
        @endforeach

        @if($slides->count() > 1)
            <button
                type="button"
                data-hero-prev
                class="absolute left-3 top-1/2 z-20 -translate-y-1/2 hidden sm:flex h-10 w-10 items-center justify-center rounded-full bg-white/95 text-brand shadow-md hover:bg-white transition"
                aria-label="Slide sebelumnya"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            </button>
            <button
                type="button"
                data-hero-next
                class="absolute right-3 top-1/2 z-20 -translate-y-1/2 hidden sm:flex h-10 w-10 items-center justify-center rounded-full bg-white/95 text-brand shadow-md hover:bg-white transition"
                aria-label="Slide berikutnya"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
            </button>

            <div class="absolute inset-x-0 bottom-3 z-20 flex items-center justify-center gap-1.5" data-hero-dots role="tablist" aria-label="Pilih slide">
                @foreach($slides as $index => $slide)
                    <button
                        type="button"
                        data-hero-dot="{{ $index }}"
                        class="hero-dot h-1.5 rounded-full transition-all {{ $index === 0 ? 'w-6 bg-white' : 'w-1.5 bg-white/45 hover:bg-white/70' }}"
                        aria-label="Slide {{ $index + 1 }}"
                        aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
                    ></button>
                @endforeach
            </div>
            <span class="sr-only"><span data-hero-current>1</span> / {{ $slides->count() }}</span>
        @endif
    </div>
</section>

<script>
(function () {
    var root = document.getElementById('hero-slider');
    if (!root || root.dataset.ready) return;
    root.dataset.ready = '1';

    var slides = Array.prototype.slice.call(root.querySelectorAll('[data-hero-slide]'));
    if (slides.length < 2) return;

    var dots = Array.prototype.slice.call(root.querySelectorAll('[data-hero-dot]'));
    var currentEl = root.querySelector('[data-hero-current]');
    var index = 0;
    var timer = null;
    var INTERVAL = 6500;

    function show(next) {
        index = (next + slides.length) % slides.length;
        slides.forEach(function (slide, i) {
            var active = i === index;
            slide.classList.toggle('opacity-100', active);
            slide.classList.toggle('z-[1]', active);
            slide.classList.toggle('opacity-0', !active);
            slide.classList.toggle('pointer-events-none', !active);
            slide.setAttribute('aria-hidden', active ? 'false' : 'true');
        });
        dots.forEach(function (dot, i) {
            var active = i === index;
            dot.classList.toggle('w-6', active);
            dot.classList.toggle('bg-white', active);
            dot.classList.toggle('w-1.5', !active);
            dot.classList.toggle('bg-white/45', !active);
            dot.setAttribute('aria-selected', active ? 'true' : 'false');
        });
        if (currentEl) currentEl.textContent = String(index + 1);
    }

    function start() {
        stop();
        timer = window.setInterval(function () { show(index + 1); }, INTERVAL);
    }

    function stop() {
        if (timer) window.clearInterval(timer);
        timer = null;
    }

    var prev = root.querySelector('[data-hero-prev]');
    var next = root.querySelector('[data-hero-next]');
    if (prev) prev.addEventListener('click', function () { show(index - 1); start(); });
    if (next) next.addEventListener('click', function () { show(index + 1); start(); });
    dots.forEach(function (dot) {
        dot.addEventListener('click', function () {
            show(Number(dot.getAttribute('data-hero-dot') || 0));
            start();
        });
    });

    root.addEventListener('mouseenter', stop);
    root.addEventListener('mouseleave', start);
    root.addEventListener('focusin', stop);
    root.addEventListener('focusout', start);

    if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        start();
    }
})();
</script>
@endif
