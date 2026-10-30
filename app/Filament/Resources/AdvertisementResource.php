<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdvertisementResource\Pages;
use App\Models\Advertisement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AdvertisementResource extends Resource
{
    protected static ?string $model = Advertisement::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('slot_key')
                    ->label('Posisi Iklan')
                    ->options([
                        'header_leaderboard' => 'Header Leaderboard (728x90)',
                        'sidebar_mrec' => 'Sidebar MREC (300x250)',
                        'sidebar_skyscraper' => 'Sidebar Skyscraper (300x600)',
                        'skin_ad_left' => 'Skin Ad Left (160x600)',
                        'skin_ad_right' => 'Skin Ad Right (160x600)',
                        'in_feed_leaderboard' => 'In-Feed Leaderboard (728x90)',
                        'sponsor_leaderboard' => 'Sponsor Leaderboard (728x90)',
                        'sidebar_sponsor_mrec' => 'Sidebar Sponsor MREC (300x250)',
                        'bottom_billboard' => 'Bottom Billboard (970x90)',
                    ])
                    ->required()
                    ->disabled(fn (string $context): bool => $context === 'edit'),
                Forms\Components\TextInput::make('name')
                    ->label('Nama Iklan (Untuk Pencatatan)')
                    ->required(),
                Forms\Components\Select::make('type')
                    ->label('Tipe Konten')
                    ->options([
                        'image' => 'Gambar Mandiri (Banner/Sponsor)',
                        'script' => 'Script Google AdSense / HTML',
                    ])
                    ->required()
                    ->live(),
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
                Forms\Components\FileUpload::make('image_url')
                    ->label('File Gambar Banner')
                    ->image()
                    ->disk('public')
                    ->directory('ads')
                    ->visible(fn (Forms\Get $get) => $get('type') === 'image')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('link_url')
                    ->label('Link Tujuan URL')
                    ->url()
                    ->visible(fn (Forms\Get $get) => $get('type') === 'image')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('script_code')
                    ->label('Kode Script Google AdSense / HTML')
                    ->rows(6)
                    ->visible(fn (Forms\Get $get) => $get('type') === 'script')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Iklan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slot_key')
                    ->label('Posisi')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'image' => 'success',
                        'script' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Status Aktif'),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAdvertisements::route('/'),
        ];
    }
}
