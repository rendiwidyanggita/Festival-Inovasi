<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Institution;
use App\Models\User;

class InstitutionPolicy
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
    public function view(User $user, Institution $institution): bool
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
    public function update(User $user, Institution $institution): bool
    {
        return $user->isSuperadmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Institution $institution): bool
    {
        if (!$user->isSuperadmin()) {
            return false;
        }

        // Can only delete if subcategory is still in preparation
        return !$institution->awardCategory->isRosterLocked();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Institution $institution): bool
    {
        if (!$user->isSuperadmin()) {
            return false;
        }

        // Can only restore if subcategory is still in preparation
        return !$institution->awardCategory->isRosterLocked();
    }
}
