<?php

namespace App\Livewire\Meetings\Assignments;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate; 
use Illuminate\Support\Facades\Auth;
use App\Enums\Assignments\SubmissionStatus;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Meetings\Assignments\Assignment;
use App\Models\Meetings\Assignments\AssignmentSubmission;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class SubmitAssignment extends Component
{
    use AuthorizesRequests;

    public User $user;

    public Assignment $assignment;
    public string $instructions;
    
    public Collection $submissions;
    public ?AssignmentSubmission $latestSubmission = null;
    public Collection $pastCheckedSubmissions;

    public bool $isEditing = false;

    #[Validate('required|min:5|max:500')]
    public string $submission;

    public function mount(Assignment $assignment)
    {
        $this->assignment = $assignment;
        
        $this->instructions = Str::of($this->assignment->description)->markdown([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        $this->user = Auth::user()->load('trainee');

        $this->submissions = AssignmentSubmission::with('instructor')
            ->where('assignment_id', $this->assignment->id)
            ->where('trainee_id', $this->user->trainee->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $this->latestSubmission = $this->submissions->first();
        $this->pastCheckedSubmissions = $this->submissions->where('submission_status', '<>', SubmissionStatus::NOT_CHECKED);
        
        // Show empty textarea for a new submission if previous submission was graded incomplete
        if ($this->latestSubmission && $this->latestSubmission->submission_status != SubmissionStatus::INCOMPLETE) {
            $this->submission = $this->latestSubmission->submission;
        } else {
            $this->submission = '';
        }
    }

    public function submit()
    {
        $this->authorize('create', AssignmentSubmission::class);

        // Prevents submitting on someone else's behalf via dev tools
        if (Auth::user()->hasRole('Trainee') && Auth::user()->trainee->id != $this->user->trainee->id) {
            abort(401);
        }
    
        $this->validate();
    
        $this->latestSubmission = AssignmentSubmission::create([
            'assignment_id' => $this->assignment->id,
            'trainee_id' => $this->user->trainee->id,
            'submission' => $this->submission,
            'submission_status' => SubmissionStatus::NOT_CHECKED,
            'submitted_at' => now(),
        ]);
    
        $this->submission = $this->latestSubmission->submission;
    }

    public function startEditing()
    {
        $this->isEditing = true;
    }

    // Put the latest submission back if the user cancels edit
    public function cancelEditing()
    {
        $this->submission = $this->latestSubmission->submission;
        $this->isEditing = false;
    }

    public function updateSubmission()
    {
        // This should suffice since the policy checks for trainee id and submission id
        $this->authorize('update', $this->latestSubmission);

        $this->validate();

        $this->latestSubmission->update([
            'submission' => $this->submission,
            'edited_at' => now(),
        ]);

        $this->isEditing = false;
    }

    public function render()
    {
        return view('livewire.meetings.assignments.submit-assignment');
    }
}
