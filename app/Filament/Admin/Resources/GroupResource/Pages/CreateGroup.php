<?php

namespace App\Filament\Admin\Resources\GroupResource\Pages;

use App\Filament\Admin\Resources\GroupResource;
use App\Models\Agencies\Agency;
use App\Models\Grouping\GroupType;
use Filament\Resources\Pages\CreateRecord;

class CreateGroup extends CreateRecord
{
    protected static string $resource = GroupResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $group_code = GroupType::find($data['group_type_id'])->code;
        $data['agency_id'] = Agency::get_agency_id_by_group_name($group_code);

        return $data;
    }

    function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
