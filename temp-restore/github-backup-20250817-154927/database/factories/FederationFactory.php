<?php

namespace Database\Factories;

use App\Models\Federation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Federation>
 */
class FederationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $federations = [
            [
                'name' => 'Fédération Française de Football',
                'short_name' => 'FFF',
                'country' => 'France',
                'region' => 'Europe',
                'fifa_code' => 'FRA',
                'website' => 'https://www.fff.fr',
                'email' => 'contact@fff.fr',
                'phone' => '+33 1 44 31 73 00',
                'address' => '87 Boulevard de Grenelle, 75738 Paris',
                'status' => 'active'
            ],
            [
                'name' => 'The Football Association',
                'short_name' => 'FA',
                'country' => 'England',
                'region' => 'Europe',
                'fifa_code' => 'ENG',
                'website' => 'https://www.thefa.com',
                'email' => 'info@thefa.com',
                'phone' => '+44 20 7745 4545',
                'address' => 'Wembley Stadium, Wembley, London HA9 0WS',
                'status' => 'active'
            ],
            [
                'name' => 'Real Federación Española de Fútbol',
                'short_name' => 'RFEF',
                'country' => 'Spain',
                'region' => 'Europe',
                'fifa_code' => 'ESP',
                'website' => 'https://www.rfef.es',
                'email' => 'info@rfef.es',
                'phone' => '+34 91 495 98 00',
                'address' => 'Ramón y Cajal, s/n, 28232 Las Rozas, Madrid',
                'status' => 'active'
            ],
            [
                'name' => 'Deutscher Fußball-Bund',
                'short_name' => 'DFB',
                'country' => 'Germany',
                'region' => 'Europe',
                'fifa_code' => 'GER',
                'website' => 'https://www.dfb.de',
                'email' => 'info@dfb.de',
                'phone' => '+49 69 678 80',
                'address' => 'Otto-Fleck-Schneise 6, 60528 Frankfurt am Main',
                'status' => 'active'
            ],
            [
                'name' => 'Federazione Italiana Giuoco Calcio',
                'short_name' => 'FIGC',
                'country' => 'Italy',
                'region' => 'Europe',
                'fifa_code' => 'ITA',
                'website' => 'https://www.figc.it',
                'email' => 'info@figc.it',
                'phone' => '+39 06 84 91 11',
                'address' => 'Via Gregorio Allegri 14, 00198 Roma',
                'status' => 'active'
            ]
        ];

        $federation = $this->faker->randomElement($federations);

        return [
            'name' => $federation['name'],
            'short_name' => $federation['short_name'],
            'country' => $federation['country'],
            'region' => $federation['region'],
            'fifa_code' => $federation['fifa_code'],
            'website' => $federation['website'],
            'email' => $federation['email'],
            'phone' => $federation['phone'],
            'address' => $federation['address'],
            'status' => $federation['status'],
            'created_at' => $this->faker->dateTimeBetween('-2 years', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Indicate that the federation is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    /**
     * Indicate that the federation is suspended.
     */
    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'suspended',
        ]);
    }
}
