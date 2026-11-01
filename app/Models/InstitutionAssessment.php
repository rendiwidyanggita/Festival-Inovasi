<?php

namespace App\Models;

use App\Enums\AssessmentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[\Illuminate\Database\Eloquent\Attributes\Fillable(['festival_id', 'award_category_id', 'jury_subcategory_participation_id', 'institution_id', 'status', 'submitted_at', 'locked_at'])]
class InstitutionAssessment extends Model
{
    protected function casts(): array
    {
        return [
            'status' => AssessmentStatus::class,
            'submitted_at' => 'datetime',
            'locked_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Festival, $this> */
    public function festival(): BelongsTo
    {
        return $this->belongsTo(Festival::class);
    }

    /** @return BelongsTo<AwardCategory, $this> */
    public function awardCategory(): BelongsTo
    {
        return $this->belongsTo(AwardCategory::class);
    }

    /** @return BelongsTo<JurySubcategoryParticipation, $this> */
    public function participation(): BelongsTo
    {
        return $this->belongsTo(JurySubcategoryParticipation::class, 'jury_subcategory_participation_id');
    }

    /** @return BelongsTo<Institution, $this> */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    /** @return HasMany<InstitutionAssessmentDetail, $this> */
    public function details(): HasMany
    {
        return $this->hasMany(InstitutionAssessmentDetail::class);
    }

    /** Total tertimbang juri: SUM(score * weight) / 100. */
    public function weightedTotal(): float
    {
        return (float) $this->details
            ->sum(fn (InstitutionAssessmentDetail $detail) => $detail->score * $detail->criterion->weight) / 100;
    }
}
