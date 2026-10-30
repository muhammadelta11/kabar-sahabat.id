<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use App\Support\PostSlug;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationBadge(): ?string
    {
        $pending = Post::where('status', 'Menunggu Review')->count();

        return $pending > 0 ? (string) $pending : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('penulis')
                    ->label('Penulis')
                    ->placeholder('Contoh: Ahmad Fauzi'),
                Forms\Components\TextInput::make('editor')
                    ->label('Editor')
                    ->placeholder('Contoh: Tim Redaksi'),
                Forms\Components\Section::make('Kontributor (Kiriman Publik)')
                    ->columns(3)
                    ->visible(fn (callable $get): bool => $get('status') === 'Menunggu Review')
                    ->schema([
                        Forms\Components\TextInput::make('author_name')
                            ->label('Nama Kontributor'),
                        Forms\Components\TextInput::make('author_wa')
                            ->label('No. WA Kontributor'),
                        Forms\Components\TextInput::make('author_instansi')
                            ->label('Instansi Kontributor'),
                    ]),
                Forms\Components\Select::make('categories')
                    ->label('Kanal')
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->preload(),
                Forms\Components\Select::make('relatedPosts')
                    ->relationship('relatedPosts', 'title', fn ($query) => $query->where('status', 'Published'))
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->label('Berita Terkait (Baca Juga)')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state, Forms\Components\TextInput $component): void {
                        if (blank($state)) {
                            return;
                        }

                        $record = $component->getRecord();

                        // Saat edit, jangan timpa slug yang sudah bersih (hindari ubah URL tanpa sengaja).
                        if ($record && filled($get('slug')) && PostSlug::isClean($get('slug'))) {
                            return;
                        }

                        $set('slug', PostSlug::unique($state, $record?->getKey()));
                    }),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->helperText('Otomatis dari judul (huruf kecil + strip). Bisa diedit manual.'),
                Forms\Components\FileUpload::make('image_url')
                    ->label('Gambar Utama')
                    ->image()
                    ->disk(fn () => media_disk())
                    ->directory('posts')
                    ->visibility('public')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(2048)
                    ->imageResizeMode('cover')
                    ->imageCropAspectRatio('16:9')
                    ->imageResizeTargetWidth('1200')
                    ->imageResizeTargetHeight('675')
                    ->helperText('Maks. 2MB. Otomatis dipotong 16:9 dan di-resize ke 1200×675. Disimpan ke disk media (lokal/CDN).'),
                Forms\Components\TextInput::make('image_caption')
                    ->label('Caption Gambar Utama')
                    ->placeholder('Contoh: Foto: Dok. PMII Walisongo')
                    ->helperText('Tampil di bawah gambar utama pada halaman berita'),
                Forms\Components\Textarea::make('excerpt')
                    ->columnSpanFull(),
                Forms\Components\RichEditor::make('content')
                    ->columnSpanFull()
                    ->helperText('Caption gambar: ketik teks pada baris tepat di bawah gambar (maks. ±150 karakter), otomatis tampil sebagai caption.'),
                Forms\Components\Select::make('status')
                    ->options([
                        'Menunggu Review' => 'Menunggu Review',
                        'Draft' => 'Draft',
                        'Published' => 'Published',
                        'Archived' => 'Archived',
                    ])
                    ->required()
                    ->default('Draft'),
                Forms\Components\TextInput::make('views_count')
                    ->numeric()
                    ->default(0)
                    ->disabled()
                    ->dehydrated(false)
                    ->helperText('Otomatis bertambah saat dibaca pembaca'),
                Forms\Components\DateTimePicker::make('published_at')
                    ->default(now())
                    ->seconds(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\ImageColumn::make('image_url')
                    ->disk(fn () => media_disk()),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Menunggu Review' => 'warning',
                        'Draft' => 'gray',
                        'Published' => 'success',
                        'Archived' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('views_count')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
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
            ])
            ->actions([
                Tables\Actions\Action::make('setujui')
                    ->label('Setujui')
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
                    }),
                Tables\Actions\Action::make('tolak')
                    ->label('Tolak')
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalDescription('Kiriman ini beserta gambarnya akan dihapus permanen.')
                    ->visible(fn (Post $record): bool => $record->status === 'Menunggu Review')
                    ->action(fn (Post $record) => $record->delete()),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
