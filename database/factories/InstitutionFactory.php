<?php

namespace Database\Factories;

use App\Models\AwardCategory;
use App\Models\Institution;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Institution>
 */
class InstitutionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'award_category_id' => function (array $attributes) {
                $festivalId = $attributes['festival_id'] ?? null;
                if ($festivalId) {
                    $category = \App\Models\FestivalCategory::firstOrCreate([
                        'festival_id' => $festivalId,
                        'assessment_type' => \App\Enums\AssessmentType::Institution,
                    ], [
                        'name' => 'Kategori Instansi ' . fake()->unique()->word(),
                    ]);

                    return AwardCategory::factory()->create([
                        'festival_id' => $festivalId,
                        'festival_category_id' => $category->id,
                    ])->id;
                }

                return AwardCategory::factory();
            },
            'festival_id' => function (array $attributes) {
                if (isset($attributes['award_category_id'])) {
                    $awardCat = AwardCategory::find($attributes['award_category_id']);
                    if ($awardCat) {
                        return $awardCat->festival_id;
                    }
                }
                return \App\Models\Festival::factory();
            },
            'name' => fake()->company(),
        ];
    }
}
