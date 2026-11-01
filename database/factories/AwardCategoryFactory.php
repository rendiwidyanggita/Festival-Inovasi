<?php

namespace Database\Factories;

use App\Enums\ScoringStatus;
use App\Models\AwardCategory;
use App\Models\Festival;
use App\Models\FestivalCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AwardCategory>
 */
class AwardCategoryFactory extends Factory
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
            'festival_category_id' => FestivalCategory::factory(),
            'name' => fake()->words(3, true),
            'winner_count' => fake()->numberBetween(1, 5),
            'scoring_status' => ScoringStatus::Preparation,
            'sort_order' => fake()->numberBetween(1, 10),
        ];
    }

    /**
     * Indicate the scoring is open.
     */
    public function open(): static
    {
        return $this->state(fn (array $attributes) => [
            'scoring_status' => ScoringStatus::Open,
        ]);
    }

    /**
     * Indicate the scoring is locked.
     */
    public function locked(): static
    {
        return $this->state(fn (array $attributes) => [
            'scoring_status' => ScoringStatus::Locked,
        ]);
    }
}
