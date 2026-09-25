<?php

namespace Database\Factories;

use App\Models\Contract;
use App\Models\Player;
use App\Models\Club;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contract>
 */
class ContractFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $contractTypes = ['professional', 'amateur', 'youth', 'loan'];
        $statuses = ['active', 'expired', 'terminated', 'suspended'];
        
        $startDate = $this->faker->dateTimeBetween('-2 years', 'now');
        $endDate = $this->faker->dateTimeBetween($startDate, '+5 years');
        
        return [
            'player_id' => Player::factory(),
            'club_id' => Club::factory(),
            'contract_type' => $this->faker->randomElement($contractTypes),
            'status' => $this->faker->randomElement($statuses),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'salary' => $this->faker->randomFloat(2, 50000, 50000000),
            'currency' => $this->faker->randomElement(['EUR', 'USD', 'GBP']),
            'bonus_performance' => $this->faker->randomFloat(2, 0, 1000000),
            'bonus_appearance' => $this->faker->randomFloat(2, 0, 50000),
            'bonus_goals' => $this->faker->randomFloat(2, 0, 100000),
            'bonus_assists' => $this->faker->randomFloat(2, 0, 50000),
            'release_clause' => $this->faker->optional()->randomFloat(2, 1000000, 200000000),
            'buyout_clause' => $this->faker->optional()->randomFloat(2, 500000, 100000000),
            'contract_number' => $this->faker->unique()->numerify('CON-####-####'),
            'fifa_contract_id' => $this->faker->unique()->uuid(),
            'agent_commission' => $this->faker->randomFloat(2, 0, 15),
            'agent_commission_amount' => $this->faker->randomFloat(2, 0, 5000000),
            'additional_terms' => $this->faker->optional()->paragraph(),
            'notes' => $this->faker->optional()->paragraph(),
            'created_at' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Indicate that the contract is professional.
     */
    public function professional(): static
    {
        return $this->state(fn (array $attributes) => [
            'contract_type' => 'professional',
            'salary' => $this->faker->randomFloat(2, 100000, 50000000),
        ]);
    }

    /**
     * Indicate that the contract is amateur.
     */
    public function amateur(): static
    {
        return $this->state(fn (array $attributes) => [
            'contract_type' => 'amateur',
            'salary' => $this->faker->randomFloat(2, 0, 50000),
        ]);
    }

    /**
     * Indicate that the contract is youth.
     */
    public function youth(): static
    {
        return $this->state(fn (array $attributes) => [
            'contract_type' => 'youth',
            'salary' => $this->faker->randomFloat(2, 0, 25000),
        ]);
    }

    /**
     * Indicate that the contract is a loan.
     */
    public function loan(): static
    {
        return $this->state(fn (array $attributes) => [
            'contract_type' => 'loan',
            'salary' => $this->faker->randomFloat(2, 50000, 2000000),
        ]);
    }

    /**
     * Indicate that the contract is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    /**
     * Indicate that the contract is expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'expired',
            'end_date' => $this->faker->dateTimeBetween('-1 year', '-1 month'),
        ]);
    }

    /**
     * Indicate that the contract is terminated.
     */
    public function terminated(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'terminated',
        ]);
    }
}
