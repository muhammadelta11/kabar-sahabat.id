<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\CommentResource;
use App\Filament\Resources\PostResource;
use App\Models\Comment;
use App\Models\Post;
use App\Models\PostView;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected static ?string $pollingInterval = null;

    protected int|string|array $columns = 4;

    public static function canView(): bool
    {
        return auth()->user()?->canAccess('view_editorial_dashboard') ?? false;
    }

    protected function getStats(): array
    {
        $stats = Cache::remember('admin.stats_overview', 60, function () {
            return [
                'published' => Post::query()->where('status', 'Published')->count(),
                'views' => (int) Post::query()->where('status', 'Published')->sum('views_count'),
                'today' => PostView::query()
                    ->where('viewed_at', '>=', now()->startOfDay())
                    ->count(),
                'comments' => Comment::query()->count(),
            ];
        });

        return [
            Stat::make('Total Berita', number_format($stats['published']))
                ->description('Berita yang sudah diterbitkan')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('success')
                ->url(PostResource::getUrl('index')),
            Stat::make('Total Pembaca Berita', number_format($stats['views']))
                ->description('Total tayangan (views) berita')
                ->descriptionIcon('heroicon-m-eye')
                ->color('info'),
            Stat::make('Pembaca Hari Ini', number_format($stats['today']))
                ->description('Kunjungan artikel hari ini')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('success'),
            Stat::make('Total Komentar', number_format($stats['comments']))
                ->description('Komentar dari pembaca')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('warning')
                ->url(CommentResource::getUrl('index')),
        ];
    }
}
