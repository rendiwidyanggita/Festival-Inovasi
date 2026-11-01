<?php

namespace App\Models;

use App\Enums\AssessmentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[\Illuminate\Database\Eloquent\Attributes\Fillable(['festival_id', 'assessment_type', 'name', 'weight', 'sort_order'])]
class AssessmentCriterion extends Model
{
    protected function casts(): array
    {
        return [
            'assessment_type' => AssessmentType::class,
        ];
    }

    /** @return BelongsTo<Festival, $this> */
    public function festival(): BelongsTo
    {
        return $this->belongsTo(Festival::class);
    }

    /** @return HasMany<InnovationAssessmentDetail, $this> */
    public function innovationAssessmentDetails(): HasMany
    {
        return $this->hasMany(InnovationAssessmentDetail::class, 'assessment_criterion_id');
    }

    /** @return HasMany<InstitutionAssessmentDetail, $this> */
    public function institutionAssessmentDetails(): HasMany
    {
        return $this->hasMany(InstitutionAssessmentDetail::class, 'assessment_criterion_id');
    }
}
