<?php

namespace Database\Factories;

use App\Models\Player;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HealthRecord>
 */
class HealthRecordFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'player_id' => Player::factory(),
            'blood_pressure_systolic' => fake()->optional()->numberBetween(90, 180),
            'blood_pressure_diastolic' => fake()->optional()->numberBetween(60, 120),
            'heart_rate' => fake()->optional()->numberBetween(50, 120),
            'temperature' => fake()->optional()->randomFloat(1, 36.0, 39.0),
            'weight' => fake()->optional()->randomFloat(2, 50.0, 100.0),
            'height' => fake()->optional()->randomFloat(2, 160.0, 200.0),
            'bmi' => fake()->optional()->randomFloat(2, 18.0, 35.0),
            'blood_type' => fake()->optional()->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
            'allergies' => fake()->optional()->randomElements(['pollen', 'dust', 'nuts', 'shellfish'], 2),
            'medications' => fake()->optional()->randomElements(['vitamins', 'painkillers', 'anti_inflammatories'], 2),
            'medical_history' => fake()->optional()->randomElements(['surgery', 'injury', 'illness'], 2),
            'symptoms' => fake()->optional()->randomElements(['fatigue', 'pain', 'swelling', 'stiffness'], 2),
            'diagnosis' => fake()->optional()->sentence(),
            'treatment_plan' => fake()->optional()->paragraph(),
            'risk_score' => fake()->randomFloat(4, 0.0, 1.0),
            'prediction_confidence' => fake()->randomFloat(4, 0.0, 1.0),
            'record_date' => fake()->dateTimeBetween('-1 year', 'now'),
            'next_checkup_date' => fake()->optional()->dateTimeBetween('now', '+6 months'),
            'status' => fake()->randomElement(['active', 'archived', 'pending']),
        ];
    }

    /**
     * Indicate that the player has low medical risk.
     */
    public function lowRisk(): static
    {
        return $this->state(fn (array $attributes) => [
            'risk_score' => fake()->randomFloat(4, 0.0, 0.3),
            'prediction_confidence' => fake()->randomFloat(4, 0.7, 1.0),
        ]);
    }

    /**
     * Indicate that the player has medium medical risk.
     */
    public function mediumRisk(): static
    {
        return $this->state(fn (array $attributes) => [
            'risk_score' => fake()->randomFloat(4, 0.3, 0.6),
            'prediction_confidence' => fake()->randomFloat(4, 0.5, 0.8),
        ]);
    }

    /**
     * Indicate that the player has high medical risk.
     */
    public function highRisk(): static
    {
        return $this->state(fn (array $attributes) => [
            'risk_score' => fake()->randomFloat(4, 0.8, 1.0),
            'prediction_confidence' => fake()->randomFloat(4, 0.3, 0.7),
        ]);
    }

    /**
     * Indicate that the player has serious medical issues.
     */
    public function seriousIssues(): static
    {
        return $this->state(fn (array $attributes) => [
            'risk_score' => fake()->randomFloat(4, 0.9, 1.0),
            'symptoms' => ['douleur intense', 'fracture'],
            'diagnosis' => 'Fracture de la jambe droite',
            'treatment_plan' => 'Repos complet pendant 6 semaines',
        ]);
    }
} 