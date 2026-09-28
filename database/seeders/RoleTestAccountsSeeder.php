<?php

namespace Database\Seeders;

use App\Models\Association;
use App\Models\Club;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleTestAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $association = Association::query()->first();
        $club = Club::query()->first();
        $password = Hash::make('TestRole2026!');
        $accounts = [
            ['role' => 'system_admin', 'email' => 'test.system-admin@fit.local', 'name' => 'Test System Admin'],
            ['role' => 'dtn', 'email' => 'test.dtn@fit.local', 'name' => 'Test Directeur Technique National'],
            ['role' => 'association_admin', 'email' => 'test.ligue-admin@fit.local', 'name' => 'Test Administrateur Ligue'],
            ['role' => 'association_registrar', 'email' => 'test.registrar@fit.local', 'name' => 'Test Gestionnaire Licences Ligue'],
            ['role' => 'association_medical', 'email' => 'test.medical@fit.local', 'name' => 'Test Médecin Ligue'],
            ['role' => 'referee', 'email' => 'test.referee@fit.local', 'name' => 'Test Arbitre'],
            ['role' => 'club_admin', 'email' => 'test.club-admin@fit.local', 'name' => 'Test Administrateur Club'],
            ['role' => 'club_manager', 'email' => 'test.club-manager@fit.local', 'name' => 'Test Manager Club'],
            ['role' => 'club_medical', 'email' => 'test.club-medical@fit.local', 'name' => 'Test Médecin Club'],
            ['role' => 'team_doctor', 'email' => 'test.team-doctor@fit.local', 'name' => 'Test Médecin Équipe'],
        ];

        foreach ($accounts as $account) {
            User::updateOrCreate(['email' => $account['email']], array_merge($account, [
                'password' => $password,
                'association_id' => $association?->id,
                'club_id' => $club?->id,
            ]));
        }

        $this->command->info('Comptes de test des rôles créés/mis à jour. Mot de passe commun : TestRole2026!');
    }
}
