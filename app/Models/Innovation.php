<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;


#[\Illuminate\Database\Eloquent\Attributes\Fillable(['festival_id', 'award_category_id', 'institution_id', 'name', 'innovator_name'])]
class Innovation extends Model
{
    use HasFactory, SoftDeletes;


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

    /** @return BelongsTo<Institution, $this> */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    /** @return HasMany<InnovationAssessment, $this> */
    public function innovationAssessments(): HasMany
    {
        return $this->hasMany(InnovationAssessment::class);
    }
}
