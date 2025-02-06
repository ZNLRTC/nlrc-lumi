<?php

namespace App\Livewire\Exams;

use Livewire\Component;
use App\Models\Exams\Exam;
use App\Models\Exams\ExamAttempt;
use App\Models\Exams\ExamTaskScore;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ShowExam extends Component
{

    // This is exam grading for instructors

    public $exam;
    public $type;
    public $scores = [];

    public $sectionVisibility = [];

    public function mount()
    {
        $this->exam = Exam::with([
            'trainees',
            'sections.tasks'
        ])->findOrFail($this->exam->id);

        $this->exam->trainees = $this->sortTrainees($this->exam->trainees);

        // All sections are visible by default
        foreach ($this->exam->sections as $section) {
            $this->sectionVisibility[$section->id] = true;
        }
        
        // Fetch and map scores for fields that already have them in the db
        $existingScores = ExamTaskScore::whereIn('trainee_id', $this->exam->trainees->pluck('id'))
            ->whereIn('exam_task_id', $this->exam->sections->pluck('tasks.*.id')->flatten())
            ->get();
        
        foreach ($existingScores as $score) {
            $this->scores["task_{$score->exam_task_id}_trainee_{$score->trainee_id}"] = $score->score;
        }
        
        // Scores for tasks without existing scores
        foreach ($this->exam->trainees as $trainee) {
            foreach ($this->exam->sections as $section) {
                foreach ($section->tasks as $task) {
                    $key = "task_{$task->id}_trainee_{$trainee->id}";
                    if (!isset($this->scores[$key])) {
                        $this->scores[$key] = null;
                    }
                }
            }
        }
        
        // dd($this->scores);
    }

    public function getSortedTraineesProperty()
    {
        return $this->sortTrainees($this->exam->trainees);
    }

    // Sort by trainee alias
    private function sortTrainees($trainees)
    {
        // Usually consists of the group number and the trainee number so can be split and sorted separately
        return $trainees->sort(function ($a, $b) {
            $aliasA = $a->pivot->trainee_alias ?? '';
            $aliasB = $b->pivot->trainee_alias ?? '';
    
            // Handle cases where it's missing or something else than XXX-YYY
            if ($aliasA === '' && $aliasB === '') {
                return strcmp($a->last_name, $b->last_name);
            } elseif ($aliasA === '') {
                return 1;
            } elseif ($aliasB === '') {
                return -1;
            }
    
            $partsA = explode('-', $aliasA);
            $partsB = explode('-', $aliasB);
    
            if (count($partsA) === 2 && count($partsB) === 2) {
                $firstComparison = (int) $partsA[0] <=> (int) $partsB[0];
                if ($firstComparison === 0) {
                    return (int) $partsA[1] <=> (int) $partsB[1];
                }
                return $firstComparison;
            }
    
            return strcmp($aliasA, $aliasB);
        })->values();
    }

    public function toggleSectionVisibility($sectionId)
    {
        $this->sectionVisibility[$sectionId] = !$this->sectionVisibility[$sectionId];
    }

    public function updated($propertyName, $value)
    {
        if (str_starts_with($propertyName, 'scores.')) {
            $this->saveScore($propertyName, $value);
        }
    }
    
    public function saveScore($propertyName, $value)
    {
        // This mega hack pulls the task and trainee ids from the property name
        list($taskId, $traineeId) = sscanf($propertyName, 'scores.task_%d_trainee_%d');
       
        // Score table rows need to reference this so it's created first
        $examAttempt = ExamAttempt::firstOrCreate([
            'exam_id' => $this->exam->id,
            'trainee_id' => $traineeId,
        ], [
            'instructor_id' => Auth::id(),
            'date' => $this->exam->date,
        ]);
    
        if ($value === null || $value === '') {
            // Remove the score if the grader chooses the placeholder option tin the dropdown
            ExamTaskScore::where([
                'exam_task_id' => $taskId,
                'trainee_id' => $traineeId,
                'exam_attempt_id' => $examAttempt->id,
            ])->delete();
        } else {
            ExamTaskScore::updateOrCreate([
                'exam_task_id' => $taskId,
                'trainee_id' => $traineeId,
                'exam_attempt_id' => $examAttempt->id,
            ], [
                'score' => $value,
                'instructor_id' => Auth::id(),
            ]);
        }
    
        // Log::info("Score saved for taskId: $taskId, traineeId: $traineeId, value: $value");
    }

    public function render()
    {
        return view('livewire.exams.show-exam', [
            'exam' => $this->exam,
            'sectionVisibility' => $this->sectionVisibility,
            'sortedTrainees' => $this->sortedTrainees,
        ]);
    }
}
