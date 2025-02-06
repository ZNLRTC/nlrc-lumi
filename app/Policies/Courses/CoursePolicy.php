<?php

namespace App\Policies\Courses;

use App\Models\Courses\Course;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CoursePolicy
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
        return $user->hasAnyRole(['Admin', 'Manager', 'Staff']);
    }

    public function view(User $user, Course $course): bool
    {
        return $user->hasAnyRole(['Admin', 'Staff', 'Manager', 'Editing instructor']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Staff', 'Manager', 'Editing instructor']);
    }

    public function update(User $user, Course $course): bool
    {
        return $user->hasAnyRole(['Admin', 'Staff', 'Manager', 'Editing instructor']);
    }

    public function delete(User $user, Course $course): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager']);
    }

    public function restore(User $user, Course $course): bool
    {
        return $user->hasRole('Admin');
    }

    public function forceDelete(User $user, Course $course): bool
    {
        return $user->hasRole('Admin');
    }
}
