<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DopingControlsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $players = DB::table('players')->get();
        
        foreach ($players as $player) {
            // Contrôle inopiné
            DB::table('doping_controls')->insert([
                'player_id' => $player->id,
                'control_type' => 'random',
                'location' => 'Centre d\'entraînement',
                'control_date' => Carbon::now()->subDays(rand(1, 30)),
                'control_time' => Carbon::createFromTime(rand(6, 18), rand(0, 59)),
                'result' => 'negative',
                'notes' => 'Contrôle standard, aucun problème détecté',
                'control_authority' => 'national',
                'sample_id' => 'SMP' . rand(100000, 999999),
                'next_control' => Carbon::now()->addDays(rand(30, 90)),
                'substances_tested' => json_encode([
                    'steroids', 'stimulants', 'diuretics', 'beta_blockers',
                    'cannabinoids', 'epo', 'growth_hormone', 'masking_agents'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Contrôle post-match
            DB::table('doping_controls')->insert([
                'player_id' => $player->id,
                'control_type' => 'post_match',
                'location' => 'Stade de France',
                'control_date' => Carbon::now()->subDays(rand(15, 45)),
                'control_time' => Carbon::createFromTime(22, rand(0, 59)),
                'result' => 'negative',
                'notes' => 'Contrôle après match France vs Italie',
                'control_authority' => 'national',
                'sample_id' => 'SMP' . rand(100000, 999999),
                'next_control' => Carbon::now()->addDays(rand(45, 120)),
                'substances_tested' => json_encode([
                    'steroids', 'stimulants', 'diuretics', 'beta_blockers',
                    'cannabinoids', 'epo', 'growth_hormone'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Contrôle ciblé
            DB::table('doping_controls')->insert([
                'player_id' => $player->id,
                'control_type' => 'targeted',
                'location' => 'Domicile',
                'control_date' => Carbon::now()->subDays(rand(30, 60)),
                'control_time' => Carbon::createFromTime(6, rand(0, 59)),
                'result' => 'negative',
                'notes' => 'Contrôle surprise à 6h du matin',
                'control_authority' => 'national',
                'sample_id' => 'SMP' . rand(100000, 999999),
                'next_control' => Carbon::now()->addDays(rand(60, 150)),
                'substances_tested' => json_encode([
                    'steroids', 'stimulants', 'diuretics', 'beta_blockers',
                    'cannabinoids', 'epo', 'growth_hormone', 'masking_agents'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Contrôle pré-saison
            DB::table('doping_controls')->insert([
                'player_id' => $player->id,
                'control_type' => 'routine',
                'location' => 'Centre médical du club',
                'control_date' => Carbon::now()->subDays(rand(45, 75)),
                'control_time' => Carbon::createFromTime(10, rand(0, 59)),
                'result' => 'negative',
                'notes' => 'Contrôle de routine avant reprise',
                'control_authority' => 'club',
                'sample_id' => 'SMP' . rand(100000, 999999),
                'next_control' => Carbon::now()->addDays(rand(90, 180)),
                'substances_tested' => json_encode([
                    'steroids', 'stimulants', 'diuretics', 'beta_blockers',
                    'cannabinoids', 'epo', 'growth_hormone'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Contrôle UEFA
            DB::table('doping_controls')->insert([
                'player_id' => $player->id,
                'control_type' => 'post_match',
                'location' => 'Centre de contrôle UEFA',
                'control_date' => Carbon::now()->subDays(rand(60, 90)),
                'control_time' => Carbon::createFromTime(23, rand(0, 59)),
                'result' => 'negative',
                'notes' => 'Contrôle Champions League',
                'control_authority' => 'UEFA',
                'sample_id' => 'UEFA' . rand(100000, 999999),
                'next_control' => Carbon::now()->addDays(rand(120, 240)),
                'substances_tested' => json_encode([
                    'steroids', 'stimulants', 'diuretics', 'beta_blockers',
                    'cannabinoids', 'epo', 'growth_hormone', 'masking_agents',
                    'gene_doping'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Contrôle FIFA
            DB::table('doping_controls')->insert([
                'player_id' => $player->id,
                'control_type' => 'post_match',
                'location' => 'Centre de contrôle FIFA',
                'control_date' => Carbon::now()->subDays(rand(90, 120)),
                'control_time' => Carbon::createFromTime(22, rand(0, 59)),
                'result' => 'negative',
                'notes' => 'Contrôle match international',
                'control_authority' => 'FIFA',
                'sample_id' => 'FIFA' . rand(100000, 999999),
                'next_control' => Carbon::now()->addDays(rand(150, 300)),
                'substances_tested' => json_encode([
                    'steroids', 'stimulants', 'diuretics', 'beta_blockers',
                    'cannabinoids', 'epo', 'growth_hormone', 'masking_agents',
                    'gene_doping', 'blood_doping'
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
