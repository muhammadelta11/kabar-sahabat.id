<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ManageSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Pengaturan';

    protected static ?string $navigationGroup = 'Sistem';

    protected static ?int $navigationSort = 100;

    protected static ?string $title = 'Pengaturan Website';

    protected static string $view = 'filament.pages.manage-settings';

    public static function canAccess(): bool
    {
        return auth()->user()?->canAccess('manage_settings') ?? false;
    }
}
