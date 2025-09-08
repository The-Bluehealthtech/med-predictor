<?php

namespace Database\Factories;

use App\Models\Player;
use App\Models\Club;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PlayerLicense>
 */
class PlayerLicenseFactory extends Factory
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
            'club_id' => Club::factory(),
            'season' => fake()->year(),
            'license_number' => fake()->unique()->regexify('[A-Z]{2}[0-9]{6}'),
            'registration_number' => fake()->unique()->regexify('REG-[0-9]{8}'),
            'license_type' => fake()->randomElement(['federation', 'amateur', 'professional', 'youth']),
            'license_category' => fake()->randomElement(['A', 'B', 'C', 'D', 'E']),
            'status' => fake()->randomElement(['pending', 'approved', 'rejected', 'expired', 'suspended']),
            'approval_status' => fake()->randomElement(['pending', 'approved', 'rejected']),
            'approved_at' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'approved_by' => User::factory(),
            'bonus_structure' => fake()->optional()->randomElement([json_encode(['goal_bonus' => 100, 'win_bonus' => 200]), null]),
            'contract_start_date' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'contract_end_date' => fake()->optional()->dateTimeBetween('now', '+3 years'),
            'contract_type' => fake()->optional()->randomElement(['permanent', 'loan', 'free_agent']),
            'disciplinary_record' => fake()->optional()->paragraph(),
            'documentation_status' => fake()->optional()->randomElement(['complete', 'pending', 'incomplete']),
            'expiry_date' => fake()->optional()->dateTimeBetween('now', '+2 years'),
            'fifa_connect_id' => fake()->optional()->regexify('FIFA[0-9]{6}'),
            'fitness_certificate' => fake()->optional()->boolean(),
            'international_clearance' => fake()->optional()->boolean(),
            'issue_date' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
            'issuing_authority' => fake()->optional()->company(),
            'medical_clearance' => fake()->optional()->paragraph(),
            'notes' => fake()->optional()->paragraph(),
            'release_clause' => fake()->optional()->numberBetween(10000, 1000000),
            'renewal_date' => fake()->optional()->dateTimeBetween('now', '+1 year'),
            'transfer_status' => fake()->optional()->randomElement(['registered', 'pending', 'completed']),
            'visa_status' => fake()->optional()->randomElement(['valid', 'expired', 'pending']),
            'wage_agreement' => fake()->optional()->numberBetween(1000, 100000),
            'work_permit' => fake()->optional()->boolean(),
            'submitted_by_club_admin_id' => User::factory(),
            'processed_by_license_agent_id' => fake()->optional()->randomElement([User::factory(), null]),
            'requested_by' => User::factory(),
            'document_path' => fake()->optional()->filePath(),
            'documents_payload' => fake()->optional()->randomElement([json_encode(['passport', 'medical_cert', 'contract']), null]),
            'rejection_reason' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the license is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'approval_status' => 'approved',
            'approved_at' => now(),
        ]);
    }

    /**
     * Indicate that the license is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'approval_status' => 'pending',
        ]);
    }

    /**
     * Indicate that the license is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'approval_status' => 'rejected',
            'rejection_reason' => fake()->sentence(),
        ]);
    }

    /**
     * Indicate that the license is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'expired',
            'expiry_date' => fake()->dateTimeBetween('-1 year', 'now'),
        ]);
    }
} 