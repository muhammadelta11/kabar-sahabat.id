<div class="relative flex-shrink-0" x-data="{ open: false, mobileOpen: false }" @keydown.escape.window="mobileOpen = false; open = false">
    {{-- Desktop search --}}
    <form action="/" method="GET" class="relative hidden lg:block">
        <input type="text"
               name="q"
               wire:model.live.debounce.250ms="q"
               @focus="open = true"
               placeholder="Cari berita..."
               autocomplete="off"
               class="w-52 px-3 py-1 text-xs text-neutral-900 bg-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-[#FFCC00] rounded-md border border-neutral-200">

        <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 text-neutral-600 hover:text-[#003399]" aria-label="Cari">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </button>
    </form>

    {{-- Mobile search toggle --}}
    <button type="button"
            class="lg:hidden flex items-center justify-center w-9 h-9 text-white hover:bg-[#002b80] transition-colors"
            @click="mobileOpen = !mobileOpen; if(mobileOpen) $nextTick(() => $refs.mobileInput.focus())"
            aria-label="Buka pencarian"
            :aria-expanded="mobileOpen.toString()">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
    </button>

    {{-- Mobile search panel --}}
    <div x-show="mobileOpen"
         x-cloak
         class="lg:hidden fixed inset-x-0 top-0 z-[60] bg-[#003399] border-b-4 border-[#FFCC00] px-3 py-2.5 shadow-lg"
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <form action="/" method="GET" class="flex items-center gap-2">
            <button type="button" class="text-white p-1.5 flex-shrink-0" @click="mobileOpen = false" aria-label="Tutup pencarian">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <div class="relative flex-1 min-w-0">
                <input type="text"
                       name="q"
                       x-ref="mobileInput"
                       wire:model.live.debounce.250ms="q"
                       @focus="open = true"
                       placeholder="Cari berita..."
                       autocomplete="off"
                       class="w-full px-3 py-2 text-sm text-neutral-900 bg-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-[#FFCC00] rounded-md">
            </div>
            <button type="submit" class="bg-[#FFCC00] text-neutral-950 px-3 py-2 rounded-md text-xs font-bold flex-shrink-0">Cari</button>
        </form>

        @if($suggestions->isNotEmpty())
            <div class="mt-2 bg-white rounded-md overflow-hidden divide-y divide-neutral-100 max-h-72 overflow-y-auto shadow-xl">
                @foreach($suggestions as $post)
                    <a href="{{ route('news.show', $post->slug) }}" class="flex items-start gap-2.5 p-3 hover:bg-neutral-50 transition-colors">
                        <div class="w-12 h-8 bg-neutral-200 flex-shrink-0 overflow-hidden relative rounded-sm">
                            @if($post->image_url)
                                <img src="{{ asset('storage/' . $post->image_url) }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-neutral-300 flex items-center justify-center text-[8px] font-bold text-neutral-500">KABAR</div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="text-[8px] font-bold uppercase tracking-wider text-[#003399] block mb-0.5">{{ optional($post->categories->first())->name ?? 'Nasional' }}</span>
                            <h4 class="text-xs font-bold text-neutral-800 leading-tight line-clamp-2 font-serif">{{ $post->title }}</h4>
                        </div>
                    </a>
                @endforeach
                <a href="/?q={{ urlencode($q) }}" class="block text-center py-2.5 text-[10px] font-bold text-[#003399] hover:bg-neutral-50">
                    Lihat Semua Hasil
                </a>
            </div>
        @endif
    </div>

    {{-- Desktop suggestions --}}
    @if($suggestions->isNotEmpty())
        <div x-show="open"
             @click.outside="open = false"
             class="hidden lg:block absolute right-0 top-full mt-2 w-80 bg-white border border-neutral-200 shadow-xl rounded-md z-50 overflow-hidden divide-y divide-neutral-100"
             x-transition>
            <div class="px-3 py-2 bg-neutral-50 text-[10px] font-black tracking-wider text-neutral-500 uppercase">
                Saran Pencarian
            </div>

            <div class="max-h-80 overflow-y-auto divide-y divide-neutral-100">
                @foreach($suggestions as $post)
                    <a href="{{ route('news.show', $post->slug) }}" class="flex items-start gap-2.5 p-3 hover:bg-neutral-50 transition-colors group">
                        <div class="w-12 h-8 bg-neutral-200 flex-shrink-0 overflow-hidden relative rounded-sm">
                            @if($post->image_url)
                                <img src="{{ asset('storage/' . $post->image_url) }}" alt="{{ $post->title }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full bg-neutral-300 flex items-center justify-center text-[8px] font-bold text-neutral-500">KABAR</div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="text-[8px] font-bold uppercase tracking-wider text-[#003399] block mb-0.5">{{ optional($post->categories->first())->name ?? 'Nasional' }}</span>
                            <h4 class="text-xs font-black text-neutral-800 leading-tight group-hover:text-[#003399] transition-colors line-clamp-2 font-serif">{{ $post->title }}</h4>
                        </div>
                    </a>
                @endforeach
            </div>

            <a href="/?q={{ urlencode($q) }}" class="block text-center py-2 text-[10px] font-bold text-[#003399] hover:bg-neutral-50 transition-colors">
                Lihat Semua Hasil Pencarian
            </a>
        </div>
    @endif
</div>
