<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AlHazemClubAdminSeeder extends Seeder
{
    public function run(): void
    {
        $club = Club::where('name', 'Al-Hazem SC')->firstOrFail();

        $user = User::updateOrCreate(
            ['email' => 'admin.alhazem@fit.local'],
            [
                'name' => 'Administrateur Al-Hazem SC',
                'password' => Hash::make('AlHazem2026!'),
                'role' => 'club_admin',
                'entity_type' => 'club',
                'entity_id' => $club->id,
                'club_id' => $club->id,
                'association_id' => $club->association_id,
                'status' => 'active',
                'language' => 'fr',
                'timezone' => 'Asia/Riyadh',
                'permissions' => ['club_only' => true],
            ]
        );

        $this->command?->info("Compte club_admin créé: {$user->email} (club_id={$club->id}).");
    }
}
