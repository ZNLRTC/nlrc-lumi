<?php

namespace App\Livewire\Meetings;

use App\Models\Courses\Unit;
use Livewire\Component;

class UnitMeetings extends Component
{
    public Unit $unit;

    public function mount(Unit $unit)
    {
        $traineeId = auth()->user()->trainee->id;
    
        $this->unit = $unit->load([
            'meetings' => function ($query) use ($traineeId) {
                $query->whereHas('trainees', function ($query) use ($traineeId) {
                    $query->where('meeting_trainee.trainee_id', $traineeId);
                });
            },
            'meetings.trainees' => function ($query) use ($traineeId) {
                $query->where('trainees.id', $traineeId);
            },
            'meetings.trainees.meetingTrainees.instructor',
            'meetings.trainees.meetingTrainees.meetingStatus',
        ]);
    }
    
    public function render()
    {
        return view('livewire.meetings.unit-meetings', ['unit' => $this->unit]);
    }
}
