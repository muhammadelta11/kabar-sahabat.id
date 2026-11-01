<?php

namespace App\Filament\Widgets;

use App\Models\Advertisement;
use App\Models\Post;
use App\Models\PostView;
use App\Support\NewsCache;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Number;

class BusinessStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected static ?string $pollingInterval = null;

    protected int|string|array $columns = 4;

    public static function canView(): bool
    {
        return auth()->user()?->canAccess('view_business_dashboard') ?? false;
    }

    protected function getStats(): array
    {
        $stats = Cache::remember('admin.business_stats.v2', 60, function () {
            return [
                'active_ads' => Advertisement::query()->where('is_active', true)->count(),
                'total_ads' => Advertisement::query()->count(),
                'mrr' => (int) Advertisement::query()
                    ->where('is_active', true)
                    ->whereNotNull('monthly_rate')
                    ->sum('monthly_rate'),
                'views_week' => PostView::query()
                    ->where('viewed_at', '>=', now()->subDays(7)->startOfDay())
                    ->count(),
                'published' => Post::query()->where('status', 'Published')->count(),
            ];
        });

        $settings = NewsCache::siteSettings();
        $gaReady = filled($settings->ga_measurement_id) || filled($settings->analytics_code);

        return [
            Stat::make('Iklan Aktif', number_format($stats['active_ads']))
                ->description('Dari '.$stats['total_ads'].' slot terdaftar')
                ->descriptionIcon('heroicon-m-megaphone')
                ->color('warning'),
            Stat::make('Estimasi MRR', Number::currency($stats['mrr'], 'IDR', 'id'))
                ->description('Jumlah tarif bulanan iklan aktif')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
            Stat::make('Pembaca 7 Hari', number_format($stats['views_week']))
                ->description('Tayangan artikel internal')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('info'),
            Stat::make('Google Analytics', $gaReady ? 'Terhubung' : 'Belum')
                ->description($gaReady
                    ? ($settings->ga_measurement_id ?: 'Snippet tracking aktif')
                    : 'Isi Measurement ID di Pengaturan')
                ->descriptionIcon('heroicon-m-presentation-chart-line')
                ->color($gaReady ? 'success' : 'gray'),
        ];
    }
}
