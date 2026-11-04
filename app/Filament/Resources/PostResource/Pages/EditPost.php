<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use App\Support\AutoRelatedPosts;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    public function getTitle(): string
    {
        return 'Edit Berita';
    }

    public function getHeading(): string
    {
        return 'Edit Berita';
    }

    public function getSubheading(): ?string
    {
        return Str::limit((string) $this->record->title, 90);
    }

    public function getMaxContentWidth(): MaxWidth|string|null
    {
        return MaxWidth::Full;
    }

    protected function resolveRecord(int|string $key): Model
    {
        /** @var Model $record */
        $record = parent::resolveRecord($key);

        $record->loadMissing([
            'categories:id,name',
            'relatedPosts:id,title,slug,status',
            'user:id,name',
        ]);

        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('lihat')
                ->label('Lihat di situs')
                ->icon('heroicon-m-arrow-top-right-on-square')
                ->url(fn (): string => route('news.show', $this->record->slug))
                ->openUrlInNewTab()
                ->visible(fn (): bool => $this->record->status === 'Published'),
            Actions\DeleteAction::make()
                ->after(fn () => PostResource::forgetPendingReviewCount()),
        ];
    }

    protected function hasUnsavedDataChangesAlert(): bool
    {
        return true;
    }

    protected function afterSave(): void
    {
        AutoRelatedPosts::syncFor($this->record);
        PostResource::forgetPendingReviewCount();
        Cache::forget('admin.posts.status_tabs');
    }
}
