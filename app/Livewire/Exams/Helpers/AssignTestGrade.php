<?php

namespace App\Livewire\Exams\Helpers;

use DateTime;
use DateInterval;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Validation\Rule;
use App\Models\Exams\ExamAttempt;
use App\Models\Exams\ExamSection;
use Livewire\Attributes\Validate;
use App\Models\Exams\ExamTaskScore;
use Illuminate\Support\Facades\Auth;
use App\Enums\Exams\ExamAttemptStatus;
use App\Models\Exams\ProficiencyTrainee;

class AssignTestGrade extends Component
{
    public $traineeId;
    public $exam;
    public $type;
    
    public $isSaved = false;
    
    #[Validate('max:500')]
    public $attemptFeedback;
    
    #[Validate('max:500')]
    public $attemptNotes;
    
    public $grades = [];
    public $maxScores = [];
    public $minScores = [];
    public $status;
    
    public $totalSections;
    public $sectionsWithTasks;

    public $sectionTotals = [];
    public $selectedSectionTotals = [];
    public $sectionPassingStatus = [];

    #[On('reload-form')]
    #[On('load-trainees-exam-details')]
    public function loadDetails($traineeId)
    {
        $this->resetForm();
        $this->isSaved = false;

        $this->traineeId = $traineeId;

        $sections = ExamSection::whereHas('exams', function ($query) {
            $query->where('exam_exam_section.exam_id', $this->exam->id);
        })->with('tasks')->get();
    
        $this->totalSections = $sections->count();
    
        $this->sectionsWithTasks = $sections->map(function ($section) {
            return [
                'section' => $section,
                'tasks' => $section->tasks
            ];
        });
    
        foreach ($this->sectionsWithTasks as $sectionWithTasks) {
            $sectionTotal = 0;
            foreach ($sectionWithTasks['tasks'] as $task) {
                $this->maxScores[$task->id] = $task->max_score;
                $this->minScores[$task->id] = $task->min_score;
                $sectionTotal += $task->max_score;
            }
            $this->sectionTotals[$sectionWithTasks['section']->id] = $sectionTotal;
        }
    }

    public function calculateSelectedSectionTotals()
    {
        $this->selectedSectionTotals = [];
    
        foreach ($this->sectionsWithTasks as $sectionWithTasks) {
            $sectionId = $sectionWithTasks['section']->id;
            $sectionTotal = 0;
    
            foreach ($sectionWithTasks['tasks'] as $task) {
                if (isset($this->grades[$task->id]) && is_numeric($this->grades[$task->id])) {
                    $sectionTotal += (float) $this->grades[$task->id];
                }
            }
    
            $this->selectedSectionTotals[$sectionId] = $sectionTotal;
        }
    
        $this->checkSectionPassingStatus();
        $this->checkIfAllSectionsPass();
    }

    public function checkSectionPassingStatus()
    {
        foreach ($this->sectionsWithTasks as $sectionWithTasks) {
            $sectionId = $sectionWithTasks['section']->id;
            $sectionTotal = $this->sectionTotals[$sectionId];
            $selectedTotal = $this->selectedSectionTotals[$sectionId] ?? 0;
            $passingPercentage = $sectionWithTasks['section']->passing_percentage;
    
            $mandatoryTasksPassed = true;
            foreach ($sectionWithTasks['tasks'] as $task) {
                if ($task->mandatory_to_pass && (!isset($this->grades[$task->id]) || $this->grades[$task->id] < $task->passing_score)) {
                    $mandatoryTasksPassed = false;
                    break;
                }
            }
    
            $this->sectionPassingStatus[$sectionId] = $mandatoryTasksPassed && ($selectedTotal / $sectionTotal) * 100 >= $passingPercentage;
        }
    }

    public function checkIfAllSectionsPass()
    {
        $allSectionsPass = true;

        foreach ($this->sectionsWithTasks as $sectionWithTasks) {
            $sectionId = $sectionWithTasks['section']->id;
            if (empty($this->sectionPassingStatus[$sectionId]) || !$this->sectionPassingStatus[$sectionId]) {
                $allSectionsPass = false;
                break;
            }
        }

        if ($allSectionsPass) {
            $this->status = ExamAttemptStatus::PASSED->value;
        }
    }

    #[On('reload-form')]
    public function reloadForm()
    {
        $this->isSaved = false;
        $this->resetForm();
    }
    
    #[On('reset-all')]
    public function resetAll()
    {
        $this->resetForm(true);
    }
    
    public function resetForm($resetSectionsWithTasks = false)
    {
        $this->reset('grades');
        $this->reset('selectedSectionTotals');
        $this->reset('status');
        $this->reset('attemptFeedback');
        $this->reset('attemptNotes');
    
        if ($resetSectionsWithTasks) {
            $this->reset('sectionsWithTasks');
            $this->reset('sectionTotals');
            $this->reset('sectionPassingStatus');
            $this->reset('selectedSectionTotals');
        }
    }

    public function reloadDetails()
    {
        $traineeId = $this->traineeId;
    
        $this->dispatch('reload-details', $traineeId);
        $this->dispatch('reset-search-selection');
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName, $this->rules());
    }

    public function updatedGrades()
    {
        $this->calculateSelectedSectionTotals();
    }

    public function rules()
    {
        $rules = [];
        foreach ($this->maxScores as $taskId => $maxScore) {
            $minScore = $this->minScores[$taskId];
            $rules["grades.$taskId"] = "required|numeric|min:$minScore|max:$maxScore";
        }

        $rules['status'] = ['required', Rule::in(array_column(ExamAttemptStatus::cases(), 'value'))];
        return $rules;
    }

    public function messages()
    {
        $messages = [];
        foreach ($this->maxScores as $taskId => $maxScore) {
            $minScore = $this->minScores[$taskId];
            $messages["grades.$taskId.required"] = "Points are required.";
            $messages["grades.$taskId.numeric"] = "This must be a number.";
            $messages["grades.$taskId.min"] = "Points must be at least $minScore.";
            $messages["grades.$taskId.max"] = "Points must not exceed $maxScore.";
        }

        $messages['status.required'] = 'You must choose the outcome.';
        return $messages;
    }

    public function updatedStatus($value)
    {
        $this->attemptFeedback = $value === ExamAttemptStatus::ABSENT->value ? 'Did not attend.' : '';

        $this->dispatch('status-updated', attemptFeedbackText: $this->attemptFeedback);
    }

    public function submitGrades()
    {
        $this->authorize('create', ExamAttempt::class);

        if ($this->status === ExamAttemptStatus::ABSENT->value) {
            $this->validate([
                'status' => 'required',
                'attemptFeedback' => 'max:500',
                'attemptNotes' => 'max:500',
            ]);
        } else {
            $this->validate($this->rules(), $this->messages());
        }

        $existingPassedAttempt = ExamAttempt::where('trainee_id', $this->traineeId)
            ->where('exam_id', $this->exam->id)
            ->where('status', ExamAttemptStatus::PASSED->value)
            ->first();

        if ($existingPassedAttempt) {
            request()->session()->flash('already-passed', 'You cannot add another attempt if the trainee already passed. Delete the earlier attempt first.');

            $this->isSaved = true;

            return;
        }

        // Set the earliest next attempt to the next Monday so the trainee can't retake during the same week
        $monday = new DateTime();
        if ($monday->format('N') == 1) { 
            $monday->add(new DateInterval('P7D')); // Add 7 days if it's Monday
        } else {
            $monday->modify('next Monday');
        }

        $earliestNextAttempt = $this->status === ExamAttemptStatus::PASSED->value ? null : $monday->format('Y-m-d');

        $examAttempt = ExamAttempt::create([
            'trainee_id' => $this->traineeId,
            'exam_id' => $this->exam->id,
            'status' => $this->status,
            'instructor_id' => Auth::id(),
            'earliest_next_attempt' => $earliestNextAttempt,
            'feedback' => $this->attemptFeedback, 
            'internal_notes' => $this->attemptNotes,
            'is_published' => false,
            'date' => now(),
        ]);

        foreach ($this->sectionsWithTasks as $sectionWithTasks) {
            foreach ($sectionWithTasks['tasks'] as $task) {
                $grade = $this->status == ExamAttemptStatus::ABSENT->value ? 0 : ($this->grades[$task->id] ?? 0);
                ExamTaskScore::create([
                    'trainee_id' => $this->traineeId,
                    'exam_task_id' => $task->id,
                    'instructor_id' => Auth::id(),
                    'exam_attempt_id' => $examAttempt->id,
                    'score' => $grade,
                ]);
            }
        }

        if ($this->status === ExamAttemptStatus::PASSED->value) {
            // $exam = Exam::find($this->exam->id);
            $proficiencyId = $this->exam->proficiency->id;
    
            ProficiencyTrainee::updateOrCreate(
                [
                    'trainee_id' => $this->traineeId,
                    'proficiency_id' => $proficiencyId,
                ],
                [
                    'is_proficient' => true,
                    'exam_attempt_id' => $examAttempt->id,
                ]
            );
        }

        request()->session()->flash('grades-saved', 'Result saved successfully.');

        $this->reloadDetails();

        $this->isSaved = true;

        $this->resetForm();

        $this->dispatch('grades-submitted');
    }

    public function render()
    {
        return view('livewire.exams.helpers.assign-test-grade');
    }
}
