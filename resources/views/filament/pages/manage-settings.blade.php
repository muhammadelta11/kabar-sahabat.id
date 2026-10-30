<x-filament-panels::page>
    @if(session('success'))
        <div class="fi-alert fi-alert-success mb-4 p-4 rounded-lg bg-green-50 border border-green-200 text-green-800 font-semibold text-sm">
            ✅ {{ session('success') }}
        </div>
    @endif

    @php $setting = app(App\Models\Setting::class)::first() ?? new App\Models\Setting(); @endphp

    <form
        action="{{ route('admin.settings.update') }}"
        method="POST"
        enctype="multipart/form-data"
        class="space-y-6"
    >
        @csrf

        {{-- IDENTITAS --}}
        <x-filament::section heading="Identitas Website">
            <div class="space-y-4">
                {{-- Logo Preview & Upload --}}
                <div>
                    <label class="fi-fo-field-wrp-label inline-flex items-center gap-x-3 mb-1">
                        <span class="text-sm font-medium leading-6 text-gray-950 dark:text-white">Logo Website</span>
                        <span class="text-xs text-gray-500">(JPG/PNG, maks 2MB)</span>
                    </label>
                    @if($setting->logo_path && Storage::disk('public')->exists($setting->logo_path))
                        <div class="mb-2 flex items-center gap-3">
                            <img src="{{ asset('storage/' . $setting->logo_path) }}" alt="Logo" class="h-12 object-contain border border-gray-200 rounded p-1 bg-white">
                            <span class="text-xs text-gray-500">Logo saat ini</span>
                        </div>
                    @endif
                    <input
                        type="file"
                        name="logo"
                        accept="image/*"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 border border-gray-300 rounded-lg p-1"
                    >
                    @error('logo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Tagline --}}
                <div>
                    <label class="block text-sm font-medium text-gray-950 dark:text-white mb-1">Tagline Website <span class="text-red-500">*</span></label>
                    <input
                        type="text"
                        name="tagline"
                        value="{{ old('tagline', $setting->tagline) }}"
                        required
                        class="fi-input w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-primary-500 dark:bg-white/5 dark:border-white/10 dark:text-white"
                    >
                    @error('tagline') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </x-filament::section>

        {{-- FOOTER & KONTAK --}}
        <x-filament::section heading="Footer & Kontak">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-950 dark:text-white mb-1">Teks Hak Cipta Footer <span class="text-red-500">*</span></label>
                    <input
                        type="text"
                        name="copyright"
                        value="{{ old('copyright', $setting->copyright) }}"
                        required
                        class="fi-input w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-primary-500 dark:bg-white/5 dark:border-white/10 dark:text-white"
                    >
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-950 dark:text-white mb-1">Email Redaksi</label>
                    <input type="email" name="email" value="{{ old('email', $setting->email) }}" class="fi-input w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-primary-500 dark:bg-white/5 dark:border-white/10 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-950 dark:text-white mb-1">Nomor Telepon/WA</label>
                    <input type="text" name="phone" value="{{ old('phone', $setting->phone) }}" class="fi-input w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-primary-500 dark:bg-white/5 dark:border-white/10 dark:text-white">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-950 dark:text-white mb-1">Alamat Kantor</label>
                    <textarea name="address" rows="3" class="fi-input w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-primary-500 dark:bg-white/5 dark:border-white/10 dark:text-white">{{ old('address', $setting->address) }}</textarea>
                </div>
            </div>
        </x-filament::section>

        {{-- TOMBOL WHATSAPP --}}
        <x-filament::section heading="Tombol WhatsApp">
            <div class="space-y-4">
                <label class="flex items-center gap-x-3">
                    <input
                        type="hidden"
                        name="whatsapp_enabled"
                        value="0"
                    >
                    <input
                        type="checkbox"
                        name="whatsapp_enabled"
                        value="1"
                        @checked($setting->whatsapp_enabled)
                        class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                    >
                    <span class="text-sm font-medium text-gray-950 dark:text-white">Tampilkan tombol WA melayang di semua halaman</span>
                </label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-950 dark:text-white mb-1">Nomor WhatsApp</label>
                        <input
                            type="text"
                            name="whatsapp_number"
                            value="{{ old('whatsapp_number', $setting->whatsapp_number) }}"
                            placeholder="08123456789 atau 628123456789"
                            class="fi-input w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-primary-500 dark:bg-white/5 dark:border-white/10 dark:text-white"
                        >
                        @error('whatsapp_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-950 dark:text-white mb-1">Pesan Awal Chat</label>
                        <input
                            type="text"
                            name="whatsapp_message"
                            value="{{ old('whatsapp_message', $setting->whatsapp_message) }}"
                            placeholder="Halo, saya ingin bertanya..."
                            class="fi-input w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-primary-500 dark:bg-white/5 dark:border-white/10 dark:text-white"
                        >
                    </div>
                </div>
            </div>
        </x-filament::section>

        {{-- MEDIA SOSIAL --}}
        <x-filament::section heading="Media Sosial">
            <p class="text-xs text-gray-500 mb-3">Kosongkan kolom untuk menyembunyikan ikonnya dari footer.</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-950 dark:text-white mb-1">Facebook URL</label>
                    <input type="url" name="facebook_url" value="{{ old('facebook_url', $setting->facebook_url) }}" placeholder="https://facebook.com/..." class="fi-input w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-primary-500 dark:bg-white/5 dark:border-white/10 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-950 dark:text-white mb-1">Instagram URL</label>
                    <input type="url" name="instagram_url" value="{{ old('instagram_url', $setting->instagram_url) }}" placeholder="https://instagram.com/..." class="fi-input w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-primary-500 dark:bg-white/5 dark:border-white/10 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-950 dark:text-white mb-1">Threads URL</label>
                    <input type="url" name="threads_url" value="{{ old('threads_url', $setting->threads_url) }}" placeholder="https://threads.net/@..." class="fi-input w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-primary-500 dark:bg-white/5 dark:border-white/10 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-950 dark:text-white mb-1">X (Twitter) URL</label>
                    <input type="url" name="twitter_url" value="{{ old('twitter_url', $setting->twitter_url) }}" placeholder="https://x.com/..." class="fi-input w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-primary-500 dark:bg-white/5 dark:border-white/10 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-950 dark:text-white mb-1">TikTok URL</label>
                    <input type="url" name="tiktok_url" value="{{ old('tiktok_url', $setting->tiktok_url) }}" placeholder="https://tiktok.com/@..." class="fi-input w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-primary-500 dark:bg-white/5 dark:border-white/10 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-950 dark:text-white mb-1">YouTube URL</label>
                    <input type="url" name="youtube_url" value="{{ old('youtube_url', $setting->youtube_url) }}" placeholder="https://youtube.com/..." class="fi-input w-full rounded-lg border border-gray-300 px-3 py-2 text-sm shadow-sm focus:ring-2 focus:ring-primary-500 dark:bg-white/5 dark:border-white/10 dark:text-white">
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-3">Email redaksi pada bagian "Footer & Kontak" di atas otomatis tampil sebagai ikon surat jika diisi.</p>
        </x-filament::section>

        {{-- ANALITIK --}}
        <x-filament::section heading="Integrasi Analitik">
            <div>
                <label class="block text-sm font-medium text-gray-950 dark:text-white mb-1">Kode Tracking (Google Analytics / Cloudflare)</label>
                <p class="text-xs text-gray-500 mb-2">Tempelkan tag &lt;script&gt; lengkap dari provider analitik Anda. Kode ini akan diload di &lt;head&gt; semua halaman publik.</p>
                <textarea name="analytics_code" rows="6" class="fi-input w-full rounded-lg border border-gray-300 px-3 py-2 text-sm font-mono shadow-sm focus:ring-2 focus:ring-primary-500 dark:bg-white/5 dark:border-white/10 dark:text-white">{{ old('analytics_code', $setting->analytics_code) }}</textarea>
            </div>
        </x-filament::section>

        {{-- SUBMIT --}}
        <div class="flex justify-end">
            <button
                type="submit"
                class="fi-btn fi-btn-color-primary fi-btn-size-md inline-grid grid-flow-col items-center justify-center gap-1.5 font-semibold outline-none transition duration-75 focus-visible:ring-2 fi-btn-label px-4 py-2 rounded-lg bg-primary-600 text-white hover:bg-primary-500 shadow-sm text-sm"
            >
                Simpan Perubahan
            </button>
        </div>
    </form>
</x-filament-panels::page>
