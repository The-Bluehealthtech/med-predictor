<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SimpleSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Nettoyer les tables existantes
        DB::table('athletes')->truncate();
        DB::table('teams')->truncate();
        DB::table('clubs')->truncate();
        DB::table('associations')->truncate();
        
        // 1. Créer une association simple
        $associationId = DB::table('associations')->insertGetId([
            'name' => 'Association Test',
            'fifa_id' => 'ASSOC_001',
            'country' => 'France',
            'status' => 'active',
            'fifa_version' => '2024.1',
            'fifa_sync_status' => 'synced',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Créer un club simple
        $clubId = DB::table('clubs')->insertGetId([
            'name' => 'Club Test',
            'fifa_connect_id' => 'CLUB_001',
            'association_id' => $associationId,
            'country' => 'France',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 3. Créer une équipe simple
        $teamId = DB::table('teams')->insertGetId([
            'name' => 'Équipe Test',
            'type' => 'first_team',
            'status' => 'active',
            'club_id' => $clubId,
            'association_id' => $associationId,
            'founded_year' => 2020,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 4. Créer des athlètes pour les joueurs existants
        $players = DB::table('players')->get();
        foreach ($players as $player) {
            DB::table('athletes')->insert([
                'name' => $player->first_name . ' ' . $player->last_name,
                'fifa_id' => $player->fifa_connect_id ?? 'FIFA_' . $player->id,
                'dob' => $player->date_of_birth ?? '1990-01-01',
                'nationality' => $player->nationality ?? 'Unknown',
                'team_id' => $teamId,
                'position' => $player->position ?? 'Unknown',
                'jersey_number' => (string)$player->id,
                'gender' => 'male',
                'blood_type' => 'A+',
                'active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Simple seeder completed successfully!');
        $this->command->info("Created: Association ID {$associationId}, Club ID {$clubId}, Team ID {$teamId}");
        $this->command->info('Athletes created for ' . $players->count() . ' players');
    }
}
