<?php

namespace Database\Seeders;

use App\Models\Club;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AlHazemClubAdminSeeder extends Seeder
{
    public function run(): void
    {
        $club = Club::where('name', 'Al-Hazem SC')->firstOrFail();

        // Club users require an explicit tenant context for TenantEnforcer.
        $tenant = Tenant::firstOrCreate(
            ['slug' => 'al-hazem-sc'],
            [
                'name' => 'Al-Hazem SC',
                'display_name' => 'Al-Hazem SC',
                'description' => 'Tenant du club Al-Hazem SC',
                'type' => 'club',
                'status' => 'active',
                'country' => 'SA',
                'timezone' => 'Asia/Riyadh',
                'language' => 'fr',
            ]
        );

        $club->forceFill(['tenant_id' => $tenant->id])->save();

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
                'tenant_id' => $tenant->id,
                'status' => 'active',
                'language' => 'fr',
                'timezone' => 'Asia/Riyadh',
            ]
        );

        $this->command?->info("Compte club_admin créé: {$user->email} (club_id={$club->id}).");
    }
}
