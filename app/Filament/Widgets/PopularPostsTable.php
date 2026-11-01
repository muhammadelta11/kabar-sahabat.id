<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class PopularPostsTable extends BaseWidget
{
    protected static ?string $heading = 'Daftar Berita Terpopuler (Top 5)';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()?->canAccess('view_editorial_dashboard') ?? false;
    }

    public static function cacheKey(): string
    {
        return 'admin.popular_posts_top5';
    }

    public static function forgetCache(): void
    {
        Cache::forget(static::cacheKey());
    }

    /**
     * @return Collection<int, Post>
     */
    public static function cachedRows(): Collection
    {
        $rows = Cache::get(static::cacheKey());

        if (! is_array($rows)) {
            Cache::forget(static::cacheKey());
            $rows = Cache::remember(static::cacheKey(), 60, function (): array {
                return Post::query()
                    ->published()
                    ->select(['id', 'title', 'views_count', 'published_at', 'status'])
                    ->orderByDesc('views_count')
                    ->limit(5)
                    ->get()
                    ->map(fn (Post $post): array => $post->getAttributes())
                    ->all();
            });
        }

        return Post::query()->hydrate($rows);
    }

    public function getTableRecords(): Collection|Paginator|CursorPaginator
    {
        return static::cachedRows();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Post::query()
                    ->published()
                    ->select(['id', 'title', 'views_count', 'published_at', 'status'])
                    ->orderByDesc('views_count')
                    ->limit(5)
            )
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Berita')
                    ->wrap(),
                Tables\Columns\TextColumn::make('views_count')
                    ->label('Jumlah Pembaca (Views)')
                    ->numeric()
                    ->alignEnd(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Tanggal Terbit')
                    ->dateTime('d M Y H:i'),
            ]);
    }
}
