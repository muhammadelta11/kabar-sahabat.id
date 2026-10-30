<?php

namespace App\Filament\Widgets;

use App\Models\PostView;
use Filament\Widgets\ChartWidget;

class ViewsChart extends ChartWidget
{
    protected static ?string $heading = 'Pembaca per Hari (7 Hari Terakhir)';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $days = 7;
        $labels = [];
        $data = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->startOfDay();
            $labels[] = $date->format('d M');
            $data[] = PostView::query()
                ->whereBetween('viewed_at', [$date, $date->copy()->endOfDay()])
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pembaca',
                    'data' => $data,
                    'borderColor' => '#003399',
                    'backgroundColor' => 'rgba(0, 51, 153, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }
}
