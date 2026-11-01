@extends('layouts.app')

@section('title', 'Bergabung - Kabar Sahabat')

@section('meta')
    <meta name="robots" content="noindex, follow">
    <meta name="description" content="Bergabung sebagai kontributor Kabar Sahabat untuk mengirim artikel.">
@endsection

@section('content')
<div class="bg-surface min-h-screen text-ink font-sans antialiased w-full">
    <x-site.header :show-trending="false" />

    <main class="site-container py-8 sm:py-12">
        <div class="max-w-md mx-auto space-y-5">
            <x-news.breadcrumb :items="[
                ['label' => 'Beranda', 'href' => route('news.index')],
                ['label' => 'Bergabung'],
            ]" />

            <div class="bg-white border border-neutral-100 shadow-sm rounded-2xl overflow-hidden">
                <div class="px-5 sm:px-8 py-5 border-b border-neutral-100">
                    <h1 class="text-2xl font-black font-display text-ink">Bergabung</h1>
                    <p class="mt-2 text-sm text-ink-muted">
                        Buat akun kontributor untuk mengirim berita, opini, atau liputan. Pendaftaran ditinjau redaksi sebelum Anda bisa mengirim naskah.
                    </p>
                </div>

                <form method="POST" action="{{ route('auth.register.submit') }}" class="px-5 sm:px-8 py-6 space-y-4">
                    @csrf

                    <div>
                        <label for="name" class="block text-xs font-bold uppercase tracking-wider text-ink mb-1.5">Nama lengkap</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required maxlength="100" autofocus
                               class="w-full px-3.5 py-2.5 border {{ $errors->has('name') ? 'border-danger' : 'border-neutral-200' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand">
                        @error('name')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-bold uppercase tracking-wider text-ink mb-1.5">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
                               class="w-full px-3.5 py-2.5 border {{ $errors->has('email') ? 'border-danger' : 'border-neutral-200' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand">
                        @error('email')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-bold uppercase tracking-wider text-ink mb-1.5">Password</label>
                        <input id="password" type="password" name="password" required
                               class="w-full px-3.5 py-2.5 border {{ $errors->has('password') ? 'border-danger' : 'border-neutral-200' }} rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand">
                        @error('password')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-ink mb-1.5">Ulangi password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                               class="w-full px-3.5 py-2.5 border border-neutral-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand">
                    </div>

                    <label class="flex items-start gap-2.5 text-xs text-ink-muted leading-relaxed">
                        <input type="checkbox" name="agreement" value="1" required {{ old('agreement') ? 'checked' : '' }}
                               class="mt-0.5 rounded border-neutral-300 text-brand focus:ring-brand/40">
                        <span>Saya setuju akun dipakai untuk mengirim tulisan ke Kabar Sahabat dan bersedia naskah disunting redaksi.</span>
                    </label>
                    @error('agreement')<p class="text-xs text-danger">{{ $message }}</p>@enderror

                    <button type="submit" class="w-full py-3 bg-brand text-white text-sm font-black uppercase tracking-wider rounded-xl hover:bg-brand-dark transition-colors">
                        Buat akun kontributor
                    </button>
                </form>

                <div class="px-5 sm:px-8 pb-6 text-sm text-ink-muted">
                    Sudah punya akun?
                    <a href="{{ route('auth.login') }}" class="font-bold text-brand hover:underline">Masuk</a>
                </div>
            </div>
        </div>
    </main>

    <x-site.footer />
</div>
@endsection
