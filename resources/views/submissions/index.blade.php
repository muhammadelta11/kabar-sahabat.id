@extends('layouts.app')

@section('title', 'Pengajuan Saya - Kabar Sahabat')

@section('meta')
    <meta name="robots" content="noindex, follow">
@endsection

@section('content')
<div class="bg-surface min-h-screen text-ink font-sans antialiased w-full">
    <x-site.header :show-trending="false" />

    <main class="site-container py-6 sm:py-8">
        <div class="max-w-3xl mx-auto space-y-5">
            <x-news.breadcrumb :items="[
                ['label' => 'Beranda', 'href' => route('news.index')],
                ['label' => 'Pengajuan Saya'],
            ]" />

            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-black font-display text-ink">Pengajuan Saya</h1>
                    <p class="text-sm text-ink-muted mt-1">Status naskah yang Anda kirim ke redaksi.</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('account.edit') }}" class="inline-flex px-3.5 py-2.5 rounded-xl border border-neutral-200 text-xs font-bold text-ink hover:border-brand hover:text-brand transition-colors">
                        Akun
                    </a>
                    <a href="{{ route('submissions.create') }}" class="inline-flex px-4 py-2.5 rounded-xl bg-brand text-white text-xs font-bold uppercase tracking-wider hover:bg-brand-dark transition-colors">
                        + Kirim baru
                    </a>
                </div>
            </div>

            <div class="bg-white border border-neutral-100 shadow-sm rounded-2xl overflow-hidden">
                @forelse($submissions as $submission)
                    <article class="px-4 sm:px-5 py-4 border-b border-neutral-100 last:border-b-0">
                        <div class="flex flex-wrap items-start justify-between gap-2">
                            <h2 class="text-sm sm:text-base font-bold text-ink font-display leading-snug">{{ $submission->title }}</h2>
                            @php
                                $badge = match ($submission->status) {
                                    'Published' => 'bg-emerald-100 text-emerald-800',
                                    'Menunggu Review' => 'bg-amber-100 text-amber-800',
                                    'Draft' => 'bg-neutral-100 text-ink-muted',
                                    default => 'bg-sky-100 text-sky-800',
                                };
                            @endphp
                            <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide {{ $badge }}">
                                {{ $submission->status }}
                            </span>
                        </div>
                        <div class="mt-2 flex flex-wrap gap-x-3 gap-y-1 text-[11px] text-ink-muted">
                            <time datetime="{{ $submission->created_at?->toIso8601String() }}">
                                Dikirim {{ $submission->created_at?->translatedFormat('d M Y, H:i') }}
                            </time>
                            @if($submission->status === 'Published' && $submission->slug)
                                <a href="{{ route('news.show', $submission->slug) }}" class="text-brand font-semibold hover:underline">Lihat tayangan</a>
                            @endif
                        </div>
                    </article>
                @empty
                    <p class="px-5 py-10 text-sm text-ink-muted text-center">
                        Belum ada pengajuan.
                        <a href="{{ route('submissions.create') }}" class="text-brand font-bold hover:underline">Kirim artikel pertama</a>
                    </p>
                @endforelse
            </div>

            @if($submissions->hasPages())
                <div class="pt-2">
                    {{ $submissions->links() }}
                </div>
            @endif
        </div>
    </main>

    <x-site.footer />
</div>
@endsection
