<?php

namespace Tests\Feature;

use App\Models\PlayerLicense;
use App\Services\FifaConnect\LicenseRegistrationExport;
use App\Services\FifaConnect\XsdValidator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Test général d'adéquation des licences au FIFA Connect Data Standard 3.3 :
 * traduction en message PersonLocal et validation contre le XSD officiel
 * (FIFA_CONNECT_XSD_PATH ; test ignoré si le paquet n'est pas installé).
 */
class LicenseFifaConnectConformanceTest extends TestCase
{
    use DatabaseTransactions;

    private int $clubId;

    protected function setUp(): void
    {
        parent::setUp();
        if (!is_file(config('services.fifa_connect.xsd_path') . '/registration.xsd') || !is_file(app(XsdValidator::class)->schemaPath('scenarios.xsd'))) {
            $this->markTestSkipped('Paquet XSD FIFA Connect Data Standard 3.3 non installé.');
        }
        config(['licensing.fifa_export.local_language' => null]);
        $associationId = (int) DB::table('associations')->insertGetId(['name' => 'Fédération Conforme', 'country' => 'Tunisie', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('license_scale_settings')->insert(['association_id' => $associationId, 'local_language' => 'fra', 'created_at' => now(), 'updated_at' => now()]);
        $this->clubId = (int) DB::table('clubs')->insertGetId(['name' => 'Club Conforme', 'association_id' => $associationId, 'country_code' => 'TN', 'fifa_connect_id' => 'DEF456B', 'created_at' => now(), 'updated_at' => now()]);
    }

    private function playerLicense(array $player = [], array $license = []): PlayerLicense
    {
        $playerId = DB::table('players')->insertGetId(array_merge(['name' => 'Samir Conforme', 'first_name' => 'Samir', 'last_name' => 'Conforme', 'gender' => 'male',
            'nationality' => 'Tunisia', 'country_of_birth' => 'TN', 'place_of_birth' => 'Tunis', 'date_of_birth' => '2000-01-02', 'fifa_connect_id' => 'ABC123A',
            'club_id' => $this->clubId, 'created_at' => now(), 'updated_at' => now()], $player));

        return PlayerLicense::query()->create(array_merge(['player_id' => $playerId, 'club_id' => $this->clubId, 'registration_type' => 'Player', 'discipline' => 'Football',
            'level' => 'pro', 'registration_nature' => 'Registration', 'status' => 'active', 'season' => '2026-2027',
            'contract_start_date' => '2026-07-01', 'expiry_date' => '2027-06-30'], $license));
    }

    public function test_complete_player_licence_validates_against_the_official_xsd(): void
    {
        $result = app(LicenseRegistrationExport::class)->export($this->playerLicense([], ['registration_nature' => 'Loan']));

        $this->assertSame([], $result['issues']);
        $this->assertStringContainsString('<PersonLocal', $result['xml']);
        $this->assertMatchesRegularExpression('/PlayerRegistration[^>]+Level="pro"/', $result['xml']);
        $this->assertStringContainsString('RegistrationNature="Loan"', $result['xml']);
        $this->assertStringContainsString('Discipline="Football"', $result['xml']);
        $this->assertStringContainsString('Nationality="TN"', $result['xml'], 'nationalité convertie via le référentiel ISO 3166 officiel');
        $this->assertStringContainsString('OrganisationFIFAId="DEF456B"', $result['xml']);
        $this->assertStringContainsString('RegistrationValidTo="2027-06-30"', $result['xml']);
        $this->assertStringContainsString('CountryOfBirth="TN"', $result['xml']);
        $this->assertStringContainsString('PlaceOfBirth="Tunis"', $result['xml']);
        $this->assertStringContainsString('LocalLanguage="fra"', $result['xml'], 'langue des noms : réglage de la fédération');
        $this->assertStringContainsString('LocalCountry="TN"', $result['xml'], 'pays du club (code ISO)');
    }

    public function test_person_language_overrides_the_federation_default(): void
    {
        $result = app(LicenseRegistrationExport::class)->export($this->playerLicense(['local_language' => 'ara']));

        $this->assertStringContainsString('LocalLanguage="ara"', $result['xml'] ?? '');
    }

    public function test_status_mapping_follows_simple_status_type(): void
    {
        $export = app(LicenseRegistrationExport::class);
        $first = $this->playerLicense();
        foreach (['pending' => 'pending', 'justification_requested' => 'pending', 'active' => 'active', 'revoked' => 'inactive', 'expired' => 'inactive'] as $fit => $fifa) {
            $license = $first->replicate()->fill(['status' => $fit]); // en mémoire : l'export ne lit que les attributs
            $license->setRelation('player', $first->player);
            $result = $export->export($license);
            $this->assertStringContainsString("Status=\"{$fifa}\"", $result['xml'] ?? '', "statut FIT {$fit}");
        }
    }

    public function test_official_licence_validates_with_its_fifa_connect_role(): void
    {
        $officialId = (int) DB::table('club_officials')->insertGetId(['club_id' => $this->clubId, 'person_fifa_id' => 'GHJ789C', 'international_first_name' => 'Nabil',
            'international_last_name' => 'Président', 'gender' => 'male', 'date_of_birth' => '1970-03-04', 'nationality' => 'TN', 'country_of_birth' => 'TN', 'place_of_birth' => 'Sfax',
            'registration_type' => 'OrganisationOfficial', 'organisation_official_role' => 'President', 'status' => 'active', 'discipline' => 'Football',
            'registration_valid_from' => '2026-07-01', 'created_at' => now(), 'updated_at' => now()]);
        $license = PlayerLicense::query()->create(['club_official_id' => $officialId, 'club_id' => $this->clubId, 'registration_type' => 'OrganisationOfficial',
            'organisation_official_role' => 'President', 'discipline' => 'Football', 'status' => 'active', 'season' => '2026-2027', 'contract_start_date' => '2026-07-01', 'expiry_date' => '2027-06-30']);

        $result = app(LicenseRegistrationExport::class)->export($license);
        $this->assertSame([], $result['issues']);
        $this->assertMatchesRegularExpression('/OrganisationOfficialRegistration[^>]+OrganisationOfficialRole="President"/', $result['xml']);
    }

    public function test_data_gaps_are_reported_field_by_field(): void
    {
        $result = app(LicenseRegistrationExport::class)->export($this->playerLicense(['fifa_connect_id' => null, 'nationality' => 'Atlantide', 'country_of_birth' => null, 'place_of_birth' => null]));

        $this->assertNull($result['xml']);
        $this->assertArrayHasKey('PersonFIFAId', $result['issues']);
        $this->assertArrayHasKey('PlaceOfBirth', $result['issues']);
        $this->assertArrayHasKey('CountryOfBirth', $result['issues']);
        $this->assertStringContainsString('référentiel ISO 3166 officiel', $result['issues']['Nationality']);

        $bad = app(LicenseRegistrationExport::class)->export($this->playerLicense(['fifa_connect_id' => 'ABCDEFO']));
        $this->assertStringContainsString('format invalide', $bad['issues']['PersonFIFAId'], 'FIFAIdentifier : la lettre O est exclue de la clé de contrôle');

        $legacy = app(LicenseRegistrationExport::class)->export($this->playerLicense([], ['registration_type' => null]));
        $this->assertArrayHasKey('Registration', $legacy['issues']);
    }

    public function test_conformance_command_reports_rate_and_gaps(): void
    {
        DB::table('player_licenses')->delete(); // périmètre du test (transaction annulée ensuite)
        $this->playerLicense(['fifa_connect_id' => null]);

        $code = Artisan::call('fifa:licenses:conformance');
        $output = Artisan::output();
        $this->assertSame(1, $code);
        $this->assertStringContainsString('Licences examinées : 1', $output);
        $this->assertStringContainsString('PersonFIFAId — absent', $output);
    }
}
