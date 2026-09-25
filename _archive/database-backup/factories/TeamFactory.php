<?php

namespace Database\Factories;

use App\Models\Club;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Team>
 */
class TeamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'type' => fake()->randomElement(['first_team', 'reserve', 'youth', 'academy']),
            'formation' => fake()->optional()->randomElement(['4-4-2', '4-3-3', '3-5-2', '4-2-3-1']),
            'tactical_style' => fake()->optional()->randomElement(['possession', 'counter-attack', 'pressing']),
            'playing_philosophy' => fake()->optional()->sentence(),
            'coach_name' => fake()->optional()->name(),
            'assistant_coach' => fake()->optional()->name(),
            'fitness_coach' => fake()->optional()->name(),
            'goalkeeper_coach' => fake()->optional()->name(),
            'scout' => fake()->optional()->name(),
            'medical_staff' => fake()->optional()->name(),
            'status' => fake()->randomElement(['active', 'inactive']),
            'season' => fake()->year(),
            'competition_level' => fake()->optional()->randomElement(['national', 'regional', 'local']),
            'budget_allocation' => fake()->optional()->numberBetween(10000, 1000000),
            'training_facility' => fake()->optional()->company() . ' Training Center',
            'home_ground' => fake()->optional()->company() . ' Stadium',
            'logo_url' => fake()->optional()->imageUrl(),
            'captain_id' => null, // or User::factory() if needed
            'club_id' => Club::factory(),
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Indicate that the team is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the team is a first team.
     */
    public function firstTeam(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'first_team',
        ]);
    }
} 