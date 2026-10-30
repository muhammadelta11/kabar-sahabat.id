<?php

namespace App\Providers\Filament;

use App\Models\Setting;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('kabarsahabat.id')
            ->brandLogo(function (): ?string {
                $path = Setting::query()->value('logo_path');

                return $path ? asset('storage/'.$path) : null;
            })
            ->brandLogoHeight('3rem')
            ->colors([
                'primary' => Color::hex('#003399'), // Biru
                'warning' => Color::hex('#FFCC00'), // Kuning
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                //
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    public function boot(): void
    {
        // Tombol "Situs Publik" di topbar, tepat di kiri ikon profil.
        // Membuka preview situs dalam modal (iframe lazy-load), klik di luar modal untuk kembali.
        FilamentView::registerRenderHook(
            PanelsRenderHook::USER_MENU_BEFORE,
            fn (): string => new HtmlString(<<<'HTML'
                <a href="#" id="btn-lihat-situs" title="Preview situs publik">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:14px;height:14px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418"/>
                    </svg>
                    Preview Situs Publik
                </a>
                <style>
                    #btn-lihat-situs {
                        display: inline-flex;
                        align-items: center;
                        gap: 0.4rem;
                        margin-right: 0.15rem;
                        padding: 0.3rem 0.85rem;
                        border: 1px solid rgba(255, 255, 255, 0.45);
                        border-radius: 9999px;
                        font-size: 11px;
                        font-weight: 800;
                        text-transform: uppercase;
                        letter-spacing: 0.05em;
                        color: #ffffff !important;
                        transition: all 150ms ease;
                    }
                    #btn-lihat-situs:hover {
                        background: #FFCC00;
                        border-color: #FFCC00;
                        color: #111111 !important;
                    }
                </style>
            HTML),
        );

        // Modal preview situs publik (iframe dimuat hanya saat pertama kali dibuka)
        FilamentView::registerRenderHook(
            PanelsRenderHook::BODY_END,
            fn (): string => new HtmlString(<<<'HTML'
                <div id="preview-modal">
                    <div id="preview-panel">
                        <div id="preview-header">
                            <span>Preview Situs Publik</span>
                            <span id="preview-actions">
                                <a href="/" target="_blank" rel="noopener" title="Buka di tab baru">Tab Baru &#8599;</a>
                                <button type="button" id="preview-close" title="Tutup preview (Esc)">&#10005;</button>
                            </span>
                        </div>
                        <iframe id="preview-frame" title="Preview Situs Publik"></iframe>
                    </div>
                </div>
                <style>
                    #preview-modal {
                        display: none;
                        position: fixed;
                        inset: 0;
                        z-index: 99999;
                        background: rgba(0, 0, 0, 0.65);
                        align-items: center;
                        justify-content: center;
                        padding: 2rem;
                    }
                    #preview-panel {
                        display: flex;
                        flex-direction: column;
                        width: min(1100px, 94vw);
                        height: 90vh;
                        background: #ffffff;
                        border-radius: 12px;
                        overflow: hidden;
                        box-shadow: 0 25px 60px rgba(0, 0, 0, 0.35);
                    }
                    #preview-header {
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        padding: 0.6rem 1rem;
                        background: #003399;
                        color: #ffffff;
                        font-size: 12px;
                        font-weight: 800;
                        text-transform: uppercase;
                        letter-spacing: 0.06em;
                        border-bottom: 3px solid #FFCC00;
                    }
                    #preview-header a, #preview-header button {
                        color: #ffffff !important;
                        background: none;
                        border: none;
                        cursor: pointer;
                        font-size: 12px;
                        font-weight: 700;
                    }
                    #preview-header a:hover, #preview-header button:hover { color: #FFCC00 !important; }
                    #preview-actions { display: inline-flex; gap: 1rem; align-items: center; }
                    #preview-frame {
                        flex: 1;
                        width: 100%;
                        border: 0;
                        background: #f5f5f5 url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='36' height='36' viewBox='0 0 24 24'%3E%3Ccircle cx='12' cy='12' r='10' stroke='%23cccccc' fill='none' stroke-width='2'/%3E%3C/svg%3E") center no-repeat;
                    }
                </style>
                <script>
                    (function () {
                        var button = document.getElementById('btn-lihat-situs');
                        var modal = document.getElementById('preview-modal');
                        var frame = document.getElementById('preview-frame');

                        if (!button || !modal || !frame) return;

                        function openPreview() {
                            if (!frame.src) frame.src = '/';
                            modal.style.display = 'flex';
                            document.body.style.overflow = 'hidden';
                        }

                        function closePreview() {
                            modal.style.display = 'none';
                            document.body.style.overflow = '';
                        }

                        button.addEventListener('click', function (e) { e.preventDefault(); openPreview(); });
                        modal.addEventListener('click', function (e) { if (e.target === modal) closePreview(); });
                        document.getElementById('preview-close').addEventListener('click', closePreview);
                        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closePreview(); });
                    })();
                </script>
            HTML),
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_END,
            fn (): string => new HtmlString('
                <link rel="icon" href="'.asset('favicon.ico').'" type="image/x-icon">
                <style>
                    /* Custom Brand Colors for Filament Topbar and Sidebar Header */
                    .fi-topbar,
                    .fi-topbar > nav {
                        background-color: #003399 !important;
                        height: 4rem !important;
                        border-bottom: 4px solid #FFCC00 !important;
                    }
                    /* Stat card dashboard: lebih ramping agar proporsional di grid 4 kolom */
                    .fi-wi-stats-overview-stat {
                        padding: 0.85rem 1rem !important;
                        gap: 0.15rem !important;
                    }
                    .fi-wi-stats-overview-stat-value {
                        font-size: 1.35rem !important;
                        line-height: 1.6rem !important;
                    }
                    .fi-topbar button, .fi-topbar a, .fi-topbar svg, .fi-topbar span, .fi-topbar div {
                        color: #ffffff !important;
                    }
                    /* Reset colors inside dropdown panels with higher specificity */
                    .fi-topbar .fi-dropdown-panel a,
                    .fi-topbar .fi-dropdown-panel button,
                    .fi-topbar .fi-dropdown-panel span,
                    .fi-topbar .fi-dropdown-panel svg,
                    .fi-topbar .fi-dropdown-panel div {
                        color: #1e293b !important;
                    }
                    .dark .fi-topbar .fi-dropdown-panel a,
                    .dark .fi-topbar .fi-dropdown-panel button,
                    .dark .fi-topbar .fi-dropdown-panel span,
                    .dark .fi-topbar .fi-dropdown-panel svg,
                    .dark .fi-topbar .fi-dropdown-panel div {
                        color: #f1f5f9 !important;
                    }
                    /* Sidebar Header (logo area) */
                    .fi-sidebar-header {
                        background-color: #003399 !important;
                        height: 4rem !important;
                        display: flex !important;
                        align-items: center !important;
                        padding-top: 0 !important;
                        padding-bottom: 0 !important;
                        border-bottom: 4px solid #FFCC00 !important;
                    }
                    .fi-sidebar-header .fi-logo, .fi-sidebar-header a, .fi-sidebar-header span {
                        color: #ffffff !important;
                        font-weight: 800 !important;
                    }
                </style>
            ')
        );
    }
}
