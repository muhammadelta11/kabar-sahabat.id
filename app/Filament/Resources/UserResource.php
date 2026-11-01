<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use App\UserAccountStatus;
use App\UserRole;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Sistem';

    protected static ?int $navigationSort = 90;

    public static function canViewAny(): bool
    {
        return auth()->user()?->canAccess('manage_users') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->required(fn (string $operation): bool => $operation === 'create'),
                Forms\Components\Select::make('role')
                    ->options(UserRole::options())
                    ->required()
                    ->default(UserRole::Redaktur->value)
                    ->live(),
                Forms\Components\Select::make('account_status')
                    ->label('Status akun')
                    ->options([
                        UserAccountStatus::Pending->value => UserAccountStatus::Pending->label(),
                        UserAccountStatus::Active->value => UserAccountStatus::Active->label(),
                        UserAccountStatus::Rejected->value => UserAccountStatus::Rejected->label(),
                    ])
                    ->required()
                    ->default(UserAccountStatus::Active->value)
                    ->visible(fn (Forms\Get $get): bool => $get('role') === UserRole::Kontributor->value),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->formatStateUsing(fn (UserRole|string|null $state): string => $state instanceof UserRole
                        ? $state->label()
                        : (UserRole::tryFrom((string) $state)?->label() ?? (string) $state))
                    ->color(fn (UserRole|string|null $state): string => match ($state instanceof UserRole ? $state : UserRole::tryFrom((string) $state)) {
                        UserRole::SuperAdmin => 'danger',
                        UserRole::Direktur => 'success',
                        UserRole::PimpinanRedaktur => 'warning',
                        UserRole::Redaktur => 'info',
                        UserRole::Kontributor => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('account_status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (UserAccountStatus|string|null $state): string => $state instanceof UserAccountStatus
                        ? $state->label()
                        : (UserAccountStatus::tryFrom((string) $state)?->label() ?? (string) $state))
                    ->color(fn (UserAccountStatus|string|null $state): string => match ($state instanceof UserAccountStatus ? $state : UserAccountStatus::tryFrom((string) $state)) {
                        UserAccountStatus::Pending => 'warning',
                        UserAccountStatus::Active => 'success',
                        UserAccountStatus::Rejected => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (User $record): bool => $record->isNot(auth()->user())),
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
            'index' => Pages\ManageUsers::route('/'),
        ];
    }
}
