@php
    use App\Filament\Pages\Dashboard;
    use App\Filament\Resources\PostResource;
    use Filament\Facades\Filament;

    if (! Filament::auth()->check()) {
        return;
    }

    $canPosts = PostResource::canViewAny();
    $homeUrl = Dashboard::getUrl();
    $postsUrl = $canPosts ? PostResource::getUrl('index') : $homeUrl;
    $createUrl = $canPosts ? PostResource::getUrl('create') : $homeUrl;
    $queueUrl = $canPosts ? PostResource::getUrl('queue') : $homeUrl;

    $path = '/'.ltrim(request()->path(), '/');
    $isHome = $path === '/admin' || $path === '/admin/';
    $isCreate = str_contains($path, '/admin/posts/create');
    $isEdit = (bool) preg_match('#/admin/posts/[^/]+/edit#', $path);
    $isQueue = str_contains($path, '/admin/posts/antrian');
    $isPosts = str_contains($path, '/admin/posts') && ! $isCreate && ! $isEdit && ! $isQueue;
@endphp

<nav id="ks-mobile-bottom-nav" class="ks-mobile-bottom-nav" aria-label="Navigasi cepat mobile">
    <a href="{{ $homeUrl }}" class="ks-mbn-item {{ $isHome ? 'is-active' : '' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5 12 3l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1v-9.5z"/></svg>
        <span>Beranda</span>
    </a>

    @if($canPosts)
        <a href="{{ $postsUrl }}" class="ks-mbn-item {{ $isPosts || $isEdit ? 'is-active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h7l2 2h5a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2z"/></svg>
            <span>Berita</span>
        </a>

        <a href="{{ $createUrl }}" class="ks-mbn-item ks-mbn-compose {{ $isCreate ? 'is-active' : '' }}" aria-label="Buat berita">
            <span class="ks-mbn-compose-btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/></svg>
            </span>
            <span>Tulis</span>
        </a>

        <a href="{{ $queueUrl }}" class="ks-mbn-item {{ $isQueue ? 'is-active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h10M4 18h7"/></svg>
            <span>Antrian</span>
        </a>
    @endif

    <button type="button" class="ks-mbn-item" data-ks-open-sidebar aria-label="Menu">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
        <span>Menu</span>
    </button>
</nav>

<script>
(function () {
    var btn = document.querySelector('[data-ks-open-sidebar]');
    if (!btn) return;
    btn.addEventListener('click', function () {
        var openers = document.querySelectorAll('.fi-topbar-open-sidebar-btn, .fi-topbar-close-sidebar-btn, button[aria-controls*="sidebar"]');
        for (var i = 0; i < openers.length; i++) {
            if (openers[i].offsetParent !== null || getComputedStyle(openers[i]).display !== 'none') {
                openers[i].click();
                return;
            }
        }
        var fallback = document.querySelector('button.fi-icon-btn');
        if (fallback) fallback.click();
    });
})();
</script>
