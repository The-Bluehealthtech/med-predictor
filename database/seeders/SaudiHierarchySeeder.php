<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SaudiHierarchySeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $confederationId = DB::table('confederations')->updateOrInsert(
            ['short_name' => 'AFC'],
            [
                'name' => 'Confédération asiatique de football',
                'country' => 'Asie',
                'status' => 'active',
                'fifa_sync_status' => 'pending',
                'founded_year' => 1954,
                'confederation_logo_url' => 'https://commons.wikimedia.org/wiki/Special:FilePath/Asian%20Football%20Confederation%20emblem.svg',
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );
        $confederation = DB::table('confederations')->where('short_name', 'AFC')->first();
        $association = DB::table('associations')
            ->where('short_name', 'SAFF')
            ->orWhere('name', 'Fédération saoudienne de football')
            ->first();

        $associationData = [
            'name' => 'Fédération saoudienne de football',
            'short_name' => 'SAFF',
            'country' => 'Arabie saoudite',
            'confederation' => 'AFC',
            'status' => 'active',
            'fifa_id' => 'SAFF',
            'association_logo_url' => 'https://fr.wikipedia.org/wiki/Special:Redirect/file/Logo_F%C3%A9d%C3%A9ration_Arabie_Saoudite_Football.svg',
            'nation_flag_url' => 'https://flagcdn.com/w640/sa.png',
            'updated_at' => $now,
        ];

        if ($association) {
            DB::table('associations')->where('id', $association->id)->update($associationData);
            $associationId = $association->id;
        } else {
            $associationData['created_at'] = $now;
            $associationId = DB::table('associations')->insertGetId($associationData);
        }

        DB::table('clubs')->updateOrInsert(
            ['name' => 'Al-Hazem SC'],
            [
                'short_name' => 'Al-Hazem',
                'country' => 'Arabie saoudite',
                'city' => 'Ar Rass',
                'stadium' => 'Al-Hazem Club Stadium',
                'stadium_name' => 'Al-Hazem Club Stadium',
                'association_id' => $associationId,
                'league' => 'Saudi Pro League',
                'status' => 'active',
                'founded_year' => 1957,
                'website' => 'https://alhazem.sa',
                'logo_url' => 'https://upload.wikimedia.org/wikipedia/commons/6/6e/Al-Hazem_SC_logo.png',
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );

        $club = DB::table('clubs')->where('name', 'Al-Hazem SC')->first();

        DB::table('teams')->updateOrInsert(
            ['club_id' => $club->id, 'name' => 'Al-Hazem SC Senior'],
            [
                'type' => 'first_team',
                'association_id' => $associationId,
                'status' => 'active',
                'season' => '2026/27',
                'competition_level' => 'Saudi Professional League',
                'formation' => '4-3-3',
                'home_ground' => 'Al-Hazem Club Stadium',
                'description' => 'Équipe senior masculine d’Al-Hazem SC.',
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );

        DB::table('competitions')->updateOrInsert(
            ['name' => 'Saudi Professional League', 'season' => '2026/27'],
            [
                'short_name' => 'SPL',
                'type' => 'championship',
                'country' => 'Arabie saoudite',
                'region' => 'Asie',
                'season' => '2026/27',
                'start_date' => '2026-08-01',
                'end_date' => '2027-05-31',
                'registration_deadline' => '2026-07-31',
                'min_teams' => 18,
                'max_teams' => 18,
                'format' => 'round_robin',
                'status' => 'published',
                'description' => 'Championnat national de première division saoudienne.',
                'organizer' => 'Fédération saoudienne de football',
                'association_id' => $associationId,
                'updated_at' => $now,
                'created_at' => $now,
            ]
        );

        $competition = DB::table('competitions')
            ->where('name', 'Saudi Professional League')
            ->where('season', '2026/27')
            ->first();
        DB::table('competition_club')->updateOrInsert(
            ['competition_id' => $competition->id, 'club_id' => $club->id],
            ['registration_date' => $now, 'status' => 'approved', 'updated_at' => $now, 'created_at' => $now]
        );

        $this->command?->info('Hiérarchie AFC → SAFF → Saudi Professional League → Al-Hazem SC créée.');
    }
}
