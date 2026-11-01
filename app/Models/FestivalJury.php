<?php

namespace App\Models;

use App\Enums\FestivalJuryStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FestivalJury extends Model
{
    protected function casts(): array
    {
        return [
            'status' => FestivalJuryStatus::class,
        ];
    }

    /** @return BelongsTo<Festival, $this> */
    public function festival(): BelongsTo
    {
        return $this->belongsTo(Festival::class);
    }

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return HasMany<JurySubcategoryParticipation, $this> */
    public function participations(): HasMany
    {
        return $this->hasMany(JurySubcategoryParticipation::class);
    }
}
