<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use App\Models\Post;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    public function getTitle(): string
    {
        return 'Berita';
    }

    public function getHeading(): string
    {
        return 'Redaksi Berita';
    }

    public function getSubheading(): ?string
    {
        return 'Kelola naskah seperti ruang redaksi — filter cepat lewat tab status.';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('antrian')
                ->label('Antrian Kontributor')
                ->icon('heroicon-m-inbox-stack')
                ->color('warning')
                ->url(PostResource::getUrl('queue')),
            Actions\CreateAction::make()
                ->label('Tulis Berita'),
        ];
    }

    public function getTabs(): array
    {
        // Cache array saja — Laravel 13 serializable_classes=false tidak mengembalikan Collection.
        $counts = Cache::get('admin.posts.status_tabs');

        if (! is_array($counts)) {
            Cache::forget('admin.posts.status_tabs');
            $counts = Cache::remember('admin.posts.status_tabs', 30, function (): array {
                return Post::query()
                    ->selectRaw('status, COUNT(*) as aggregate')
                    ->groupBy('status')
                    ->pluck('aggregate', 'status')
                    ->map(fn ($count): int => (int) $count)
                    ->all();
            });
        }

        return [
            'semua' => Tab::make('Semua')
                ->badge((string) array_sum($counts)),
            'review' => Tab::make('Menunggu Review')
                ->icon('heroicon-m-clock')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Menunggu Review'))
                ->badge((string) ($counts['Menunggu Review'] ?? 0))
                ->badgeColor('warning'),
            'draft' => Tab::make('Draft')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Draft'))
                ->badge((string) ($counts['Draft'] ?? 0)),
            'published' => Tab::make('Tayang')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Published'))
                ->badge((string) ($counts['Published'] ?? 0))
                ->badgeColor('success'),
            'archived' => Tab::make('Arsip')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', 'Archived'))
                ->badge((string) ($counts['Archived'] ?? 0)),
        ];
    }
}
