<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlayerPortalFieldCompletionSeeder extends Seeder
{
    public function run(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            throw new \RuntimeException('Ce complément groupé nécessite PostgreSQL.');
        }

        $updates = [];
        $updates['mesures temps réel'] = DB::affectingStatement(<<<'SQL'
            UPDATE player_real_time_health
            SET metadata = jsonb_set(
                jsonb_set(
                    COALESCE(metadata::jsonb, '{}'::jsonb),
                    '{vitals}',
                    jsonb_build_object('heart_rate_recovery', 20 + (player_id % 11))
                        || COALESCE(metadata::jsonb->'vitals', '{}'::jsonb), true
                ),
                '{recovery}',
                jsonb_build_object(
                    'stretching_minutes', 10 + (player_id % 15),
                    'muscle_soreness', 15 + (player_id % 25)
                ) || COALESCE(metadata::jsonb->'recovery', '{}'::jsonb), true
            ), updated_at = CURRENT_TIMESTAMP
            WHERE notes LIKE 'synthetic_demo%'
              AND (
                  metadata::jsonb #>> '{vitals,heart_rate_recovery}' IS NULL
                  OR metadata::jsonb #>> '{recovery,stretching_minutes}' IS NULL
                  OR metadata::jsonb #>> '{recovery,muscle_soreness}' IS NULL
              )
        SQL);
        // Expressions SQL corrélées explicites : chaque mise à jour est limitée au même joueur de test.
        $exists = fn (string $table) => 'EXISTS (SELECT 1 FROM player_real_time_health rt WHERE rt.player_id = '
            . $table . ".player_id AND rt.notes LIKE 'synthetic_demo%')";

        $updates['nutrition SDOH'] = DB::affectingStatement(
            'UPDATE player_sdoh_data SET has_healthy_diet = TRUE, updated_at = CURRENT_TIMESTAMP '
            . 'WHERE has_healthy_diet IS NULL AND ' . $exists('player_sdoh_data')
        );
        $updates['score de performance'] = DB::affectingStatement(
            'UPDATE player_performances SET overall_performance_score = '
            . 'ROUND((physical_score + technical_score + tactical_score + mental_score + social_score) / 5.0, 2), '
            . 'updated_at = CURRENT_TIMESTAMP WHERE overall_performance_score IS NULL '
            . 'AND physical_score IS NOT NULL AND technical_score IS NOT NULL '
            . 'AND tactical_score IS NOT NULL AND mental_score IS NOT NULL '
            . 'AND social_score IS NOT NULL AND ' . $exists('player_performances')
        );
        $updates['clinicien de test'] = DB::affectingStatement(
            "UPDATE health_records SET doctor_name = 'Clinicien fictif (test)', updated_at = CURRENT_TIMESTAMP "
            . "WHERE (doctor_name IS NULL OR doctor_name = '') AND " . $exists('health_records')
        );
        $updates['rendez-vous de test'] = DB::affectingStatement(
            "UPDATE medical_records SET next_appointment = DATE '2026-10-26', updated_at = CURRENT_TIMESTAMP "
            . 'WHERE next_appointment IS NULL AND ' . $exists('medical_records')
        );
        $updates['API de démonstration'] = DB::affectingStatement(
            "UPDATE player_connected_devices SET metadata = jsonb_set(COALESCE(metadata::jsonb, '{}'::jsonb), "
            . "'{api_endpoint}', to_jsonb('https://example.invalid/synthetic-device/' || player_id::text), true), "
            . "updated_at = CURRENT_TIMESTAMP WHERE metadata::jsonb->>'api_endpoint' IS NULL AND "
            . $exists('player_connected_devices')
        );
        $updates['licences de test'] = DB::affectingStatement(
            "UPDATE player_licenses SET license_number = COALESCE(NULLIF(license_number, ''), 'SYNTH-LIC-' || id::text), "
            . "season = COALESCE(NULLIF(season, ''), '2026-2027'), "
            . "issuing_authority = COALESCE(NULLIF(issuing_authority, ''), 'Autorité fictive de test'), "
            . "updated_at = CURRENT_TIMESTAMP WHERE (license_number IS NULL OR license_number = '' "
            . "OR season IS NULL OR season = '' OR issuing_authority IS NULL OR issuing_authority = '') AND "
            . $exists('player_licenses')
        );
        $updates['capteurs de démonstration'] = DB::affectingStatement(
            "UPDATE player_connected_devices SET sensors_available = '[\"heart_rate\",\"sleep\",\"activity\"]'::json, "
            . "updated_at = CURRENT_TIMESTAMP WHERE (sensors_available IS NULL "
            . "OR sensors_available::jsonb = '[]'::jsonb) AND "
            . $exists('player_connected_devices')
        );
        $updates['conseil médical de test'] = DB::affectingStatement(
            "UPDATE medical_predictions SET recommendations = '[\"Conseil fictif de test, sans valeur médicale.\"]'::json, "
            . "updated_at = CURRENT_TIMESTAMP WHERE (recommendations IS NULL "
            . "OR recommendations::jsonb = '[]'::jsonb) AND "
            . $exists('medical_predictions')
        );
        foreach ($updates as $label => $count) {
            $this->command?->info("{$label} : {$count} lignes complétées.");
        }
    }
}
