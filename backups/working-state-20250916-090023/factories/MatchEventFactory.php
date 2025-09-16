<?php

namespace Database\Factories;

use App\Models\MatchEvent;
use App\Models\MatchModel;
use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MatchEventFactory extends Factory
{
    protected $model = MatchEvent::class;

    public function definition(): array
    {
        $eventTypes = [
            'goal', 'assist', 'yellow_card', 'red_card', 'substitution_in', 
            'substitution_out', 'injury', 'save', 'missed_penalty', 'penalty_saved',
            'own_goal', 'var_decision', 'free_kick_goal', 'header_goal', 'volley_goal',
            'long_range_goal', 'penalty_goal'
        ];

        $periods = ['first_half', 'second_half', 'extra_time_first', 'extra_time_second', 'penalty_shootout'];
        $severities = ['low', 'medium', 'high'];

        return [
            'match_id' => null, // Will be set explicitly in tests
            'match_sheet_id' => null,
            'player_id' => null, // Will be set explicitly in tests
            'team_id' => null, // Will be set explicitly in tests
            'assisted_by_player_id' => null,
            'substituted_player_id' => null,
            'recorded_by_user_id' => null, // Will be set explicitly in tests
            'event_type' => $this->faker->randomElement($eventTypes),
            'type' => $this->faker->randomElement(['goal', 'yellow_card', 'red_card', 'substitution', 'injury']), // Match the enum constraint
            'minute' => $this->faker->numberBetween(1, 90),
            'extra_time_minute' => $this->faker->optional()->numberBetween(1, 15),
            'period' => $this->faker->randomElement($periods),
            'event_data' => null,
            'description' => $this->faker->optional()->sentence(),
            'location' => $this->faker->optional()->word(),
            'severity' => $this->faker->randomElement($severities),
            'is_confirmed' => $this->faker->boolean(80), // 80% chance of being confirmed
            'is_contested' => $this->faker->boolean(10), // 10% chance of being contested
            'contest_reason' => null,
            'recorded_at' => $this->faker->dateTimeBetween('-1 year', 'now')
        ];
    }

    public function goal(): static
    {
        return $this->state(fn (array $attributes) => [
            'event_type' => 'goal',
            'minute' => $this->faker->numberBetween(1, 90),
            'description' => 'Goal scored'
        ]);
    }

    public function assist(): static
    {
        return $this->state(fn (array $attributes) => [
            'event_type' => 'assist',
            'minute' => $this->faker->numberBetween(1, 90),
            'description' => 'Assist provided'
        ]);
    }

    public function yellowCard(): static
    {
        return $this->state(fn (array $attributes) => [
            'event_type' => 'yellow_card',
            'minute' => $this->faker->numberBetween(1, 90),
            'description' => 'Yellow card shown'
        ]);
    }

    public function redCard(): static
    {
        return $this->state(fn (array $attributes) => [
            'event_type' => 'red_card',
            'minute' => $this->faker->numberBetween(1, 90),
            'description' => 'Red card shown'
        ]);
    }

    public function substitution(): static
    {
        return $this->state(fn (array $attributes) => [
            'event_type' => 'substitution_in',
            'minute' => $this->faker->numberBetween(45, 85),
            'description' => 'Player substitution'
        ]);
    }

    public function injury(): static
    {
        return $this->state(fn (array $attributes) => [
            'event_type' => 'injury',
            'minute' => $this->faker->numberBetween(1, 90),
            'description' => 'Player injured',
            'severity' => $this->faker->randomElement(['low', 'medium', 'high'])
        ]);
    }

    public function save(): static
    {
        return $this->state(fn (array $attributes) => [
            'event_type' => 'save',
            'minute' => $this->faker->numberBetween(1, 90),
            'description' => 'Goalkeeper save'
        ]);
    }

    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_confirmed' => true,
            'is_contested' => false,
            'contest_reason' => null
        ]);
    }

    public function contested(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_confirmed' => false,
            'is_contested' => true,
            'contest_reason' => $this->faker->sentence()
        ]);
    }

    public function firstHalf(): static
    {
        return $this->state(fn (array $attributes) => [
            'period' => 'first_half',
            'minute' => $this->faker->numberBetween(1, 45)
        ]);
    }

    public function secondHalf(): static
    {
        return $this->state(fn (array $attributes) => [
            'period' => 'second_half',
            'minute' => $this->faker->numberBetween(46, 90)
        ]);
    }

    public function extraTime(): static
    {
        return $this->state(fn (array $attributes) => [
            'period' => $this->faker->randomElement(['extra_time_first', 'extra_time_second']),
            'minute' => $this->faker->numberBetween(91, 120),
            'extra_time_minute' => $this->faker->numberBetween(1, 15)
        ]);
    }
} 