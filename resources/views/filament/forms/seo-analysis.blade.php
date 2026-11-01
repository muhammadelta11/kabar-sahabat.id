@php
    /** @var array $analysis */
    $score = (int) ($analysis['score'] ?? 0);
    $grade = $analysis['grade'] ?? 'D';
    $label = $analysis['label'] ?? '';
    $checks = $analysis['checks'] ?? [];
    $bar = match (true) {
        $score >= 90 => 'bg-emerald-500',
        $score >= 75 => 'bg-sky-500',
        $score >= 60 => 'bg-amber-500',
        default => 'bg-rose-500',
    };
@endphp

<div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">Analisa SEO</p>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">{{ $label }}</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-gray-900 text-sm font-black text-white dark:bg-white dark:text-gray-900">{{ $grade }}</span>
            <span class="text-2xl font-black tabular-nums text-gray-900 dark:text-white">{{ $score }}</span>
            <span class="text-xs font-semibold text-gray-500">/100</span>
        </div>
    </div>

    <div class="mt-3 h-2 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800">
        <div class="h-full rounded-full transition-all {{ $bar }}" style="width: {{ max(4, $score) }}%"></div>
    </div>

    <ul class="mt-4 space-y-2">
        @foreach($checks as $check)
            <li class="flex items-start gap-2.5 rounded-lg px-2.5 py-2 {{ $check['ok'] ? 'bg-emerald-50 dark:bg-emerald-950/30' : 'bg-amber-50 dark:bg-amber-950/20' }}">
                <span class="mt-0.5 inline-flex h-5 w-5 flex-shrink-0 items-center justify-center rounded-full text-[11px] font-black {{ $check['ok'] ? 'bg-emerald-500 text-white' : 'bg-amber-500 text-white' }}">
                    {{ $check['ok'] ? '✓' : '!' }}
                </span>
                <div class="min-w-0">
                    <p class="text-xs font-bold text-gray-800 dark:text-gray-100">{{ $check['label'] }}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-300">{{ $check['message'] }}</p>
                </div>
            </li>
        @endforeach
    </ul>

    <p class="mt-3 text-[11px] text-gray-500 dark:text-gray-400">
        Tip: ubah judul/ringkasan/isi lalu keluar dari kolom — skor akan diperbarui otomatis.
    </p>
</div>
