<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\UserRole;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class RedakturPerformanceTable extends BaseWidget
{
    protected static ?string $heading = 'Performa Redaktur';

    protected static ?string $description = 'Operasional harian/mingguan. Hub lengkap (konsistensi + bulanan + top viewer) di Redaksi → Performa Redaksi.';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->canAccess('view_redaktur_performance') ?? false;
    }

    public static function cacheKey(): string
    {
        return 'admin.redaktur_performance.'.now()->toDateString();
    }

    public static function forgetCache(): void
    {
        Cache::forget(static::cacheKey());
    }

    /**
     * @return Collection<int, User>
     */
    public static function cachedRows(): Collection
    {
        $today = now()->toDateString();
        $weekStart = now()->startOfWeek()->toDateTimeString();

        return Cache::remember(static::cacheKey(), 60, function () use ($today, $weekStart) {
            return User::query()
                ->select(['id', 'name', 'role'])
                ->whereIn('role', [UserRole::Redaktur, UserRole::PimpinanRedaktur])
                ->withCount([
                    'posts as uploaded_today' => fn (Builder $query) => $query
                        ->whereDate('created_at', $today),
                    'posts as uploaded_week' => fn (Builder $query) => $query
                        ->where('created_at', '>=', $weekStart),
                    'posts as published_today' => fn (Builder $query) => $query
                        ->where('status', 'Published')
                        ->whereDate('published_at', $today),
                    'posts as published_week' => fn (Builder $query) => $query
                        ->where('status', 'Published')
                        ->where('published_at', '>=', $weekStart),
                ])
                ->orderByDesc('uploaded_today')
                ->get();
        });
    }

    public function getTableRecords(): Collection|Paginator|CursorPaginator
    {
        return static::cachedRows();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->select(['id', 'name', 'role'])
                    ->whereIn('role', [UserRole::Redaktur, UserRole::PimpinanRedaktur])
            )
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Redaktur')
                    ->description(fn (User $record): string => $record->role?->label() ?? ''),
                Tables\Columns\TextColumn::make('uploaded_today')
                    ->label('Upload hari ini')
                    ->numeric()
                    ->alignCenter()
                    ->color(fn (int $state): string => $state > 0 ? 'success' : 'gray'),
                Tables\Columns\TextColumn::make('published_today')
                    ->label('Terbit hari ini')
                    ->numeric()
                    ->alignCenter()
                    ->color(fn (int $state): string => $state > 0 ? 'info' : 'gray'),
                Tables\Columns\TextColumn::make('uploaded_week')
                    ->label('Upload minggu ini')
                    ->numeric()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('published_week')
                    ->label('Terbit minggu ini')
                    ->numeric()
                    ->alignCenter(),
            ])
            ->emptyStateHeading('Belum ada akun redaktur')
            ->emptyStateDescription('Tambahkan redaktur di menu Pengguna agar performa bisa dipantau.');
    }
}
