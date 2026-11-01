<?php

namespace App\Policies;

use App\Models\AssessmentCriterion;
use App\Models\User;

class AssessmentCriterionPolicy
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
    public function view(User $user, AssessmentCriterion $assessmentCriterion): bool
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
    public function update(User $user, AssessmentCriterion $assessmentCriterion): bool
    {
        if (!$user->isSuperadmin()) {
            return false;
        }

        // Can only update if no assessment details exist
        return !$this->hasAssessmentDetails($assessmentCriterion);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AssessmentCriterion $assessmentCriterion): bool
    {
        if (!$user->isSuperadmin()) {
            return false;
        }

        // Can only delete if no assessment details exist
        return !$this->hasAssessmentDetails($assessmentCriterion);
    }

    /**
     * Check if the criterion has any assessment details.
     */
    private function hasAssessmentDetails(AssessmentCriterion $criterion): bool
    {
        return $criterion->innovationAssessmentDetails()->exists()
            || $criterion->institutionAssessmentDetails()->exists();
    }
}
