<?php

namespace Database\Factories;

use App\Enums\AssessmentType;
use App\Models\Festival;
use App\Models\FestivalCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FestivalCategory>
 */
class FestivalCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'festival_id' => Festival::factory(),
            'name' => fake()->words(3, true),
            'assessment_type' => fake()->randomElement([AssessmentType::Innovation, AssessmentType::Institution]),
            'sort_order' => fake()->numberBetween(1, 10),
        ];
    }

    /**
     * Indicate category is for innovations.
     */
    public function innovation(): static
    {
        return $this->state(fn (array $attributes) => [
            'assessment_type' => AssessmentType::Innovation,
        ]);
    }

    /**
     * Indicate category is for institutions.
     */
    public function institution(): static
    {
        return $this->state(fn (array $attributes) => [
            'assessment_type' => AssessmentType::Institution,
        ]);
    }
}
