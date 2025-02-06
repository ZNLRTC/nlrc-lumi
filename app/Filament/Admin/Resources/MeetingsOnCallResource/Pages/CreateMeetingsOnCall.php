<?php

namespace App\Filament\Admin\Resources\MeetingsOnCallResource\Pages;

use App\Filament\Admin\Resources\MeetingsOnCallResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Carbon;

class CreateMeetingsOnCall extends CreateRecord
{
    protected static string $resource = MeetingsOnCallResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = [
            'user_id' => $data['user_id'],
            'meeting_link' => $data['meeting_link'],
            'meeting_date' => $data['start_time_meeting_date'],
            'start_time' => Carbon::parse($data['start_time_meeting_date']. ' ' .$data['start_time_hours_mins']. ' ' .$data['start_time_am_pm'], 'UTC'),
            'end_time' => Carbon::parse($data['end_time_meeting_date']. ' ' .$data['end_time_hours_mins']. ' ' .$data['end_time_am_pm'], 'UTC'),
        ];

        return $data;
    }
}
