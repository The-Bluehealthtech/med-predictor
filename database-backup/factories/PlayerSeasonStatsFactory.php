<?php

namespace Database\Factories;

use App\Models\Player;
use App\Models\Competition;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PlayerSeasonStats>
 */
class PlayerSeasonStatsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'player_id' => Player::factory(),
            'competition_id' => Competition::factory(),
            'season' => fake()->year(),
            'matches_played' => fake()->numberBetween(0, 38),
            'matches_started' => fake()->numberBetween(0, 38),
            'minutes_played' => fake()->numberBetween(0, 3420), // 38 matches * 90 minutes
            'goals_scored' => fake()->numberBetween(0, 30),
            'assists' => fake()->numberBetween(0, 20),
            'yellow_cards' => fake()->numberBetween(0, 10),
            'red_cards' => fake()->numberBetween(0, 3),
            'clean_sheets' => fake()->numberBetween(0, 20),
            'goals_conceded' => fake()->numberBetween(0, 50),
            'saves' => fake()->numberBetween(0, 100),
            'pass_accuracy' => fake()->randomFloat(2, 60, 95),
            'tackles_won' => fake()->numberBetween(0, 100),
            'interceptions' => fake()->numberBetween(0, 50),
            'clearances' => fake()->numberBetween(0, 200),
            'crosses_attempted' => fake()->numberBetween(0, 100),
            'crosses_completed' => fake()->numberBetween(0, 50),
            'shots_on_target' => fake()->numberBetween(0, 50),
            'shots_off_target' => fake()->numberBetween(0, 50),
            'fouls_committed' => fake()->numberBetween(0, 30),
            'fouls_suffered' => fake()->numberBetween(0, 30),
            'offsides' => fake()->numberBetween(0, 20),
            'duels_won' => fake()->numberBetween(0, 100),
            'duels_lost' => fake()->numberBetween(0, 100),
            'aerial_duels_won' => fake()->numberBetween(0, 50),
            'aerial_duels_lost' => fake()->numberBetween(0, 50),
            'dribbles_attempted' => fake()->numberBetween(0, 100),
            'dribbles_completed' => fake()->numberBetween(0, 50),
            'rating' => fake()->randomFloat(2, 5.0, 10.0),
            'man_of_the_match' => fake()->numberBetween(0, 5),
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Indicate that the player is a forward with high goal stats.
     */
    public function forward(): static
    {
        return $this->state(fn (array $attributes) => [
            'goals_scored' => fake()->numberBetween(10, 30),
            'assists' => fake()->numberBetween(5, 15),
            'shots_on_target' => fake()->numberBetween(20, 50),
        ]);
    }

    /**
     * Indicate that the player is a midfielder with balanced stats.
     */
    public function midfielder(): static
    {
        return $this->state(fn (array $attributes) => [
            'goals_scored' => fake()->numberBetween(3, 10),
            'assists' => fake()->numberBetween(8, 20),
            'pass_accuracy' => fake()->randomFloat(2, 80, 95),
        ]);
    }

    /**
     * Indicate that the player is a defender with defensive stats.
     */
    public function defender(): static
    {
        return $this->state(fn (array $attributes) => [
            'clean_sheets' => fake()->numberBetween(5, 20),
            'tackles_won' => fake()->numberBetween(30, 100),
            'clearances' => fake()->numberBetween(50, 200),
        ]);
    }

    /**
     * Indicate that the player is a goalkeeper with keeper stats.
     */
    public function goalkeeper(): static
    {
        return $this->state(fn (array $attributes) => [
            'clean_sheets' => fake()->numberBetween(5, 20),
            'saves' => fake()->numberBetween(50, 150),
            'goals_conceded' => fake()->numberBetween(20, 60),
        ]);
    }
} 