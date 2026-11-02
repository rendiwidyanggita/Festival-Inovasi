<?php

namespace App\Services;

use App\Enums\AssessmentStatus;
use App\Enums\AssessmentType;
use App\Models\AwardCategory;
use App\Models\Innovation;
use App\Models\InnovationAssessment;
use App\Models\Institution;
use App\Models\InstitutionAssessment;
use Illuminate\Support\Collection;

class RankingService
{
    /**
     * Get rankings for an award category.
     * Returns collection of targets with their average scores and ranks.
     *
     * @return Collection<int, object>
     */
    public function getRankings(AwardCategory $category): Collection
    {
        $isInnovation = $category->assessment_type === AssessmentType::Innovation;
        
        $targets = $isInnovation
            ? $category->innovations()->with(['institution'])->get()
            : $category->institutions()->get();

        $rankings = $targets->map(function ($target) use ($category, $isInnovation) {
            $averageScore = $this->calculateAverageScore($target, $category);
            
            return (object) [
                'id' => $target->id,
                'name' => $target->name,
                'target' => $target,
                'target_type' => $isInnovation ? 'innovation' : 'institution',
                'average_score' => $averageScore,
                'institution_name' => $isInnovation ? ($target->institution->name ?? '-') : $target->name,
                'innovator_name' => $isInnovation ? ($target->innovator_name ?? '-') : null,
            ];
        });

        // Sort by average score DESC and assign ranks.
        // Skor sama mendapat peringkat seri (competition ranking: 1, 1, 3).
        $sorted = $rankings->sortByDesc('average_score')->values();

        $lastScore = null;
        $lastRank = 0;
        return $sorted->map(function ($item, $index) use (&$lastScore, &$lastRank) {
            $item->rank = ($lastScore !== null && (float) $item->average_score === $lastScore)
                ? $lastRank
                : $index + 1;
            $lastScore = (float) $item->average_score;
            $lastRank = $item->rank;
            return $item;
        });
    }

    /**
     * Calculate average score for a target (innovation or institution).
     * Only considers locked assessments.
     */
    public function calculateAverageScore($target, AwardCategory $category): float
    {
        $isInnovation = $target instanceof Innovation;
        
        $assessmentClass = $isInnovation ? InnovationAssessment::class : InstitutionAssessment::class;
        $foreignKey = $isInnovation ? 'innovation_id' : 'institution_id';

        $assessments = $assessmentClass::where('award_category_id', $category->id)
            ->where($foreignKey, $target->id)
            ->where('status', AssessmentStatus::Locked)
            ->with('details.criterion')
            ->get();

        if ($assessments->isEmpty()) {
            return 0.0;
        }

        $weightedTotals = $assessments->map(fn ($assessment) => $assessment->weightedTotal());

        return (float) $weightedTotals->average();
    }
}
