<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Exams\Exam;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use function PHPSTORM_META\type;

class InstructorCanGrade
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user->hasAnyRole(['Instructor', 'Editing instructor'])) {

            $examId = $request->route('exam');
            $exam = Exam::find($examId);

            if ($exam) {
                $allowedInstructors = $exam->allowed_instructors ? $exam->allowed_instructors : [];

                if (in_array($user->id, $allowedInstructors) || $exam->any_instructor_can_grade) {
                    return $next($request);
                }
            }

            session()->flash('flash.bannerStyle', 'danger');
            session()->flash('flash.banner', "You are not authorized to grade this (or the resource does not exist). Contact the office if you believe this is a mistake.");

            return redirect()->route('exams.index');
        }

        return redirect()->route('dashboard');
    }
}
