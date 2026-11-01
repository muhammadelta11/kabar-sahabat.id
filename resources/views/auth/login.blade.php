@extends('layouts.app')

@section('title', 'Masuk - Kabar Sahabat')

@section('meta')
    <meta name="robots" content="noindex, follow">
@endsection

@section('content')
<div class="bg-surface min-h-screen text-ink font-sans antialiased w-full">
    <x-site.header :show-trending="false" />

    <main class="site-container py-8 sm:py-12">
        <div class="max-w-md mx-auto space-y-5">
            <x-news.breadcrumb :items="[
                ['label' => 'Beranda', 'href' => route('news.index')],
                ['label' => 'Masuk'],
            ]" />

            <div class="bg-white border border-neutral-100 shadow-sm rounded-2xl overflow-hidden">
                <div class="px-5 sm:px-8 py-5 border-b border-neutral-100">
                    <h1 class="text-2xl font-black font-display text-ink">Masuk</h1>
                    <p class="mt-2 text-sm text-ink-muted">Masuk untuk mengirim artikel sebagai kontributor.</p>
                </div>

                @if(! empty($currentUser))
                    <div class="mx-5 sm:mx-8 mt-5 p-4 rounded-2xl bg-amber-50 border border-amber-100 text-sm text-amber-900">
                        Anda sedang masuk sebagai <strong>{{ $currentUser->name }}</strong> (kontributor).
                        Isi form di bawah untuk <strong>ganti akun</strong>, atau
                        <form method="POST" action="{{ route('auth.logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="font-bold underline">keluar dulu</button>
                        </form>.
                    </div>
                @endif

                <form method="POST" action="{{ route('auth.login.submit') }}" class="px-5 sm:px-8 py-6 space-y-4">
                    @csrf

                    <div>
                        <label for="email" class="block text-xs font-bold uppercase tracking-wider text-ink mb-1.5">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                               class="w-full px-3.5 py-2.5 border {{ $errors->has('email') ? 'border-danger' : 'border-neutral-200' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand">
                        @error('email')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-ink mb-1.5">Password</label>
                        <input id="password" type="password" name="password" required
                               class="w-full px-3.5 py-2.5 border {{ $errors->has('password') ? 'border-danger' : 'border-neutral-200' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand">
                        @error('password')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
                    </div>

                    <label class="flex items-center gap-2 text-xs text-ink-muted">
                        <input type="checkbox" name="remember" value="1" class="rounded border-neutral-300 text-brand focus:ring-brand/40">
                        Ingat saya
                    </label>

                    <button type="submit" class="w-full py-3 bg-brand text-white text-sm font-black uppercase tracking-wider rounded-xl hover:bg-brand-dark transition-colors">
                        {{ ! empty($currentUser) ? 'Ganti akun & masuk' : 'Masuk' }}
                    </button>
                </form>

                <div class="px-5 sm:px-8 pb-6 space-y-2 text-sm text-ink-muted">
                    <p>
                        Belum punya akun?
                        <a href="{{ route('auth.register') }}" class="font-bold text-brand hover:underline">Bergabung sebagai kontributor</a>
                    </p>
                    <p>
                        Admin / redaksi?
                        <a href="{{ url('/admin/login') }}" class="font-bold text-brand hover:underline">Masuk panel admin</a>
                    </p>
                </div>
            </div>
        </div>
    </main>

    <x-site.footer />
</div>
@endsection
