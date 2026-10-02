<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/** Conversion des licences de démonstration existantes au format FIFA Connect (cas observés en prod). */
class LicenseDemoConversionTest extends TestCase
{
    use DatabaseTransactions;

    public function test_demo_licences_are_converted_to_fifa_connect_registrations(): void
    {
        $clubId = (int) DB::table('clubs')->insertGetId(['name' => 'Club Conversion', 'created_at' => now(), 'updated_at' => now()]);
        $player = fn (string $dob, ?string $gender = null) => (int) DB::table('players')->insertGetId(['name' => 'Démo', 'first_name' => 'Démo', 'last_name' => 'Joueur',
            'date_of_birth' => $dob, 'gender' => $gender, 'club_id' => $clubId, 'created_at' => now(), 'updated_at' => now()]);
        $license = fn (int $playerId, array $values) => (int) DB::table('player_licenses')->insertGetId($values + ['player_id' => $playerId, 'club_id' => $clubId, 'status' => 'active', 'created_at' => now(), 'updated_at' => now()]);

        $senior = $player('1996-05-01');
        $young = $player('2010-03-10', 'male');
        $a = $license($senior, ['license_type' => 'player', 'season' => '2026-2027', 'contract_start_date' => '2026-07-01', 'expiry_date' => '2027-06-30']);
        $b = $license($young, ['license_type' => 'professional', 'season' => '2026', 'contract_start_date' => '2024-07-01', 'expiry_date' => '2027-06-30']);
        $c = $license($senior, ['license_type' => 'professional', 'season' => null, 'contract_start_date' => '2026-09-28', 'expiry_date' => '2027-06-28', 'status' => 'pending']);

        (require base_path('database/migrations/2026_10_03_140000_convert_demo_licenses_to_fifa_connect.php'))->up();

        $rows = DB::table('player_licenses')->whereIn('id', [$a, $b, $c])->get()->keyBy('id');
        foreach ($rows as $row) {
            $this->assertSame(['Player', 'Football', 'pro', 'Registration', 'male', '2026-2027'], [$row->registration_type, $row->discipline, $row->level, $row->registration_nature, $row->gender, $row->season]);
        }
        $this->assertSame('SENIOR', $rows[$a]->age_category);
        $this->assertSame('U17', $rows[$b]->age_category, '16 ans au 01/01/2027');
        $this->assertSame('2026-07-01', substr($rows[$b]->contract_start_date, 0, 10), 'début ramené au début de saison');
        $this->assertSame('2027-06-28', substr($rows[$c]->expiry_date, 0, 10));
        $this->assertSame('male', DB::table('players')->where('id', $senior)->value('gender'), 'genre renseigné sur le joueur');

        // Rejouable : une seconde exécution ne change rien.
        DB::table('player_licenses')->where('id', $a)->update(['level' => 'amateur']);
        (require base_path('database/migrations/2026_10_03_140000_convert_demo_licenses_to_fifa_connect.php'))->up();
        $this->assertSame('amateur', DB::table('player_licenses')->where('id', $a)->value('level'));
    }
}
