<?php

namespace App\Filament\Admin\Resources\AgencyResource\Pages;

use App\Filament\Admin\Resources\AgencyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAgencies extends ListRecords
{
    protected static string $resource = AgencyResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
