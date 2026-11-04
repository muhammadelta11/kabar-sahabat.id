<x-filament-widgets::widget>
    <div class="ks-quick-compose rounded-2xl border border-primary-500/20 bg-gradient-to-br from-primary-600 to-primary-800 p-4 text-white shadow-lg sm:p-5">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0">
                <p class="text-[11px] font-extrabold uppercase tracking-widest text-orange-300">Redaksi cepat</p>
                <h3 class="mt-1 text-lg font-extrabold tracking-tight sm:text-xl">Buat berita baru</h3>
                <p class="mt-1 text-sm text-white/80">Langsung tulis naskah — layout mobile sudah dioptimalkan untuk menulis nyaman.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a
                    href="{{ \App\Filament\Resources\PostResource::getUrl('create') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-primary-700 shadow-sm hover:bg-orange-50"
                >
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/></svg>
                    Buat Berita
                </a>
                <a
                    href="{{ \App\Filament\Resources\PostResource::getUrl('index') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/30 bg-white/10 px-4 py-2.5 text-sm font-bold text-white hover:bg-white/20"
                >
                    Kelola Berita
                </a>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
