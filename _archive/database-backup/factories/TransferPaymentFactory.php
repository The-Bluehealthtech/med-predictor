<?php

namespace Database\Factories;

use App\Models\TransferPayment;
use App\Models\Transfer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TransferPayment>
 */
class TransferPaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $paymentTypes = ['transfer_fee', 'agent_commission', 'bonus', 'penalty', 'other'];
        $paymentMethods = ['bank_transfer', 'wire_transfer', 'escrow', 'cash', 'crypto'];
        $statuses = ['pending', 'completed', 'failed', 'cancelled', 'refunded'];
        
        return [
            'transfer_id' => Transfer::factory(),
            'payment_type' => $this->faker->randomElement($paymentTypes),
            'payment_method' => $this->faker->randomElement($paymentMethods),
            'status' => $this->faker->randomElement($statuses),
            'amount' => $this->faker->randomFloat(2, 1000, 50000000),
            'currency' => $this->faker->randomElement(['EUR', 'USD', 'GBP']),
            'exchange_rate' => $this->faker->randomFloat(4, 0.8, 1.2),
            'amount_eur' => $this->faker->randomFloat(2, 1000, 50000000),
            'due_date' => $this->faker->dateTimeBetween('now', '+6 months'),
            'payment_date' => $this->faker->optional()->dateTimeBetween('-1 month', 'now'),
            'transaction_id' => $this->faker->unique()->uuid(),
            'bank_reference' => $this->faker->optional()->numerify('REF-####-####'),
            'sender_account' => $this->faker->optional()->bankAccountNumber(),
            'receiver_account' => $this->faker->optional()->bankAccountNumber(),
            'sender_name' => $this->faker->company(),
            'receiver_name' => $this->faker->company(),
            'payment_notes' => $this->faker->optional()->paragraph(),
            'fees' => $this->faker->randomFloat(2, 0, 1000),
            'tax_amount' => $this->faker->randomFloat(2, 0, 500000),
            'tax_percentage' => $this->faker->randomFloat(2, 0, 25),
            'created_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }

    /**
     * Indicate that the payment is a transfer fee.
     */
    public function transferFee(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_type' => 'transfer_fee',
            'amount' => $this->faker->randomFloat(2, 100000, 50000000),
        ]);
    }

    /**
     * Indicate that the payment is an agent commission.
     */
    public function agentCommission(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_type' => 'agent_commission',
            'amount' => $this->faker->randomFloat(2, 1000, 5000000),
        ]);
    }

    /**
     * Indicate that the payment is a bonus.
     */
    public function bonus(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_type' => 'bonus',
            'amount' => $this->faker->randomFloat(2, 1000, 1000000),
        ]);
    }

    /**
     * Indicate that the payment is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'payment_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * Indicate that the payment is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the payment is failed.
     */
    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
        ]);
    }

    /**
     * Indicate that the payment is a bank transfer.
     */
    public function bankTransfer(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'bank_transfer',
        ]);
    }

    /**
     * Indicate that the payment is a wire transfer.
     */
    public function wireTransfer(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_method' => 'wire_transfer',
        ]);
    }
}
