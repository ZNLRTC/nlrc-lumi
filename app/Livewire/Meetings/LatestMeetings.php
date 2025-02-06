<?php

namespace App\Livewire\Meetings;

use App\Models\Trainee;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Meetings\MeetingTrainee;

class LatestMeetings extends Component
{
    public $latestMeetings = [];
    public $currentTraineeId;
    public $count = 5;
    public $totalMeetings;
    public $allMeetingsLoaded = false;

    #[On('update-meetings-plz')]
    public function setTrainee($traineeId = null)
    {
        $this->currentTraineeId = $traineeId;
        $this->count = 5;
        $this->loadLatestMeetings($traineeId);
    }

    public function loadLatestMeetings($traineeId)
    {
        $trainee = Trainee::find($traineeId);

        if ($trainee->meetings()->exists()) {
            $this->latestMeetings = $trainee->meetings()
                ->withPivot('instructor_id','created_at','id') // This is needed to add the id of the meeting_trainee pivot thingy to the collection so that meetings can be deleted
                ->orderByDesc('date')
                ->take($this->count)
                ->get();
            
            $totalMeetingsCount = $trainee->meetings()->count();
            $this->allMeetingsLoaded = $totalMeetingsCount <= $this->count;
        } else {
            $this->latestMeetings = collect();
            $this->allMeetingsLoaded = true;
        }
    }

    public function loadMore()
    {
        $this->count += 5;
        $this->loadLatestMeetings($this->currentTraineeId);
    }

    public function allMeetingsLoaded()
    {
        return $this->count >= $this->totalMeetings;
    }

    public function deleteMeeting(MeetingTrainee $meeting)
    {
        $this->authorize('delete', $meeting);

        $meeting->delete();
        
        $this->loadLatestMeetings($this->currentTraineeId);
    }

    public function render()
    {
        return view('livewire.meetings.latest-meetings', [
            'allMeetingsLoaded' => $this->allMeetingsLoaded,
        ]);
    }
}
