<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use App\Support\AutoRelatedPosts;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Get;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Js;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    public ?string $saveIntent = null;

    public mixed $publishAt = null;

    public function getTitle(): string
    {
        return 'Edit berita';
    }

    public function getHeading(): string
    {
        return 'Edit berita';
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

    protected function resolveRecord(int|string $key): Model
    {
        /** @var Model $record */
        $record = parent::resolveRecord($key);

        $record->loadMissing([
            'categories:id,name',
            'tags:id,name',
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
                ->visible(fn (): bool => $this->record->status === 'Published'
                    && $this->record->published_at
                    && $this->record->published_at->lte(now())),
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
                        ->default(fn () => $this->record->published_at?->isFuture()
                            ? $this->record->published_at
                            : now()->addHour())
                        ->visible(fn (Get $get): bool => $get('when') === 'schedule')
                        ->required(fn (Get $get): bool => $get('when') === 'schedule'),
                ])
                ->action(function (array $data): void {
                    $this->saveIntent = 'Published';
                    $this->publishAt = ($data['when'] ?? 'now') === 'schedule'
                        ? $data['published_at']
                        : now();
                    $this->data['status'] = 'Published';
                    $this->save();
                }),
            Actions\Action::make('saveDraft')
                ->label('Simpan (Draft)')
                ->icon('heroicon-m-document')
                ->color('warning')
                ->keyBindings(['mod+s'])
                ->action(function (): void {
                    $this->saveIntent = 'Draft';
                    $this->publishAt = null;
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

        if (($data['status'] ?? null) === 'Published' && $this->publishAt !== null) {
            $data['published_at'] = $this->publishAt;
        } elseif (($data['status'] ?? null) === 'Published' && blank($data['published_at'] ?? null)) {
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
