<?php

namespace App\Policies\Exams;

use App\Models\Exams\ExamAttempt;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ExamAttemptPolicy
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
    public function view(User $user, ExamAttempt $examAttempt): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager', 'Staff', 'Instructor', 'Editing instructor']);
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
    public function update(User $user, ExamAttempt $examAttempt): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager', 'Staff', 'Instructor', 'Editing instructor']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ExamAttempt $examAttempt): bool
    {
        if ($user->hasAnyRole(['Admin', 'Manager', 'Staff'])) {
            return true;
        }
    
        // Instructors can only delete attempts they've graded
        if ($user->hasAnyRole(['Instructor', 'Editing instructor'])) {
            return $user->id === $examAttempt->instructor_id;
        }
    
        return false;
    }

    public function bulkDelete(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    // public function restore(User $user, ExamAttempt $examAttempt): bool
    // {
    //     //
    // }

    /**
     * Determine whether the user can permanently delete the model.
     */
    // public function forceDelete(User $user, ExamAttempt $examAttempt): bool
    // {
    //     //
    // }
}
