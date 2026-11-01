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
            'institution_id' => Institution::factory(),
            'name' => fake()->sentence(4),
            'innovator_name' => fake()->name(),
        ];
    }
}
