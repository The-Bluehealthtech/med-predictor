<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Athlete;
use App\Models\Player;

class AthleteSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing athletes first
        Athlete::truncate();
        
        // Récupérer tous les joueurs existants
        $players = Player::all();
        
        if ($players->isEmpty()) {
            $this->command->warn('Aucun joueur trouvé. Créez d\'abord des joueurs avec PlayerSeeder.');
            return;
        }

        // Créer un athlète pour chaque joueur
        foreach ($players as $player) {
            Athlete::create([
                'name' => $player->first_name . ' ' . $player->last_name,
                'fifa_id' => $player->fifa_connect_id ?? 'FIFA_' . $player->id,
                'dob' => $player->date_of_birth ?? '1990-01-01',
                'nationality' => $player->nationality ?? 'Unknown',
                'position' => $player->position ?? 'Unknown',
                'jersey_number' => $player->id,
                'gender' => 'male',
                'blood_type' => 'A+', // Valeur par défaut
                'team_id' => $player->club_id ?? 1,
                'active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Athletes seeded successfully!');
        $this->command->info('Created ' . $players->count() . ' athletes');
    }
}







