<?php

namespace App\Providers\Filament;

use App\Filament\Auth\Login;
use App\Filament\Pages\Dashboard;
use App\Http\Middleware\AuthenticateFilament;
use App\Models\Setting;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
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
            ->login(Login::class)
            ->brandName('kabarsahabat.id')
            // Login (latar putih) = logo terang; sidebar/topbar gelap = logo dark via CSS.
            ->brandLogo(function (): HtmlString {
                $path = Setting::query()->value('logo_path');
                $light = $path ? media_url($path) : null;
                $dark = asset('images/brand/logo-dark.png');

                $lightHtml = $light
                    ? '<img src="'.e($light).'" alt="kabarsahabat.id" class="ks-brand-logo ks-brand-logo--light">'
                    : '<span class="ks-brand-logo ks-brand-logo--light text-xl font-extrabold tracking-tight">kabarsahabat.id</span>';

                return new HtmlString(
                    $lightHtml.
                    '<img src="'.e($dark).'" alt="kabarsahabat.id" class="ks-brand-logo ks-brand-logo--dark">'
                );
            })
            ->brandLogoHeight('3.5rem')
            ->favicon(asset('favicon.ico'))
            ->colors([
                'primary' => Color::hex('#003399'),
                'warning' => Color::hex('#f97316'),
                'danger' => Color::hex('#cc0000'),
                'success' => Color::hex('#059669'),
                'info' => Color::hex('#0284c7'),
                'gray' => Color::Slate,
            ])
            ->font('Plus Jakarta Sans')
            ->sidebarCollapsibleOnDesktop()
            ->collapsedSidebarWidth('4.5rem')
            ->sidebarWidth('16.5rem')
            ->maxContentWidth(MaxWidth::Full)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                //
            ])
            ->navigationGroups([
                'Redaksi',
                'Bisnis',
                'Sistem',
            ])
            ->breadcrumbs(true)
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
                AuthenticateFilament::class,
            ]);
    }

    public function boot(): void
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::STYLES_AFTER,
            fn (): string => new HtmlString(
                '<link rel="stylesheet" href="'.asset('css/filament-admin.css').'?v=15">'
            ),
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::BODY_START,
            function (): string {
                if (! request()->is('admin/posts/create', 'admin/posts/*/edit')) {
                    return '';
                }

                return (string) new HtmlString(<<<'HTML'
                    <script>document.documentElement.classList.add('ks-writer-mode');document.body?.classList.add('ks-writer-mode');</script>
                    <style id="ks-writer-critical">
                    @media (max-width:1023px){
                      .fi-main{padding-left:0!important;padding-right:0!important;max-width:100%!important}
                      .ks-writer-title-wrap .fi-input-wrp{background:transparent!important;border:0!important;box-shadow:none!important}
                      .ks-writer-title{font-size:1.45rem!important;font-weight:700!important;letter-spacing:-.03em!important}
                    }
                    </style>
                    HTML);
            },
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::USER_MENU_BEFORE,
            fn (): string => new HtmlString(<<<'HTML'
                <a href="#" id="btn-lihat-situs" title="Preview situs publik">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:14px;height:14px;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418"/>
                    </svg>
                    Preview Situs
                </a>
            HTML),
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::BODY_END,
            fn (): string => view('filament.components.mobile-bottom-nav')->render(),
        );

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
            fn (): string => new HtmlString(
                '<link rel="icon" href="'.asset('favicon.ico').'" type="image/x-icon">'
            ),
        );
    }
}
