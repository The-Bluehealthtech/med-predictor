<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MatchSheetOfficialsTestFixtureSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $password = Hash::make('TestOfficials!2026');
        $referees = ['Arbitre Test Principal', 'Arbitre Test Assistant 1', 'Arbitre Test Assistant 2', 'Arbitre Test VAR'];
        foreach ($referees as $index => $name) {
            DB::table('users')->updateOrInsert(['email' => 'referee.test' . ($index + 1) . '@synthetic.test'], [
                'name' => $name, 'password' => $password, 'role' => 'referee', 'status' => 'active',
                'language' => 'fr', 'created_at' => $now, 'updated_at' => $now,
            ]);
        }
        $created = 0;
        foreach (DB::table('clubs')->whereNotNull('association_id')->orderBy('id')->get() as $club) {
            $email = 'official.club' . $club->id . '@synthetic.test';
            $teamId = DB::table('teams')->where('club_id', $club->id)->orderBy('id')->value('id');
            DB::table('users')->updateOrInsert(['email' => $email], [
                'name' => 'Responsable test - ' . $club->name, 'password' => $password,
                'role' => 'team_official', 'status' => 'active', 'club_id' => $club->id,
                'team_id' => $teamId, 'language' => 'fr', 'created_at' => $now, 'updated_at' => $now,
            ]);
            $created++;
        }
        $this->command?->info("Officiels de feuilles de match : 4 arbitres, {$created} responsables de clubs.");
    }
}
