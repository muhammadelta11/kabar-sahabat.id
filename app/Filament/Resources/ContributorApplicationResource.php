<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContributorApplicationResource\Pages;
use App\Models\User;
use App\UserAccountStatus;
use App\UserRole;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class ContributorApplicationResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    protected static ?string $navigationLabel = 'Pengajuan Akun';

    protected static ?string $modelLabel = 'Pengajuan Kontributor';

    protected static ?string $pluralModelLabel = 'Pengajuan Kontributor';

    protected static ?string $navigationGroup = 'Redaksi';

    protected static ?int $navigationSort = 5;

    protected static ?string $slug = 'pengajuan-akun';

    public static function canViewAny(): bool
    {
        return auth()->user()?->canAccess('manage_contributors') ?? false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->kontributor()
            ->latest();
    }

    public static function getNavigationBadge(): ?string
    {
        if (! static::canViewAny()) {
            return null;
        }

        $pending = (int) Cache::remember(
            'admin.contributor_pending_count',
            30,
            fn () => User::query()->pendingApproval()->count()
        );

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
                Forms\Components\TextInput::make('name')
                    ->label('Nama')
                    ->disabled(),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->disabled(),
                Forms\Components\Select::make('account_status')
                    ->label('Status akun')
                    ->options([
                        UserAccountStatus::Pending->value => UserAccountStatus::Pending->label(),
                        UserAccountStatus::Active->value => UserAccountStatus::Active->label(),
                        UserAccountStatus::Rejected->value => UserAccountStatus::Rejected->label(),
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
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
                    ->label('Daftar')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('account_status')
                    ->label('Status')
                    ->options([
                        UserAccountStatus::Pending->value => UserAccountStatus::Pending->label(),
                        UserAccountStatus::Active->value => UserAccountStatus::Active->label(),
                        UserAccountStatus::Rejected->value => UserAccountStatus::Rejected->label(),
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (User $record): bool => $record->account_status !== UserAccountStatus::Active)
                    ->requiresConfirmation()
                    ->action(function (User $record): void {
                        $record->update([
                            'account_status' => UserAccountStatus::Active,
                            'role' => UserRole::Kontributor,
                        ]);

                        Notification::make()
                            ->title('Akun kontributor disetujui')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (User $record): bool => $record->account_status !== UserAccountStatus::Rejected)
                    ->requiresConfirmation()
                    ->action(function (User $record): void {
                        $record->update([
                            'account_status' => UserAccountStatus::Rejected,
                        ]);

                        Notification::make()
                            ->title('Pengajuan akun ditolak')
                            ->danger()
                            ->send();
                    }),
                Tables\Actions\EditAction::make()
                    ->label('Ubah status'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageContributorApplications::route('/'),
        ];
    }
}
