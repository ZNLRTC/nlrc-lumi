<?php

namespace App\Filament\Clusters\Courses\Resources\CourseResource\Pages;

use App\Filament\Clusters\Courses\Resources\CourseResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCourse extends CreateRecord
{
    protected static string $resource = CourseResource::class;

    function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
