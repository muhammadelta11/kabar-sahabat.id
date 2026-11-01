<?php

namespace App\Filament\Widgets;

use App\Models\PostView;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ViewsChart extends ChartWidget
{
    protected static ?string $heading = 'Pembaca per Hari (7 Hari Terakhir)';

    protected static ?int $sort = 2;

    protected static ?string $pollingInterval = null;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = auth()->user();

        return ($user?->canAccess('view_business_dashboard') || $user?->canAccess('view_editorial_dashboard')) ?? false;
    }

    protected function getData(): array
    {
        $payload = Cache::remember('admin.views_chart_7d', 120, function () {
            $start = now()->subDays(6)->startOfDay();
            $driver = DB::connection()->getDriverName();

            $dateExpression = match ($driver) {
                'sqlite' => "strftime('%Y-%m-%d', viewed_at)",
                'pgsql' => 'DATE(viewed_at)',
                default => 'DATE(viewed_at)',
            };

            $rows = PostView::query()
                ->selectRaw("{$dateExpression} as day, COUNT(*) as total")
                ->where('viewed_at', '>=', $start)
                ->groupBy('day')
                ->pluck('total', 'day');

            $labels = [];
            $data = [];

            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->startOfDay();
                $key = $date->format('Y-m-d');
                $labels[] = $date->format('d M');
                $data[] = (int) ($rows[$key] ?? 0);
            }

            return compact('labels', 'data');
        });

        return [
            'datasets' => [
                [
                    'label' => 'Pembaca',
                    'data' => $payload['data'],
                    'borderColor' => '#003399',
                    'backgroundColor' => 'rgba(0, 51, 153, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $payload['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        // Warna netral agar label chart terbaca di light & dark tanpa reload theme.
        $tick = 'rgb(100, 116, 139)';
        $grid = 'rgba(148, 163, 184, 0.25)';

        return [
            'plugins' => [
                'legend' => [
                    'labels' => [
                        'color' => $tick,
                    ],
                ],
            ],
            'scales' => [
                'x' => [
                    'ticks' => [
                        'color' => $tick,
                    ],
                    'grid' => [
                        'color' => $grid,
                    ],
                ],
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                        'color' => $tick,
                    ],
                    'grid' => [
                        'color' => $grid,
                    ],
                ],
            ],
        ];
    }
}
