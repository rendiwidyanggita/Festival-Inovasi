<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[\Illuminate\Database\Eloquent\Attributes\Fillable(['festival_id', 'award_category_id', 'innovation_id', 'position', 'appointed_by', 'appointed_at', 'notes'])]
class InnovationWinner extends Model
{
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

    /** @return BelongsTo<Innovation, $this> */
    public function innovation(): BelongsTo
    {
        return $this->belongsTo(Innovation::class);
    }

    /** @return BelongsTo<User, $this> */
    public function appointedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'appointed_by');
    }
}
