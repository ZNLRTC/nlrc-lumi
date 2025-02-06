<?php

namespace App\Filament\Admin\Resources\AnnouncementResource\Pages;

use App\Filament\Admin\Resources\AnnouncementResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditAnnouncement extends EditRecord
{
    protected static string $resource = AnnouncementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    public function getTitle(): string
    {
        $record = $this->getRecord();

        return 'Edit ' .$record->title. ' announcement';
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $old_thumbnail_image_path = $this->record->thumbnail_image_path;

        if ($old_thumbnail_image_path && ($old_thumbnail_image_path != $data['thumbnail_image_path'])) {
            Storage::disk('announcements')->delete($old_thumbnail_image_path);
        }

        return $data;
    }
}
