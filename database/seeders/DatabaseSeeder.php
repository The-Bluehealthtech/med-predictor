<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AssociationSeeder::class,
            ClubSeeder::class,
            NationalitySeeder::class,
            AdminUserSeeder::class,
            SeasonSeeder::class,
            PlayerUserSeeder::class,
            TunisianLeaguePlayersSeeder::class,
            PlayerDetailedDataSeeder::class,
            // FFF Ligue 1 Data
            FFFLigue1Seeder::class,

            // Seeders V3
            V3AiPredictionSeeder::class,
            V3PerformanceMetricSeeder::class,
        ]);
    }
}
