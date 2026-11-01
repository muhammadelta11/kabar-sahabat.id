<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\EditorialQueueOverview;
use App\Filament\Widgets\PopularPostsTable;
use App\Filament\Widgets\RedakturPerformanceTable;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\ViewsChart;
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

    public function getColumns(): int|string|array
    {
        return [
            'default' => 1,
            'lg' => 2,
        ];
    }

    /**
     * Widget bisnis (iklan/MRR) tetap di menu Analytics — beranda hanya ringkas editorial.
     *
     * @return array<class-string>
     */
    public function getWidgets(): array
    {
        return [
            EditorialQueueOverview::class,
            StatsOverview::class,
            ViewsChart::class,
            RedakturPerformanceTable::class,
            PopularPostsTable::class,
        ];
    }
}
