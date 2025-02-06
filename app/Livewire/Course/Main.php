<?php

namespace App\Livewire\Course;

use Livewire\Component;
use App\Models\Courses\Course;
use Illuminate\Support\Facades\Cache;

class Main extends Component
{
    public Course $course;

    public function mount(Course $course)
    {
        $this->course = Cache::remember("course.{$course->id}.units", 60 * 60 * 24, function () use ($course) {
            return Course::with(['units' => function ($query) {
                $query->select('id', 'course_id', 'name', 'slug', 'description');
            }])->withCount('units')->find($course->id);
        });
    }

    public function render()
    {
        return view('livewire.course.main');
    }
}
