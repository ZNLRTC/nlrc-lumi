<?php

namespace App\Filament\Admin\Resources\AnnouncementResource\Pages;

use App\Filament\Admin\Resources\AnnouncementResource;
use Filament\Resources\Pages\ViewRecord;

class ViewAnnouncement extends ViewRecord
{
    protected static string $resource = AnnouncementResource::class;

    protected static ?string $navigationIcon = 'heroicon-o-eye';

    protected static ?string $activeNavigationIcon = 'heroicon-s-eye';

    public function getTitle(): string
    {
        $record = $this->getRecord();

        return $record->title;
    }

    protected function getActions(): array
    {
        return [];
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
}
