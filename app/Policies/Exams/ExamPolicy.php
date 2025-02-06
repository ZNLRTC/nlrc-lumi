<?php

namespace App\Policies\Exams;

use App\Models\Exams\Exam;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ExamPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager', 'Staff', 'Instructor', 'Editing instructor']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Exam $exam): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager', 'Staff', 'Instructor', 'Editing instructor']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager', 'Staff']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Exam $exam): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager', 'Staff', 'Instructor', 'Editing instructor']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Exam $exam): bool
    {
        // Only empty unused exams can be deleted
        if (!$user->hasAnyRole(['Admin', 'Manager', 'Staff'])) {
            return false;
        }

        if ($exam->trainees()->exists()) {
            return false;
        }

        if ($exam->attempts()->exists()) {
            return false;
        }
    
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    // public function restore(User $user, Exam $exam): bool
    // {
    //     return true;
    // }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Exam $exam): bool
    {
        return false;
        // return $user->hasAnyRole(['Admin', 'Manager', 'Staff']);
    }
}
