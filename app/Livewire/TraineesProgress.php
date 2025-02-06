<?php

namespace App\Livewire;

use App\Enums\DocumentTraineesStatus;
use App\Filament\Admin\Resources\TraineeResource;
use App\Models\Courses\Course;
use App\Models\Courses\Unit;
use App\Models\Documents\DocumentTrainee;
use App\Models\Trainee;
use App\Models\Exams\Proficiency;
use App\Models\Exams\ProficiencyTrainee;
use App\Models\Meetings\Meeting;
use App\Models\Meetings\MeetingTrainee;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TraineesProgress extends Component
{
    public $trainee_id = 0;
    public $courses;
    public $courses_completion;
    public $language_proficiencies = [];
    public $document_completion = 0;
    public $profile_completion = 0;

    public function render()
    {
        $auth_user_is_trainee = Auth::user()->hasRole('Trainee');
        if ($auth_user_is_trainee) {
            $trainee = Auth::user()->trainee;
            $this->trainee_id = $trainee->id;
        } else if (!$auth_user_is_trainee && 
            (url()->current() == TraineeResource::getUrl('progress', ['record' => $this->trainee_id]))
        ) { // Admin panel
            $trainee = Trainee::find($this->trainee_id);
        }

        if ($trainee) {
            // Courses
            $active_group = $trainee->activeGroup;
            if ($active_group) {
                $this->courses = Course::get_courses_of_trainee_if_group_is_active($active_group, ['id', 'name']);
            }

            // NOTE: Currently applies to users not in beginner's course
            // Though we may still need to add units to the beginner's course?
            if ($this->courses->isNotEmpty()) {
                foreach ($this->courses as $idx => $course) {
                    $units_completed = 0;
                    $all_meeting_ids = [];

                    $units_on_course = Unit::select('id')->where('course_id', $course['id'])
                        ->get()
                        ->pluck('id');

                    foreach ($units_on_course as $unit_id) {
                        $meetings = Meeting::select('id')->where('unit_id', $unit_id)
                            ->get();
                        $meeting_ids_for_unit = array_column($meetings->toArray(), 'id');

                        array_push($all_meeting_ids, $meeting_ids_for_unit);
                    }

                    foreach ($all_meeting_ids as $meeting_ids_as_array) {
                        $has_completed_attempts_for_meeting = MeetingTrainee::select('meeting_id')->where('trainee_id', $trainee->id)
                            ->where('meeting_status_id', 1)
                            ->whereIn('meeting_id', $meeting_ids_as_array)
                            ->get()
                            ->isNotEmpty();

                        if ($has_completed_attempts_for_meeting) {
                            $units_completed++;
                        }
                    }

                    $course_as_array = $this->courses[$idx];
                    $course_as_array['units_completed'] = $units_completed;
                    $course_as_array['units_count'] = $units_on_course->count();
                    $course_as_array['course_completion'] = round(($units_completed / $units_on_course->count()) * 100, 2);
                    $this->courses[$idx] = $course_as_array;
                }

                $sum_course_completion = array_reduce($this->courses->toArray(), function($completion, $course) {
                    $completion += $course['course_completion'];

                    return $completion;
                });

                $this->courses_completion = round(($sum_course_completion / (100 * $this->courses->count())) * 100, 2);
            }

            // Language Proficiencies
            $proficiencies = Proficiency::all();

            foreach ($proficiencies as $language_proficiency) {
                $proficiency_trainee = ProficiencyTrainee::select('is_proficient')
                    ->where('proficiency_id', $language_proficiency['id'])
                    ->where('trainee_id', $trainee->id)
                    ->first();

                array_push($this->language_proficiencies, [
                    'proficiency' => $language_proficiency['name'],
                    'description' => $language_proficiency['description'],
                    'is_proficient' => $proficiency_trainee ? 1 : 0
                ]);
            }

            $required_documents_for_agency_count = Trainee::get_required_documents_of_agency_for_trainee_count($trainee->id);

            $completed_documents_count = DocumentTrainee::select('document_trainees.document_id')
                ->where('trainee_id', $trainee->id)
                ->where('status', DocumentTraineesStatus::APPROVED)
                ->count();

            if ($completed_documents_count > 0) {
                $this->document_completion = round(($completed_documents_count / $required_documents_for_agency_count) * 100);
            }

            $trainee_verification_requests = $trainee->verified_requests;

            if ($trainee_verification_requests->isNotEmpty()) {
                // Get the most recent verification request
                $this->profile_completion = $trainee_verification_requests->last()->is_verified == 1 ? 100 : 50;
            }

            return view('livewire.progress.trainees-progress');
        }
    }
}
