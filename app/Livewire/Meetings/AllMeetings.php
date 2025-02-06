<?php

namespace App\Livewire\Meetings;

use Livewire\Component;
use Livewire\Attributes\Locked;

class AllMeetings extends Component
{
    #[Locked]
    public $trainee;

    public function mount()
    {
        $traineeId = auth()->user()->trainee->id;
    
        $this->trainee = auth()->user()->trainee->load([
            'activeGroup.group.courses.units.meetings' => function ($query) use ($traineeId) {
                $query->whereHas('trainees', function ($query) use ($traineeId) {
                    $query->where('meeting_trainee.trainee_id', $traineeId);
                })
                ->with([
                    'trainees' => function ($query) use ($traineeId) {
                        $query->where('trainees.id', $traineeId);
                    },
                    'trainees.meetingTrainees.instructor',
                    'trainees.meetingTrainees.meetingStatus',
                ]);
            },
        ]);
    }

    public function render()
    {
        return view('livewire.meetings.all-meetings', ['trainee' => $this->trainee]);
    }
}