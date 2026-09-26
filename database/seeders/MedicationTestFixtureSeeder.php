<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MedicationTestFixtureSeeder extends Seeder
{
    public function run(): void
    {
        $fixture = [[
            'type' => 'Traitement fictif de test',
            'status' => 'active',
            'source' => 'synthetic_demo',
            'start_date' => '2026-09-01',
        ]];

        $updated = DB::table('health_records')
            ->whereExists(function ($query): void {
                $query->selectRaw('1')
                    ->from('player_real_time_health')
                    ->whereColumn(
                        'player_real_time_health.player_id',
                        'health_records.player_id'
                    )
                    ->where(
                        'player_real_time_health.notes',
                        'like',
                        'synthetic_demo%'
                    );
            })
            ->whereNull('medications')
            ->update([
                'medications' => json_encode($fixture, JSON_THROW_ON_ERROR),
                'updated_at' => now(),
            ]);

        $this->command?->info("Dossiers de test complétés : {$updated}.");
    }
}
