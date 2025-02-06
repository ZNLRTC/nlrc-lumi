<?php

namespace App\Filament\Admin\Resources\TraineeResource\Pages;

use App\Filament\Admin\Resources\TraineeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTrainee extends EditRecord
{
    protected static string $resource = TraineeResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }

    public function getTitle(): string
    {
        return 'Edit ' .$this->record->first_name. ' ' .$this->record->last_name;
    }

    function getRedirectUrl(): ?string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
}
