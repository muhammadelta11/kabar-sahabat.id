<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\BusinessStatsOverview;
use App\Filament\Widgets\ViewsChart;
use App\Support\NewsCache;
use Filament\Pages\Page;

class AnalyticsDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static ?string $navigationLabel = 'Analytics';

    protected static ?string $navigationGroup = 'Bisnis';

    protected static ?int $navigationSort = 35;

    protected static ?string $title = 'Performa & Analytics';

    protected static string $view = 'filament.pages.analytics-dashboard';

    public static function canAccess(): bool
    {
        return auth()->user()?->canAccess('view_business_dashboard') ?? false;
    }

    /**
     * @return array<class-string>
     */
    public function getHeaderWidgets(): array
    {
        return [
            BusinessStatsOverview::class,
        ];
    }

    /**
     * @return array<class-string>
     */
    public function getFooterWidgets(): array
    {
        return [
            ViewsChart::class,
        ];
    }

    protected function getViewData(): array
    {
        $settings = NewsCache::siteSettings();

        return [
            'gaMeasurementId' => $settings->ga_measurement_id,
            'gaDashboardUrl' => $settings->ga_dashboard_url,
            'gaLookerEmbedUrl' => $settings->ga_looker_embed_url,
            'hasAnalyticsSnippet' => filled($settings->analytics_code),
        ];
    }
}
