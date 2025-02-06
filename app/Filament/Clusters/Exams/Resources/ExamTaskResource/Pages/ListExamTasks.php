<?php

namespace App\Filament\Clusters\Exams\Resources\ExamTaskResource\Pages;

use App\Filament\Clusters\Exams\Resources\ExamTaskResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExamTasks extends ListRecords
{
    protected static string $resource = ExamTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
