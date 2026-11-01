@php
    $primaryCategory = $post->categories->first();
    $authorName = $post->displayAuthorName();
    $breadcrumbItems = [
        ['label' => 'Beranda', 'href' => url('/')],
        ['label' => 'Berita', 'href' => url('/')],
    ];
    if ($primaryCategory) {
        $breadcrumbItems[] = [
            'label' => $primaryCategory->name,
            'href' => url('/?category='.$primaryCategory->slug),
        ];
    }
@endphp

<main class="site-container py-4 sm:py-6 relative z-10 w-full min-w-0 space-y-8">
    <x-news.breadcrumb :items="$breadcrumbItems" />

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">
        <article class="lg:col-span-8 min-w-0 bg-white rounded-2xl border border-neutral-100 shadow-sm p-4 sm:p-6 lg:p-8">
            @if($primaryCategory)
                <div class="mb-3">
                    <x-news.category-badge
                        :name="$primaryCategory->name"
                        :slug="$primaryCategory->slug"
                    />
                </div>
            @endif

            <h1 class="text-[1.35rem] sm:text-3xl lg:text-[2.15rem] font-black font-display text-ink leading-snug sm:leading-tight mb-4">
                {{ $post->title }}
            </h1>

            <div class="flex items-start justify-between gap-3 border-b border-neutral-100 pb-4 mb-5">
                <div class="flex flex-col min-w-0 gap-0.5">
                    <span class="text-xs font-bold text-brand">Penulis: {{ $authorName }}</span>
                    <time class="text-[11px] text-ink-muted" datetime="{{ $post->published_at?->toIso8601String() }}">
                        {{ $post->published_at?->translatedFormat('l, d F Y | H:i') }} WIB
                    </time>
                </div>
                <x-news.article-share
                    :url="route('news.show', $post->slug)"
                    :title="$post->title"
                />
            </div>

            @if($post->image_url)
                <figure class="mb-6">
                    <div class="overflow-hidden rounded-2xl bg-neutral-200">
                        <x-post-image
                            :src="$post->image_url"
                            :alt="$post->image_caption ?? $post->title"
                            :priority="true"
                            class="w-full h-auto object-cover max-h-[500px]"
                        />
                    </div>
                    @if($post->image_caption)
                        <figcaption>{{ $post->image_caption }}</figcaption>
                    @endif
                </figure>
            @endif

            <div
                id="article-content"
                class="max-w-none text-ink leading-relaxed text-[15px] sm:text-base font-medium [&_p]:mb-5 [&_p:last-child]:mb-0 [&_h1]:text-2xl [&_h1]:font-bold [&_h1]:font-display [&_h2]:text-xl [&_h2]:font-bold [&_h2]:font-display [&_h3]:text-lg [&_h3]:font-bold [&_h3]:font-display [&_ul]:list-disc [&_ul]:pl-6 [&_ol]:list-decimal [&_ol]:pl-6 [&_a]:text-brand [&_a]:underline [&_blockquote]:border-l-4 [&_blockquote]:border-accent [&_blockquote]:pl-4 [&_blockquote]:italic [&_blockquote]:text-ink-muted"
            >
                {!! \App\Support\Media::rewriteContentUrls($post->content) !!}
            </div>

            @if($post->penulis || $post->editor)
                <div class="mt-6 pt-4 border-t border-neutral-100 text-sm text-ink-muted space-y-1">
                    @if($post->penulis)
                        <div><span class="font-bold text-ink">Penulis:</span> {{ $post->penulis }}</div>
                    @endif
                    @if($post->editor)
                        <div><span class="font-bold text-ink">Editor:</span> {{ $post->editor }}</div>
                    @endif
                </div>
            @endif

            @if($post->categories->count() > 0)
                <div class="mt-8 pt-6 border-t border-neutral-100">
                    <h4 class="text-[11px] font-bold uppercase tracking-widest text-ink mb-3">Kanal terkait</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($post->categories as $category)
                            <a
                                href="/?category={{ $category->slug }}"
                                class="inline-flex items-center px-3 py-1.5 bg-surface text-ink-muted text-[11px] font-bold hover:bg-brand hover:text-white transition-colors border border-neutral-100 rounded-full"
                            >
                                #{{ $category->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($melewatkanPosts->isNotEmpty())
                <div class="mt-8 pt-8 border-t border-neutral-100">
                    <x-news.section-heading title="Mungkin Anda Melewatkan Ini" class="mb-4" />
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($melewatkanPosts as $mp)
                            <x-news.related-item :post="$mp" />
                        @endforeach
                    </div>
                </div>
            @endif

            <livewire:post-comments :post="$post" />
        </article>

        <aside class="lg:col-span-4 min-w-0">
            <x-news.sidebar
                :popular-posts="$popularPosts"
                :ads="$ads"
                :exclude-post-id="$post->id"
            />
        </aside>
    </div>

    @if($pilihanPosts->isNotEmpty())
        <section>
            <x-news.section-heading title="Berita Pilihan Untukmu" class="mb-4" />
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach($pilihanPosts as $pp)
                    <x-news.card-md :post="$pp" />
                @endforeach
            </div>
        </section>
    @endif

    <section>
        <x-news.section-heading title="Terkini Lainnya" class="mb-4" />
        <div class="bg-white rounded-2xl border border-neutral-100 shadow-sm overflow-hidden">
            <div class="divide-y divide-neutral-100">
                @foreach($terkiniPosts as $tp)
                    <div class="px-4 py-4 hover:bg-surface/80 transition-colors">
                        <x-news.feed-item :post="$tp" />
                    </div>
                @endforeach
            </div>

            @if ($terkiniPosts->hasPages())
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-3 sm:px-4 py-3 border-t border-neutral-100 text-xs bg-white">
                    <span class="text-ink-muted font-medium text-center sm:text-left">
                        Halaman {{ $terkiniPosts->currentPage() }} dari {{ $terkiniPosts->lastPage() }}
                    </span>
                    <div class="flex items-center justify-center gap-1.5 overflow-x-auto max-w-full scrollbar-none">
                        @if ($terkiniPosts->onFirstPage())
                            <button disabled class="px-2.5 sm:px-3 py-1.5 border border-neutral-100 font-bold text-neutral-300 cursor-not-allowed rounded-lg whitespace-nowrap">←</button>
                        @else
                            <a href="{{ $terkiniPosts->previousPageUrl() }}" class="px-2.5 sm:px-3 py-1.5 border border-neutral-200 font-bold text-ink hover:bg-brand hover:text-white hover:border-brand transition-colors rounded-lg whitespace-nowrap">←</a>
                        @endif

                        @foreach ($terkiniPosts->getUrlRange(max(1, $terkiniPosts->currentPage() - 1), min($terkiniPosts->lastPage(), $terkiniPosts->currentPage() + 1)) as $page => $url)
                            @if ($page == $terkiniPosts->currentPage())
                                <span class="px-3 py-1.5 bg-brand text-white font-bold border border-brand rounded-lg">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="px-3 py-1.5 border border-neutral-200 font-bold text-ink hover:bg-brand hover:text-white hover:border-brand transition-colors rounded-lg">{{ $page }}</a>
                            @endif
                        @endforeach

                        @if ($terkiniPosts->hasMorePages())
                            <a href="{{ $terkiniPosts->nextPageUrl() }}" class="px-2.5 sm:px-3 py-1.5 border border-neutral-200 font-bold text-ink hover:bg-brand hover:text-white hover:border-brand transition-colors rounded-lg whitespace-nowrap">→</a>
                        @else
                            <button disabled class="px-2.5 sm:px-3 py-1.5 border border-neutral-100 font-bold text-neutral-300 cursor-not-allowed rounded-lg whitespace-nowrap">→</button>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </section>
</main>
