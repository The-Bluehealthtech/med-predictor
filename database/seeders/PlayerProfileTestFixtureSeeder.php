<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlayerProfileTestFixtureSeeder extends Seeder
{
    public function run(): void
    {
        $updated = DB::table('players')
            ->whereExists(function ($query): void {
                $query->selectRaw('1')
                    ->from('player_real_time_health')
                    ->whereColumn('player_real_time_health.player_id', 'players.id')
                    ->where('player_real_time_health.notes', 'like', 'synthetic_demo%');
            })
            ->where(function ($query): void {
                $query->whereNull('height')->orWhereNull('weight')
                    ->orWhereNull('preferred_foot')->orWhere('preferred_foot', '');
            })
            ->update([
                'height' => DB::raw('COALESCE(height, 170 + (id % 16))'),
                'weight' => DB::raw('COALESCE(weight, 68 + (id % 15))'),
                'preferred_foot' => DB::raw(
                    "COALESCE(NULLIF(preferred_foot, ''), CASE WHEN id % 4 = 0 THEN 'left' ELSE 'right' END)"
                ),
                'updated_at' => now(),
            ]);
        $this->command?->info("Profils de test complétés : {$updated}.");
    }
}
