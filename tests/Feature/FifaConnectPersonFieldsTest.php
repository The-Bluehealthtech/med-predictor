<?php

namespace Tests\Feature;

use App\Services\FifaConnect\IsoCountries;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Référentiel ISO 3166 (nationalités, pays de naissance, pays des clubs) et
 * reprise des données de démonstration pour l'export FIFA Connect PersonLocal.
 */
class FifaConnectPersonFieldsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_iso_reference_resolves_codes_and_french_or_english_names(): void
    {
        $iso = app(IsoCountries::class);

        $this->assertSame('TN', $iso->code('TN'));
        $this->assertSame('TN', $iso->code('Tunisie'));
        $this->assertSame('TN', $iso->code('Tunisia'));
        $this->assertSame('CI', $iso->code("Côte d'Ivoire"));
        $this->assertSame('CI', $iso->code('Ivory Coast'));
        $this->assertSame('BA', $iso->code('Bosnia and Herzegovina'), '« & » des noms CLDR');
        $this->assertSame('GB', $iso->code('England'));
        $this->assertSame('SA', $iso->code('Arabie saoudite'));
        $this->assertNull($iso->code('Atlantide'));
        $this->assertNull($iso->code(''));
        $this->assertSame('Tunisie', $iso->label('TN'));
        $this->assertCount(250, $iso->options());
    }

    public function test_every_nationality_offered_in_fit_resolves_to_an_iso_code(): void
    {
        $iso = app(IsoCountries::class);
        $missing = array_filter(\App\Helpers\NationalityHelper::getNationalities(), fn ($n) => !$iso->code($n));

        $this->assertSame([], array_values($missing));
    }

    public function test_migration_fills_club_countries_federation_language_and_fixes_nationalities(): void
    {
        $tunisia = (int) DB::table('associations')->insertGetId(['name' => 'Fédération Reprise', 'country' => 'Tunisie', 'created_at' => now(), 'updated_at' => now()]);
        $saudi = (int) DB::table('associations')->insertGetId(['name' => 'Fédération Saoudienne', 'country' => 'Arabie saoudite', 'created_at' => now(), 'updated_at' => now()]);
        $clubTn = (int) DB::table('clubs')->insertGetId(['name' => 'Club TN', 'association_id' => $tunisia, 'created_at' => now(), 'updated_at' => now()]);
        $clubSa = (int) DB::table('clubs')->insertGetId(['name' => 'Club SA', 'association_id' => $saudi, 'created_at' => now(), 'updated_at' => now()]);
        $clubSet = (int) DB::table('clubs')->insertGetId(['name' => 'Club déjà codé', 'association_id' => $tunisia, 'country_code' => 'FR', 'created_at' => now(), 'updated_at' => now()]);
        $player = (int) DB::table('players')->insertGetId(['name' => 'Joueur Ivoirien', 'first_name' => 'Joueur', 'last_name' => 'Ivoirien', 'nationality' => 'Ivory Coast',
            'date_of_birth' => '2001-01-01', 'created_at' => now(), 'updated_at' => now()]);
        // La fédération tunisienne de la base de test peut précéder celle-ci : la reprise vise la première.
        DB::table('associations')->where('id', '!=', $tunisia)->where('country', 'like', 'Tunisi%')->update(['country' => 'Autre']);

        (require database_path('migrations/2026_10_03_221000_add_fifa_connect_person_fields.php'))->up();

        $this->assertSame('TN', DB::table('clubs')->where('id', $clubTn)->value('country_code'));
        $this->assertSame('SA', DB::table('clubs')->where('id', $clubSa)->value('country_code'));
        $this->assertSame('FR', DB::table('clubs')->where('id', $clubSet)->value('country_code'), 'valeur saisie conservée');
        $this->assertSame('fra', DB::table('license_scale_settings')->where('association_id', $tunisia)->value('local_language'));
        $this->assertSame("Côte d'Ivoire", DB::table('players')->where('id', $player)->value('nationality'));
    }
}
