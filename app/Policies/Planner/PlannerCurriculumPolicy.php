<?php

namespace App\Policies\Planner;

use App\Models\Planner\PlannerCurriculum;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PlannerCurriculumPolicy
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
    public function view(User $user, PlannerCurriculum $plannerCurriculum): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PlannerCurriculum $plannerCurriculum): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PlannerCurriculum $plannerCurriculum): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PlannerCurriculum $plannerCurriculum): bool
    {
        return $user->hasRole('Admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PlannerCurriculum $plannerCurriculum): bool
    {
        return $user->hasRole('Admin');
    }
}
