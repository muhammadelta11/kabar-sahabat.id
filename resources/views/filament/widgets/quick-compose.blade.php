<x-filament-widgets::widget>
    <div class="ks-quick-compose">
        <div class="ks-quick-compose__row">
            <div class="ks-quick-compose__copy">
                <p class="ks-quick-compose__eyebrow">Redaksi cepat</p>
                <h3 class="ks-quick-compose__title">Buat berita baru</h3>
                <p class="ks-quick-compose__desc">Langsung tulis naskah — layout mobile sudah dioptimalkan untuk menulis nyaman.</p>
            </div>
            <div class="ks-quick-compose__actions">
                <a
                    href="{{ \App\Filament\Resources\PostResource::getUrl('create') }}"
                    class="ks-quick-compose__btn ks-quick-compose__btn--primary"
                >
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/></svg>
                    Buat Berita
                </a>
                <a
                    href="{{ \App\Filament\Resources\PostResource::getUrl('index') }}"
                    class="ks-quick-compose__btn ks-quick-compose__btn--ghost"
                >
                    Kelola Berita
                </a>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
