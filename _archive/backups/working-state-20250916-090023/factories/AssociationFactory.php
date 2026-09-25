<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Association>
 */
class AssociationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company() . ' Association',
            'logo' => 'defaults/association-logo.png',
            'country' => fake()->country(),
            'fifa_id' => fake()->unique()->numberBetween(1000, 9999),
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the association is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }
} 