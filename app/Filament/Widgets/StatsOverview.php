<?php

namespace App\Filament\Widgets;

use App\Models\Comment;
use App\Models\Post;
use App\Models\PostView;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columns = 4;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Berita', Post::where('status', 'Published')->count())
                ->description('Berita yang sudah diterbitkan')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('success'),
            Stat::make('Total Pembaca Berita', number_format(Post::where('status', 'Published')->sum('views_count')))
                ->description('Total tayangan (views) berita')
                ->descriptionIcon('heroicon-m-eye')
                ->color('info'),
            Stat::make('Pembaca Hari Ini', number_format(PostView::whereBetween('viewed_at', [now()->startOfDay(), now()->endOfDay()])->count()))
                ->description('Kunjungan artikel hari ini')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('success'),
            Stat::make('Total Komentar', Comment::count())
                ->description('Komentar dari pembaca')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('warning'),
        ];
    }
}
