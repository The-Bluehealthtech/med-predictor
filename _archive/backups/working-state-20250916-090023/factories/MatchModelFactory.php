<?php

namespace Database\Factories;

use App\Models\MatchModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class MatchModelFactory extends Factory
{
    protected $model = MatchModel::class;

    public function definition()
    {
        return [
            'competition_id' => \App\Models\Competition::factory(),
            'home_team_id' => \App\Models\Team::factory(),
            'away_team_id' => \App\Models\Team::factory(),
            'home_club_id' => \App\Models\Club::factory(),
            'away_club_id' => \App\Models\Club::factory(),
            'match_date' => $this->faker->date(),
            'kickoff_time' => $this->faker->dateTime(),
            'venue' => $this->faker->word,
            'stadium' => $this->faker->word,
            'capacity' => $this->faker->numberBetween(20000, 80000),
            'attendance' => null,
            'weather_conditions' => $this->faker->randomElement(['Clear', 'Cloudy', 'Rainy', 'Windy']),
            'pitch_condition' => $this->faker->randomElement(['Excellent', 'Good', 'Fair', 'Poor']),
            'referee' => $this->faker->name,
            'assistant_referee_1' => $this->faker->name,
            'assistant_referee_2' => $this->faker->name,
            'fourth_official' => $this->faker->name,
            'var_referee' => $this->faker->name,
            'match_status' => 'scheduled',
            'home_score' => null,
            'away_score' => null,
            'home_penalties' => null,
            'away_penalties' => null,
            'home_yellow_cards' => null,
            'away_yellow_cards' => null,
            'home_red_cards' => null,
            'away_red_cards' => null,
            'home_possession' => null,
            'away_possession' => null,
            'home_shots' => null,
            'away_shots' => null,
            'home_shots_on_target' => null,
            'away_shots_on_target' => null,
            'home_corners' => null,
            'away_corners' => null,
            'home_fouls' => null,
            'away_fouls' => null,
            'home_offsides' => null,
            'away_offsides' => null,
            'match_highlights' => null,
            'match_report' => null,
            'broadcast_info' => null,
            'ticket_info' => null,
        ];
    }
} 