<?php

namespace Database\Factories;

use App\Models\Club;
use App\Models\Association;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Club>
 */
class ClubFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'short_name' => fake()->lexify('???'),
            'country' => fake()->country(),
            'city' => fake()->city(),
            'founded_year' => fake()->numberBetween(1900, 2020),
            'stadium_name' => fake()->company() . ' Stadium',
            'stadium_capacity' => fake()->numberBetween(5000, 100000),
            'association_id' => Association::factory(),
            'logo_path' => null,
            'website' => fake()->url(),
            'email' => fake()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'fifa_connect_id' => fake()->unique()->regexify('CLUB[0-9]{6}'),
            'status' => 'active',
        ];
    }

    /**
     * Indicate that the club is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }
} 