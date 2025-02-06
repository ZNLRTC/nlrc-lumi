<?php

namespace App\Filament\Admin\Resources\DocumentTraineeResource\Pages;

use App\Filament\Admin\Resources\DocumentTraineeResource;
use Filament\Resources\Pages\EditRecord;

class EditDocumentTrainee extends EditRecord
{
    protected static string $resource = DocumentTraineeResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
