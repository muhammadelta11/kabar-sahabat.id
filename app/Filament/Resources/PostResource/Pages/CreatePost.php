<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use App\Support\AutoRelatedPosts;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Js;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    protected static bool $canCreateAnother = false;

    public ?string $saveIntent = null;

    public function getTitle(): string
    {
        return 'Tulis';
    }

    public function getHeading(): string
    {
        return 'Tulis';
    }

    public function getSubheading(): ?string
    {
        return null;
    }

    public function getMaxContentWidth(): MaxWidth|string|null
    {
        return MaxWidth::Full;
    }

    /**
     * @return array<Action>
     */
    protected function getHeaderActions(): array
    {
        return [];
    }

    /**
     * @return array<Action>
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('publish')
                ->label('Publish')
                ->icon('heroicon-m-globe-alt')
                ->color('success')
                ->action(function (): void {
                    $this->saveIntent = 'Published';
                    $this->data['status'] = 'Published';
                    $this->data['published_at'] ??= now();
                    $this->create();
                }),
            Action::make('saveDraft')
                ->label('Simpan (Draft)')
                ->icon('heroicon-m-document')
                ->color('warning')
                ->keyBindings(['mod+s'])
                ->action(function (): void {
                    $this->saveIntent = 'Draft';
                    $this->data['status'] = 'Draft';
                    $this->create();
                }),
            Action::make('cancel')
                ->label('Batal')
                ->color('gray')
                ->alpineClickHandler(
                    'document.referrer ? window.history.back() : (window.location.href = '.Js::from(PostResource::getUrl('index')).')'
                ),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] ??= auth()->id();
        $data['status'] = $this->saveIntent ?? ($data['status'] ?? 'Draft');

        if ($data['status'] === 'Published' && blank($data['published_at'] ?? null)) {
            $data['published_at'] = now();
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }

    protected function hasUnsavedDataChangesAlert(): bool
    {
        return true;
    }

    protected function afterCreate(): void
    {
        AutoRelatedPosts::syncFor($this->record);
        PostResource::forgetPendingReviewCount();
    }
}
