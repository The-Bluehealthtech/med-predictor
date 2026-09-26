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
            ->where('notes', 'like', 'synthetic_demo%')
            ->whereNull('medications')
            ->update([
                'medications' => json_encode($fixture, JSON_THROW_ON_ERROR),
                'updated_at' => now(),
            ]);

        $this->command?->info("Dossiers de test complétés : {$updated}.");
    }
}
