<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PopularPostsTable extends BaseWidget
{
    protected static ?string $heading = 'Daftar Berita Terpopuler (Top 5)';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Post::query()->published()->limit(5)
            )
            ->defaultSort('views_count', 'desc')
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Berita')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('views_count')
                    ->label('Jumlah Pembaca (Views)')
                    ->numeric()
                    ->sortable()
                    ->alignEnd(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Tanggal Terbit')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ]);
    }
}
