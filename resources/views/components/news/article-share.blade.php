@props([
    'url' => null,
    'title' => null,
])

@php
    $shareUrl = $url ?? url()->current();
    $shareTitle = $title ?? trim($__env->yieldContent('title') ?: config('app.name'));
    $encodedUrl = rawurlencode($shareUrl);
    $encodedTitle = rawurlencode($shareTitle);
@endphp

<div {{ $attributes->class(['flex flex-wrap items-center gap-2']) }} data-article-share>
    <span class="text-[10px] font-bold uppercase tracking-wider text-ink-muted mr-0.5">Bagikan</span>

    {{-- Hanya URL: WhatsApp menampilkan kartu preview (judul + gambar) dari Open Graph --}}
    <a
        href="https://api.whatsapp.com/send?text={{ $encodedUrl }}"
        target="_blank"
        rel="noopener noreferrer"
        aria-label="Bagikan ke WhatsApp"
        title="WhatsApp"
        class="w-9 h-9 rounded-full bg-[#25D366] text-white flex items-center justify-center hover:opacity-90 transition-opacity"
    >
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
    </a>

    <a
        href="https://www.facebook.com/sharer/sharer.php?u={{ $encodedUrl }}"
        target="_blank"
        rel="noopener noreferrer"
        aria-label="Bagikan ke Facebook"
        title="Facebook"
        class="w-9 h-9 rounded-full bg-[#1877F2] text-white flex items-center justify-center hover:opacity-90 transition-opacity"
    >
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M9.101 23.691v-7.98H6.627v-3.667h2.474v-1.58c0-4.085 1.848-5.978 5.858-5.978.401 0 .955.042 1.468.103a8.68 8.68 0 0 1 1.141.195v3.325a8.623 8.623 0 0 0-.653-.036 26.805 26.805 0 0 0-.733-.009c-.707 0-1.259.096-1.675.309a1.686 1.686 0 0 0-.679.622c-.258.42-.374.995-.374 1.752v1.297h3.919l-.386 2.103-.287 1.564h-3.246v8.245C19.396 23.238 24 18.179 24 12.044c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.628 3.874 10.35 9.101 11.647Z"/></svg>
    </a>

    <a
        href="https://twitter.com/intent/tweet?text={{ $encodedTitle }}&url={{ $encodedUrl }}"
        target="_blank"
        rel="noopener noreferrer"
        aria-label="Bagikan ke X"
        title="X"
        class="w-9 h-9 rounded-full bg-ink text-white flex items-center justify-center hover:opacity-90 transition-opacity"
    >
        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M18.901 1.153h3.68l-8.04 9.19L24 22.846h-7.406l-5.8-7.584-6.638 7.584H.474l8.6-9.83L0 1.154h7.594l5.243 6.932ZM17.61 20.644h2.039L6.486 3.24H4.298Z"/></svg>
    </a>

    <button
        type="button"
        id="native-share-btn"
        aria-label="Bagikan artikel"
        title="Bagikan"
        class="hidden w-9 h-9 rounded-full bg-brand text-white items-center justify-center hover:bg-brand-dark transition-colors"
    >
        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92s2.92-1.31 2.92-2.92-1.31-2.92-2.92-2.92z"/>
        </svg>
    </button>

    <button
        type="button"
        id="copy-link-btn"
        aria-label="Salin tautan"
        title="Salin tautan"
        class="w-9 h-9 rounded-full bg-neutral-100 text-ink flex items-center justify-center hover:bg-accent hover:text-white transition-colors border border-neutral-200"
    >
        <svg data-copy-icon="link" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/>
        </svg>
        <svg data-copy-icon="check" class="hidden w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
        </svg>
    </button>
</div>
