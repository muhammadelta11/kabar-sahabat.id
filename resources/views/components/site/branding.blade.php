@props([
    'ads' => null,
])

@php
    $ads = $ads instanceof \Illuminate\Support\Collection ? $ads : collect($ads ?? []);
    $headerAd = $ads->get('header_leaderboard');
    $settings = $siteSettings ?? \App\Support\NewsCache::siteSettings();
    $tagline = $settings->tagline ?? 'Jernih Melihat Pergerakan';
@endphp

<div {{ $attributes->class(['bg-white border-b border-neutral-100']) }} data-site-branding>
    <div class="site-container py-2.5 sm:py-3 flex flex-col md:flex-row items-center justify-between gap-3">
        <a href="{{ route('news.index') }}" class="flex items-center gap-3 group flex-shrink-0 min-w-0">
            @if(filled($settings->logo_path ?? null))
                <img
                    src="{{ media_url($settings->logo_path) }}"
                    class="h-9 sm:h-11 w-auto max-w-[200px] sm:max-w-[260px] object-contain object-left"
                    alt="Kabar Sahabat"
                >
            @else
                <div class="w-11 h-11 rounded-xl bg-brand flex items-center justify-center text-white font-black text-xl shadow-sm ring-2 ring-accent/80">
                    K
                </div>
                <div class="flex flex-col min-w-0">
                    <span class="text-xl sm:text-2xl font-black tracking-tight text-brand font-display leading-none">
                        kabarsahabat<span class="text-accent">.id</span>
                    </span>
                    <span class="text-[10px] text-ink-muted font-medium mt-1 truncate">{{ $tagline }}</span>
                </div>
            @endif
        </a>

        @if($headerAd)
            <div class="w-full max-w-full md:max-w-[560px] h-[50px] sm:h-[70px] relative flex-shrink-0 overflow-hidden rounded-xl border border-neutral-100 bg-surface">
                @if($headerAd->type === 'image')
                    <a href="{{ $headerAd->link_url }}" target="_blank" rel="noopener noreferrer" class="block w-full h-full">
                        <img src="{{ media_url($headerAd->image_url) }}" class="w-full h-full object-cover" alt="{{ $headerAd->name }}">
                    </a>
                @else
                    {!! $headerAd->script_code !!}
                @endif
            </div>
        @endif
    </div>
</div>
