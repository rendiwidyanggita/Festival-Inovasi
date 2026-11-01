<?php

namespace App\Models;

use App\Enums\FestivalStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[\Illuminate\Database\Eloquent\Attributes\Fillable(['year', 'name', 'status', 'results_published_at', 'results_published_by'])]
class Festival extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => FestivalStatus::class,
            'results_published_at' => 'datetime',
        ];
    }

    /** @return HasOne<FestivalSetting, $this> */
    public function settings(): HasOne
    {
        return $this->hasOne(FestivalSetting::class);
    }

    /** @return HasMany<FestivalCategory, $this> */
    public function festivalCategories(): HasMany
    {
        return $this->hasMany(FestivalCategory::class);
    }
    
    /** Alias for festivalCategories */
    public function categories()
    {
        return $this->festivalCategories();
    }

    /** @return HasMany<AwardCategory, $this> */
    public function awardCategories(): HasMany
    {
        return $this->hasMany(AwardCategory::class);
    }

    /** @return HasMany<FestivalJury, $this> */
    public function festivalJuries(): HasMany
    {
        return $this->hasMany(FestivalJury::class);
    }

    /** @return HasMany<JurySubcategoryParticipation, $this> */
    public function participations(): HasMany
    {
        return $this->hasMany(JurySubcategoryParticipation::class);
    }

    /** @return HasMany<AssessmentCriterion, $this> */
    public function assessmentCriteria(): HasMany
    {
        return $this->hasMany(AssessmentCriterion::class);
    }
    
    /** Alias for assessmentCriteria */
    public function criteria()
    {
        return $this->assessmentCriteria();
    }

    /** @return HasMany<Institution, $this> */
    public function institutions(): HasMany
    {
        return $this->hasMany(Institution::class);
    }

    /** @return HasMany<Innovation, $this> */
    public function innovations(): HasMany
    {
        return $this->hasMany(Innovation::class);
    }

    /** @return BelongsTo<User, $this> */
    public function publishedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'results_published_by');
    }

    /** Publikasi hasil sudah dilakukan atau belum. */
    public function hasPublishedResults(): bool
    {
        return $this->results_published_at !== null;
    }
}
