<?php

namespace Database\Factories;

use App\Models\MatchSheet;
use App\Models\MatchModel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MatchSheetFactory extends Factory
{
    protected $model = MatchSheet::class;

    public function definition(): array
    {
        return [
            'match_id' => null,
            'match_number' => $this->faker->unique()->numberBetween(1000, 9999),
            'stadium_venue' => $this->faker->city() . ' Stadium',
            'weather_conditions' => $this->faker->randomElement(['Sunny', 'Cloudy', 'Rainy', 'Windy', 'Clear']),
            'pitch_conditions' => $this->faker->randomElement(['Excellent', 'Good', 'Fair', 'Poor']),
            'kickoff_time' => $this->faker->dateTimeBetween('now', '+30 days'),
            'home_team_roster' => $this->faker->randomElements(range(1, 50), $this->faker->numberBetween(11, 18)),
            'away_team_roster' => $this->faker->randomElements(range(51, 100), $this->faker->numberBetween(11, 18)),
            'home_team_substitutes' => $this->faker->randomElements(range(1, 50), $this->faker->numberBetween(0, 7)),
            'away_team_substitutes' => $this->faker->randomElements(range(51, 100), $this->faker->numberBetween(0, 7)),
            'home_team_coach' => $this->faker->name(),
            'away_team_coach' => $this->faker->name(),
            'home_team_manager' => $this->faker->name(),
            'away_team_manager' => $this->faker->name(),
            'referee_id' => null,
            'main_referee_id' => null,
            'assistant_referee_1_id' => null,
            'assistant_referee_2_id' => null,
            'fourth_official_id' => null,
            'var_referee_id' => null,
            'var_assistant_id' => null,
            'match_observer_id' => null,
            'validated_by' => null,
            'rejected_by' => null,
            'fa_validated_by' => null,
            'assigned_referee_id' => null,
            'match_statistics' => [
                'possession' => ['home' => $this->faker->numberBetween(30, 70), 'away' => $this->faker->numberBetween(30, 70)],
                'shots' => ['home' => $this->faker->numberBetween(5, 25), 'away' => $this->faker->numberBetween(5, 25)],
                'shots_on_target' => ['home' => $this->faker->numberBetween(1, 10), 'away' => $this->faker->numberBetween(1, 10)]
            ],
            'home_team_score' => $this->faker->numberBetween(0, 5),
            'away_team_score' => $this->faker->numberBetween(0, 5),
            'referee_report' => $this->faker->paragraph(),
            'match_status' => $this->faker->randomElement(['scheduled', 'in_progress', 'completed', 'cancelled', 'postponed']),
            'suspension_reason' => $this->faker->optional()->sentence(),
            'crowd_issues' => $this->faker->optional()->sentence(),
            'protests_incidents' => $this->faker->optional()->sentence(),
            'notes' => $this->faker->optional()->paragraph(),
            'home_team_signature' => $this->faker->sha1(),
            'away_team_signature' => $this->faker->sha1(),
            'home_team_signed_at' => $this->faker->optional()->dateTime(),
            'away_team_signed_at' => $this->faker->optional()->dateTime(),
            'observer_comments' => $this->faker->optional()->paragraph(),
            'observer_signed_at' => $this->faker->optional()->dateTime(),
            'referee_digital_signature' => $this->faker->sha1(),
            'referee_signed_at' => $this->faker->optional()->dateTime(),
            'penalty_shootout_data' => $this->faker->optional()->randomElements(['data1', 'data2', 'data3'], $this->faker->numberBetween(1, 3)),
            'var_decisions' => $this->faker->optional()->randomElements(['decision1', 'decision2', 'decision3'], $this->faker->numberBetween(1, 3)),
            'status' => $this->faker->randomElement(['draft', 'submitted', 'validated', 'rejected']),
            'submitted_at' => $this->faker->optional()->dateTime(),
            'validated_at' => $this->faker->optional()->dateTime(),
            'validation_notes' => $this->faker->optional()->paragraph(),
            'rejected_at' => $this->faker->optional()->dateTime(),
            'rejection_reason' => $this->faker->optional()->paragraph(),
            'version' => $this->faker->numberBetween(1, 10),
            'change_log' => $this->faker->optional()->randomElements(['change1', 'change2', 'change3'], $this->faker->numberBetween(1, 3)),
            'stage' => $this->faker->randomElement(['in_progress', 'before_game_signed', 'after_game_signed', 'fa_validated']),
            'stage_in_progress_at' => $this->faker->optional()->dateTime(),
            'stage_before_game_signed_at' => $this->faker->optional()->dateTime(),
            'stage_after_game_signed_at' => $this->faker->optional()->dateTime(),
            'stage_fa_validated_at' => $this->faker->optional()->dateTime(),
            'home_team_lineup_signature' => $this->faker->sha1(),
            'away_team_lineup_signature' => $this->faker->sha1(),
            'home_team_lineup_signed_at' => $this->faker->optional()->dateTime(),
            'away_team_lineup_signed_at' => $this->faker->optional()->dateTime(),
            'home_team_post_match_signature' => $this->faker->sha1(),
            'away_team_post_match_signature' => $this->faker->sha1(),
            'home_team_post_match_signed_at' => $this->faker->optional()->dateTime(),
            'away_team_post_match_signed_at' => $this->faker->optional()->dateTime(),
            'home_team_post_match_comments' => $this->faker->optional()->paragraph(),
            'away_team_post_match_comments' => $this->faker->optional()->paragraph(),
            'fa_validation_notes' => $this->faker->optional()->paragraph(),
            'referee_assigned_at' => $this->faker->optional()->dateTime(),
            'lineups_locked' => $this->faker->boolean(),
            'lineups_locked_at' => $this->faker->optional()->dateTime(),
            'match_events_locked' => $this->faker->boolean(),
            'match_events_locked_at' => $this->faker->optional()->dateTime(),
            'stage_transition_log' => $this->faker->optional()->randomElements(['transition1', 'transition2', 'transition3'], $this->faker->numberBetween(1, 3)),
            'user_action_log' => $this->faker->optional()->randomElements(['action1', 'action2', 'action3'], $this->faker->numberBetween(1, 3)),
            'signed_sheet_path' => $this->faker->optional()->filePath(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
            'stage' => 'in_progress',
        ]);
    }

    public function submitted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'submitted',
            'stage' => 'in_progress',
            'submitted_at' => now(),
        ]);
    }

    public function validated(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'validated',
            'stage' => 'in_progress',
            'validated_at' => now(),
            'validated_by' => User::factory()->create(['role' => 'fa_admin'])->id,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'stage' => 'in_progress',
            'rejected_at' => now(),
            'rejected_by' => User::factory()->create(['role' => 'fa_admin'])->id,
            'rejection_reason' => $this->faker->sentence(),
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'stage' => 'in_progress',
            'stage_in_progress_at' => now(),
        ]);
    }

    public function beforeGameSigned(): static
    {
        return $this->state(fn (array $attributes) => [
            'stage' => 'before_game_signed',
            'stage_before_game_signed_at' => now(),
            'home_team_lineup_signature' => $this->faker->sha1(),
            'away_team_lineup_signature' => $this->faker->sha1(),
            'home_team_lineup_signed_at' => now(),
            'away_team_lineup_signed_at' => now(),
        ]);
    }

    public function afterGameSigned(): static
    {
        return $this->state(fn (array $attributes) => [
            'stage' => 'after_game_signed',
            'stage_after_game_signed_at' => now(),
            'home_team_post_match_signature' => $this->faker->sha1(),
            'away_team_post_match_signature' => $this->faker->sha1(),
            'home_team_post_match_signed_at' => now(),
            'away_team_post_match_signed_at' => now(),
        ]);
    }

    public function faValidated(): static
    {
        return $this->state(fn (array $attributes) => [
            'stage' => 'fa_validated',
            'stage_fa_validated_at' => now(),
            'fa_validated_by' => User::factory()->create(['role' => 'fa_admin'])->id,
            'fa_validation_notes' => $this->faker->paragraph(),
        ]);
    }
} 