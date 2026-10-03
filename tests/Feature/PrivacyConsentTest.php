<?php

namespace Tests\Feature;

use App\Http\Controllers\Privacy\PrivacyPolicyController;
use App\Models\FhirPatientLink;
use App\Models\Player;
use App\Models\PlayerConsent;
use App\Models\PrivacyPolicy;
use App\Models\User;
use App\Services\ApiConnectorState;
use App\Services\Fhir\IpsDocumentSharing;
use App\Services\Privacy\PlayerConsents;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

/**
 * Consentement du joueur au partage hors du club (IHE PCF 1.1.0, Basic Consent) : politique de
 * la fédération versionnée, signature électronique, activation, Consent FHIR, contrôle du partage.
 */
class PrivacyConsentTest extends TestCase
{
    use DatabaseTransactions;

    private const BASE = 'http://fit-fhir.test/fhir';

    private const ADOBE = 'https://api.adobesign.test/api/rest/v6';

    private int $associationId;

    private Player $player;

    private User $secretary;

    protected function setUp(): void
    {
        parent::setUp();
        if (!Route::has('privacy-policies.show')) {
            Route::middleware('web')->get('/privacy-policies/{policy}', [PrivacyPolicyController::class, 'show'])->name('privacy-policies.show');
        }
        if (!Route::has('privacy-policies.store')) {
            Route::middleware(['web', 'auth'])->post('/privacy-policies', [PrivacyPolicyController::class, 'store'])->name('privacy-policies.store');
            Route::middleware(['web', 'auth'])->get('/privacy-policies', [PrivacyPolicyController::class, 'index'])->name('privacy-policies.index');
        }
        if (!Route::has('privacy.consents.document')) {
            Route::get('/_t/consents/{consent}/document', fn () => 'ok')->name('privacy.consents.document');
        }
        app('router')->getRoutes()->refreshNameLookups();
        Storage::fake('local');
        config(['fhir.base_url' => self::BASE, 'services.adobe_sign.base_url' => self::ADOBE, 'services.adobe_sign.access_token' => 'token-adobe',
            'services.document_signatures.disk' => 'local']);
        app(ApiConnectorState::class)->setEnabled('adobe_sign', true);

        $this->associationId = (int) DB::table('associations')->insertGetId(['name' => 'Fédération PCF', 'country' => 'Tunisie', 'created_at' => now(), 'updated_at' => now()]);
        $clubId = (int) DB::table('clubs')->insertGetId(['name' => 'Club PCF', 'association_id' => $this->associationId, 'created_at' => now(), 'updated_at' => now()]);
        $id = DB::table('players')->insertGetId(['name' => 'Yassine Mineur', 'first_name' => 'Yassine', 'last_name' => 'Mineur', 'date_of_birth' => now()->subYears(16)->toDateString(),
            'club_id' => $clubId, 'association_id' => $this->associationId, 'created_at' => now(), 'updated_at' => now()]);
        $this->player = Player::withoutGlobalScopes()->findOrFail($id);
        $this->secretary = User::factory()->create(['role' => 'secretary', 'club_id' => $clubId, 'status' => 'active', 'tenant_id' => 1]);
        FhirPatientLink::query()->create(['player_id' => $id, 'role' => 'fit', 'patient_id' => 'fit-1', 'status' => 'linked']);
    }

    private function policy(int $version = 1): PrivacyPolicy
    {
        $body = str_repeat('Les données de santé du joueur sont partagées à des fins de soins uniquement. ', 3);

        return PrivacyPolicy::query()->create(['association_id' => $this->associationId, 'version' => $version, 'title' => 'Politique PCF',
            'body' => $body, 'body_sha256' => hash('sha256', $body), 'published_at' => now()]);
    }

    private function fakeServices(string $adobeStatus = 'OUT_FOR_SIGNATURE'): void
    {
        Http::fake([
            self::ADOBE . '/transientDocuments' => Http::response(['transientDocumentId' => 'td-1']),
            self::ADOBE . '/agreements' => Http::response(['id' => 'ag-1']),
            self::ADOBE . '/agreements/ag-1/combinedDocument' => Http::response('%PDF-1.7 signé'),
            self::ADOBE . '/agreements/ag-1' => Http::response(['status' => $adobeStatus]),
            self::BASE . '/Consent?*' => Http::response(['resourceType' => 'Consent', 'id' => 'c-1']),
        ]);
    }

    private function prepare(array $overrides = []): PlayerConsent
    {
        return app(PlayerConsents::class)->prepare($this->player, $overrides + ['decision' => 'permit', 'performer_type' => 'guardian', 'performer_name' => 'Amel Mineur',
            'performer_relationship' => 'MTH', 'performer_email' => 'amel@example.org', 'provider' => 'adobe_sign'], $this->secretary);
    }

    public function test_federation_publishes_immutable_versions_readable_publicly(): void
    {
        $admin = User::factory()->create(['role' => 'association_admin', 'association_id' => $this->associationId, 'status' => 'active', 'tenant_id' => 1]);
        $body = str_repeat('Texte de la politique de confidentialité de la fédération. ', 3);
        $this->actingAs($admin)->post(route('privacy-policies.store'), ['title' => 'Politique', 'body' => $body])->assertRedirect();
        $this->actingAs($admin)->post(route('privacy-policies.store'), ['title' => 'Politique', 'body' => $body . ' Mise à jour.'])->assertRedirect();

        $versions = PrivacyPolicy::query()->where('association_id', $this->associationId)->orderBy('version')->get();
        $this->assertSame([1, 2], $versions->pluck('version')->all());
        $this->assertSame([trim($body), hash('sha256', trim($body))], [$versions[0]->body, $versions[0]->body_sha256], 'la version 1 reste inchangée');

        auth()->logout();
        $this->get(route('privacy-policies.show', $versions[1]))->assertOk()->assertSee('version 2')->assertSee('Mise à jour.');
        $this->actingAs(User::factory()->create(['role' => 'club_admin', 'status' => 'active', 'tenant_id' => 1]))
            ->post(route('privacy-policies.store'), ['title' => 'X', 'body' => $body])->assertForbidden();
    }

    public function test_sharing_is_refused_until_the_consent_is_signed(): void
    {
        $this->policy();
        $this->fakeServices();
        $consent = $this->prepare();

        $this->assertSame('pending_signature', $consent->status);
        Http::assertSent(fn (Request $r) => $r->url() === self::ADOBE . '/agreements' && $r['participantSetsInfo'][0]['memberInfos'][0]['email'] === 'amel@example.org');
        $this->assertFalse(app(PlayerConsents::class)->allowsExternalSharing($this->player));
        $this->assertStringContainsString('consentement', implode(' ', app(IpsDocumentSharing::class)->blockers(['state' => 'valid'], $this->player)));
        try {
            app(\App\Services\Fhir\ClinicalDataQuery::class)->fetch($this->player, 'lab');
            $this->fail('données des établissements lues sans consentement');
        } catch (HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
        }

        $this->assertSame('pending_signature', app(PlayerConsents::class)->refresh($consent)->status, 'toujours en attente côté Adobe');
    }

    public function test_signed_consent_activates_sharing_and_is_recorded_as_a_pcf_consent(): void
    {
        $this->policy();
        $previous = PlayerConsent::query()->create(['player_id' => $this->player->id, 'privacy_policy_id' => $this->policy(2)->id, 'decision' => 'deny',
            'status' => 'active', 'performer_type' => 'guardian', 'performer_name' => 'Amel Mineur', 'performer_relationship' => 'MTH', 'signed_at' => now()->subMonth()]);
        $this->fakeServices('SIGNED');
        $consent = app(PlayerConsents::class)->refresh($this->prepare());

        $this->assertSame('active', $consent->status);
        $this->assertSame('superseded', $previous->fresh()->status);
        $this->assertTrue(app(PlayerConsents::class)->allowsExternalSharing($this->player));
        $this->assertSame('c-1', $consent->fhir_consent_id);

        Http::assertSent(function (Request $r) use ($consent) {
            if (!str_starts_with($r->url(), self::BASE . '/Consent?')) {
                return false;
            }
            $c = $r->data();

            return $c['meta']['profile'] === [PlayerConsents::PCF_BASIC] && $c['status'] === 'active'
                && $c['scope']['coding'][0]['code'] === 'patient-privacy' && $c['category'][0]['coding'][0]['code'] === '59284-0'
                && $c['patient']['reference'] === 'Patient/fit-1' && $c['performer'] === [['reference' => '#representant']]
                && $c['contained'][0]['resourceType'] === 'RelatedPerson' && $c['contained'][0]['relationship'][0]['coding'][0]['code'] === 'MTH'
                && $c['policy'][0]['uri'] === route('privacy-policies.show', $consent->privacy_policy_id)
                && $c['provision']['type'] === 'permit' && $c['provision']['purpose'][0]['code'] === 'TREAT'
                && $c['sourceAttachment']['contentType'] === 'application/pdf' && !empty($c['organization'][0]['display']);
        });
        $this->assertTrue(DB::table('audit_logs')->where('action', 'consent_activate')->exists());
    }

    public function test_revoked_denied_or_expired_consents_do_not_allow_sharing(): void
    {
        $this->policy();
        $this->fakeServices('SIGNED');
        $consents = app(PlayerConsents::class);
        $consent = $consents->refresh($this->prepare(['performer_type' => 'player', 'performer_name' => 'Yassine Mineur', 'performer_relationship' => null]));
        $this->assertTrue($consents->allowsExternalSharing($this->player));

        $consents->revoke($consent, $this->secretary);
        $this->assertFalse($consents->allowsExternalSharing($this->player));
        Http::assertSent(fn (Request $r) => str_starts_with($r->url(), self::BASE . '/Consent?') && $r->data()['status'] === 'inactive'
            && $r->data()['performer'] === [['reference' => 'Patient/fit-1']]);

        $denied = $consents->refresh($this->prepare(['decision' => 'deny']));
        $this->assertSame(['active', 'deny'], [$denied->status, $denied->decision]);
        $this->assertFalse($consents->allowsExternalSharing($this->player), 'refus signé');

        $denied->update(['decision' => 'permit', 'period_end' => now()->subDay()->toDateString()]);
        $this->assertFalse($consents->allowsExternalSharing($this->player), 'consentement échu');
    }

    public function test_consent_cannot_be_prepared_without_a_federation_policy(): void
    {
        $this->fakeServices();
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('politique de confidentialité');
        $this->prepare();
    }
}
