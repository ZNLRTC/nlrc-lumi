<?php

namespace App\Filament\Clusters;

use Filament\Clusters\Cluster;

class KnowledgeBase extends Cluster
{
    protected static ?string $navigationGroup = 'Administration';

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $activeNavigationIcon = 'heroicon-s-squares-2x2';
}
