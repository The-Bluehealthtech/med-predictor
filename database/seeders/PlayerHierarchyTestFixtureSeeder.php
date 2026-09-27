<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlayerHierarchyTestFixtureSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $teamByClub = [];
        foreach (DB::table('clubs')->orderBy('id')->get() as $club) {
            $team = DB::table('teams')->where('club_id', $club->id)->orderBy('id')->first();
            if (!$team) {
                $teamId = DB::table('teams')->insertGetId([
                    'name' => $club->name . ' Test Team', 'short_name' => 'TEST-' . $club->id,
                    'type' => 'first_team', 'status' => 'active', 'season' => '2026',
                    'club_id' => $club->id, 'association_id' => $club->association_id,
                    'created_at' => $now, 'updated_at' => $now,
                ]);
                $team = DB::table('teams')->find($teamId);
            }
            if ($team->association_id !== $club->association_id) {
                DB::table('teams')->where('id', $team->id)->update(['association_id' => $club->association_id, 'updated_at' => $now]);
            }
            $teamByClub[$club->id] = $team->id;
        }
        $updated = 0;
        foreach (DB::table('players')->whereNull('team_id')->get() as $player) {
            if (isset($teamByClub[$player->club_id])) {
                DB::table('players')->where('id', $player->id)->update([
                    'team_id' => $teamByClub[$player->club_id],
                    'association_id' => DB::table('clubs')->where('id', $player->club_id)->value('association_id'),
                    'updated_at' => $now,
                ]);
                $updated++;
            }
        }
        $this->command?->info("Hiérarchie FIFA complétée : {$updated} joueurs rattachés à leur équipe.");
    }
}
