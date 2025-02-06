<?php

namespace App\Policies\KnowledgeBase;

use App\Models\User;
use App\Models\KnowledgeBase\Article;
use App\Enums\KnowledgeBase\ArticleStatus;

class ArticlePolicy
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

    public function view(User $user, Article $article): bool
    {
        $rolesWithFullAccess = ['Admin', 'Manager', 'Staff'];
    
        if (in_array($user->role->name, $rolesWithFullAccess)) {
            return true;
        }
    
        return $article->status === ArticleStatus::PUBLISHED && in_array($user->role->name, $article->audiences);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager', 'Staff', 'Editing Instructor']);
    }

    public function update(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager', 'Staff', 'Editing Instructor']);
    }

    public function delete(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager', 'Staff']);
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
