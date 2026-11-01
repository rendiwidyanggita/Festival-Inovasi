<?php

namespace App\Policies;

use App\Models\AwardCategory;
use App\Models\User;

class AwardCategoryPolicy
{
    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AwardCategory $awardCategory): bool
    {
        // Cannot delete if scoring is not in preparation
        if ($awardCategory->scoring_status->value !== 'preparation') {
            return false;
        }

        // Cannot delete if has participants
        if ($awardCategory->institutions()->exists() || $awardCategory->innovations()->exists() || $awardCategory->participations()->exists()) {
            return false;
        }

        return $user->isSuperadmin();
    }
}
