<?php

namespace Database\Factories;

use App\Enums\FestivalStatus;
use App\Models\Festival;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Festival>
 */
class FestivalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'year' => fake()->unique()->numberBetween(2020, 2030),
            'name' => 'Festival Inovasi ' . fake()->year(),
            'status' => FestivalStatus::Preparation,
        ];
    }

    /**
     * Indicate that the festival is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => FestivalStatus::Active,
        ]);
    }

    /**
     * Indicate that the festival is archived.
     */
    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => FestivalStatus::Archived,
        ]);
    }
}
