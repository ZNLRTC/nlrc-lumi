<?php

namespace App\Policies\Documents;

use App\Models\User;

class DocumentPolicy
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

    public function view(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager', 'Staff']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager']);
    }

    public function update(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager']);
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
