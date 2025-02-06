<?php

namespace App\Policies;

use App\Models\Trainee;
use App\Models\User;

class TraineePolicy
{
    public function viewAny(User $user): bool
    {
        return !$user->hasRole('Trainee');
    }

    public function view(User $user, Trainee $trainee): bool
    {
        return $user->id === $trainee->user_id || !$user->hasRole('Trainee');
    }

    public function viewWidgetStats(?User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager', 'Staff']);
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Trainee $trainee): bool
    {
        return $user->id === $trainee->user_id || $user->hasAnyRole(['Admin', 'Manager', 'Staff']);
    }

    public function delete(User $user, Trainee $trainee): bool
    {
        return false;
    }

    public function restore(User $user, Trainee $trainee): bool
    {
        return $user->hasRole('Admin');
    }

    public function forceDelete(User $user, Trainee $trainee): bool
    {
        return false;
    }
}
