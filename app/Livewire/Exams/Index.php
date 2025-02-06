<?php

namespace App\Livewire\Exams;

use Livewire\Component;
use App\Models\Exams\Exam;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{

    // This lists gradable exams for instructors

    protected $gradableExams;

    public function mount()
    {
        $this->fetchExams();
    }

    public function fetchExams()
    {
        $userId = Auth::id();
        $oneWeekAgo = now()->subWeek();
    
        $exams = Exam::where(function ($query) use ($userId) {
                $query->where('any_instructor_can_grade', true)
                      ->orWhereJsonContains('allowed_instructors', $userId);
            })
            ->where(function ($query) use ($oneWeekAgo) {
                $query->whereNull('date')
                      ->orWhere('date', '>=', $oneWeekAgo);
            })
            ->get();
    
        $this->gradableExams = $exams->groupBy('type');
    
        // Tests are most probs used most frequently so they go firsst
        $order = ['tests', 'assessments', 'exams'];
        $this->gradableExams = $this->gradableExams->sortBy(function ($value, $key) use ($order) {
            return array_search($key, $order);
        });
    }

    public function render()
    {
        return view('livewire.exams.index', [
            'gradableExams' => $this->gradableExams,
        ]);
    }
}
