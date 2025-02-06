<?php

namespace App\Filament\Admin\Resources\GroupResource\Pages;

use App\Filament\Admin\Resources\GroupResource;
use App\Models\Agencies\Agency;
use App\Models\Grouping\GroupType;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGroup extends EditRecord
{
    protected static string $resource = GroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Delete group'),
        ];
    }

    public function getTitle(): string
    {
        $record = $this->getRecord();

        return 'Edit ' .$record->group_code;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $group_code = GroupType::find($data['group_type_id'])->code;
        $data['agency_id'] = Agency::get_agency_id_by_group_name($group_code);

        return $data;
    }

    function getRedirectUrl(): ?string
    {
        return $this->getResource()::getUrl('index');
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
}
