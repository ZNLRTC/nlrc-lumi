<?php

namespace App\Policies\KnowledgeBase;

use App\Models\User;
use App\Models\KnowledgeBase\Category;

class CategoryPolicy
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
        return !$user->hasRole('Observer');
    }

    public function view(User $user): bool
    {
        return !$user->hasRole('Observer');
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager', 'Staff', 'Editing Instructor']);
    }

    public function update(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager', 'Staff', 'Editing Instructor']);
    }

    public function delete(User $user, Category $category): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager', 'Staff']) && $category->articles()->doesntExist();
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
