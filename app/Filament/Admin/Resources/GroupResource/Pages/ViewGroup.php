<?php

namespace App\Filament\Admin\Resources\GroupResource\Pages;

use App\Filament\Admin\Resources\GroupResource;
use Filament\Resources\Pages\ViewRecord;

class ViewGroup extends ViewRecord
{
    protected static string $resource = GroupResource::class;

    protected static ?string $navigationIcon = 'heroicon-o-eye';

    protected static ?string $activeNavigationIcon = 'heroicon-s-eye';

    public function getTitle(): string
    {
        $record = $this->getRecord();

        return $record->group_code;
    }

    protected function getActions(): array
    {
        return [];
    }
}