<?php

namespace Tests\Feature;

use App\Http\Controllers\MedicalSecretaryController;
use App\Models\FhirPatientLink;
use App\Models\Player;
use App\Models\User;
use App\Services\Fhir\FhirException;
use App\Services\Fhir\PatientIdentity;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Identité clinique du joueur sur le serveur FHIR de FIT : flux d'identité PIXm (ITI-104),
 * recherche de candidats PDQm (ITI-78), rattachement confirmé par Patient.link « seealso ».
 */
class FhirPatientIdentityTest extends TestCase
{
    use DatabaseTransactions;
    use \Tests\Concerns\GrantsSharingConsent;

    private const BASE = 'http://fit-fhir.test/fhir';

    private Player $player;

    private User $secretary;

    protected function setUp(): void
    {
        parent::setUp();
        config(['fhir.base_url' => self::BASE]);
        foreach (['secretary.dashboard' => '/_t/secretary', 'logout' => '/_t/logout'] as $name => $uri) {
            Route::get($uri, fn () => 'ok')->name($name);
        }
        Route::get('/_t/identity/{player}', fn () => 'ok')->name('secretary.identity');
        Route::post('/_t/identity/{player}/feed', fn () => 'ok')->name('secretary.identity.feed');
        Route::post('/_t/identity/{player}/decision', fn () => 'ok')->name('secretary.identity.decision');
        Route::post('/_t/consents/{player}', fn () => 'ok')->name('privacy.consents.store');
        foreach (['refresh', 'revoke'] as $action) {
            Route::post("/_t/consent/{consent}/{$action}", fn () => 'ok')->name("privacy.consents.{$action}");
        }
        Route::get('/_t/consent/{consent}/document', fn () => 'ok')->name('privacy.consents.document');
        Route::get('/_t/policy/{policy}', fn () => 'ok')->name('privacy-policies.show');
        app('router')->getRoutes()->refreshNameLookups();

        $associationId = (int) DB::table('associations')->insertGetId(['name' => 'Fédération FHIR', 'country' => 'Tunisie', 'created_at' => now(), 'updated_at' => now()]);
        $clubId = (int) DB::table('clubs')->insertGetId(['name' => 'Club FHIR', 'association_id' => $associationId, 'created_at' => now(), 'updated_at' => now()]);
        $id = DB::table('players')->insertGetId(['name' => 'Samir Ben Ali', 'first_name' => 'Samir', 'last_name' => 'Ben Ali', 'gender' => 'male', 'date_of_birth' => '2000-05-14',
            'fifa_connect_id' => 'ABC123A', 'club_id' => $clubId, 'association_id' => $associationId, 'created_at' => now(), 'updated_at' => now()]);
        $this->player = Player::query()->findOrFail($id);
        $this->secretary = User::factory()->create(['role' => 'secretary', 'club_id' => $clubId, 'status' => 'active']);
        $this->actingAs($this->secretary);
        $this->grantSharingConsent($this->player); // partage hors du club consenti (IHE PCF)
    }

    private function patient(string $id, string $family, string $birthDate, array $identifiers = []): array
    {
        return ['resourceType' => 'Patient', 'id' => $id, 'meta' => ['source' => 'emr-hopital'], 'name' => [['family' => $family, 'given' => ['Samir']]],
            'birthDate' => $birthDate, 'gender' => 'male', 'identifier' => $identifiers];
    }

    private function bundle(array $patients): array
    {
        return ['resourceType' => 'Bundle', 'type' => 'searchset', 'total' => count($patients), 'entry' => array_map(fn ($p) => ['resource' => $p], $patients)];
    }

    public function test_fit_patient_follows_the_pixm_patient_profile(): void
    {
        FhirPatientLink::query()->create(['player_id' => $this->player->id, 'role' => 'external', 'patient_id' => 'emr-7', 'status' => 'linked']);
        FhirPatientLink::query()->create(['player_id' => $this->player->id, 'role' => 'external', 'patient_id' => 'emr-9', 'status' => 'rejected']);

        $patient = app(PatientIdentity::class)->resource($this->player);

        $this->assertSame([PatientIdentity::PIXM_PATIENT_BIRTHDATE], $patient['meta']['profile']);
        $this->assertSame([
            ['use' => 'usual', 'system' => 'https://fit.tbhc.uk/fhir/sid/player', 'value' => (string) $this->player->id],
            ['use' => 'official', 'system' => 'https://fit.tbhc.uk/fhir/sid/fifa-id', 'value' => 'ABC123A'],
        ], $patient['identifier']);
        $this->assertSame([['use' => 'official', 'family' => 'Ben Ali', 'given' => ['Samir']]], $patient['name']);
        $this->assertSame(['male', '2000-05-14', true], [$patient['gender'], $patient['birthDate'], $patient['active']]);
        $this->assertSame([['other' => ['reference' => 'Patient/emr-7'], 'type' => 'seealso']], $patient['link'], 'seul le rattachement confirmé est lié');
    }

    public function test_identity_feed_is_a_conditional_update_on_the_fit_identifier(): void
    {
        Http::fake([self::BASE . '/Patient?*' => Http::response(['resourceType' => 'Patient', 'id' => 'fit-1'], 201)]);

        $this->assertSame('fit-1', app(PatientIdentity::class)->feed($this->player));
        Http::assertSent(fn (Request $r) => $r->method() === 'PUT'
            && $r->url() === self::BASE . '/Patient?identifier=' . rawurlencode('https://fit.tbhc.uk/fhir/sid/player|' . $this->player->id));
        $link = FhirPatientLink::query()->where(['player_id' => $this->player->id, 'role' => 'fit'])->firstOrFail();
        $this->assertSame(['fit-1', null], [$link->patient_id, $link->sync_error]);
        $this->assertTrue(DB::table('audit_logs')->where('action', 'patient_identity_feed')->where('model_id', $this->player->id)->exists());
    }

    public function test_feed_failure_is_recorded_and_never_blocks_the_intake(): void
    {
        Http::fake([self::BASE . '/*' => Http::response(['resourceType' => 'OperationOutcome', 'issue' => [['severity' => 'error', 'code' => 'invalid', 'diagnostics' => 'Patient.name: minimum required = 1']]], 422)]);

        app(PatientIdentity::class)->feedQuietly($this->player);
        $error = FhirPatientLink::query()->where(['player_id' => $this->player->id, 'role' => 'fit'])->value('sync_error');
        $this->assertStringContainsString('Patient.name: minimum required = 1', $error);

        $this->expectException(FhirException::class);
        app(PatientIdentity::class)->feed($this->player);
    }

    public function test_unconfigured_server_sends_nothing(): void
    {
        config(['fhir.base_url' => null]);
        Http::fake();
        app(PatientIdentity::class)->feedQuietly($this->player);
        Http::assertNothingSent();
        $this->assertFalse(FhirPatientLink::query()->where('player_id', $this->player->id)->exists());
    }

    public function test_pdqm_candidates_exclude_fit_own_and_decided_patients(): void
    {
        FhirPatientLink::query()->create(['player_id' => $this->player->id, 'role' => 'fit', 'patient_id' => 'fit-1', 'status' => 'linked']);
        FhirPatientLink::query()->create(['player_id' => $this->player->id, 'role' => 'external', 'patient_id' => 'lab-3', 'status' => 'rejected']);
        Http::fake([
            self::BASE . '/Patient?identifier=ABC123A*' => Http::response($this->bundle([
                $this->patient('fit-1', 'Ben Ali', '2000-05-14'),
                $this->patient('emr-7', 'Ben Ali', '2000-05-14', [['system' => 'urn:oid:1.2.788.1', 'value' => 'ABC123A']]),
            ])),
            self::BASE . '/Patient?family=*' => Http::response($this->bundle([
                $this->patient('emr-7', 'Ben Ali', '2000-05-14'),
                $this->patient('lab-3', 'Ben Ali', '2000-05-14'),
                $this->patient('ris-5', 'Ben Ali', '2000-05-14'),
            ])),
        ]);

        $candidates = app(PatientIdentity::class)->candidates($this->player);

        $this->assertSame(['emr-7' => 'fifa_id', 'ris-5' => 'demographics'], collect($candidates)->pluck('matched_on', 'id')->all());
        $this->assertSame(['urn:oid:1.2.788.1 | ABC123A'], $candidates[0]['identifiers']);
        $this->assertSame('emr-hopital', $candidates[0]['source']);
        Http::assertSent(fn (Request $r) => str_contains($r->url(), 'family=Ben%20Ali') && str_contains($r->url(), 'birthdate=2000-05-14'));
    }

    public function test_confirmed_link_is_published_as_seealso_and_can_be_withdrawn(): void
    {
        FhirPatientLink::query()->create(['player_id' => $this->player->id, 'role' => 'fit', 'patient_id' => 'fit-1', 'status' => 'linked']);
        Http::fake([
            self::BASE . '/Patient/emr-7' => Http::response($this->patient('emr-7', 'Ben Ali', '2000-05-14')),
            self::BASE . '/Patient?*' => Http::response(['resourceType' => 'Patient', 'id' => 'fit-1']),
        ]);
        $identity = app(PatientIdentity::class);

        $identity->confirm($this->player, 'emr-7', $this->secretary, 'demographics');
        Http::assertSent(fn (Request $r) => $r->method() === 'PUT' && ($r->data()['link'] ?? null) === [['other' => ['reference' => 'Patient/emr-7'], 'type' => 'seealso']]);
        $link = FhirPatientLink::query()->where(['player_id' => $this->player->id, 'patient_id' => 'emr-7'])->firstOrFail();
        $this->assertSame(['linked', 'demographics', $this->secretary->id, 'Samir Ben Ali'], [$link->status, $link->matched_on, $link->decided_by, $link->snapshot['name']]);
        $this->assertTrue(DB::table('audit_logs')->where('action', 'patient_identity_link')->exists());

        $identity->reject($this->player, 'emr-7', $this->secretary);
        $this->assertSame('rejected', $link->fresh()->status);
        Http::assertSent(fn (Request $r) => $r->method() === 'PUT' && !array_key_exists('link', $r->data()));
    }

    public function test_identity_page_lists_candidates_for_the_secretary(): void
    {
        FhirPatientLink::query()->create(['player_id' => $this->player->id, 'role' => 'fit', 'patient_id' => 'fit-1', 'status' => 'linked', 'synced_at' => now()]);
        Http::fake([
            self::BASE . '/Patient?identifier=*' => Http::response($this->bundle([])),
            self::BASE . '/Patient?family=*' => Http::response($this->bundle([$this->patient('ris-5', 'Ben Ali', '2000-05-14')])),
        ]);

        view()->share('errors', new \Illuminate\Support\ViewErrorBag); // fourni par le middleware web en temps normal
        $html = app(MedicalSecretaryController::class)->identity(HttpRequest::create('/', 'GET', ['search' => 1]), $this->player)->render();

        $this->assertStringContainsString('Patient/fit-1', $html);
        $this->assertStringContainsString('Patient/ris-5', $html);
        $this->assertStringContainsString('Trouvé par nom et date de naissance', $html);
    }
}
