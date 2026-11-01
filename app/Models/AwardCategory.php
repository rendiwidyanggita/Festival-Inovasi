<?php

namespace App\Models;

use App\Enums\ScoringStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AwardCategory extends Model
{
    protected function casts(): array
    {
        return [
            'scoring_status' => ScoringStatus::class,
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    /** @return BelongsTo<FestivalCategory, $this> */
    public function festivalCategory(): BelongsTo
    {
        return $this->belongsTo(FestivalCategory::class);
    }

    /** @return BelongsTo<Festival, $this> */
    public function festival(): BelongsTo
    {
        return $this->belongsTo(Festival::class);
    }

    /** @return HasMany<Institution, $this> */
    public function institutions(): HasMany
    {
        return $this->hasMany(Institution::class)->orderBy('name');
    }

    /** @return HasMany<Innovation, $this> */
    public function innovations(): HasMany
    {
        return $this->hasMany(Innovation::class)->orderBy('name');
    }

    /** @return HasMany<JurySubcategoryParticipation, $this> */
    public function participations(): HasMany
    {
        return $this->hasMany(JurySubcategoryParticipation::class);
    }

    /** @return HasMany<InnovationWinner, $this> */
    public function innovationWinners(): HasMany
    {
        return $this->hasMany(InnovationWinner::class)->orderBy('position');
    }

    /** @return HasMany<InstitutionWinner, $this> */
    public function institutionWinners(): HasMany
    {
        return $this->hasMany(InstitutionWinner::class)->orderBy('position');
    }

    /** @return BelongsTo<User, $this> */
    public function openedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    /** @return BelongsTo<User, $this> */
    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    /** Roster juri sudah terkunci setelah subkategori dibuka. */
    public function isRosterLocked(): bool
    {
        return $this->scoring_status !== ScoringStatus::Preparation;
    }
}
