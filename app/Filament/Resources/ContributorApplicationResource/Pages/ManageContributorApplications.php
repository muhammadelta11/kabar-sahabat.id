<?php

namespace App\Filament\Resources\ContributorApplicationResource\Pages;

use App\Filament\Resources\ContributorApplicationResource;
use Filament\Resources\Pages\ManageRecords;

class ManageContributorApplications extends ManageRecords
{
    protected static string $resource = ContributorApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
