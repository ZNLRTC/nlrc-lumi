<?php

namespace App\Livewire\Meetings\Assignments;

use Livewire\Component;
use Illuminate\Support\Str;
// use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On; 
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Enum;
use App\Enums\Assignments\SubmissionStatus;
use App\Models\Meetings\Assignments\Assignment;
use App\Models\Meetings\Assignments\AssignmentSubmission;

class MarkAssignment extends Component
{
    public $originalSubmissionState = [];
    public $editingSubmissionId;
    public $selectedTraineeId;
    public $selectedUnitId;
    public $feedback = [];
    public $status = [];

    // This receives data from the meeting logging component
    #[On('unit-and-trainee-selected')]
    public function setAssignment($infoFromEvent)
    {
        $this->selectedTraineeId = $infoFromEvent['traineeId'];
        $this->selectedUnitId = $infoFromEvent['unitId'];
    }

    #[Computed]
    public function getAssignmentsProperty()
    {
        $assignments = Assignment::where('unit_id', $this->selectedUnitId)
            ->whereHas('submissions', function ($query) {
                $query->where('trainee_id', $this->selectedTraineeId);
            })
            ->with(['submissions' => function ($query) {
                $query->where('trainee_id', $this->selectedTraineeId);
            }])
            ->get();

        foreach ($assignments as $assignment) {
            $assignment->instructions = Str::of($assignment->description)->markdown([
                'html_input' => 'strip',
                'allow_unsafe_links' => false,
            ]);
        }

        return $assignments;
    }

    #[Computed]
    public function getSubmissionsProperty()
    {
        return AssignmentSubmission::where('trainee_id', $this->selectedTraineeId)
            ->whereIn('assignment_id', $this->assignments->pluck('id'))
            ->get();
    }


    public function edit($submissionId)
    {
        $submission = AssignmentSubmission::with('instructor')->find($submissionId);
    
        // Store the original state in case the edit is canceled
        $this->originalSubmissionState = [
            'checked_at' => $submission->checked_at,
            'instructor_id' => $submission->instructor_id,
            'feedback' => $submission->feedback,
            'submission_status' => $submission->submission_status,
        ];

        $this->authorize('update', $submission);
    
        // Clear stuff in case the instructor reverts their edit and closes the window, wanting to erase the entry they made. Otherwise, the instructor_id would remain in the entry, making it impossible for other instructors to update it
        $submission->update([
            'checked_at' => null,
            'instructor_id' => null,
            'feedback' => null,
            'submission_status' => SubmissionStatus::NOT_CHECKED,
        ]);
    
        $this->editingSubmissionId = $submissionId;
    }

    public function cancelEdit()
    {

        if ($this->originalSubmissionState) {
            $submission = AssignmentSubmission::find($this->editingSubmissionId);
            $this->authorize('update', $submission);
            $submission->update($this->originalSubmissionState);
        }
    
        $this->editingSubmissionId = null;
        $this->originalSubmissionState = null;
    }

    public function saveFeedback($submissionId)
    {
        $this->validate([
            'feedback.' . $submissionId => 'max:500',
            'status.' . $submissionId => ['required', new Enum(SubmissionStatus::class)],
        ], [
            'feedback.' . $submissionId . '.max' => 'Feedback can only be up to 500 characters.',
            'status.' . $submissionId . '.required' => 'Status is required.',
            'status.' . $submissionId . '.in' => 'Status must be either "Completed" or "Incomplete".',
        ]);

        // Update the submission
        $submission = AssignmentSubmission::where('id', $submissionId)
            ->where('trainee_id', $this->selectedTraineeId)
            ->first();

        if ($submission) {
            $submission->update([
                'feedback' => $this->feedback[$submissionId] ?? null,
                'submission_status' => $this->status[$submissionId],
                'instructor_id' => Auth::id(),
                'checked_at' => now(),
            ]);

            // Clear the feedback and status
            unset($this->feedback[$submissionId], $this->status[$submissionId], $this->editingSubmissionId);
        }
    }

    public function render()
    {
        return view('livewire.meetings.assignments.mark-assignment', [
            'assignments' => $this->assignments,
            'submissions' => $this->submissions,
        ]);
    }
}