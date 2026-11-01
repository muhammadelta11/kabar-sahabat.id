@extends('layouts.app')

@section('title', 'Menunggu Persetujuan - Kabar Sahabat')

@section('meta')
    <meta name="robots" content="noindex, follow">
    <meta name="description" content="Status pengajuan akun kontributor Kabar Sahabat.">
@endsection

@section('content')
<div class="bg-surface min-h-screen text-ink font-sans antialiased w-full">
    <x-site.header :show-trending="false" />

    <main class="site-container py-8 sm:py-12">
        <div class="max-w-md mx-auto space-y-5">
            <x-news.breadcrumb :items="[
                ['label' => 'Beranda', 'href' => route('news.index')],
                ['label' => 'Status akun'],
            ]" />

            <div class="bg-white border border-neutral-100 shadow-sm rounded-2xl overflow-hidden">
                <div class="px-5 sm:px-8 py-5 border-b border-neutral-100">
                    <h1 class="text-2xl font-black font-display text-ink">Status akun</h1>
                    <p class="mt-2 text-sm text-ink-muted">
                        Halo, {{ $user->name }}. Berikut status pengajuan kontributor Anda.
                    </p>
                </div>

                <div class="px-5 sm:px-8 py-6 space-y-4">
                    @if (session('status'))
                        <p class="text-sm text-brand font-medium">{{ session('status') }}</p>
                    @endif

                    @if ($status === \App\UserAccountStatus::Pending)
                        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                            <p class="font-bold">Menunggu persetujuan redaksi</p>
                            <p class="mt-1 text-amber-800/90">
                                Tim redaksi akan meninjau pendaftaran Anda. Setelah disetujui, Anda bisa mengirim artikel lewat halaman Kirim Artikel.
                            </p>
                        </div>
                    @elseif ($status === \App\UserAccountStatus::Rejected)
                        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-900">
                            <p class="font-bold">Pengajuan ditolak</p>
                            <p class="mt-1 text-red-800/90">
                                Maaf, pengajuan akun kontributor Anda belum dapat diterima. Hubungi redaksi jika butuh klarifikasi.
                            </p>
                        </div>
                    @endif

                    <div class="text-sm text-ink-muted space-y-1">
                        <p>Email: <span class="font-medium text-ink">{{ $user->email }}</span></p>
                        <p>Status: <span class="font-bold text-ink">{{ $status->label() }}</span></p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2 pt-2">
                        <a href="{{ route('account.edit') }}"
                           class="inline-flex justify-center py-3 px-4 bg-brand text-white text-sm font-black uppercase tracking-wider rounded-xl hover:bg-brand-dark transition-colors">
                            Kelola akun
                        </a>
                        <form method="POST" action="{{ route('auth.logout') }}" class="sm:flex-1">
                            @csrf
                            <button type="submit"
                                    class="w-full py-3 border border-neutral-200 text-ink text-sm font-bold uppercase tracking-wider rounded-xl hover:bg-neutral-50 transition-colors">
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <x-site.footer />
</div>
@endsection
