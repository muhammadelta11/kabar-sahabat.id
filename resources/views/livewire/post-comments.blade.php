<div>
    <div class="mt-10 border-t border-neutral-100 pt-6 mb-2">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-black text-ink font-display">Komentar</h3>
            <span class="text-xs text-ink-muted font-bold">{{ $comments->count() }} Komentar</span>
        </div>

        <div class="border border-neutral-100 bg-surface p-4 rounded-2xl mb-6">
            @if (session()->has('message'))
                <div class="bg-emerald-50 text-emerald-700 p-3 text-xs font-semibold rounded-xl border border-emerald-200 mb-3">
                    {{ session('message') }}
                </div>
            @endif

            <div class="mb-3">
                <input
                    type="text"
                    wire:model="guestName"
                    placeholder="Nama Anda..."
                    class="w-full bg-white border border-neutral-200 p-2.5 text-sm focus:outline-none focus:border-brand rounded-xl placeholder-neutral-400"
                >
                @error('guestName') <span class="text-xs text-danger mt-1 block font-semibold">{{ $message }}</span> @enderror
            </div>

            <div class="mb-3">
                <textarea
                    wire:model="content"
                    class="w-full bg-white border border-neutral-200 p-3 text-sm focus:outline-none focus:border-brand resize-none h-24 placeholder-neutral-400 rounded-xl"
                    placeholder="Tulis komentar Anda..."
                ></textarea>
                @error('content') <span class="text-xs text-danger mt-1 block font-semibold">{{ $message }}</span> @enderror
            </div>

            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                <span class="text-[10px] text-ink-muted max-w-sm leading-tight">
                    Berkomentarlah secara bijaksana dan bertanggung jawab. Komentar sepenuhnya menjadi tanggung jawab komentator seperti diatur dalam UU ITE.
                </span>
                <button
                    wire:click="postComment"
                    class="bg-danger hover:bg-red-800 text-white px-5 py-2 text-xs font-bold uppercase tracking-wider transition-colors rounded-xl"
                >
                    Kirim
                </button>
            </div>
        </div>

        <div class="space-y-3">
            @forelse($comments as $comment)
                <div class="flex gap-3 bg-white p-4 border border-neutral-100 rounded-2xl shadow-sm">
                    <div class="w-9 h-9 rounded-full bg-brand text-white flex items-center justify-center font-bold text-sm uppercase flex-shrink-0">
                        {{ substr($comment->guest_name, 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <h4 class="text-xs font-bold text-ink">{{ $comment->guest_name }}</h4>
                            <span class="text-[10px] text-ink-muted">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-xs text-ink-muted leading-relaxed">{{ $comment->content }}</p>
                    </div>
                </div>
            @empty
                <p class="text-xs text-ink-muted italic text-center py-4 bg-surface border border-dashed border-neutral-200 rounded-2xl">
                    Belum ada komentar. Jadilah yang pertama berkomentar!
                </p>
            @endforelse
        </div>
    </div>
</div>
