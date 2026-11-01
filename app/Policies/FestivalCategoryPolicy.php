<?php

namespace App\Policies;

use App\Models\FestivalCategory;
use App\Models\User;

class FestivalCategoryPolicy
{
    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FestivalCategory $festivalCategory): bool
    {
        // Cannot delete if has award categories (subcategories)
        if ($festivalCategory->awardCategories()->exists()) {
            return false;
        }

        return $user->isSuperadmin();
    }
}
