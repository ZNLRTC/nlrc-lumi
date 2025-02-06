<?php

namespace App\Filament\Clusters\Planner\Resources\CurriculumResource\Pages;

use App\Filament\Clusters\Planner\Resources\CurriculumResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCurricula extends ListRecords
{
    protected static string $resource = CurriculumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
