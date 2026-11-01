<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[\Illuminate\Database\Eloquent\Attributes\Fillable(['festival_id', 'award_category_id', 'innovation_id', 'position', 'appointed_by', 'appointed_at', 'total_score', 'notes'])]
class InnovationWinner extends Model
{
    protected function casts(): array
    {
        return [
            'appointed_at' => 'datetime',
            'total_score' => 'decimal:2',
        ];
    }
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
