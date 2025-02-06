<?php

namespace App\Policies\Meetings\Assignments;

use App\Enums\Assignments\SubmissionStatus;
use App\Models\Meetings\Assignments\AssignmentSubmission;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AssignmentSubmissionPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return !$user->hasRole('Trainee');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AssignmentSubmission $assignmentSubmission): bool
    {
        return $user->trainee->id === $assignmentSubmission->trainee_id || !$user->hasRole('Trainee');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Admin','Staff','Trainee']);
        // return true;
    }

    /**
     * Determine whether the user can update the model. Trainees can only update their own submissions, instructors can edit those with null or their own id in the instructor field.
     */
    public function update(User $user, AssignmentSubmission $assignmentSubmission): bool
    {
        $isAdminOrStaff = $user->hasAnyRole(['Admin', 'Manager', 'Staff']);

        $isInstructorOwner = $user->hasAnyRole(['Instructor','Editing instructor']) && (is_null($assignmentSubmission->instructor_id) || $assignmentSubmission->instructor_id == $user->id);

        $isTraineeOwnerAndNotChecked = $user->hasRole('Trainee') && $user->trainee->id === $assignmentSubmission->trainee_id &&  $assignmentSubmission->submission_status == SubmissionStatus::NOT_CHECKED;
    
        return $isAdminOrStaff || $isInstructorOwner || $isTraineeOwnerAndNotChecked;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AssignmentSubmission $assignmentSubmission): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager', 'Staff']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, AssignmentSubmission $assignmentSubmission): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager', 'Staff']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, AssignmentSubmission $assignmentSubmission): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager', 'Staff']);
    }
}
