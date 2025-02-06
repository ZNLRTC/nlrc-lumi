<?php

namespace App\Filament\Clusters\Exams\Resources\ExamResource\Pages;

use App\Filament\Clusters\Exams\Resources\ExamResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateExam extends CreateRecord
{
    protected static string $resource = ExamResource::class;
}
