<x-filament-panels::page>
    <div class="mb-4 rounded-2xl border border-slate-200/80 bg-gradient-to-r from-[#003399]/[0.06] via-white to-orange-500/[0.06] px-4 py-3 text-sm text-slate-600 dark:border-slate-700 dark:from-[#003399]/20 dark:via-slate-900 dark:to-orange-500/10 dark:text-slate-300">
        Pantau performa trafik dan ringkasan bisnis iklan. Data GA mengikuti konfigurasi di Pengaturan.
        Performa redaktur harian–bulanan ada di menu <strong>Redaksi → Performa Redaksi</strong>.
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
        <x-filament::section heading="Google Analytics">
            <div class="space-y-3 text-sm">
                @if($gaMeasurementId)
                    <p class="text-gray-700 dark:text-gray-200">
                        Measurement ID:
                        <span class="font-mono font-semibold text-primary-600">{{ $gaMeasurementId }}</span>
                    </p>
                @else
                    <p class="text-gray-500">Belum ada Measurement ID. Isi di Pengaturan → Integrasi Analitik.</p>
                @endif

                <p class="text-gray-500">
                    Status snippet publik:
                    <span class="font-semibold {{ $hasAnalyticsSnippet ? 'text-success-600' : 'text-warning-600' }}">
                        {{ $hasAnalyticsSnippet ? 'Snippet ekstra terpasang' : 'Mengikuti Measurement ID layout' }}
                    </span>
                </p>

                <div class="flex flex-wrap gap-2 pt-1">
                    @if($gaDashboardUrl)
                        <a
                            href="{{ $gaDashboardUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-size-sm fi-btn-color-primary fi-btn-size-sm gap-1 px-3 py-2 text-sm inline-grid shadow-sm bg-primary-600 text-white hover:bg-primary-500"
                        >
                            Buka dashboard GA
                        </a>
                    @endif
                    <a
                        href="https://analytics.google.com/"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-size-sm gap-1 px-3 py-2 text-sm inline-grid shadow-sm bg-white text-gray-700 ring-1 ring-gray-950/10 hover:bg-gray-50 dark:bg-white/5 dark:text-white dark:ring-white/20"
                    >
                        analytics.google.com
                    </a>
                    <a
                        href="{{ \App\Filament\Pages\EditorialPerformanceHub::getUrl() }}"
                        class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 rounded-lg fi-size-sm gap-1 px-3 py-2 text-sm inline-grid shadow-sm bg-white text-gray-700 ring-1 ring-gray-950/10 hover:bg-gray-50 dark:bg-white/5 dark:text-white dark:ring-white/20"
                    >
                        Performa Redaksi →
                    </a>
                </div>

                @if($gaLookerEmbedUrl)
                    <div class="pt-3">
                        <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">Looker Studio</p>
                        <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-white/10">
                            <iframe
                                src="{{ $gaLookerEmbedUrl }}"
                                title="Looker Studio GA"
                                class="w-full h-[24rem] border-0"
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                            ></iframe>
                        </div>
                    </div>
                @else
                    <p class="text-xs text-gray-400 pt-2">
                        Data API Google Analytics penuh (OAuth) belum diaktifkan. Fase ini memakai metrik internal situs + tautan/embed Looker.
                    </p>
                @endif
            </div>
        </x-filament::section>

        <x-filament::section heading="Keuangan Iklan (ringkas)">
            <div class="space-y-2 text-sm text-gray-700 dark:text-gray-200">
                <p>
                    Estimasi MRR dihitung dari field <strong>Tarif bulanan</strong> pada iklan aktif.
                    Kelola detail klien &amp; periode di menu <strong>Iklan</strong>.
                </p>
                <a
                    href="{{ \App\Filament\Resources\AdvertisementResource::getUrl() }}"
                    class="inline-flex text-primary-600 font-semibold hover:underline"
                >
                    Kelola iklan →
                </a>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
