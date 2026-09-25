<?php

namespace Database\Factories;

use App\Models\Competition;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GameMatch>
 */
class GameMatchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $competition = Competition::factory()->create();
        $teams = Team::factory()->count(2)->create();
        
        return [
            'competition_id' => $competition->id,
            'home_team_id' => $teams[0]->id,
            'away_team_id' => $teams[1]->id,
            'match_date' => $this->faker->dateTimeBetween('now', '+2 months'),
            'kickoff_time' => $this->faker->dateTimeBetween('now', '+2 months'),
            'venue' => $this->faker->city() . ' Stadium',
            'match_status' => $this->faker->randomElement(['scheduled', 'live', 'completed', 'postponed', 'cancelled']),
            'home_score' => $this->faker->numberBetween(0, 5),
            'away_score' => $this->faker->numberBetween(0, 5),
            'matchday' => $this->faker->numberBetween(1, 38),
            'completed_at' => $this->faker->optional()->dateTimeBetween('-1 month', 'now'),
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
        ];
    }

    /**
     * Indicate that the match is scheduled.
     */
    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'match_status' => 'scheduled',
            'home_score' => null,
            'away_score' => null,
            'completed_at' => null,
        ]);
    }

    /**
     * Indicate that the match is in progress.
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'match_status' => 'live',
            'completed_at' => null,
        ]);
    }

    /**
     * Indicate that the match is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'match_status' => 'completed',
            'completed_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * Indicate that the match is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'match_status' => 'cancelled',
            'home_score' => null,
            'away_score' => null,
            'completed_at' => null,
        ]);
    }
} 