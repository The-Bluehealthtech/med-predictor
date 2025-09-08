<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SyncTenants extends Command
{
    protected $signature = 'tenants:sync';
    protected $description = 'Create missing tenants and assign tenant_id to users based on role';

    public function handle(): int
    {
        $this->info('Starting tenants sync...');

        // 1) Ensure FTF tenant exists
        $ftfTenantId = DB::table('tenants')->where('slug', 'ftf')->value('id');
        if (!$ftfTenantId) {
            $ftfTenantId = DB::table('tenants')->insertGetId([
                'name' => 'FTF',
                'slug' => 'ftf',
                'display_name' => 'Fédération Tunisienne de Football',
                'type' => 'association',
                'status' => 'active',
                'timezone' => 'UTC',
                'language' => 'fr',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->info("Created FTF tenant id={$ftfTenantId}");
        } else {
            $this->info("FTF tenant exists id={$ftfTenantId}");
        }

        // 2) Create tenants for clubs if missing
        $created = 0;
        $clubs = DB::table('clubs')->select('name')->distinct()->pluck('name');
        foreach ($clubs as $clubName) {
            if (!$clubName) { continue; }
            $slug = Str::slug($clubName);
            $exists = DB::table('tenants')->where('slug', $slug)->exists();
            if (!$exists) {
                DB::table('tenants')->insert([
                    'name' => $clubName,
                    'slug' => $slug,
                    'display_name' => $clubName,
                    'type' => 'club',
                    'status' => 'active',
                    'timezone' => 'UTC',
                    'language' => 'fr',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $created++;
            }
        }
        $this->info("Club tenants created: {$created}");

        // 3) Assign tenant for association_admin and referee -> FTF
        $updatedFTF = DB::update(
            "UPDATE users SET tenant_id = ? WHERE tenant_id IS NULL AND role IN ('association_admin','referee')",
            [$ftfTenantId]
        );
        $this->info("Users assigned to FTF: {$updatedFTF}");

        // 4) Assign tenant for club roles using club_id
        $updatedClubUsers = DB::update(
            "UPDATE users u
             JOIN clubs c ON c.id = u.club_id
             JOIN tenants t ON (t.slug COLLATE utf8mb4_unicode_ci) = (REPLACE(LOWER(c.name), ' ', '-') COLLATE utf8mb4_unicode_ci)
             SET u.tenant_id = t.id
             WHERE u.tenant_id IS NULL AND u.role IN ('club_admin','club_manager','club_medical')"
        );
        $this->info("Club users assigned to their club tenant: {$updatedClubUsers}");

        // 5) Assign tenant for players using player->club
        $updatedPlayers = DB::update(
            "UPDATE users u
             JOIN players p ON p.id = u.player_id
             JOIN clubs c ON c.id = p.club_id
             JOIN tenants t ON (t.slug COLLATE utf8mb4_unicode_ci) = (REPLACE(LOWER(c.name), ' ', '-') COLLATE utf8mb4_unicode_ci)
             SET u.tenant_id = t.id
             WHERE u.tenant_id IS NULL AND u.role = 'player'"
        );
        $this->info("Players assigned to their club tenant: {$updatedPlayers}");

        // 6) Summary of users without tenant
        $rows = DB::table('users')
            ->select('role', DB::raw('COUNT(*) as count'))
            ->whereNull('tenant_id')
            ->groupBy('role')
            ->get();

        if ($rows->isEmpty()) {
            $this->info('All users have tenant_id assigned.');
        } else {
            $this->warn('Users still without tenant_id:');
            foreach ($rows as $row) {
                $this->line(" - {$row->role}: {$row->count}");
            }
        }

        $this->info('Tenants sync completed.');
        return Command::SUCCESS;
    }
}



