<?php

namespace App\Filament\Pages;

use App\Support\EditorialInsights;
use App\Support\NewsCache;
use App\Support\RedakturMonthlyRecap;
use App\Support\RedakturOperationalRecap;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;

class EditorialPerformanceHub extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Performa Redaksi';

    protected static ?string $navigationGroup = 'Redaksi';

    protected static ?int $navigationSort = 5;

    protected static ?string $title = 'Performa Redaksi & Analisis';

    protected static ?string $slug = 'performa-redaksi';

    protected static string $view = 'filament.pages.editorial-performance-hub';

    public ?string $month = null;

    public static function canAccess(): bool
    {
        return auth()->user()?->canAccess('view_redaktur_performance') ?? false;
    }

    public function mount(): void
    {
        $this->month = now()->format('Y-m');
        $this->form->fill([
            'month' => $this->month,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('month')
                    ->label('Bulan rekapan')
                    ->options(RedakturMonthlyRecap::monthOptions())
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (?string $state): void {
                        $this->month = $state ?: now()->format('Y-m');
                    }),
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $month = $this->month ?: now()->format('Y-m');
        $monthStart = RedakturMonthlyRecap::parseMonth($month);
        $settings = NewsCache::siteSettings();

        return [
            'operationalRows' => RedakturOperationalRecap::rows(),
            'weekDaysCounted' => RedakturOperationalRecap::weekDaysCounted(),
            'monthLabel' => $monthStart->translatedFormat('F Y'),
            'monthlyRows' => RedakturMonthlyRecap::rows($month),
            'daysCounted' => RedakturMonthlyRecap::consistencyDenominator($monthStart),
            'targetPublished' => RedakturMonthlyRecap::TARGET_PUBLISHED,
            'targetViews' => RedakturMonthlyRecap::TARGET_VIEWS,
            'topPostsAllTime' => EditorialInsights::topPostsAllTime(10),
            'topPosts7d' => EditorialInsights::topPostsLast7Days(10),
            'topCategories7d' => EditorialInsights::topCategoriesLast7Days(5),
            'gaMeasurementId' => $settings->ga_measurement_id,
            'gaDashboardUrl' => $settings->ga_dashboard_url,
            'gaLookerEmbedUrl' => $settings->ga_looker_embed_url,
            'hasAnalyticsSnippet' => filled($settings->analytics_code),
            'canViewBusiness' => auth()->user()?->canAccess('view_business_dashboard') ?? false,
        ];
    }
}
