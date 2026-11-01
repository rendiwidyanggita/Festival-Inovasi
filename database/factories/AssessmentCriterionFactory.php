<?php

namespace Database\Factories;

use App\Enums\AssessmentType;
use App\Models\AssessmentCriterion;
use App\Models\Festival;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AssessmentCriterion>
 */
class AssessmentCriterionFactory extends Factory
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
            'assessment_type' => AssessmentType::Innovation,
            'name' => fake()->sentence(3),
            'weight' => 20.00,
            'sort_order' => fake()->numberBetween(1, 10),
        ];
    }

    public function innovation(): static
    {
        return $this->state(fn (array $attributes) => [
            'assessment_type' => AssessmentType::Innovation,
        ]);
    }

    public function institution(): static
    {
        return $this->state(fn (array $attributes) => [
            'assessment_type' => AssessmentType::Institution,
        ]);
    }
}
