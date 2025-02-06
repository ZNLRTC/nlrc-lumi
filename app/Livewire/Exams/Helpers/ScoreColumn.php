<?php

namespace App\Livewire\Exams\Helpers;

use Livewire\Component;
use App\Models\Exams\ExamTask;
use App\Models\Exams\ExamTaskScore;

class ScoreColumn extends Component
{
    public $record;
    public $taskScores;
    public $groupedTaskScores;
    public $uniqueSectionsCount;
    public $sectionTotals;
    public $sectionMaxScores;
    public $sectionPercentages;
    public $scores = [];

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function mount($record)
    {
        $this->record = $record;
        $this->loadData();
    }

    public function updatedRecord()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->taskScores = $this->getTaskScoresProperty();
        $this->groupedTaskScores = $this->getGroupedTaskScoresProperty();
        $this->uniqueSectionsCount = $this->getUniqueSectionsCountProperty();
        $this->sectionTotals = $this->getSectionTotalsProperty();
        $this->sectionMaxScores = $this->getSectionMaxScoresProperty();
        $this->sectionPercentages = $this->getSectionPercentagesProperty();

        foreach ($this->taskScores as $score) {
            $this->scores[$score->exam_task_id] = $score->score;
        }
    }

    public function getTaskScoresProperty()
    {
        $examAttemptId = $this->record->id;
        $traineeId = $this->record->trainee_id;

        return $this->record->taskScores()
            ->where('exam_attempt_id', $examAttemptId)
            ->where('trainee_id', $traineeId)
            ->with(['examTask.sections'])
            ->get();
    }

    public function getExamTasksProperty()
    {
        return $this->record->exam->sections()
            ->with('tasks')
            ->get()
            ->pluck('tasks')
            ->flatten();
    }

    public function getGroupedTaskScoresProperty()
    {
        $groupedTaskScores = $this->taskScores->groupBy(function ($score) {
            $section = $score->examTask->sections->first();
            return $section->short_name ?: $section->name ?? 'Uncategorized';
        });

        $groupedExamTasks = $this->getGroupedExamTasksProperty();

        // Includes tasks without scores so the staff can spot ungraded tasks and contact the instructor
        return $groupedExamTasks->map(function ($tasks, $sectionName) use ($groupedTaskScores) {
            $scores = $groupedTaskScores->get($sectionName, collect());

            return $tasks->map(function ($task) use ($scores) {
                $score = $scores->firstWhere('exam_task_id', $task->id);
                return $score ?: (object) [
                    'score' => 0,
                    'examTask' => $task,
                    'note' => 'not graded'
                ];
            });
        });
    }

    public function getGroupedExamTasksProperty()
    {
        $groupedExamTasks = $this->examTasks->groupBy(function ($task) {
            $section = $task->sections->first();
            return $section->short_name ?: $section->name ?? 'Uncategorized';
        });

        return $groupedExamTasks->map(function ($tasks) {
            return $tasks->sortBy(function ($task) {
                return $task->short_name ?: $task->name;
            });
        });
    }

    public function getUniqueSectionsCountProperty()
    {
        return $this->groupedTaskScores->keys()->count();
    }

    public function getSectionTotalsProperty()
    {
        return $this->groupedTaskScores->map(function ($scores) {
            return $scores->sum('score');
        });
    }

    public function getSectionMaxScoresProperty()
    {
        return $this->groupedExamTasks->map(function ($tasks) {
            return $tasks->sum('max_score');
        });
    }

    public function getSectionPercentagesProperty()
    {
        return $this->groupedTaskScores->map(function ($scores, $sectionName) {
            $totalScore = $this->sectionTotals[$sectionName];
            $maxScore = $this->sectionMaxScores[$sectionName];
            return $maxScore > 0 ? ($totalScore / $maxScore) * 100 : 0;
        });
    }

    private function getMaxScore($taskId)
    {
        $task = ExamTask::find($taskId);
        return $task ? $task->max_score : 100;
    }

    public function saveScore($taskId)
    {
        $this->validate([
            "scores.$taskId" => 'required|numeric|min:0|max:' . $this->getMaxScore($taskId),
        ], [
            "scores.$taskId.required" => 'Required.',
            "scores.$taskId.numeric" => 'Enter number.',
            "scores.$taskId.min" => 'Min is 0.',
            "scores.$taskId.max" => 'Max is ' . number_format($this->getMaxScore($taskId), $this->getMaxScore($taskId) == (int) $this->getMaxScore($taskId)? 0 : 2) . '.',
        ]);
        $score = $this->scores[$taskId];

        $examTaskScore = ExamTaskScore::firstOrNew([
            'exam_task_id' => $taskId,
            'exam_attempt_id' => $this->record->id,
            'trainee_id' => $this->record->trainee_id,
        ]);

        $examTaskScore->score = $score;
        $examTaskScore->instructor_id = auth()->id();
        $examTaskScore->save();

        // Alpine thingy listens to this to hide the input field
        $this->dispatch('score-saved', $taskId);
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.exams.helpers.score-column', [
            'groupedTaskScores' => $this->groupedTaskScores,
            'uniqueSectionsCount' => $this->uniqueSectionsCount,
            'sectionMaxScores' => $this->sectionMaxScores,
            'sectionTotals' => $this->sectionTotals,
            'sectionPercentages' => $this->sectionPercentages,
        ]);
    }
}