<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Innovation;
use App\Models\User;

class InnovationPolicy
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
    public function view(User $user, Innovation $innovation): bool
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
    public function update(User $user, Innovation $innovation): bool
    {
        return $user->isSuperadmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Innovation $innovation): bool
    {
        if (!$user->isSuperadmin()) {
            return false;
        }

        // Can only delete if subcategory is still in preparation
        return !$innovation->awardCategory->isRosterLocked();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Innovation $innovation): bool
    {
        if (!$user->isSuperadmin()) {
            return false;
        }

        // Can only restore if subcategory is still in preparation
        return !$innovation->awardCategory->isRosterLocked();
    }
}
