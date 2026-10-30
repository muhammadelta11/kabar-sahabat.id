@extends('layouts.app')

@section('title', 'Kirim Artikel - Kabar Sahabat')

@section('content')
<div class="bg-[#F8F9FA] min-h-screen text-neutral-900 font-sans antialiased">

    <!-- BRANDING -->
    <div class="bg-white border-b border-neutral-200 py-4">
        <div class="max-w-[1080px] mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            <a href="/" class="flex items-center gap-3 group">
                @if(isset($siteSettings) && $siteSettings->logo_path)
                    <img src="{{ media_url($siteSettings->logo_path) }}" class="h-14 w-auto max-w-[300px] object-contain object-left" alt="Kabar Sahabat">
                @else
                    <div class="w-11 h-11 bg-[#003399] flex items-center justify-center text-white font-black text-2xl border-2 border-[#FFCC00]">K</div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-1 leading-none">
                            <span class="text-3xl font-black tracking-tighter text-[#003399] font-serif">KABAR</span>
                            <span class="text-3xl font-black tracking-tighter text-[#FFCC00] font-serif">SAHABAT</span>
                        </div>
                        <span class="text-[9px] text-neutral-500 font-bold uppercase tracking-widest mt-1">{{ $siteSettings->tagline ?? 'Jernih Melihat Pergerakan' }}</span>
                    </div>
                @endif
            </a>
            <a href="/" class="text-xs font-bold uppercase tracking-wider text-white bg-[#003399] px-4 py-2 rounded hover:bg-[#002b80] transition-colors">Beranda</a>
        </div>
    </div>

    <!-- FORM -->
    <main class="max-w-[760px] mx-auto px-4 sm:px-6 py-10">

        @if(session('submitted'))
        <div class="mb-8 p-5 bg-green-50 border-l-4 border-green-600 rounded-r-md">
            <h2 class="font-black text-green-800 mb-1">Artikel Terkirim!</h2>
            <p class="text-sm text-green-700">Terima kasih. Artikel Anda akan kami review terlebih dahulu. Jika lolos kurasi, artikel akan tayang atas nama Anda.</p>
        </div>
        @endif

        <h1 class="text-3xl sm:text-4xl font-black font-serif text-neutral-900 leading-tight">Kirim Artikel</h1>
        <p class="mt-3 text-sm text-neutral-600 leading-relaxed">
            Tulis dan kirimkan tulisan Anda untuk ditayangkan di Kabar Sahabat. Tidak perlu akun &mdash; cukup lengkapi data di bawah.
            Setiap artikel akan melalui proses review redaksi sebelum dipublikasikan.
        </p>

        <form action="{{ route('submissions.store') }}" method="POST" class="mt-8 space-y-6">
            @csrf

            <!-- Honeypot (tersembunyi dari manusia, umpan untuk bot) -->
            <div style="display:none" aria-hidden="true">
                <label for="website">Website</label>
                <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="author_name" class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Nama Lengkap <span class="text-red-600">*</span></label>
                    <input type="text" id="author_name" name="author_name" value="{{ old('author_name') }}" required maxlength="100"
                           class="w-full px-3.5 py-2.5 border {{ $errors->has('author_name') ? 'border-red-500' : 'border-neutral-300' }} rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#003399]/40 focus:border-[#003399] transition">
                    @error('author_name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="author_wa" class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Nomor WhatsApp <span class="text-red-600">*</span></label>
                    <input type="tel" id="author_wa" name="author_wa" value="{{ old('author_wa') }}" required maxlength="20" placeholder="08xxxxxxxxxx"
                           class="w-full px-3.5 py-2.5 border {{ $errors->has('author_wa') ? 'border-red-500' : 'border-neutral-300' }} rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#003399]/40 focus:border-[#003399] transition">
                    @error('author_wa')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label for="author_instansi" class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Instansi / Organisasi <span class="text-neutral-400 font-medium normal-case">(opsional)</span></label>
                <input type="text" id="author_instansi" name="author_instansi" value="{{ old('author_instansi') }}" maxlength="150"
                       class="w-full px-3.5 py-2.5 border border-neutral-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#003399]/40 focus:border-[#003399] transition">
            </div>

            <div>
                <label for="title" class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Judul Artikel <span class="text-red-600">*</span></label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required minlength="10" maxlength="150"
                       class="w-full px-3.5 py-2.5 border {{ $errors->has('title') ? 'border-red-500' : 'border-neutral-300' }} rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-[#003399]/40 focus:border-[#003399] transition">
                @error('title')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="submission-content" class="block text-xs font-bold uppercase tracking-wider text-neutral-700 mb-1.5">Isi Artikel <span class="text-red-600">*</span></label>
                <input type="hidden" id="submission-content" name="content" value="{{ old('content') }}">
                <trix-editor id="submission-editor" input="submission-content"
                             class="border {{ $errors->has('content') ? 'border-red-500' : 'border-neutral-300' }} rounded-md bg-white"></trix-editor>
                <p class="mt-1.5 text-xs text-neutral-400">Minimal 200 karakter. Gambar bisa disisipkan dengan drag-and-drop atau tombol lampiran.</p>
                @error('content')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="flex items-start gap-2.5 cursor-pointer">
                    <input type="checkbox" name="agreement" value="1" required {{ old('agreement') ? 'checked' : '' }}
                           class="mt-0.5 h-4 w-4 rounded border-neutral-300 text-[#003399] focus:ring-[#003399]/40">
                    <span class="text-xs text-neutral-600 leading-relaxed">Saya menyatakan bahwa tulisan ini adalah karya saya sendiri dan bersedia artikel disunting redaksi sebelum tayang.</span>
                </label>
                @error('agreement')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="pt-2">
                <button type="submit"
                        class="w-full sm:w-auto px-8 py-3 bg-[#003399] text-white text-sm font-black uppercase tracking-wider rounded-md hover:bg-[#002b80] focus:outline-none focus:ring-4 focus:ring-[#003399]/30 transition-colors">
                    Kirim untuk Direview
                </button>
            </div>
        </form>
    </main>

    <!-- FOOTER RINGKAS -->
    <footer class="bg-black text-white mt-auto border-t-4 border-[#003399]">
        <div class="max-w-[1080px] mx-auto px-4 sm:px-6 lg:px-8 py-6 text-center text-[11px] text-neutral-500">
            &copy; {{ now()->format('Y') }} Kabar Sahabat. All Rights Reserved.
        </div>
    </footer>
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