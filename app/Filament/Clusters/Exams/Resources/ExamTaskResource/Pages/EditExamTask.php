<?php

namespace App\Filament\Clusters\Exams\Resources\ExamTaskResource\Pages;

use App\Filament\Clusters\Exams\Resources\ExamTaskResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExamTask extends EditRecord
{
    protected static string $resource = ExamTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
