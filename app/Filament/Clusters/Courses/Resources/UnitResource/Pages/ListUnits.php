<?php

namespace App\Filament\Clusters\Courses\Resources\UnitResource\Pages;

use App\Filament\Clusters\Courses\Resources\UnitResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUnits extends ListRecords
{
    protected static string $resource = UnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
