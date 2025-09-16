<?php

namespace Database\Factories;

use App\Models\Transfer;
use App\Models\Player;
use App\Models\Club;
use App\Models\Federation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transfer>
 */
class TransferFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $transferTypes = ['permanent', 'loan', 'free_transfer', 'exchange'];
        $statuses = ['pending', 'approved', 'rejected', 'completed', 'cancelled'];
        
        return [
            'player_id' => Player::factory(),
            'from_club_id' => Club::factory(),
            'to_club_id' => Club::factory(),
            'federation_id' => Federation::factory(),
            'transfer_type' => $this->faker->randomElement($transferTypes),
            'status' => $this->faker->randomElement($statuses),
            'transfer_fee' => $this->faker->randomFloat(2, 0, 200000000),
            'currency' => $this->faker->randomElement(['EUR', 'USD', 'GBP']),
            'transfer_date' => $this->faker->dateTimeBetween('-1 year', '+6 months'),
            'contract_start_date' => $this->faker->dateTimeBetween('now', '+1 year'),
            'contract_end_date' => $this->faker->dateTimeBetween('+1 year', '+5 years'),
            'fifa_transfer_id' => $this->faker->unique()->uuid(),
            'fifa_status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
            'fifa_approval_date' => $this->faker->optional()->dateTimeBetween('-6 months', 'now'),
            'fifa_rejection_reason' => $this->faker->optional()->sentence(),
            'medical_passed' => $this->faker->boolean(80),
            'medical_date' => $this->faker->optional()->dateTimeBetween('-1 month', 'now'),
            'medical_notes' => $this->faker->optional()->paragraph(),
            'agent_name' => $this->faker->name(),
            'agent_contact' => $this->faker->phoneNumber(),
            'agent_email' => $this->faker->email(),
            'commission_percentage' => $this->faker->randomFloat(2, 0, 15),
            'commission_amount' => $this->faker->randomFloat(2, 0, 5000000),
            'additional_terms' => $this->faker->optional()->paragraph(),
            'notes' => $this->faker->optional()->paragraph(),
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }

    /**
     * Indicate that the transfer is permanent.
     */
    public function permanent(): static
    {
        return $this->state(fn (array $attributes) => [
            'transfer_type' => 'permanent',
        ]);
    }

    /**
     * Indicate that the transfer is a loan.
     */
    public function loan(): static
    {
        return $this->state(fn (array $attributes) => [
            'transfer_type' => 'loan',
        ]);
    }

    /**
     * Indicate that the transfer is a free transfer.
     */
    public function freeTransfer(): static
    {
        return $this->state(fn (array $attributes) => [
            'transfer_type' => 'free_transfer',
            'transfer_fee' => 0,
        ]);
    }

    /**
     * Indicate that the transfer is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the transfer is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'fifa_status' => 'approved',
            'fifa_approval_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * Indicate that the transfer is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'fifa_status' => 'approved',
            'fifa_approval_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'medical_passed' => true,
            'medical_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * Indicate that the transfer is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'fifa_status' => 'rejected',
            'fifa_rejection_reason' => $this->faker->sentence(),
        ]);
    }
}
