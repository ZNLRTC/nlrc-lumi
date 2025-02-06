<?php

namespace App\Filament\Admin\Resources\FlagResource\Pages;

use App\Filament\Admin\Resources\FlagResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFlags extends ListRecords
{
    protected static string $resource = FlagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
