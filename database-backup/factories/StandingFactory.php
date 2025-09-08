<?php

namespace Database\Factories;

use App\Models\Competition;
use App\Models\Season;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Standing>
 */
class StandingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $played = $this->faker->numberBetween(0, 38);
        $won = $this->faker->numberBetween(0, $played);
        $drawn = $this->faker->numberBetween(0, $played - $won);
        $lost = $played - $won - $drawn;
        $goalsFor = $this->faker->numberBetween(0, $played * 3);
        $goalsAgainst = $this->faker->numberBetween(0, $played * 3);
        $points = ($won * 3) + $drawn;

        return [
            'competition_id' => Competition::factory(),
            'season_id' => Season::factory(),
            'team_id' => Team::factory(),
            'played' => $played,
            'won' => $won,
            'drawn' => $drawn,
            'lost' => $lost,
            'goals_for' => $goalsFor,
            'goals_against' => $goalsAgainst,
            'goal_difference' => $goalsFor - $goalsAgainst,
            'points' => $points,
            'position' => $this->faker->numberBetween(1, 20),
            'form' => json_encode($this->faker->randomElements(['W', 'D', 'L'], 3)),
            'home_played' => $this->faker->numberBetween(0, $played),
            'home_won' => $this->faker->numberBetween(0, $won),
            'home_drawn' => $this->faker->numberBetween(0, $drawn),
            'home_lost' => $this->faker->numberBetween(0, $lost),
            'home_goals_for' => $this->faker->numberBetween(0, $goalsFor),
            'home_goals_against' => $this->faker->numberBetween(0, $goalsAgainst),
            'away_played' => $this->faker->numberBetween(0, $played),
            'away_won' => $this->faker->numberBetween(0, $won),
            'away_drawn' => $this->faker->numberBetween(0, $drawn),
            'away_lost' => $this->faker->numberBetween(0, $lost),
            'away_goals_for' => $this->faker->numberBetween(0, $goalsFor),
            'away_goals_against' => $this->faker->numberBetween(0, $goalsAgainst),
            'clean_sheets' => $this->faker->numberBetween(0, $won + $drawn),
            'failed_to_score' => $this->faker->numberBetween(0, $lost + $drawn),
            'biggest_win' => $this->faker->numberBetween(1, 5),
            'biggest_loss' => $this->faker->numberBetween(1, 5),
            'last_updated' => now(),
        ];
    }

    /**
     * Indicate that the team is in first position.
     */
    public function first(): static
    {
        return $this->state(fn (array $attributes) => [
            'position' => 1,
            'points' => $this->faker->numberBetween(80, 100),
            'played' => $this->faker->numberBetween(30, 38),
            'won' => $this->faker->numberBetween(25, 35),
            'drawn' => $this->faker->numberBetween(5, 10),
            'lost' => $this->faker->numberBetween(0, 5),
        ]);
    }

    /**
     * Indicate that the team is in relegation zone.
     */
    public function relegation(): static
    {
        return $this->state(fn (array $attributes) => [
            'position' => $this->faker->numberBetween(18, 20),
            'points' => $this->faker->numberBetween(20, 35),
            'played' => $this->faker->numberBetween(30, 38),
            'won' => $this->faker->numberBetween(5, 10),
            'drawn' => $this->faker->numberBetween(5, 10),
            'lost' => $this->faker->numberBetween(15, 25),
        ]);
    }

    /**
     * Indicate that the team has good form.
     */
    public function goodForm(): static
    {
        return $this->state(fn (array $attributes) => [
            'form' => ['W', 'W', 'D', 'W', 'W'],
        ]);
    }

    /**
     * Indicate that the team has poor form.
     */
    public function poorForm(): static
    {
        return $this->state(fn (array $attributes) => [
            'form' => ['L', 'L', 'D', 'L', 'L'],
        ]);
    }
}
