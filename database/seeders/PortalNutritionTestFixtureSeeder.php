<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PortalNutritionTestFixtureSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            throw new \RuntimeException('Cette insertion groupée nécessite PostgreSQL.');
        }

        $updated = DB::affectingStatement(<<<'SQL'
            UPDATE player_real_time_health
            SET metadata = jsonb_set(
                    COALESCE(metadata::jsonb, '{}'::jsonb),
                    '{nutrition}',
                    jsonb_build_object(
                        'total_calories', 2200 + (player_id % 400),
                        'protein_grams', 100 + (player_id % 40),
                        'meal_quality_score', 7 + (player_id % 3)
                    ) || COALESCE(metadata::jsonb->'nutrition', '{}'::jsonb),
                    true
                ),
                updated_at = CURRENT_TIMESTAMP
            WHERE notes LIKE 'synthetic_demo%'
              AND (
                  metadata::jsonb #>> '{nutrition,total_calories}' IS NULL
                  OR metadata::jsonb #>> '{nutrition,protein_grams}' IS NULL
                  OR metadata::jsonb #>> '{nutrition,meal_quality_score}' IS NULL
              )
        SQL);

        $this->command?->info("Dossiers nutrition de test complétés : {$updated}.");
    }
}
