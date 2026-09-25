<?php

namespace Database\Factories;

use App\Models\Club;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlayerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'fifa_connect_id' => $this->faker->unique()->numberBetween(10000, 99999),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'birth_date' => $this->faker->date(),
            'gender' => $this->faker->randomElement(['male', 'female']),
            'club_id' => Club::factory(),
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
} 