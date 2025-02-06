<?php

namespace App\Livewire\Exams;

use Livewire\Component;
use App\Models\Exams\Exam;
use App\Models\Exams\ExamAttempt;
use Illuminate\Support\Facades\Auth;

class Results extends Component
{

    // This shows the results to the trainee

    // Livewire doesn't allow a groupBy query when assigning models to properties so this needs a computed property
    // https://livewire.laravel.com/docs/properties#eloquent-constraints-arent-preserved-between-requests
    public function getGroupedExamAttemptsProperty()
    {
        $examAttempts = ExamAttempt::where('trainee_id', Auth::user()->trainee->id)
                            ->where('is_published', true)        
                            ->with('taskScores.examTask.sections.exams')    
                            ->get();

        $exams = Exam::all()->keyBy('id');

        // Group exam attempts by type
        $groupedByType = $examAttempts->groupBy(function ($attempt) use ($exams) {
            return $exams[$attempt->exam_id]->type;
        });

        $sortedGroupedByType = $groupedByType->map(function ($group) {
            return $group->sortBy('date');
        });

        return $sortedGroupedByType;
    }

    // Group task scores by section and calculate their totals, called from the view
    public function getSectionScores($attempt)
    {
        if ($attempt->exam->type !== 'exam') {
            return collect();
        }
        
        $groupedBySection = $attempt->taskScores->groupBy(function ($score) {
            return $score->examTask->sections->first()->id;
        });

        $sectionScores = $groupedBySection->map(function ($scores) {
            $totalScore = $scores->sum('score');
            $totalMaxScore = $scores->sum('examTask.max_score');
            $section = $scores->first()->examTask->sections->first();

            // This groups tasks by short_name up to the first space so that "Listening A, B, C, and "Listening D" are shown as just "Listening"
            $groupedTasks = $scores->groupBy(function ($score) {
                return explode(' ', $score->examTask->short_name ?? $score->examTask->name)[0];
            });

            $tasks = $groupedTasks->map(function ($group, $shortName) {
                return [
                    'task_name' => $shortName ?? $group->first()->examTask->name,
                    'score' => $group->sum('score'),
                    'max_score' => $group->sum('examTask.max_score'),
                ];
            });

            return [
                'section' => $section,
                'total_score' => $totalScore,
                'total_max_score' => $totalMaxScore,
                'tasks' => $tasks,
            ];
        });

        return $sectionScores;
    }

    public function render()
    {
        return view('livewire.exams.results', [
            'groupedExamAttempts' => $this->groupedExamAttempts,
        ]);
    }
}
