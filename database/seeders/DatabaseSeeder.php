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
            AdminUserSeeder::class,
            PlayerUserSeeder::class,
            TunisianLeaguePlayersSeeder::class,
            PlayerDetailedDataSeeder::class,
            
            // Seeders V3
            V3AiPredictionSeeder::class,
            V3PerformanceMetricSeeder::class,
            
            // Données de test pour les compétitions
            CompetitionTestDataSeeder::class,
        ]);
    }
}
