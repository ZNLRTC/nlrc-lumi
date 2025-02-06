<?php

namespace App\Filament\Admin\Resources\TraineeResource\Pages;

use App\Filament\Admin\Resources\TraineeResource;
use Filament\Resources\Pages\ViewRecord;

class ViewTrainee extends ViewRecord
{
    protected static string $resource = TraineeResource::class;

    protected static ?string $navigationIcon = 'heroicon-o-eye';

    protected static ?string $activeNavigationIcon = 'heroicon-s-eye';

    public function getTitle(): string
    {
        $record = $this->getRecord();

        return $record->first_name. ' ' .$record->last_name;
    }

    protected function getActions(): array
    {
        return [];
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
}