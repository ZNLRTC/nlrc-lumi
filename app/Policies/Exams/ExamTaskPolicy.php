<?php

namespace App\Policies\Exams;

use App\Models\User;

class ExamTaskPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager', 'Staff', 'Instructor', 'Editing Instructor', 'Trainee']);
    }

    public function view(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager', 'Staff', 'Instructor', 'Editing Instructor', 'Trainee']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager', 'Staff', 'Instructor', 'Editing Instructor']);
    }

    public function update(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager', 'Staff', 'Instructor', 'Editing Instructor']);
    }

    public function delete(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager']);
    }

    public function restore(User $user): bool
    {
        return $user->hasRole('Admin');
    }

    public function forceDelete(User $user): bool
    {
        return $user->hasRole('Admin');
    }
}
