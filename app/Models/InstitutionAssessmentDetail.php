<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstitutionAssessmentDetail extends Model
{
    protected function casts(): array
    {
        return [
            'score' => 'integer',
        ];
    }

    /** @return BelongsTo<InstitutionAssessment, $this> */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(InstitutionAssessment::class, 'institution_assessment_id');
    }

    /** @return BelongsTo<AssessmentCriterion, $this> */
    public function criterion(): BelongsTo
    {
        return $this->belongsTo(AssessmentCriterion::class, 'assessment_criterion_id');
    }
}
