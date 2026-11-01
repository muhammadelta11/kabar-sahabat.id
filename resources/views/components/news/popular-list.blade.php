@props([
    'posts',
    'title' => 'Berita Populer',
    'href' => null,
])

@php
    $posts = collect($posts);
    $href = $href ?? url('/');
@endphp

<section {{ $attributes->class(['bg-white rounded-xl border border-neutral-100 shadow-sm overflow-hidden flex flex-col']) }}>
    <div class="px-3.5 py-2.5 border-b border-neutral-100 flex items-center justify-between gap-2 flex-shrink-0">
        <h2 class="text-sm font-black text-brand font-display flex items-center gap-2">
            <span class="w-1 h-4 rounded-full bg-accent"></span>
            {{ $title }}
        </h2>
        <a href="{{ $href }}" class="text-[10px] font-bold text-brand hover:text-accent transition-colors whitespace-nowrap">
            Lihat Semua →
        </a>
    </div>

    <ol class="divide-y divide-neutral-50 flex-1">
        @foreach($posts->take(5) as $index => $pp)
            <li>
                <a href="{{ route('news.show', $pp->slug) }}" class="flex items-start gap-2.5 px-3.5 py-2 group hover:bg-surface/80 active:bg-surface transition-colors">
                    <span class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-accent text-[11px] font-black text-white font-display">
                        {{ $index + 1 }}
                    </span>
                    <div class="w-12 h-12 rounded-lg overflow-hidden bg-neutral-200 flex-shrink-0">
                        <x-post-image :src="$pp->image_url" :alt="$pp->title" />
                    </div>
                    <div class="min-w-0 flex-1 space-y-0.5">
                        <h3 class="text-xs sm:text-[13px] font-bold text-ink leading-snug line-clamp-2 group-hover:text-brand transition-colors font-display">
                            {{ $pp->title }}
                        </h3>
                        <time class="block text-[10px] text-ink-muted" datetime="{{ $pp->published_at?->toIso8601String() }}">
                            {{ $pp->published_at?->translatedFormat('d M Y, H:i') }} WIB
                        </time>
                    </div>
                </a>
            </li>
        @endforeach
    </ol>
</section>
