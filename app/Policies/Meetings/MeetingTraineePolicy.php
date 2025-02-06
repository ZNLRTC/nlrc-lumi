<?php

namespace App\Policies\Meetings;

use App\Models\User;
use App\Models\Meetings\MeetingTrainee;
use Illuminate\Auth\Access\Response;

class MeetingTraineePolicy
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
    public function view(User $user, MeetingTrainee $meetingTrainee): bool
    {
        return $user->id === $meetingTrainee->trainee->user_id || !$user->hasRole('Trainee');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager', 'Staff', 'Instructor', 'Editing instructor']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager', 'Staff', 'Instructor', 'Editing instructor']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MeetingTrainee $meeting): bool
    {
        // Instructors can delete their own stuff and staff and admins can delete everything
        return $user->hasAnyRole(['Instructor', 'Editing instructor']) && $meeting->instructor_id == $user->id ||
            $user->hasAnyRole(['Admin', 'Manager', 'Staff']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MeetingTrainee $meetingTrainee): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager', 'Staff']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, MeetingTrainee $meetingTrainee): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager', 'Staff', 'Instructor', 'Editing instructor']);
    }
}
