<div>
    <div class="mt-10 border-t-4 border-neutral-200 pt-6 mb-10">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-black text-neutral-900 font-serif">Komentar</h3>
            <span class="text-xs text-neutral-500 font-bold">{{ $comments->count() }} Komentar</span>
        </div>
        
        <!-- Form Komentar -->
        <div class="border border-neutral-300 bg-neutral-50 p-4 rounded-lg mb-6">
            @if (session()->has('message'))
                <div class="bg-green-50 text-green-700 p-3 text-xs font-semibold rounded border border-green-200 mb-3">
                    {{ session('message') }}
                </div>
            @endif
            
            <div class="mb-3">
                <input type="text" wire:model="guestName" placeholder="Nama Anda..." class="w-full bg-white border border-neutral-300 p-2.5 text-sm focus:outline-none focus:border-[#003399] rounded-md placeholder-neutral-400">
                @error('guestName') <span class="text-xs text-red-500 mt-1 block font-semibold">{{ $message }}</span> @enderror
            </div>
            
            <div class="mb-3">
                <textarea wire:model="content" class="w-full bg-white border border-neutral-300 p-3 text-sm focus:outline-none focus:border-[#003399] resize-none h-24 placeholder-neutral-400 rounded-md" placeholder="Tulis komentar Anda..."></textarea>
                @error('content') <span class="text-xs text-red-500 mt-1 block font-semibold">{{ $message }}</span> @enderror
            </div>
            
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                <span class="text-[10px] text-neutral-500 max-w-sm leading-tight">Berkomentarlah secara bijaksana dan bertanggung jawab. Komentar sepenuhnya menjadi tanggung jawab komentator seperti diatur dalam UU ITE.</span>
                <button wire:click="postComment" class="bg-[#CC0000] hover:bg-[#b30000] text-white px-5 py-2 text-xs font-bold uppercase tracking-wider transition-colors rounded">Kirim</button>
            </div>
        </div>
        
        <!-- Daftar Komentar -->
        <div class="space-y-4">
            @forelse($comments as $comment)
                <div class="flex gap-3 bg-white p-4 border border-neutral-200 rounded-lg shadow-sm">
                    <div class="w-9 h-9 rounded-full bg-[#003399] text-white flex items-center justify-center font-bold text-sm uppercase flex-shrink-0">
                        {{ substr($comment->guest_name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                           <h4 class="text-xs font-bold text-neutral-900">{{ $comment->guest_name }}</h4>
                           <span class="text-[10px] text-neutral-400">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-xs text-neutral-700 leading-relaxed">{{ $comment->content }}</p>
                    </div>
                </div>
            @empty
                <p class="text-xs text-neutral-500 italic text-center py-4 bg-neutral-50 border border-dashed border-neutral-200 rounded-lg">Belum ada komentar. Jadilah yang pertama berkomentar!</p>
            @endforelse
        </div>
    </div>
</div>
