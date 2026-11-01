<x-filament-panels::page>
    <div class="mb-4 rounded-2xl border border-slate-200/80 bg-gradient-to-r from-[#003399]/[0.06] via-white to-orange-500/[0.06] px-4 py-3 text-sm text-slate-600 dark:border-slate-700 dark:from-[#003399]/20 dark:via-slate-900 dark:to-orange-500/10 dark:text-slate-300">
        Rekapan bulanan redaktur untuk acuan evaluasi dan gajian — skor dari terbit, konsistensi, dan views.
    </div>
    <div class="space-y-6">
        <x-filament::section>
            <form wire:submit.prevent class="max-w-xs">
                {{ $this->form }}
            </form>

            <div class="mt-4 text-sm text-gray-600 dark:text-gray-300 space-y-1">
                <p>
                    Acuan gaji (bukan nominal otomatis):
                    <strong>Jumlah terbit 40%</strong> +
                    <strong>Konsistensi 35%</strong> +
                    <strong>Viewers 25%</strong>.
                </p>
                <p>
                    Target bulan: {{ $targetPublished }} berita terbit · {{ number_format($targetViews) }} views ·
                    konsistensi dihitung dari {{ $daysCounted }} hari.
                </p>
            </div>
        </x-filament::section>

        <x-filament::section :heading="'Periode '.$monthLabel">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs uppercase text-gray-500 border-b border-gray-200 dark:border-white/10">
                        <tr>
                            <th class="py-3 pr-4 font-bold">Redaktur</th>
                            <th class="py-3 px-3 font-bold text-center">Terbit</th>
                            <th class="py-3 px-3 font-bold text-center">Hari aktif</th>
                            <th class="py-3 px-3 font-bold text-center">Konsistensi</th>
                            <th class="py-3 px-3 font-bold text-center">Viewers</th>
                            <th class="py-3 pl-3 font-bold text-center">Indeks</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/5">
                        @forelse($rows as $row)
                            <tr wire:key="recap-{{ $row['user']->id }}">
                                <td class="py-3 pr-4">
                                    <div class="font-semibold text-gray-950 dark:text-white">{{ $row['user']->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $row['user']->role?->label() }}</div>
                                </td>
                                <td class="py-3 px-3 text-center font-semibold tabular-nums">{{ $row['published'] }}</td>
                                <td class="py-3 px-3 text-center tabular-nums">{{ $row['active_days'] }} / {{ $daysCounted }}</td>
                                <td class="py-3 px-3 text-center tabular-nums">{{ $row['consistency'] }}%</td>
                                <td class="py-3 px-3 text-center tabular-nums">{{ number_format($row['views']) }}</td>
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
                                <td colspan="6" class="py-8 text-center text-gray-500">
                                    Belum ada akun redaktur untuk direkap.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
