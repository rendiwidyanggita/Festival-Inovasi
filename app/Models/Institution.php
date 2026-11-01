<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;


#[\Illuminate\Database\Eloquent\Attributes\Fillable(['festival_id', 'award_category_id', 'name'])]
class Institution extends Model
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

    /** @return HasMany<Innovation, $this> */
    public function innovations(): HasMany
    {
        return $this->hasMany(Innovation::class);
    }
}
