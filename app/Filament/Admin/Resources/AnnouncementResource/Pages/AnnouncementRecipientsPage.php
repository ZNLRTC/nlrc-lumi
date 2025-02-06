<?php

namespace App\Filament\Admin\Resources\AnnouncementResource\Pages;

use App\Filament\Admin\Resources\AnnouncementResource;
use Filament\Resources\Pages\ManageRelatedRecords;

class AnnouncementRecipientsPage extends ManageRelatedRecords
{
    // NOTE: This is a custom page integrated into AnnouncementResource in a "hacky" way
    // $resource and $relationship is required but the value for $relationship is set to
    // a random relationship of model Announcement
    protected static string $resource = AnnouncementResource::class;

    protected static ?string $title = 'Recipients';

    protected static ?string $breadcrumb = 'Recipients';

    protected static string $relationship = 'user';

    protected static ?string $navigationIcon = 'heroicon-o-rocket-launch';

    protected static ?string $activeNavigationIcon = 'heroicon-s-rocket-launch';

    protected static string $view = 'filament.admin.pages.announcement-recipients';
}
