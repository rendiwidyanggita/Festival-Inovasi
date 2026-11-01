<?php

namespace Database\Factories;

use App\Models\AwardCategory;
use App\Models\Innovation;
use App\Models\Institution;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Innovation>
 */
class InnovationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'award_category_id' => AwardCategory::factory(),
            'festival_id' => function (array $attributes) {
                if (isset($attributes['award_category_id'])) {
                    $awardCat = AwardCategory::find($attributes['award_category_id']);
                    if ($awardCat) {
                        return $awardCat->festival_id;
                    }
                }
                return \App\Models\Festival::factory();
            },
            'institution_id' => function (array $attributes) {
                $festivalId = $attributes['festival_id'] ?? null;
                $awardCat = isset($attributes['award_category_id']) ? AwardCategory::find($attributes['award_category_id']) : null;
                $actualFestivalId = $awardCat ? $awardCat->festival_id : $festivalId;
                
                return Institution::factory()->create([
                    'festival_id' => $actualFestivalId,
                ])->id;
            },
            'name' => fake()->sentence(4),
            'innovator_name' => fake()->name(),
        ];
    }
}
