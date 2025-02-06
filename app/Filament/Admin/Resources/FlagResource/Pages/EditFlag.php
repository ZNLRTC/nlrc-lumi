<?php

namespace App\Filament\Admin\Resources\FlagResource\Pages;

use App\Filament\Admin\Resources\FlagResource;
use Filament\Resources\Pages\EditRecord;

class EditFlag extends EditRecord
{
    protected static string $resource = FlagResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTitle(): string
    {
        return 'Edit ' .$this->record->name. ' flag';
    }
}
