<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use App\Support\AutoRelatedPosts;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\MaxWidth;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    protected static bool $canCreateAnother = false;

    public function getTitle(): string
    {
        return 'Tulis Berita';
    }

    public function getHeading(): string
    {
        return 'Tulis Berita';
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
    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->label('Simpan')
                ->icon('heroicon-m-check'),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] ??= auth()->id();
        $data['status'] ??= 'Draft';

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
