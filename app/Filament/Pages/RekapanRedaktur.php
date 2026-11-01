<?php

namespace App\Filament\Pages;

use App\Support\RedakturMonthlyRecap;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;

class RekapanRedaktur extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';

    protected static ?string $navigationLabel = 'Rekapan Redaktur';

    protected static ?string $navigationGroup = 'Redaksi';

    protected static ?int $navigationSort = 6;

    protected static ?string $title = 'Rekapan Bulanan Redaktur';

    protected static string $view = 'filament.pages.rekapan-redaktur';

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
                    ->label('Bulan')
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

        return [
            'monthLabel' => $monthStart->translatedFormat('F Y'),
            'rows' => RedakturMonthlyRecap::rows($month),
            'daysCounted' => RedakturMonthlyRecap::consistencyDenominator($monthStart),
            'targetPublished' => RedakturMonthlyRecap::TARGET_PUBLISHED,
            'targetViews' => RedakturMonthlyRecap::TARGET_VIEWS,
        ];
    }
}
