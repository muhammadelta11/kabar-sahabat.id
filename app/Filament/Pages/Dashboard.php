<?php

namespace App\Filament\Pages;

use App\Filament\Resources\PostResource;
use App\Filament\Widgets\EditorialQueueOverview;
use App\Filament\Widgets\PopularPostsTable;
use App\Filament\Widgets\QuickComposeWidget;
use App\Filament\Widgets\RedakturPerformanceTable;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\ViewsChart;
use Filament\Actions;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Beranda';

    protected static ?string $title = 'Beranda';

    public function getHeading(): string
    {
        $name = auth()->user()?->name ?? 'Redaksi';
        $hour = (int) now()->format('G');

        $salam = match (true) {
            $hour < 11 => 'Selamat pagi',
            $hour < 15 => 'Selamat siang',
            $hour < 18 => 'Selamat sore',
            default => 'Selamat malam',
        };

        return "{$salam}, {$name}";
    }

    public function getSubheading(): ?string
    {
        $role = auth()->user()?->role?->label() ?? 'Pengguna';

        return "Panel {$role} · ".now()->translatedFormat('l, d F Y').' · fokus kerja yang nyaman dan ringkas';
    }

    protected function getHeaderActions(): array
    {
        if (! PostResource::canViewAny()) {
            return [];
        }

        return [
            Actions\Action::make('buatBerita')
                ->label('Buat Berita')
                ->icon('heroicon-m-pencil-square')
                ->color('warning')
                ->url(PostResource::getUrl('create')),
            Actions\Action::make('kelolaBerita')
                ->label('Kelola Berita')
                ->icon('heroicon-m-newspaper')
                ->color('gray')
                ->url(PostResource::getUrl('index')),
        ];
    }

    public function getColumns(): int|string|array
    {
        return [
            'default' => 1,
            'lg' => 2,
        ];
    }

    /**
     * @return array<class-string>
     */
    public function getWidgets(): array
    {
        return [
            QuickComposeWidget::class,
            EditorialQueueOverview::class,
            StatsOverview::class,
            ViewsChart::class,
            RedakturPerformanceTable::class,
            PopularPostsTable::class,
        ];
    }
}
