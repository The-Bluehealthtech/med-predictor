<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PlayerPerformanceTestFixtureSeeder extends Seeder
{
    public function run(): void
    {
        $fields = [
            'physical_score', 'endurance_score', 'strength_score',
            'speed_score', 'agility_score', 'technical_score',
            'passing_accuracy', 'shooting_accuracy', 'dribbling_skill',
            'tackling_effectiveness', 'heading_accuracy',
            'crossing_accuracy', 'free_kick_accuracy', 'penalty_accuracy',
            'tactical_score', 'positioning_awareness', 'decision_making',
            'game_intelligence', 'team_work_rate', 'pressing_intensity',
            'defensive_organization', 'attacking_movement',
            'mental_score', 'confidence_level', 'stress_management',
            'focus_concentration', 'motivation_level',
            'leadership_qualities', 'pressure_handling', 'mental_toughness',
            'social_score', 'team_cohesion', 'communication_skills',
            'coachability', 'discipline_level', 'professional_attitude',
            'media_handling', 'fan_interaction',
        ];
        $fields = array_values(array_filter(
            $fields,
            fn (string $field): bool =>
                Schema::hasColumn('player_performances', $field)
        ));
        $updated = 0;

        DB::table('player_performances')->orderBy('id')
            ->chunkById(100, function ($rows) use ($fields, &$updated): void {
                DB::transaction(function () use ($rows, $fields, &$updated): void {
                    foreach ($rows as $row) {
                        $notes = json_decode($row->notes ?? '{}', true);
                        if (!is_array($notes)
                            || ($notes['source'] ?? null) !== 'synthetic_demo') {
                            continue;
                        }

                        $values = [];
                        foreach ($fields as $index => $field) {
                            if ($row->{$field} === null) {
                                $values[$field] = 55
                                    + (((int) $row->player_id * 7
                                        + $index * 11) % 36);
                            }
                        }

                        if (!$values) {
                            continue;
                        }

                        $notes['synthetic_test_fields'] = array_values(
                            array_unique(array_merge(
                                $notes['synthetic_test_fields'] ?? [],
                                array_keys($values)
                            ))
                        );
                        $values['notes'] = json_encode(
                            $notes,
                            JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
                        );
                        $values['updated_at'] = now();

                        DB::table('player_performances')
                            ->where('id', $row->id)
                            ->update($values);
                        $updated++;
                    }
                });
            });

        $this->command?->info(
            "Évaluations synthétiques complétées : {$updated} lignes."
        );
    }
}
