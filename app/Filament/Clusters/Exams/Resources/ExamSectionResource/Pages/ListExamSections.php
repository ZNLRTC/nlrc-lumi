<?php

namespace App\Filament\Clusters\Exams\Resources\ExamSectionResource\Pages;

use App\Filament\Clusters\Exams\Resources\ExamSectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExamSections extends ListRecords
{
    protected static string $resource = ExamSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
