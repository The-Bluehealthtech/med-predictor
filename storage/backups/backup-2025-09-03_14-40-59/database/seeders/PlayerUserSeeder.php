<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Joueur;
use Illuminate\Support\Facades\Hash;

class PlayerUserSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer les 2 premiers joueurs
        $joueur1 = Joueur::find(1); // Youssef Ben Salah
        $joueur2 = Joueur::find(2); // Moussa Diallo

        if ($joueur1) {
            $user1 = User::where('email', 'youssef.bensalah@fifa.com')->first();

            if (!$user1) {
                $user1 = User::create([
                    'name' => $joueur1->prenom . ' ' . $joueur1->nom,
                    'email' => 'youssef.bensalah@fifa.com',
                    'password' => Hash::make('Joueur123!'),
                    'role' => 'player',
                    'entity_type' => 'player',
                    'entity_id' => $joueur1->id,
                    'association_id' => 1,
                    'club_id' => 1,
                    'team_id' => 1,
                    'fifa_connect_id' => 'PLAYER001',
                    'phone' => '+212612345678',
                    'preferences' => json_encode(['theme' => 'light', 'language' => 'fr']),
                    'last_login_at' => now(),
                    'status' => 'active',
                    'login_count' => 0,
                    'timezone' => 'Africa/Casablanca',
                    'language' => 'fr',
                    'notifications_email' => true,
                    'notifications_sms' => false,
                    'player_id' => $joueur1->id
                ]);
                $this->command->info('✅ Compte joueur 1 créé avec succès!');
                $this->command->info('📧 Email: youssef.bensalah@fifa.com');
                $this->command->info('🔑 Mot de passe: Joueur123!');
                $this->command->info('👤 ID: ' . $user1->id);
                $this->command->info('🔐 Rôle: ' . $user1->role);
                $this->command->info('⚽ Joueur: ' . $joueur1->prenom . ' ' . $joueur1->nom);
            } else {
                $this->command->info('⚠️ L\'utilisateur joueur 1 existe déjà!');
                $this->command->info('📧 Email: ' . $user1->email);
                $this->command->info('👤 ID: ' . $user1->id);
                $user1->update([
                    'password' => Hash::make('Joueur123!'),
                    'role' => 'player',
                    'entity_type' => 'player',
                    'entity_id' => $joueur1->id,
                    'player_id' => $joueur1->id
                ]);
                $this->command->info('🔄 Compte joueur 1 mis à jour!');
            }
        }

        if ($joueur2) {
            $user2 = User::where('email', 'moussa.diallo@fifa.com')->first();

            if (!$user2) {
                $user2 = User::create([
                    'name' => $joueur2->prenom . ' ' . $joueur2->nom,
                    'email' => 'moussa.diallo@fifa.com',
                    'password' => Hash::make('Joueur456!'),
                    'role' => 'player',
                    'entity_type' => 'player',
                    'entity_id' => $joueur2->id,
                    'association_id' => 1,
                    'club_id' => 1,
                    'team_id' => 1,
                    'fifa_connect_id' => 'PLAYER002',
                    'phone' => '+22501234567',
                    'preferences' => json_encode(['theme' => 'dark', 'language' => 'fr']),
                    'last_login_at' => now(),
                    'status' => 'active',
                    'login_count' => 0,
                    'timezone' => 'Africa/Abidjan',
                    'language' => 'fr',
                    'notifications_email' => true,
                    'notifications_sms' => true,
                    'player_id' => $joueur2->id
                ]);
                $this->command->info('✅ Compte joueur 2 créé avec succès!');
                $this->command->info('📧 Email: moussa.diallo@fifa.com');
                $this->command->info('🔑 Mot de passe: Joueur456!');
                $this->command->info('👤 ID: ' . $user2->id);
                $this->command->info('🔐 Rôle: ' . $user2->role);
                $this->command->info('⚽ Joueur: ' . $joueur2->prenom . ' ' . $joueur2->nom);
            } else {
                $this->command->info('⚠️ L\'utilisateur joueur 2 existe déjà!');
                $this->command->info('📧 Email: ' . $user2->email);
                $this->command->info('👤 ID: ' . $user2->id);
                $user2->update([
                    'password' => Hash::make('Joueur456!'),
                    'role' => 'player',
                    'entity_type' => 'player',
                    'entity_id' => $joueur2->id,
                    'player_id' => $joueur2->id
                ]);
                $this->command->info('🔄 Compte joueur 2 mis à jour!');
            }
        }

        $this->command->info('');
        $this->command->info('🎯 Résumé des comptes joueur créés:');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->info('1️⃣ Youssef Ben Salah');
        $this->command->info('   📧 Email: youssef.bensalah@fifa.com');
        $this->command->info('   🔑 Mot de passe: Joueur123!');
        $this->command->info('   🌍 Pays: Maroc');
        $this->command->info('');
        $this->command->info('2️⃣ Moussa Diallo');
        $this->command->info('   📧 Email: moussa.diallo@fifa.com');
        $this->command->info('   🔑 Mot de passe: Joueur456!');
        $this->command->info('   🌍 Pays: Côte d\'Ivoire');
        $this->command->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }
}












