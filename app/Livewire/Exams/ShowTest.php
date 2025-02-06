<?php

namespace App\Livewire\Exams;

use Carbon\Carbon;
use App\Models\Trainee;
use Livewire\Component;
use App\Models\Exams\Exam;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use App\Enums\Exams\ExamAttemptStatus;

class ShowTest extends Component
{

    // This is assessment and test grading for instructors

    public $search;
    public $traineeFullName;
    public $selectedTraineeId;
    public $exam;
    public $type;

    public function selectTrainee($traineeId)
    {
        $this->selectedTraineeId = $traineeId;

        $trainee = Trainee::find($traineeId);
        $this->traineeFullName = "$trainee->last_name, $trainee->first_name";
    }

    #[On('reset-search-selection')]
    public function render()
    {
        $searchTerms = collect(explode(',', str_replace(' ', ',', $this->search)))
            ->filter()
            ->all();

        $trainees = collect();

        if (empty($searchTerms)) {
            $this->dispatch('reset-all'); // This resets the detail and the form component that are on the same page
        } else {
            $trainees = Trainee::with([
                'user', 
                'proficiencyTrainees', 
                'examAttempts' => function ($query) {
                    $query->where('exam_id', $this->exam->id);
                },
                'exams' => function ($query) {
                    $query->wherePivot('exam_id', $this->exam->id);
                },])
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
                ->get()
                ->map(function ($trainee) {
                    $trainee->eligibility_status = $this->determineEligibility($trainee);
                    return $trainee;
                });
        }

        return view('livewire.exams.show-test', [
            'trainees' => $trainees,
            'exam' => $this->exam,
            'type' => $this->type,
        ]);
    }

    private function determineEligibility($trainee)
    {
        $pastAttempts = $trainee->examAttempts;

        // Not allowed to retake too early
        if ($pastAttempts->isNotEmpty()) {

            $latestAttempt = $pastAttempts->sortByDesc('created_at')->first();
            $earliestNextAttempt = $pastAttempts->max('earliest_next_attempt');
            
            if ($latestAttempt && 
                $latestAttempt->instructor_id == Auth::id() && 
                $latestAttempt->created_at->greaterThanOrEqualTo(Carbon::now()->subHours(2))) {
                return 'View only';
            }

            if ($earliestNextAttempt && now()->lt($earliestNextAttempt)) {

                if ($latestAttempt && $latestAttempt->absent) {
                    return "Cannot re-take this $this->type until " . Carbon::parse($earliestNextAttempt)->format('D, F j, Y');
                }

                return "Trainee cannot re-take this $this->type until " . Carbon::parse($earliestNextAttempt)->format('D, F j, Y');

            }
        }

        // Not allowed to retake if the trainee already passed
        $successfulAttemptExists = $pastAttempts->contains('status', ExamAttemptStatus::PASSED);

        if ($successfulAttemptExists) {
            return "Trainee already passed this $this->type";
        }

        // Not allowed to retake if the trainee already reached the proficiency level
        $proficiencyReached = $trainee->proficiencyTrainees
            ->where('proficiency_id', $this->exam->proficiency_id)
            ->first();
    
        if ($proficiencyReached && $proficiencyReached->is_proficient) {
            return "Trainee already passed this $this->type";
        }

        // THere needs to be a pivot table entry for the exam before the trainee can take it
        $examExistsInPivot = $trainee->exams->contains('pivot.exam_id', $this->exam->id);

        if ($examExistsInPivot) {
            return 'Eligible';
        } else {
            return "Trainee is not eligible to take this $this->type";
        }
        
        return "Trainee is not eligible to take this $this->type";
    }
}
