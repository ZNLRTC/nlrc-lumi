<?php

namespace App\Policies\Meetings;

use App\Models\User;
use Illuminate\Support\Carbon;
use App\Models\Meetings\Meeting;
use Illuminate\Auth\Access\Response;

class MeetingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Meeting $meeting): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager', 'Staff', 'Editing instructor']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Meeting $meeting): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager', 'Staff', 'Editing instructor']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Meeting $meeting): bool
    {
        // Cannot be deleted if the unit is used in the schedule planner
        if ($meeting->curriculumContents()->exists()) {
            return false;
        }

        if ($user->hasAnyRole(['Admin', 'Manager'])) {
            return true;
        }

        // Staff can delete right after making a mistake
        if ($user->hasRole('Staff') && $meeting->created_at->lt(Carbon::now()->subMinutes(5))) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Meeting $meeting): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Meeting $meeting): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager']);
    }
}
