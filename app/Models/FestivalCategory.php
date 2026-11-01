<?php

namespace App\Models;

use App\Enums\AssessmentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FestivalCategory extends Model
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

    /** @return HasMany<AwardCategory, $this> */
    public function awardCategories(): HasMany
    {
        return $this->hasMany(AwardCategory::class)->orderBy('sort_order');
    }
}
