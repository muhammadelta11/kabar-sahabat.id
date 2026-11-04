<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use App\Models\Tag;
use App\Support\AutoRelatedPosts;
use App\Support\EditorialInsights;
use App\Support\MediaImageProcessor;
use App\Support\PostSlug;
use App\Support\RedakturOperationalRecap;
use App\Support\SeoAnalyzer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationLabel = 'Berita';

    protected static ?string $modelLabel = 'Berita';

    protected static ?string $pluralModelLabel = 'Berita';

    protected static ?string $navigationGroup = 'Redaksi';

    protected static ?int $navigationSort = 10;

    public static function canViewAny(): bool
    {
        return auth()->user()?->canAccess('manage_posts') ?? false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user:id,name', 'categories:id,name', 'tags:id,name']);
    }

    public static function pendingReviewCount(): int
    {
        return (int) Cache::remember('admin.posts.pending_review_count', 30, function () {
            return Post::query()->where('status', 'Menunggu Review')->count();
        });
    }

    public static function forgetPendingReviewCount(): void
    {
        Cache::forget('admin.posts.pending_review_count');
        Cache::forget('admin.posts.status_tabs');
        Cache::forget('admin.editorial_queue');
        Cache::forget('admin.stats_overview');
        Cache::forget('admin.views_chart_7d');
        Cache::forget('admin.business_stats.v2');
        Cache::forget('admin.popular_posts_top5');
        Cache::forget('admin.redaktur_performance.'.now()->toDateString());
        RedakturOperationalRecap::forgetCache();
        EditorialInsights::forgetCache();
    }

    public static function getNavigationBadge(): ?string
    {
        if (! static::canViewAny()) {
            return null;
        }

        $pending = static::pendingReviewCount();

        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getNavigationItems(): array
    {
        if (! static::canViewAny()) {
            return [];
        }

        $items = parent::getNavigationItems();
        $pending = static::pendingReviewCount();

        $items[] = NavigationItem::make('Antrian Kontributor')
            ->icon('heroicon-o-inbox-stack')
            ->group(static::getNavigationGroup())
            ->sort(11)
            ->badge($pending > 0 ? (string) $pending : null, 'warning')
            ->url(static::getUrl('queue'))
            ->isActiveWhen(fn (): bool => request()->routeIs(static::getRouteBaseName().'.queue'));

        return $items;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->hiddenLabel()
                            ->placeholder('Judul berita…')
                            ->required()
                            ->maxLength(180)
                            ->live(onBlur: true)
                            ->extraInputAttributes(['class' => 'ks-writer-title'])
                            ->extraFieldWrapperAttributes(['class' => 'ks-writer-title-wrap'])
                            ->afterStateUpdated(function (Set $set, Get $get, ?string $state, Forms\Components\TextInput $component): void {
                                if (blank($state)) {
                                    return;
                                }

                                $record = $component->getRecord();

                                if ($record && filled($get('slug')) && PostSlug::isClean($get('slug'))) {
                                    return;
                                }

                                $set('slug', PostSlug::unique($state, $record?->getKey()));
                            }),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->helperText('Otomatis dari judul')
                            ->required()
                            ->maxLength(180)
                            ->live(onBlur: true)
                            ->unique(ignoreRecord: true)
                            ->extraFieldWrapperAttributes(['class' => 'ks-writer-meta-field']),
                        Forms\Components\FileUpload::make('image_url')
                            ->label('Gambar utama')
                            ->image()
                            ->disk(fn () => media_disk())
                            ->directory('posts')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(5120)
                            ->saveUploadedFileUsing(MediaImageProcessor::filamentSaver('posts'))
                            ->helperText('JPG/PNG/WebP maks. 5MB. Otomatis kompres + watermark.')
                            ->validationMessages([
                                'max' => 'Gambar terlalu besar (maks. 5MB). Sistem akan mengompres setelah diunggah.',
                                'mimetypes' => 'Format gambar harus JPG, PNG, atau WebP.',
                            ])
                            ->extraFieldWrapperAttributes(['class' => 'ks-writer-image-wrap']),
                        Forms\Components\TextInput::make('image_caption')
                            ->label('Caption gambar')
                            ->placeholder('Foto: Dok. …')
                            ->extraFieldWrapperAttributes(['class' => 'ks-writer-meta-field']),
                        Forms\Components\RichEditor::make('content')
                            ->label('Isi berita')
                            ->columnSpanFull()
                            ->extraFieldWrapperAttributes(['class' => 'ks-writer-body-wrap'])
                            ->toolbarButtons([
                                'attachFiles',
                                'blockquote',
                                'bold',
                                'bulletList',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'strike',
                                'underline',
                                'undo',
                            ]),
                    ]),
                Forms\Components\Section::make('Kategori & tag')
                    ->compact()
                    ->schema([
                        Forms\Components\Select::make('categories')
                            ->label('Kanal')
                            ->relationship('categories', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->native(false)
                            ->extraAttributes(['class' => 'ks-kanal-select']),
                        Forms\Components\Select::make('tags')
                            ->label('Tag')
                            ->relationship('tags', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->label('Nama tag')
                                    ->required()
                                    ->maxLength(80),
                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->maxLength(100)
                                    ->unique('tags', 'slug'),
                            ])
                            ->createOptionUsing(function (array $data): int {
                                $tag = Tag::query()->create([
                                    'name' => $data['name'],
                                    'slug' => $data['slug'] ?: Str::slug($data['name']),
                                ]);

                                return $tag->getKey();
                            })
                            ->native(false),
                        Forms\Components\Select::make('status')
                            ->options([
                                'Menunggu Review' => 'Menunggu Review',
                                'Draft' => 'Draft',
                                'Published' => 'Published',
                                'Archived' => 'Archived',
                            ])
                            ->required()
                            ->native(false)
                            ->live()
                            ->default('Draft')
                            ->visibleOn('edit'),
                        Forms\Components\TextInput::make('views_count')
                            ->label('Views')
                            ->numeric()
                            ->default(0)
                            ->disabled()
                            ->dehydrated(false)
                            ->visibleOn('edit'),
                    ]),
                Forms\Components\Section::make('SEO')
                    ->compact()
                    ->collapsed()
                    ->schema([
                        Forms\Components\Textarea::make('excerpt')
                            ->label('Ringkasan / meta deskripsi')
                            ->placeholder('Ringkasan singkat untuk Google & share…')
                            ->rows(3)
                            ->live(onBlur: true)
                            ->columnSpanFull(),
                        Forms\Components\Placeholder::make('seo_analysis')
                            ->hiddenLabel()
                            ->content(function (Get $get): HtmlString {
                                $analysis = SeoAnalyzer::analyze(
                                    $get('title'),
                                    $get('slug'),
                                    $get('excerpt'),
                                    $get('content'),
                                    $get('image_url'),
                                );

                                return new HtmlString(
                                    view('filament.forms.seo-analysis', compact('analysis'))->render()
                                );
                            }),
                    ]),
                Forms\Components\Section::make('Penulis')
                    ->compact()
                    ->collapsed()
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Akun redaksi')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->default(fn () => auth()->id())
                            ->native(false),
                        Forms\Components\TextInput::make('penulis')
                            ->label('Byline')
                            ->placeholder('Nama penulis'),
                        Forms\Components\TextInput::make('editor')
                            ->label('Editor')
                            ->placeholder('Nama editor'),
                    ]),
                Forms\Components\Section::make('Kontributor')
                    ->columns(1)
                    ->compact()
                    ->collapsed()
                    ->visible(fn (Get $get): bool => $get('status') === 'Menunggu Review')
                    ->schema([
                        Forms\Components\TextInput::make('author_name')
                            ->label('Nama kontributor'),
                        Forms\Components\TextInput::make('author_wa')
                            ->label('No. WA'),
                        Forms\Components\TextInput::make('author_instansi')
                            ->label('Instansi'),
                    ]),
                Forms\Components\Section::make('Baca Juga')
                    ->compact()
                    ->collapsed()
                    ->schema([
                        Forms\Components\Placeholder::make('related_posts_auto')
                            ->hiddenLabel()
                            ->content(function (?Post $record): HtmlString {
                                if (! $record?->exists) {
                                    return new HtmlString(
                                        '<p class="text-sm text-gray-600 dark:text-gray-300">Sistem memilih <strong>3 berita terkait</strong> saat disimpan.</p>'
                                    );
                                }

                                $items = AutoRelatedPosts::previewFor($record);
                                if ($items->isEmpty()) {
                                    return new HtmlString(
                                        '<p class="text-sm text-amber-700 dark:text-amber-300">Belum ada kandidat. Simpan ulang setelah ada berita lain yang tayang.</p>'
                                    );
                                }

                                $lis = $items->map(fn (Post $p) => '<li class="truncate">'.e($p->title).'</li>')->implode('');

                                return new HtmlString(
                                    '<ol class="list-decimal space-y-1 pl-5 text-sm font-medium text-gray-800 dark:text-gray-100">'.$lis.'</ol>'
                                );
                            }),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption(15)
            ->paginated([10, 15, 25, 50])
            ->deferLoading()
            ->striped()
            ->persistSearchInSession()
            ->persistFiltersInSession()
            ->searchPlaceholder('Cari judul atau slug…')
            ->modifyQueryUsing(function (Builder $query): Builder {
                $tableName = $query->getModel()->getTable();

                // List tidak butuh kolom content (HTML besar) — hemat memori & Livewire.
                return $query->select([
                    "{$tableName}.id",
                    "{$tableName}.user_id",
                    "{$tableName}.penulis",
                    "{$tableName}.author_name",
                    "{$tableName}.title",
                    "{$tableName}.slug",
                    "{$tableName}.image_url",
                    "{$tableName}.status",
                    "{$tableName}.views_count",
                    "{$tableName}.published_at",
                    "{$tableName}.updated_at",
                    "{$tableName}.created_at",
                ]);
            })
            ->columns([
                Tables\Columns\ImageColumn::make('image_url')
                    ->label('')
                    ->disk(fn () => media_disk())
                    ->height(48)
                    ->width(76)
                    ->extraImgAttributes([
                        'class' => 'rounded-md object-cover ring-1 ring-slate-200/80 dark:ring-white/10',
                        'loading' => 'lazy',
                        'decoding' => 'async',
                    ])
                    ->defaultImageUrl('https://placehold.co/152x96/e2e8f0/64748b?text=KS'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Berita')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->limit(90)
                    ->weight('bold')
                    ->description(function (Post $record): string {
                        $kanal = $record->categories->pluck('name')->filter()->join(', ');
                        $author = $record->displayAuthorName();

                        return collect([
                            $kanal !== '' ? $kanal : 'Tanpa kanal',
                            $author,
                        ])->join(' · ');
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Menunggu Review' => 'warning',
                        'Draft' => 'gray',
                        'Published' => 'success',
                        'Archived' => 'danger',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'Menunggu Review' => 'heroicon-m-clock',
                        'Draft' => 'heroicon-m-pencil-square',
                        'Published' => 'heroicon-m-check-badge',
                        'Archived' => 'heroicon-m-archive-box',
                        default => 'heroicon-m-document',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('views_count')
                    ->label('Views')
                    ->numeric()
                    ->sortable()
                    ->alignEnd()
                    ->icon('heroicon-m-eye')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Tayang')
                    ->since()
                    ->dateTimeTooltip('d M Y H:i')
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diubah')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Menunggu Review' => 'Menunggu Review',
                        'Draft' => 'Draft',
                        'Published' => 'Published',
                        'Archived' => 'Archived',
                    ]),
                Tables\Filters\SelectFilter::make('categories')
                    ->label('Kanal')
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->preload(),
            ])
            ->filtersFormColumns(2)
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Edit')
                    ->iconButton()
                    ->tooltip('Edit berita'),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('setujui')
                        ->label('Setujui & Tayangkan')
                        ->icon('heroicon-m-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalDescription('Artikel akan langsung tayang atas nama kontributor.')
                        ->visible(fn (Post $record): bool => $record->status === 'Menunggu Review')
                        ->action(function (Post $record): void {
                            $record->update([
                                'status' => 'Published',
                                'penulis' => $record->author_name ?: ($record->penulis ?: 'Kontributor'),
                                'editor' => auth()->user()?->name,
                                'published_at' => now(),
                            ]);
                            AutoRelatedPosts::syncFor($record->fresh(['categories']));
                            static::forgetPendingReviewCount();
                        }),
                    Tables\Actions\Action::make('tolak')
                        ->label('Tolak & Hapus')
                        ->icon('heroicon-m-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalDescription('Kiriman ini beserta gambarnya akan dihapus permanen.')
                        ->visible(fn (Post $record): bool => $record->status === 'Menunggu Review')
                        ->action(function (Post $record): void {
                            $record->delete();
                            static::forgetPendingReviewCount();
                        }),
                    Tables\Actions\Action::make('lihat')
                        ->label('Lihat di situs')
                        ->icon('heroicon-m-arrow-top-right-on-square')
                        ->url(fn (Post $record): string => route('news.show', $record->slug))
                        ->openUrlInNewTab()
                        ->visible(fn (Post $record): bool => $record->status === 'Published'),
                ])
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->iconButton()
                    ->tooltip('Aksi lain'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->after(fn () => static::forgetPendingReviewCount()),
                ]),
            ])
            ->emptyStateHeading('Belum ada berita')
            ->emptyStateDescription('Buat berita baru atau tinjau antrian kontributor.')
            ->emptyStateIcon('heroicon-o-newspaper')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()->label('Tulis Berita'),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'queue' => Pages\ListContributorQueue::route('/antrian'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
