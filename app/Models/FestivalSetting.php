<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[\Illuminate\Database\Eloquent\Attributes\Fillable(['festival_id', 'organizer_name', 'organizer_logo_path', 'report_title', 'report_footer'])]
class FestivalSetting extends Model
{
    /** @return BelongsTo<Festival, $this> */
    public function festival(): BelongsTo
    {
        return $this->belongsTo(Festival::class);
    }
}
