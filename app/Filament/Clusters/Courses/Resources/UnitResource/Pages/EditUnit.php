<?php

namespace App\Filament\Clusters\Courses\Resources\UnitResource\Pages;

use App\Filament\Clusters\Courses\Resources\UnitResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUnit extends EditRecord
{
    protected static string $resource = UnitResource::class;

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
        return 'Editing ' .$this->record->internal_name;
    }
}
