<?php

namespace App\Livewire\Exams\Helpers;

use App\Models\Trainee;
use App\Models\Exams\ExamAttempt;
use App\Models\Exams\ExamSection;
use App\Models\Exams\ExamTask;
use App\Models\Exams\ProficiencyTrainee;
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Container\Attributes\Auth;

class ShowTraineeDetails extends Component
{
    public $trainee;
    public $exam;
    public $type;

    public $trainee_profile_photo;
    public $pastAttempts;
    public $sectionsWithTasks;
    public $totalSections;

    #[On('load-trainees-exam-details')]
    #[On('reload-details')]
    public function showDetails($traineeId)
    {
        $this->trainee = Trainee::find($traineeId);
        $this->trainee_profile_photo = $this->trainee->user->profile_photo_path ?? null;
        $this->pastAttempts = ExamAttempt::with('instructor')
                                        ->where('trainee_id', $traineeId)
                                        ->where('exam_id', $this->exam->id)
                                        ->orderBy('created_at', 'desc')
                                        ->get();

        $sections = ExamSection::whereHas('exams', function ($query) {
            $query->where('exam_exam_section.exam_id', $this->exam->id);
        })->get();

        $this->totalSections = $sections->count();

        $this->sectionsWithTasks = $sections->map(function ($section) use ($traineeId) {
            $tasks = ExamTask::whereHas('sections', function ($query) use ($section) {
                $query->where('exam_section_task.exam_section_id', $section->id);
            })
            ->with(['examTaskScores' => function ($query) use ($traineeId) {
                $query->where('trainee_id', $traineeId)
                    ->with('instructor'); // Instructor's name is not used in the test view. If we don't re-use this component anywhere, we could remove the eager loading
            }])
            ->get();

            return [
                'section' => $section,
                'tasks' => $tasks
            ];
        });
    }

    public function reloadForm()
    {
        $traineeId = $this->trainee->id;
    
        $this->dispatch('reload-form', $traineeId);
    }

    #[On('reset-all')]
    public function resetDetails()
    {
        $this->reset('trainee');
        $this->reset('trainee_profile_photo');
        $this->reset('pastAttempts');
        $this->reset('sectionsWithTasks');
    }

    public function deleteAttempt(ExamAttempt $attempt)
    {
        $this->authorize('delete', $attempt);
        
        $proficiencyId = $this->exam->proficiency->id;

        ProficiencyTrainee::where('trainee_id', $this->trainee->id)
            ->where('proficiency_id', $proficiencyId)
            ->delete();

        $attempt->delete();

        $this->showDetails($this->trainee->id);

        $this->reloadForm();
    }

    public function render()
    {
        return view('livewire.exams.helpers.show-trainee-details');
    }
}
