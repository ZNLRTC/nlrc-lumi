<?php

namespace App\Policies\Courses;

use App\Models\Courses\Unit;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class UnitPolicy
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

    public function view(User $user, Unit $unit): bool
    {
        return $user->hasAnyRole(['Admin', 'Staff', 'Manager', 'Editing instructor']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Staff', 'Manager', 'Editing instructor']);
    }

    public function update(User $user, Unit $unit): bool
    {
        return $user->hasAnyRole(['Admin', 'Staff', 'Manager', 'Editing instructor']);
    }

    public function delete(User $user, Unit $unit): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager']);
    }

    public function restore(User $user, Unit $unit): bool
    {
        return $user->hasRole('Admin');
    }

    public function forceDelete(User $user, Unit $unit): bool
    {
        return $user->hasRole('Admin');
    }
}
