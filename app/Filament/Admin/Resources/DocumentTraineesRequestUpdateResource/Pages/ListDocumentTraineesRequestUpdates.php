<?php

namespace App\Filament\Admin\Resources\DocumentTraineesRequestUpdateResource\Pages;

use App\Filament\Admin\Resources\DocumentTraineesRequestUpdateResource;
use Filament\Resources\Pages\ListRecords;

class ListDocumentTraineesRequestUpdates extends ListRecords
{
    protected static string $resource = DocumentTraineesRequestUpdateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
