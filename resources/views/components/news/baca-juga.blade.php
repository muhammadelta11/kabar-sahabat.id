@props([
    'post',
])

<div {{ $attributes->class(['my-6 p-3 bg-surface border-l-4 border-accent shadow-sm flex items-center gap-3 rounded-r-xl']) }}>
    <div class="w-16 h-10 bg-neutral-200 overflow-hidden flex-shrink-0 relative rounded-lg border border-neutral-100">
        <x-post-image :src="$post->image_url" :alt="$post->title" />
    </div>
    <div class="flex-1 min-w-0">
        <span class="text-[9px] font-bold uppercase tracking-wider text-ink-muted block mb-0.5">Baca juga</span>
        <a href="{{ route('news.show', $post->slug) }}" class="text-xs sm:text-sm font-bold text-ink hover:text-brand leading-snug line-clamp-2 block font-display">
            {{ $post->title }}
        </a>
    </div>
</div>
