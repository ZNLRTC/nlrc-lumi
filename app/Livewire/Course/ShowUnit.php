<?php

namespace App\Livewire\Course;

use App\Models\Courses\Course;
use Illuminate\Support\Facades\Crypt;
use App\Models\Courses\Unit;
use App\Models\Quizzes\Quizzes;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class ShowUnit extends Component
{
    public Course $course;
    public Unit $unit;

    public function mount(Course $course, Unit $unit)
    {
        $this->course = $course;
        $this->unit = $unit;
    }

    public function has_quiz($topic_id){
        $decryptedId = Crypt::decrypt($topic_id);
        $quiz = Quizzes::where('topic_id', $decryptedId)->get()->toArray();
        return $quiz;
    }

    public function render()
    {
        return view('livewire.course.show-unit');
    }
}