<?php

namespace App\Filament\Clusters\Exams\Resources\ExamResource\Pages;

use App\Filament\Clusters\Exams\Resources\ExamResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExam extends EditRecord
{
    protected static string $resource = ExamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
    
    public function getTitle(): string
    {
        return "Edit {$this->record->name}";
    }
}
