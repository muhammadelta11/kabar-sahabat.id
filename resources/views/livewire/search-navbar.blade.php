<div class="relative hidden lg:block flex-shrink-0" x-data="{ open: true }" @click.outside="open = false">
    <form action="/" method="GET" class="relative">
        <input type="text" 
               name="q" 
               wire:model.live.debounce.250ms="q"
               @focus="open = true"
               placeholder="Cari berita..." 
               autocomplete="off"
               class="w-52 px-3 py-1 text-xs text-neutral-900 bg-white placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-[#FFCC00] rounded-md border border-neutral-200">
        
        <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 text-neutral-600 hover:text-[#003399]">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </button>
    </form>

    <!-- Suggestions Dropdown -->
    @if($suggestions->isNotEmpty())
        <div x-show="open" 
             class="absolute right-0 top-full mt-2 w-80 bg-white border border-neutral-200 shadow-xl rounded-md z-50 overflow-hidden divide-y divide-neutral-100"
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
