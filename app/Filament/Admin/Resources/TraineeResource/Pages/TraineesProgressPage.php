<?php

namespace App\Filament\Admin\Resources\TraineeResource\Pages;

use App\Filament\Admin\Resources\TraineeResource;
use Filament\Pages\Page;
use Filament\Resources\Pages\ManageRelatedRecords;

class TraineesProgressPage extends ManageRelatedRecords
{
    // NOTE: This is a custom page integrated into TraineeResource in a "hacky" way
    // $resource and $relationship is required but the value for $relationship is set to
    // a random relationship of model Trainee
    protected static string $resource = TraineeResource::class;

    protected static ?string $title = 'Trainee Progress';

    protected static ?string $breadcrumb = 'Progress';

    protected static string $relationship = 'user';

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    protected static ?string $activeNavigationIcon = 'heroicon-s-trophy';

    protected static string $view = 'filament.admin.pages.trainees-progress-page';
}
