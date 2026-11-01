@extends('layouts.app')

@section('title', 'Akun Saya - Kabar Sahabat')

@section('meta')
    <meta name="robots" content="noindex, follow">
@endsection

@section('content')
<div class="bg-surface min-h-screen text-ink font-sans antialiased w-full">
    <x-site.header :show-trending="false" />

    <main class="site-container py-6 sm:py-8">
        <div class="max-w-lg mx-auto space-y-5">
            <x-news.breadcrumb :items="[
                ['label' => 'Beranda', 'href' => route('news.index')],
                ['label' => 'Akun Saya'],
            ]" />

            <div>
                <h1 class="text-2xl font-black font-display text-ink">Akun Saya</h1>
                <p class="mt-1 text-sm text-ink-muted">Kelola profil kontributor dan keluar dari sesi.</p>
            </div>

            @if(session('status'))
                <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-2xl text-sm text-emerald-800 font-medium">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white border border-neutral-100 shadow-sm rounded-2xl overflow-hidden">
                <div class="px-5 py-4 border-b border-neutral-100 flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-ink-muted">Status akun</p>
                        <p class="mt-1 text-sm font-bold text-ink">{{ $user->account_status?->label() ?? '—' }}</p>
                    </div>
                    @if($user->canSubmitArticles())
                        <a href="{{ route('submissions.index') }}" class="text-xs font-bold text-brand hover:underline">Pengajuan saya →</a>
                    @endif
                </div>

                <form method="POST" action="{{ route('account.update') }}" class="px-5 py-5 space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="name" class="block text-xs font-bold uppercase tracking-wider text-ink mb-1.5">Nama</label>
                        <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full px-3.5 py-2.5 border {{ $errors->has('name') ? 'border-danger' : 'border-neutral-200' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand">
                        @error('name')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-bold uppercase tracking-wider text-ink mb-1.5">Email</label>
                        <input id="email" type="email" value="{{ $user->email }}" disabled
                               class="w-full px-3.5 py-2.5 border border-neutral-200 rounded-xl text-sm bg-surface text-ink-muted">
                        <p class="mt-1 text-[11px] text-ink-muted">Email tidak bisa diubah. Hubungi redaksi bila perlu diganti.</p>
                    </div>

                    <div class="pt-2 border-t border-neutral-100">
                        <p class="text-xs font-bold uppercase tracking-wider text-ink mb-3">Ganti password</p>
                        <div class="space-y-3">
                            <div>
                                <label for="current_password" class="block text-[11px] font-bold text-ink-muted mb-1">Password saat ini</label>
                                <input id="current_password" type="password" name="current_password"
                                       class="w-full px-3.5 py-2.5 border {{ $errors->has('current_password') ? 'border-danger' : 'border-neutral-200' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand">
                                @error('current_password')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="password" class="block text-[11px] font-bold text-ink-muted mb-1">Password baru</label>
                                <input id="password" type="password" name="password"
                                       class="w-full px-3.5 py-2.5 border {{ $errors->has('password') ? 'border-danger' : 'border-neutral-200' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand">
                                @error('password')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="password_confirmation" class="block text-[11px] font-bold text-ink-muted mb-1">Ulangi password baru</label>
                                <input id="password_confirmation" type="password" name="password_confirmation"
                                       class="w-full px-3.5 py-2.5 border border-neutral-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand">
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3 rounded-xl bg-brand text-white text-sm font-bold hover:bg-brand-dark transition-colors">
                        Simpan perubahan
                    </button>
                </form>
            </div>

            <div class="bg-white border border-neutral-100 shadow-sm rounded-2xl p-5 space-y-3">
                @if($user->canSubmitArticles())
                    <a href="{{ route('submissions.create') }}" class="flex items-center justify-between text-sm font-bold text-brand hover:underline">
                        <span>Kirim artikel</span><span>→</span>
                    </a>
                @endif
                <form method="POST" action="{{ route('auth.logout') }}">
                    @csrf
                    <button type="submit" class="w-full py-3 rounded-xl border border-danger/30 text-danger text-sm font-bold hover:bg-danger hover:text-white transition-colors">
                        Keluar / Logout
                    </button>
                </form>
            </div>
        </div>
    </main>

    <x-site.footer />
</div>
@endsection
