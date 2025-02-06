<?php

namespace App\Filament\Admin\Resources\TraineeResource\Pages;

use App\Filament\Admin\Resources\TraineeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTrainee extends CreateRecord
{
    protected static string $resource = TraineeResource::class;

    function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
