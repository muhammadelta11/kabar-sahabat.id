<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class ManageSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Pengaturan';

    protected static ?string $title = 'Pengaturan Website';

    protected static string $view = 'filament.pages.manage-settings';
}
