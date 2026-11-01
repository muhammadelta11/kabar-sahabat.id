<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use App\Support\AutoRelatedPosts;
use Filament\Resources\Pages\CreateRecord;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

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
        return 'Naskah di kiri · publikasi, gambar, dan SEO di kanan. Simpan dulu sebagai Draft jika belum siap tayang.';
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
