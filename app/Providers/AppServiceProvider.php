<?php

namespace App\Providers;

use App\Models\Exams\Exam;
use App\Models\Courses\Course;
use Illuminate\Support\Carbon;
use App\Policies\Exams\ExamPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Meetings\Assignments\AssignmentSubmission;
use App\Policies\Meetings\Assignments\AssignmentSubmissionPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // This fills the course list in the navigation menu for trainees
        View::composer('livewire.nav-menu', function ($view) {
            $user = auth()->user();
        
            if ($user && $user->hasRole('Trainee')) {
                $user->load('trainee.activeGroup.group.courses');
                $active_group = optional($user->trainee)->activeGroup;
        
                if ($active_group) {
                    $courses = Course::get_courses_of_trainee_if_group_is_active($active_group, ['id', 'name', 'slug']);
                    $view->with('courses', $courses);
                } else {
                    // This happens if the trainee has no active group
                    $view->with('courses', collect());
                }
            } else {
                $courses = Course::where('slug', '!=', 'kmh')->get();
                $view->with('courses', $courses);
            }
        });

        Gate::policy(AssignmentSubmission::class, AssignmentSubmissionPolicy::class);

        Carbon::macro('inApplicationTimezone', function () {
            return $this->tz(config('app.timezone_display'));
        });

        Carbon::macro('inUserTimezone', function () {
            return $this->tz(auth()->user()?->timezone ?? config('app.timezone_display'));
        });

        // Automatically discover policies in subfolders by pulling the policy path from the model's path
        Gate::guessPolicyNamesUsing(function (string $modelClass) {
            $modelPath = str_replace('App\\Models\\', '', $modelClass);
            $policyPath = 'App\\Policies\\' . $modelPath . 'Policy';

            return $policyPath;
        });
    }
}
