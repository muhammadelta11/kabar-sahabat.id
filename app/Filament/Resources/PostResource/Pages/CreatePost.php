<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use App\Support\AutoRelatedPosts;
use App\Support\PostSlug;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Js;

class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    protected static bool $canCreateAnother = false;

    public ?string $saveIntent = null;

    public mixed $publishAt = null;

    public function getTitle(): string
    {
        return 'Buat berita';
    }

    public function getHeading(): string
    {
        return 'Buat berita';
    }

    public function getSubheading(): ?string
    {
        return null;
    }

    public function getMaxContentWidth(): MaxWidth|string|null
    {
        // Desktop: lebar nyaman (bukan mepet pinggir). HP tetap full lewat CSS mobile.
        return MaxWidth::FiveExtraLarge;
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
                ->modalHeading('Publish berita')
                ->modalDescription('Tayangkan sekarang atau atur jadwal terbit.')
                ->modalSubmitActionLabel('Lanjut publish')
                ->form([
                    Forms\Components\Radio::make('when')
                        ->label('Kapan tayang?')
                        ->options([
                            'now' => 'Publish sekarang',
                            'schedule' => 'Jadwalkan',
                        ])
                        ->default('now')
                        ->live()
                        ->required(),
                    Forms\Components\DateTimePicker::make('published_at')
                        ->label('Jadwal tayang')
                        ->seconds(false)
                        ->native(false)
                        ->minDate(now())
                        ->default(now()->addHour())
                        ->visible(fn (Get $get): bool => $get('when') === 'schedule')
                        ->required(fn (Get $get): bool => $get('when') === 'schedule'),
                ])
                ->action(function (array $data): void {
                    $this->saveIntent = 'Published';
                    $this->publishAt = ($data['when'] ?? 'now') === 'schedule'
                        ? $data['published_at']
                        : now();
                    $this->data['status'] = 'Published';
                    $this->create();
                }),
            Action::make('saveDraft')
                ->label('Simpan (Draft)')
                ->icon('heroicon-m-document')
                ->color('warning')
                ->keyBindings(['mod+s'])
                ->action(function (): void {
                    $this->saveIntent = 'Draft';
                    $this->publishAt = null;
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

        if (blank($data['slug'] ?? null) && filled($data['title'] ?? null)) {
            $data['slug'] = PostSlug::unique((string) $data['title']);
        }

        if ($data['status'] === 'Published') {
            $data['published_at'] = $this->publishAt ?? ($data['published_at'] ?? now());
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
