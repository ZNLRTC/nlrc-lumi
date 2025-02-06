<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class Courses extends Cluster
{
    protected static ?string $navigationGroup = 'Administration';

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $activeNavigationIcon = 'heroicon-s-academic-cap';
}
