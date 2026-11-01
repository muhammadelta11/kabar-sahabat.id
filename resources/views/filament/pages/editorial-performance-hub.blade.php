<x-filament-panels::page>
    <div class="mb-4 rounded-2xl border border-slate-200/80 bg-gradient-to-r from-[#003399]/[0.06] via-white to-orange-500/[0.06] px-4 py-3 text-sm text-slate-600 dark:border-slate-700 dark:from-[#003399]/20 dark:via-slate-900 dark:to-orange-500/10 dark:text-slate-300">
        Pantau performa redaktur harian/mingguan/bulanan, konsistensi upload, berita paling banyak dibaca, serta tautan analisis Google Analytics.
    </div>

    <div class="space-y-6">
        {{-- Operasional harian / mingguan --}}
        <x-filament::section heading="Operasional harian & mingguan">
            <p class="mb-4 text-sm text-gray-600 dark:text-gray-300">
                Konsistensi minggu dihitung dari hari aktif terbit ÷ {{ $weekDaysCounted }} hari (Senin–hari ini).
                Baris merah muda = belum ada terbit hari ini atau konsistensi minggu &lt; 40%.
            </p>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs uppercase text-gray-500 border-b border-gray-200 dark:border-white/10">
                        <tr>
                            <th class="py-3 pr-4 font-bold">Redaktur</th>
                            <th class="py-3 px-2 font-bold text-center">Upload hari ini</th>
                            <th class="py-3 px-2 font-bold text-center">Terbit hari ini</th>
                            <th class="py-3 px-2 font-bold text-center">Upload minggu</th>
                            <th class="py-3 px-2 font-bold text-center">Terbit minggu</th>
                            <th class="py-3 px-2 font-bold text-center">Hari aktif</th>
                            <th class="py-3 pl-2 font-bold text-center">Konsistensi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @forelse($operationalRows as $row)
                            <tr @class([
                                'bg-danger-50/70 dark:bg-danger-500/10' => $row['low_today'] || $row['low_week'],
                            ])>
                                <td class="py-3 pr-4">
                                    <div class="font-semibold text-gray-950 dark:text-white">{{ $row['user']->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $row['user']->role?->label() }}</div>
                                </td>
                                <td class="py-3 px-2 text-center tabular-nums text-gray-800 dark:text-gray-200">{{ $row['uploaded_today'] }}</td>
                                <td class="py-3 px-2 text-center tabular-nums font-semibold text-gray-950 dark:text-white">{{ $row['published_today'] }}</td>
                                <td class="py-3 px-2 text-center tabular-nums text-gray-800 dark:text-gray-200">{{ $row['uploaded_week'] }}</td>
                                <td class="py-3 px-2 text-center tabular-nums text-gray-800 dark:text-gray-200">{{ $row['published_week'] }}</td>
                                <td class="py-3 px-2 text-center tabular-nums text-gray-800 dark:text-gray-200">{{ $row['active_days_week'] }} / {{ $weekDaysCounted }}</td>
                                <td class="py-3 pl-2 text-center">
                                    <span @class([
                                        'inline-flex min-w-[3.25rem] justify-center rounded-full px-2 py-0.5 text-xs font-bold',
                                        'bg-success-100 text-success-700 dark:bg-success-500/20 dark:text-success-400' => $row['consistency_week'] >= 70,
                                        'bg-warning-100 text-warning-700 dark:bg-warning-500/20 dark:text-warning-400' => $row['consistency_week'] >= 40 && $row['consistency_week'] < 70,
                                        'bg-gray-100 text-gray-600 dark:bg-white/10 dark:text-gray-300' => $row['consistency_week'] < 40,
                                    ])>
                                        {{ $row['consistency_week'] }}%
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-gray-500">Belum ada akun redaktur.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        {{-- Bulanan --}}
        <x-filament::section heading="Rekapan bulanan & konsistensi">
            <form wire:submit.prevent class="max-w-xs mb-4">
                {{ $this->form }}
            </form>
            <p class="mb-4 text-sm text-gray-600 dark:text-gray-300">
                Skor: terbit 40% + konsistensi 35% + viewers 25%. Target: {{ $targetPublished }} terbit · {{ number_format($targetViews) }} views · {{ $daysCounted }} hari.
                Detail acuan gaji juga di menu <strong>Rekapan Redaktur</strong>.
            </p>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs uppercase text-gray-500 border-b border-gray-200 dark:border-white/10">
                        <tr>
                            <th class="py-3 pr-4 font-bold">Redaktur · {{ $monthLabel }}</th>
                            <th class="py-3 px-3 font-bold text-center">Terbit</th>
                            <th class="py-3 px-3 font-bold text-center">Hari aktif</th>
                            <th class="py-3 px-3 font-bold text-center">Konsistensi</th>
                            <th class="py-3 px-3 font-bold text-center">Viewers</th>
                            <th class="py-3 pl-3 font-bold text-center">Indeks</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @forelse($monthlyRows as $row)
                            <tr>
                                <td class="py-3 pr-4 font-semibold text-gray-950 dark:text-white">{{ $row['user']->name }}</td>
                                <td class="py-3 px-3 text-center tabular-nums font-semibold text-gray-950 dark:text-white">{{ $row['published'] }}</td>
                                <td class="py-3 px-3 text-center tabular-nums text-gray-800 dark:text-gray-200">{{ $row['active_days'] }} / {{ $daysCounted }}</td>
                                <td class="py-3 px-3 text-center tabular-nums text-gray-800 dark:text-gray-200">{{ $row['consistency'] }}%</td>
                                <td class="py-3 px-3 text-center tabular-nums text-gray-800 dark:text-gray-200">{{ number_format($row['views']) }}</td>
                                <td class="py-3 pl-3 text-center">
                                    <span @class([
                                        'inline-flex min-w-[3.5rem] justify-center rounded-full px-2.5 py-1 text-xs font-bold',
                                        'bg-success-100 text-success-700 dark:bg-success-500/20 dark:text-success-400' => $row['score'] >= 70,
                                        'bg-warning-100 text-warning-700 dark:bg-warning-500/20 dark:text-warning-400' => $row['score'] >= 40 && $row['score'] < 70,
                                        'bg-gray-100 text-gray-600 dark:bg-white/10 dark:text-gray-300' => $row['score'] < 40,
                                    ])>
                                        {{ $row['score'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-500">Belum ada data bulan ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        {{-- Top viewer --}}
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <x-filament::section heading="Top 10 berita (all-time)">
                <ol class="divide-y divide-gray-100 dark:divide-white/5 text-sm">
                    @forelse($topPostsAllTime as $index => $post)
                        <li class="flex items-start gap-3 py-2.5">
                            <span class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-primary-600 text-[11px] font-bold text-white">{{ $index + 1 }}</span>
                            <div class="min-w-0 flex-1">
                                <a href="{{ route('news.show', $post->slug) }}" target="_blank" class="font-semibold text-gray-950 hover:text-primary-600 dark:text-white">
                                    {{ $post->title }}
                                </a>
                                <div class="text-xs text-gray-500 mt-0.5">{{ number_format($post->views_count) }} views</div>
                            </div>
                        </li>
                    @empty
                        <li class="py-6 text-center text-gray-500">Belum ada data views.</li>
                    @endforelse
                </ol>
            </x-filament::section>

            <x-filament::section heading="Top 10 berita (7 hari)">
                <ol class="divide-y divide-gray-100 dark:divide-white/5 text-sm">
                    @forelse($topPosts7d as $index => $row)
                        <li class="flex items-start gap-3 py-2.5">
                            <span class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-warning-500 text-[11px] font-bold text-white">{{ $index + 1 }}</span>
                            <div class="min-w-0 flex-1">
                                @if($row->slug)
                                    <a href="{{ route('news.show', $row->slug) }}" target="_blank" class="font-semibold text-gray-950 hover:text-primary-600 dark:text-white">
                                        {{ $row->title }}
                                    </a>
                                @else
                                    <span class="font-semibold">{{ $row->title }}</span>
                                @endif
                                <div class="text-xs text-gray-500 mt-0.5">{{ number_format($row->total) }} views (7 hari)</div>
                            </div>
                        </li>
                    @empty
                        <li class="py-6 text-center text-gray-500">Belum ada views 7 hari terakhir.</li>
                    @endforelse
                </ol>
            </x-filament::section>
        </div>

        <x-filament::section heading="Kanal teratas (7 hari)">
            <div class="flex flex-wrap gap-2">
                @forelse($topCategories7d as $cat)
                    <a
                        href="{{ url('/?category='.$cat->slug) }}"
                        target="_blank"
                        class="inline-flex items-center gap-2 rounded-full border border-gray-200 bg-white px-3 py-1.5 text-sm font-semibold text-gray-800 hover:border-primary-400 dark:border-white/10 dark:bg-white/5 dark:text-white"
                    >
                        {{ $cat->name }}
                        <span class="rounded-full bg-primary-50 px-2 py-0.5 text-xs tabular-nums text-primary-700 dark:bg-primary-500/20 dark:text-primary-300">{{ number_format($cat->total) }}</span>
                    </a>
                @empty
                    <p class="text-sm text-gray-500">Belum ada data kanal 7 hari.</p>
                @endforelse
            </div>
        </x-filament::section>

        {{-- GA panel --}}
        <x-filament::section heading="Google Analytics">
            <div class="space-y-3 text-sm">
                @if($gaMeasurementId)
                    <p class="text-gray-700 dark:text-gray-200">
                        Measurement ID:
                        <span class="font-mono font-semibold text-primary-600">{{ $gaMeasurementId }}</span>
                    </p>
                @else
                    <p class="text-warning-600">Belum ada Measurement ID. Isi di Pengaturan → Integrasi Analitik.</p>
                @endif

                <p class="text-gray-500">
                    Snippet publik:
                    <span class="font-semibold {{ $hasAnalyticsSnippet ? 'text-success-600' : 'text-gray-600' }}">
                        {{ $hasAnalyticsSnippet ? 'Ada di settings' : 'Mengikuti Measurement ID / fallback layout' }}
                    </span>
                </p>

                <div class="flex flex-wrap gap-2 pt-1">
                    @if($gaDashboardUrl)
                        <a
                            href="{{ $gaDashboardUrl }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="fi-btn relative inline-grid grid-flow-col items-center justify-center gap-1 rounded-lg bg-primary-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500"
                        >
                            Buka dashboard GA
                        </a>
                    @endif
                    <a
                        href="https://analytics.google.com/"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="fi-btn relative inline-grid grid-flow-col items-center justify-center gap-1 rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-gray-950/10 hover:bg-gray-50 dark:bg-white/5 dark:text-white dark:ring-white/20"
                    >
                        analytics.google.com
                    </a>
                    @if($canViewBusiness)
                        <a
                            href="{{ \App\Filament\Pages\AnalyticsDashboard::getUrl() }}"
                            class="fi-btn relative inline-grid grid-flow-col items-center justify-center gap-1 rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-gray-950/10 hover:bg-gray-50 dark:bg-white/5 dark:text-white dark:ring-white/20"
                        >
                            Analytics bisnis →
                        </a>
                    @endif
                </div>

                @if($gaLookerEmbedUrl)
                    <div class="pt-3">
                        <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">Looker Studio</p>
                        <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5">
                            <iframe
                                src="{{ $gaLookerEmbedUrl }}"
                                title="Looker Studio GA"
                                class="w-full h-[28rem] border-0"
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                            ></iframe>
                        </div>
                    </div>
                @else
                    <p class="text-xs text-gray-400 pt-2">
                        Opsional: tempel URL embed Looker Studio di Pengaturan agar laporan GA tampil di sini.
                        Data API GA penuh belum diaktifkan — fase ini memakai metrik internal situs + tautan/embed.
                    </p>
                @endif
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
