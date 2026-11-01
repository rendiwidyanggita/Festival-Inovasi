<?php

namespace App\Models;

use App\Enums\ParticipationStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JurySubcategoryParticipation extends Model
{
    protected function casts(): array
    {
        return [
            'status' => ParticipationStatus::class,
            'selected_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<Festival, $this> */
    public function festival(): BelongsTo
    {
        return $this->belongsTo(Festival::class);
    }

    /** @return BelongsTo<FestivalJury, $this> */
    public function festivalJury(): BelongsTo
    {
        return $this->belongsTo(FestivalJury::class);
    }

    /** @return BelongsTo<AwardCategory, $this> */
    public function awardCategory(): BelongsTo
    {
        return $this->belongsTo(AwardCategory::class);
    }

    /** @return HasMany<InnovationAssessment, $this> */
    public function innovationAssessments(): HasMany
    {
        return $this->hasMany(InnovationAssessment::class);
    }

    /** @return HasMany<InstitutionAssessment, $this> */
    public function institutionAssessments(): HasMany
    {
        return $this->hasMany(InstitutionAssessment::class);
    }
}
