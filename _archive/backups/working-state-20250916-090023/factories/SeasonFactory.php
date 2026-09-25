<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Season>
 */
class SeasonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('+1 month', '+2 years');
        $endDate = $this->faker->dateTimeBetween($startDate, '+3 years');
        $registrationStartDate = $this->faker->dateTimeBetween('-6 months', $startDate);
        $registrationEndDate = $this->faker->dateTimeBetween($registrationStartDate, $startDate);

        return [
            'name' => $this->faker->unique()->year . '-' . ($this->faker->unique()->year + 1),
            'short_name' => $this->faker->unique()->regexify('[0-9]{2}-[0-9]{2}'),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $this->faker->randomElement(['upcoming', 'active', 'completed', 'archived']),
            'description' => $this->faker->sentence(),
            'registration_start_date' => $registrationStartDate,
            'registration_end_date' => $registrationEndDate,
            'is_current' => $this->faker->boolean(20),
            'created_by' => \App\Models\User::factory(),
            'settings' => [
                'max_teams' => $this->faker->numberBetween(8, 32),
                'min_teams' => $this->faker->numberBetween(4, 16),
                'entry_fee' => $this->faker->randomFloat(2, 0, 1000),
                'prize_pool' => $this->faker->randomFloat(2, 0, 10000),
            ],
        ];
    }

    /**
     * Indicate that the season is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'start_date' => now()->subMonths(2),
            'end_date' => now()->addMonths(4),
        ]);
    }

    /**
     * Indicate that the season is upcoming.
     */
    public function upcoming(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'upcoming',
            'start_date' => now()->addMonths(1),
            'end_date' => now()->addMonths(6),
        ]);
    }

    /**
     * Indicate that the season is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'start_date' => now()->subMonths(8),
            'end_date' => now()->subMonths(2),
        ]);
    }

    /**
     * Indicate that the season is archived.
     */
    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'archived',
        ]);
    }
}
