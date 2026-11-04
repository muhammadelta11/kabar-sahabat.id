<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\PostResource;
use Filament\Widgets\Widget;

class QuickComposeWidget extends Widget
{
    protected static string $view = 'filament.widgets.quick-compose';

    protected static ?int $sort = -20;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return PostResource::canViewAny();
    }
}
