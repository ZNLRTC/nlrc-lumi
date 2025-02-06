<?php

namespace App\Filament\Admin\Resources\AgencyResource\Pages;

use App\Filament\Admin\Resources\AgencyResource;
use Filament\Resources\Pages\EditRecord;

class EditAgency extends EditRecord
{
    protected static string $resource = AgencyResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
