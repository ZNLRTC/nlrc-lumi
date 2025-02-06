<?php

namespace App\Policies;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AnnouncementPolicy
{
    // SAMPLE USAGE IN CONTROLLERS
    // if ($user->cannot('view', $current_announcement)) {
    //     abort(403);
    // }

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager', 'Staff']);
    }

    public function view(?User $user, Announcement $announcement): bool
    {
        if ($user->trainee) {
            $trainee_has_announcement = $user->trainee->notifications()
                ->where('type', 'announcement-sent')
                ->where('data->announcement_id', $announcement->id)
                ->first();

            return $trainee_has_announcement ? true : false;
        } else {
            return $user->hasAnyRole(['Admin', 'Manager', 'Staff']);
        }
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager', 'Staff']);
    }

    public function update(User $user, Announcement $announcement): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager', 'Staff']);
    }

    public function delete(User $user, Announcement $announcement): bool
    {
        return $user->hasAnyRole(['Admin', 'Manager', 'Staff']);
    }

    public function restore(User $user, Announcement $announcement): bool
    {
        return $user->id === $announcement->user_id;
    }

    public function forceDelete(User $user, Announcement $announcement): bool
    {
        return $user->id === $announcement->user_id;
    }
}
