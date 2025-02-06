<?php

namespace App\Filament\Clusters\Exams\Resources\ProficiencyResource\Pages;

use App\Filament\Clusters\Exams\Resources\ProficiencyResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProficiencies extends ListRecords
{
    protected static string $resource = ProficiencyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
