<?php

namespace App\Filament\Admin\Resources\MeetingsOnCallResource\Pages;

use App\Filament\Admin\Resources\MeetingsOnCallResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMeetingsOnCalls extends ListRecords
{
    protected static string $resource = MeetingsOnCallResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
