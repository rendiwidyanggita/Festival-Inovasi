<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InnovationAssessmentDetail extends Model
{
    protected function casts(): array
    {
        return [
            'score' => 'integer',
        ];
    }

    /** @return BelongsTo<InnovationAssessment, $this> */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(InnovationAssessment::class, 'innovation_assessment_id');
    }

    /** @return BelongsTo<AssessmentCriterion, $this> */
    public function criterion(): BelongsTo
    {
        return $this->belongsTo(AssessmentCriterion::class, 'assessment_criterion_id');
    }
}
