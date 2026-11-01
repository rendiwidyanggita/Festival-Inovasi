<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Festival;
use App\Models\User;

class FestivalPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSuperadmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Festival $festival): bool
    {
        return $user->isSuperadmin();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isSuperadmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Festival $festival): bool
    {
        return $user->isSuperadmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Festival $festival): bool
    {
        return $user->isSuperadmin();
    }
}
