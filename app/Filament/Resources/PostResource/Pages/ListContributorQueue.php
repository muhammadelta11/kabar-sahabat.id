<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListContributorQueue extends ListRecords
{
    protected static string $resource = PostResource::class;

    protected static ?string $title = 'Antrian Kontributor';

    protected static ?string $breadcrumb = 'Antrian Kontributor';

    public function getHeading(): string
    {
        return 'Antrian Kontributor';
    }

    public function getSubheading(): ?string
    {
        return 'Kiriman artikel dari kontributor yang menunggu review redaksi.';
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->where('status', 'Menunggu Review')
            ->latest('updated_at');
    }

    public static function getNavigationLabel(): string
    {
        return 'Antrian Kontributor';
    }
}
