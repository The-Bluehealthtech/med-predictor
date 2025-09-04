<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Player;

class AuthTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer ou mettre à jour un utilisateur admin
        User::updateOrCreate(
            ['email' => 'admin@fifa.com'],
            [
                'name' => 'Admin FIFA',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        // Créer des utilisateurs joueurs avec mots de passe
        $players = [
            [
                'name' => 'Cristiano Ronaldo',
                'email' => 'cr7@fifa.com',
                'password' => Hash::make('cr7123'),
                'role' => 'player',
                'player_id' => 1, // ID du joueur dans la table players
            ],
            [
                'name' => 'Lionel Messi',
                'email' => 'messi@fifa.com',
                'password' => Hash::make('messi123'),
                'role' => 'player',
                'player_id' => 2, // ID du joueur dans la table players
            ],
            [
                'name' => 'Kylian Mbappé',
                'email' => 'mbappe@fifa.com',
                'password' => Hash::make('mbappe123'),
                'role' => 'player',
                'player_id' => 3, // ID du joueur dans la table players
            ],
        ];

        foreach ($players as $playerData) {
            User::updateOrCreate(
                ['email' => $playerData['email']],
                $playerData
            );
        }

        $this->command->info('✅ Utilisateurs de test créés avec succès !');
        $this->command->info('🔐 Admin: admin@fifa.com / admin123');
        $this->command->info('⚽ CR7: cr7@fifa.com / cr7123');
        $this->command->info('⚽ Messi: messi@fifa.com / messi123');
        $this->command->info('⚽ Mbappé: mbappe@fifa.com / mbappe123');
    }
}
