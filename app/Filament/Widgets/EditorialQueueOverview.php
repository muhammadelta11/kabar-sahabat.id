<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\PostResource;
use App\Models\Post;
use App\Models\User;
use App\UserRole;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class EditorialQueueOverview extends BaseWidget
{
    protected static ?int $sort = 0;

    protected static ?string $pollingInterval = null;

    protected int|string|array $columns = 4;

    public static function canView(): bool
    {
        return auth()->user()?->canAccess('view_editorial_dashboard') ?? false;
    }

    protected function getStats(): array
    {
        $stats = Cache::remember('admin.editorial_queue', 60, function () {
            $weekStart = now()->startOfWeek();

            return [
                'pending' => Post::query()->where('status', 'Menunggu Review')->count(),
                'draft' => Post::query()->where('status', 'Draft')->count(),
                'published_week' => Post::query()
                    ->where('status', 'Published')
                    ->where('published_at', '>=', $weekStart)
                    ->count(),
                'redaktur' => User::query()->where('role', UserRole::Redaktur)->count(),
            ];
        });

        $targetWeek = 14; // target awal sederhana; nanti bisa dari settings
        $progress = $stats['published_week'].' / '.$targetWeek;

        $postsUrl = PostResource::getUrl('index');
        $queueUrl = PostResource::getUrl('queue');

        return [
            Stat::make('Antrian Review', number_format($stats['pending']))
                ->description('Kiriman menunggu validasi')
                ->descriptionIcon('heroicon-m-inbox-stack')
                ->color($stats['pending'] > 0 ? 'warning' : 'success')
                ->url($queueUrl),
            Stat::make('Draft', number_format($stats['draft']))
                ->description('Naskah belum siap tayang')
                ->descriptionIcon('heroicon-m-pencil-square')
                ->color('gray')
                ->url($postsUrl.'?activeTab=draft'),
            Stat::make('Terbit Minggu Ini', $progress)
                ->description('Target awal '.$targetWeek.' berita / minggu')
                ->descriptionIcon('heroicon-m-flag')
                ->color($stats['published_week'] >= $targetWeek ? 'success' : 'info')
                ->url($postsUrl.'?activeTab=published'),
            Stat::make('Redaktur Aktif', number_format($stats['redaktur']))
                ->description('Akun role redaktur')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),
        ];
    }
}
