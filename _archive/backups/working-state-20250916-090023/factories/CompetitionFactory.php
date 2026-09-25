<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Competition>
 */
class CompetitionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'short_name' => fake()->optional()->words(2, true),
            'description' => fake()->paragraph(),
            'start_date' => fake()->dateTimeBetween('now', '+6 months'),
            'end_date' => fake()->dateTimeBetween('+6 months', '+12 months'),
            'status' => fake()->randomElement(['draft', 'active', 'completed', 'cancelled']),
            'type' => fake()->randomElement(['league', 'cup', 'tournament', 'friendly']),
            'country' => fake()->optional()->country(),
            'region' => fake()->optional()->state(),
            'season' => fake()->year(),
            'max_teams' => fake()->numberBetween(8, 32),
            'min_teams' => fake()->optional()->numberBetween(4, 16),
            'registration_deadline' => fake()->dateTimeBetween('now', '+1 month'),
            'rules' => fake()->paragraphs(3, true),
            'prize_pool' => fake()->optional()->numberBetween(1000, 100000),
            'entry_fee' => fake()->optional()->numberBetween(100, 5000),
            'format' => fake()->optional()->randomElement(['round_robin', 'knockout', 'group_stage']),
            'logo_url' => fake()->optional()->imageUrl(),
            'website' => fake()->optional()->url(),
            'organizer' => fake()->optional()->company(),
            'sponsors' => fake()->optional()->company(),
            'broadcast_partners' => fake()->optional()->company(),
            'association_id' => \App\Models\Association::factory(),
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Indicate that the competition is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the competition is a league.
     */
    public function league(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'league',
        ]);
    }

    /**
     * Indicate that the competition is a cup.
     */
    public function cup(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'cup',
        ]);
    }
} 