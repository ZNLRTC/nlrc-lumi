<?php

namespace App\Livewire\Meetings;

use App\Models\Courses\Course;
use App\Models\Meetings\Meeting;
use App\Models\Meetings\MeetingStatus;
use App\Models\Meetings\MeetingTrainee;
use App\Models\Trainee;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Validate; 

class LogMeeting extends Component
{
    use WithPagination;

    public $search;

    public $courses = [];
    public $units = [];
    public $meetings = [];

    public $selectedTraineeId;
    public $selectedUnitId;

    #[Validate('required', message: 'You must choose a course, a unit, and a meeting. If these dropdowns are blank, the trainee has no meetings in that particular course, meeting, or unit.')]
    public $selectedMeetingId;

    #[Validate('required', message: 'Choosing an outcome is required.')]
    public $selectedMeetingStatusId;

    #[Validate('required|date')]
    public $meetingDate;

    #[Validate('required|min:5|max:500')]
    public $meetingFeedback;

    public $meetingStatuses;

    #[Validate('max:500')]
    public $meetingNotes;

    public $traineeFullName;

    public $showAssignmentModal = false;

    public function mount()
    {
        $this->meetingStatuses = MeetingStatus::all();
        $this->meetingDate = now()->toDateString();
    }

    // Clear dropdowns and unit listings when the search bar updates
    public function updatedSearch()
    {       
        $this->courses = [];
        $this->units = [];
        $this->meetings = [];
        $this->traineeFullName = '';
    }

    public function selectTrainee($traineeId)
    {
        $this->selectedTraineeId = $traineeId;

        $trainee = Trainee::find($traineeId);
        $this->traineeFullName = "$trainee->last_name, $trainee->first_name";

        $this->reset('units');
        $this->reset('meetings');
        $this->selectedMeetingStatusId = null;
    
        // Load courses...
        $this->courses = $trainee->activeGroup->group->courses->pluck('name', 'id');
    
        // ...and units right away (for the first course)
        if ($this->courses->isNotEmpty()) {
            $this->selectCourse($this->courses->keys()->first());
        }

        // Send an event to update the list of the latest meetings
        $this->dispatch('update-meetings-plz', $traineeId);
    }

    public function selectCourse($courseId)
    {
        $this->units = Course::find($courseId)
            ->units->pluck('name', 'id');

        if ($this->units->isNotEmpty()) {
            $this->selectUnit($this->units->keys()->first());
        }
    }

    public function selectUnit($unitId)
    {
        $this->selectedUnitId = $unitId;

        $this->meetings = Meeting::where('unit_id', $unitId)->get();

        if ($this->meetings->isNotEmpty()) {
            $this->selectedMeetingId = $this->meetings->first()->id;
        }
    }

    public function createMeeting()
    {
        // sleep(2);

        $this->authorize('create', MeetingTrainee::class);

        $traineeId = $this->selectedTraineeId;
        $meetingId = $this->selectedMeetingId;

        $this->validate();
   
        MeetingTrainee::create([
            'trainee_id' => $traineeId,
            'meeting_id' => $meetingId,
            'instructor_id' => Auth::id(),
            'meeting_status_id' => $this->selectedMeetingStatusId,
            'date' => $this->meetingDate,
            'feedback' => $this->meetingFeedback,
            'internal_notes' => $this->meetingNotes,
        ]);
    
        $this->meetingFeedback = '';
        $this->meetingNotes = '';
        $this->selectedMeetingStatusId = null;

        // Update the sidebar and show a success message
        $this->dispatch('update-meetings-plz', $traineeId);
        request()->session()->flash('meeting-saved','Meeting saved');
    }

    // Send selected unit and trainee to MarkAssignment
    public function openAssignment()
    {
        $this->dispatch('unit-and-trainee-selected', [
            'unitId' => $this->selectedUnitId,
            'traineeId' => $this->selectedTraineeId
        ]); 

        $this->showAssignmentModal = true;
    }

    public function render()
    {
        // This splits the string or otherwise typing "Dela Cruz, Juan" fast wouldn't show anything
        $searchTerms = collect(explode(',', str_replace(' ', ',', $this->search)))
                        ->filter()
                        ->all();
    
        $trainees = collect();

        if (!empty($searchTerms)) {
            $trainees = Trainee::with(['user', 'activeGroup.group'])
                ->where(function ($query) use ($searchTerms) {
                    foreach ($searchTerms as $term) {
                        $query->orWhere('first_name', 'like', "%{$term}%")
                            ->orWhere('last_name', 'like', "%{$term}%")
                            ->orWhereHas('user', function ($query) use ($term) {
                                $query->where('email', 'like', "%{$term}%");
                            });
                    }
                })
                ->where('active', true)
                ->whereDoesntHave('activeGroup.group', function ($query) {
                    $query->where('name', 'Kyl mä hoidan');
                })
                ->latest()
                ->paginate(5);
        }
    
        return view('livewire.meetings.log-meeting', [
            'trainees' => $trainees,
        ]);
    }
}