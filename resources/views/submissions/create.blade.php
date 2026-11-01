@extends('layouts.app')

@section('title', 'Kirim Artikel - Kabar Sahabat')

@section('canonical')
    <link rel="canonical" href="{{ route('submissions.create') }}">
@endsection

@section('meta')
    <meta name="description" content="Kirim berita, opini, atau liputan Anda ke Kabar Sahabat. Tidak perlu akun — setiap naskah ditinjau redaksi sebelum tayang.">
    <meta name="robots" content="index, follow">
@endsection

@section('content')
<div class="bg-surface min-h-screen text-ink font-sans antialiased w-full">

    <x-site.header :show-trending="false" />

    <main class="site-container py-6 sm:py-8">
        <div class="max-w-3xl mx-auto space-y-5">
        <x-news.breadcrumb :items="[
            ['label' => 'Beranda', 'href' => route('news.index')],
            ['label' => 'Kirim Artikel'],
        ]" />

        @if(session('submitted'))
            <div class="p-5 bg-emerald-50 border border-emerald-100 border-l-4 border-l-emerald-600 rounded-2xl">
                <h2 class="font-black text-emerald-800 font-display mb-1">Artikel Terkirim!</h2>
                <p class="text-sm text-emerald-700">Terima kasih. Artikel Anda akan kami review terlebih dahulu. Jika lolos kurasi, artikel akan tayang atas nama Anda.</p>
            </div>
        @endif

        @if(session('status'))
            <div class="p-4 bg-brand/5 border border-brand/20 rounded-2xl text-sm text-brand font-medium">
                {{ session('status') }}
            </div>
        @endif

        <div class="bg-white border border-neutral-100 shadow-sm rounded-2xl overflow-hidden">
            <div class="px-5 sm:px-8 py-5 border-b border-neutral-100 flex flex-wrap items-start justify-between gap-3">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black font-display text-ink leading-tight">Kirim Artikel</h1>
                    <p class="mt-2 text-sm text-ink-muted leading-relaxed">
                        Halo, <span class="font-bold text-ink">{{ auth()->user()->name }}</span>. Tulis naskah Anda di bawah.
                        Setiap artikel melalui review redaksi sebelum dipublikasikan.
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('submissions.index') }}" class="text-xs font-bold text-brand hover:underline">Pengajuan saya</a>
                    <a href="{{ route('account.edit') }}" class="text-xs font-bold text-brand hover:underline">Akun</a>
                    <form method="POST" action="{{ route('auth.logout') }}">
                        @csrf
                        <button type="submit" class="text-xs font-bold text-ink-muted hover:text-danger transition-colors">Keluar</button>
                    </form>
                </div>
            </div>

            <form action="{{ route('submissions.store') }}" method="POST" class="px-5 sm:px-8 py-6 space-y-5">
                @csrf

                <div class="hidden" aria-hidden="true">
                    <label for="website">Website</label>
                    <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="author_name" class="block text-xs font-bold uppercase tracking-wider text-ink mb-1.5">
                            Nama Lengkap <span class="text-danger">*</span>
                        </label>
                        <input
                            type="text"
                            id="author_name"
                            name="author_name"
                            value="{{ old('author_name', auth()->user()->name) }}"
                            required
                            maxlength="100"
                            class="w-full px-3.5 py-2.5 border {{ $errors->has('author_name') ? 'border-danger' : 'border-neutral-200' }} rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition"
                        >
                        @error('author_name')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="author_wa" class="block text-xs font-bold uppercase tracking-wider text-ink mb-1.5">
                            Nomor WhatsApp <span class="text-danger">*</span>
                        </label>
                        <input
                            type="tel"
                            id="author_wa"
                            name="author_wa"
                            value="{{ old('author_wa') }}"
                            required
                            maxlength="20"
                            placeholder="08xxxxxxxxxx"
                            class="w-full px-3.5 py-2.5 border {{ $errors->has('author_wa') ? 'border-danger' : 'border-neutral-200' }} rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition"
                        >
                        @error('author_wa')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label for="author_instansi" class="block text-xs font-bold uppercase tracking-wider text-ink mb-1.5">
                        Instansi / Organisasi <span class="text-ink-muted font-medium normal-case">(opsional)</span>
                    </label>
                    <input
                        type="text"
                        id="author_instansi"
                        name="author_instansi"
                        value="{{ old('author_instansi') }}"
                        maxlength="150"
                        class="w-full px-3.5 py-2.5 border border-neutral-200 rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition"
                    >
                </div>

                <div>
                    <label for="title" class="block text-xs font-bold uppercase tracking-wider text-ink mb-1.5">
                        Judul Artikel <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="{{ old('title') }}"
                        required
                        minlength="10"
                        maxlength="150"
                        class="w-full px-3.5 py-2.5 border {{ $errors->has('title') ? 'border-danger' : 'border-neutral-200' }} rounded-xl text-sm bg-white focus:outline-none focus:ring-2 focus:ring-brand/30 focus:border-brand transition"
                    >
                    @error('title')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="submission-content" class="block text-xs font-bold uppercase tracking-wider text-ink mb-1.5">
                        Isi Artikel <span class="text-danger">*</span>
                    </label>
                    <input type="hidden" id="submission-content" name="content" value="{{ old('content') }}">
                    <trix-editor
                        id="submission-editor"
                        input="submission-content"
                        class="border {{ $errors->has('content') ? 'border-danger' : 'border-neutral-200' }} rounded-xl bg-white"
                    ></trix-editor>
                    <p class="mt-1.5 text-xs text-ink-muted">Minimal 200 karakter. Gambar bisa disisipkan dengan drag-and-drop atau tombol lampiran.</p>
                    @error('content')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="flex items-start gap-2.5 cursor-pointer">
                        <input
                            type="checkbox"
                            name="agreement"
                            value="1"
                            required
                            {{ old('agreement') ? 'checked' : '' }}
                            class="mt-0.5 h-4 w-4 rounded border-neutral-300 text-brand focus:ring-brand/40"
                        >
                        <span class="text-xs text-ink-muted leading-relaxed">
                            Saya menyatakan bahwa tulisan ini adalah karya saya sendiri dan bersedia artikel disunting redaksi sebelum tayang.
                        </span>
                    </label>
                    @error('agreement')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
                </div>

                <div class="pt-1">
                    <button
                        type="submit"
                        class="w-full sm:w-auto px-8 py-3 bg-brand text-white text-sm font-black uppercase tracking-wider rounded-xl hover:bg-brand-dark focus:outline-none focus:ring-4 focus:ring-brand/30 transition-colors"
                    >
                        Kirim untuk Direview
                    </button>
                </div>
            </form>
        </div>
        </div>
    </main>

    <x-site.footer />
</div>

<style>
    trix-editor {
        min-height: 260px;
    }
    trix-editor:empty:before {
        content: attr(placeholder);
        color: #a3a3a3;
    }
</style>
@endsection
