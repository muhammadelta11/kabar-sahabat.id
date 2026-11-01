@props([
    'name' => 'Nasional',
    'slug' => null,
])

@php
    $palette = [
        'bg-brand text-white',
        'bg-emerald-600 text-white',
        'bg-accent text-white',
        'bg-violet-600 text-white',
        'bg-sky-600 text-white',
        'bg-rose-600 text-white',
        'bg-amber-500 text-ink',
        'bg-teal-600 text-white',
    ];
    $key = (string) ($slug ?: \Illuminate\Support\Str::slug((string) $name));
    $class = $palette[abs(crc32($key)) % count($palette)];
@endphp

<span {{ $attributes->class([$class, 'inline-flex items-center max-w-full px-2 py-0.5 text-[9px] sm:text-[10px] font-bold uppercase tracking-wider rounded-md truncate']) }}>
    {{ \Illuminate\Support\Str::limit((string) $name, 22) }}
</span>
