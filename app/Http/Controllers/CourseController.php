<?php

namespace App\Http\Controllers;

use App\Models\Courses\Unit;
use Illuminate\Http\Request;
use App\Models\Courses\Course;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Meetings\Assignments\Assignment;

class CourseController extends Controller
{
    public function showKMH(Course $course)
    {
        $course = Course::where('slug', 'kmh')->with('units')->firstOrFail();
        return view('courses.main', ['course' => $course]);
    }

    public function courseIndex(Course $course)
    {
        return view('courses.main', ['course' => $course]);
    }

    public function unitIndex(Course $course, Unit $unit)
    {
        if (is_null($course)) {
            $course = Course::where('slug', 'kmh')->firstOrFail();
        }

        $unit = Cache::remember("unit.{$unit->id}", 60*60*24*30, function () use ($unit) {
            return Unit::with(['assignments', 'topics'])->withCount(['assignments', 'topics'])->find($unit->id);
        });
        
        // $unit->load('assignments', 'course', 'topics');

        $previousUnit = Unit::where('course_id', $course->id)
            ->where('sort', '<', $unit->sort)
            ->orderBy('sort', 'desc')
            ->first();

        $nextUnit = Unit::where('course_id', $course->id)
            ->where('sort', '>', $unit->sort)
            ->orderBy('sort', 'asc')
            ->first();
        
        return view('courses.show-unit', compact('course', 'unit', 'previousUnit', 'nextUnit'));
    }

    public function createAssignment(Course $course, Unit $unit, Assignment $assignment)
    {
        $traineeId = Auth::user()->trainee->id;

        $assignment->load(['submissions' => function ($query) use ($traineeId) {
            $query->where('trainee_id', $traineeId);
        }]);
        
        return view('meetings.assignments.assignment', compact('course', 'unit', 'assignment'));
    }
}
