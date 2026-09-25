<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Vérifier si l'utilisateur admin existe déjà
        $admin = User::where('email', 'admin@fifa.com')->first();

        if (!$admin) {
            // Créer un nouvel utilisateur admin
            $admin = User::create([
                'name' => 'System Administrator',
                'email' => 'admin@fifa.com',
                'password' => Hash::make('Admin123!'),
                'role' => 'system_admin',
                'entity_type' => 'system',
                'entity_id' => 1,
                'association_id' => 1,
                'club_id' => 1,
                'fifa_connect_id' => 'ADMIN001',
                'phone' => '+1234567890',
                'preferences' => json_encode(['theme' => 'dark', 'language' => 'fr']),
                'last_login_at' => now(),
                'status' => 'active',
                'login_count' => 0,
                'timezone' => 'UTC',
                'language' => 'fr',
                'notifications_email' => true,
                'notifications_sms' => false
            ]);
            
            $this->command->info('✅ Utilisateur System Admin créé avec succès!');
            $this->command->info('📧 Email: admin@fifa.com');
            $this->command->info('🔑 Mot de passe: Admin123!');
            $this->command->info('👤 ID: ' . $admin->id);
            $this->command->info('🔐 Rôle: ' . $admin->role);
        } else {
            $this->command->info('⚠️ L\'utilisateur admin existe déjà!');
            $this->command->info('📧 Email: ' . $admin->email);
            $this->command->info('👤 ID: ' . $admin->id);
            $this->command->info('🔐 Rôle: ' . $admin->role);
            
            // Mettre à jour le mot de passe et le rôle si nécessaire
            $admin->update([
                'password' => Hash::make('Admin123!'),
                'role' => 'system_admin'
            ]);
            $this->command->info('🔄 Mot de passe mis à jour!');
        }
    }
}
