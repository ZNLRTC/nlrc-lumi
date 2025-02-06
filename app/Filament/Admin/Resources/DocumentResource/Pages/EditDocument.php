<?php

namespace App\Filament\Admin\Resources\DocumentResource\Pages;

use App\Filament\Admin\Resources\DocumentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDocument extends EditRecord
{
    protected static string $resource = DocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->successNotificationTitle('Document deleted')
                ->visible(function ($record): bool {
                    // Ensures that there are no more uploaded documents by trainees before the delete button shows up
                    $visible = $record->documentCount == 0;

                    return $visible;
                })
        ];
    }
}
