<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use App\Support\AutoRelatedPosts;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Js;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    public ?string $saveIntent = null;

    public function getTitle(): string
    {
        return 'Edit';
    }

    public function getHeading(): string
    {
        return 'Edit';
    }

    public function getSubheading(): ?string
    {
        return null;
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

    /**
     * @return array<Actions\Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('lihat')
                ->label('Lihat')
                ->icon('heroicon-m-arrow-top-right-on-square')
                ->url(fn (): string => route('news.show', $this->record->slug))
                ->openUrlInNewTab()
                ->color('gray')
                ->visible(fn (): bool => $this->record->status === 'Published'),
            Actions\DeleteAction::make()
                ->iconButton()
                ->after(fn () => PostResource::forgetPendingReviewCount()),
        ];
    }

    /**
     * @return array<Actions\Action>
     */
    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('publish')
                ->label('Publish')
                ->icon('heroicon-m-globe-alt')
                ->color('success')
                ->action(function (): void {
                    $this->saveIntent = 'Published';
                    $this->data['status'] = 'Published';
                    $this->data['published_at'] ??= now();
                    $this->save();
                }),
            Actions\Action::make('saveDraft')
                ->label('Simpan (Draft)')
                ->icon('heroicon-m-document')
                ->color('warning')
                ->keyBindings(['mod+s'])
                ->action(function (): void {
                    $this->saveIntent = 'Draft';
                    $this->data['status'] = 'Draft';
                    $this->save();
                }),
            Actions\Action::make('cancel')
                ->label('Batal')
                ->color('gray')
                ->alpineClickHandler(
                    'document.referrer ? window.history.back() : (window.location.href = '.Js::from(PostResource::getUrl('index')).')'
                ),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (filled($this->saveIntent)) {
            $data['status'] = $this->saveIntent;
        }

        if (($data['status'] ?? null) === 'Published' && blank($data['published_at'] ?? null)) {
            $data['published_at'] = now();
        }

        return $data;
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
