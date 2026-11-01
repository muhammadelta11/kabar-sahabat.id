@props([
    'posts' => null,
    'title' => 'Berita Terkini',
    'limit' => 5,
    'excludeId' => null,
])

@php
    $posts = collect($posts ?? \App\Support\NewsCache::latestPosts(max(8, (int) $limit + 2)))
        ->when($excludeId, fn ($c) => $c->reject(fn ($p) => (int) $p->id === (int) $excludeId))
        ->take($limit)
        ->values();
@endphp

@if($posts->isNotEmpty())
<section {{ $attributes->class(['bg-white rounded-2xl border border-neutral-100 shadow-sm overflow-hidden']) }}>
    <div class="px-4 py-3 border-b border-neutral-100">
        <h2 class="text-sm font-black text-brand font-display flex items-center gap-2">
            <span class="w-1 h-4 rounded-full bg-accent"></span>
            {{ $title }}
        </h2>
    </div>
    <ul class="divide-y divide-neutral-50">
        @foreach($posts as $post)
            @php $category = $post->categories->first(); @endphp
            <li>
                <a href="{{ route('news.show', $post->slug) }}" class="flex items-start gap-3 px-4 py-3 group hover:bg-surface/80 transition-colors">
                    <div class="w-16 h-12 rounded-lg overflow-hidden bg-neutral-200 flex-shrink-0">
                        <x-post-image :src="$post->image_url" :alt="$post->title" />
                    </div>
                    <div class="min-w-0 flex-1 space-y-0.5">
                        <span class="block text-[9px] font-bold uppercase tracking-wide text-accent truncate">
                            {{ $category?->name ?? 'Nasional' }}
                        </span>
                        <h3 class="text-xs font-bold text-ink leading-snug line-clamp-2 group-hover:text-brand transition-colors font-display">
                            {{ $post->title }}
                        </h3>
                    </div>
                </a>
            </li>
        @endforeach
    </ul>
</section>
@endif
