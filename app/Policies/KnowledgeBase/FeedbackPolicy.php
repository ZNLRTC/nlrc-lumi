<?php

namespace App\Policies\KnowledgeBase;

use App\Models\KnowledgeBase\Feedback;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class FeedbackPolicy
{
    // Check if the same user has left feedback for the same article twice
    private function hasLeftFeedbackTwice(User $user, $articleId): bool
    {
        return Feedback::where('user_id', $user->id)
            ->where('article_id', $articleId)
            ->count() >= 2;
    }

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
    public function view(User $user, Feedback $feedback): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Feedback $feedback = null): bool
    {
        if ($user->hasAnyRole(['Admin', 'Manager', 'Staff'])) {
            return true;
        }

        if ($feedback) {
            return $user->hasAnyRole(['Trainee', 'Instructor']) && !$this->hasLeftFeedbackTwice($user, $feedback->article_id);
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager', 'Staff']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager', 'Staff']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Feedback $feedback): bool
    {
        return $user->hasRole('Admin');
    }
}
