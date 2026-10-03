<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Fhir\FhirClient;
use App\Services\Fhir\FhirException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Sécurité des échanges FHIR : jeton IHE IUA (ITI-71, client credentials) et AuditEvent IHE BALP
 * (MHD ProvideBundle.Audit.Source pour ITI-65) déposés sur le serveur pour chaque échange.
 */
class FhirSecurityTest extends TestCase
{
    use DatabaseTransactions;

    private const BASE = 'http://fit-fhir.test/fhir';

    private const TOKEN = 'https://auth.fit.test/oauth2/token';

    private const BALP = 'https://profiles.ihe.net/ITI/BALP/StructureDefinition/IHE.BasicAudit.';

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        config(['fhir.base_url' => self::BASE, 'app.url' => 'https://fit.tbhc.uk', 'fhir.audit.enabled' => false]);
    }

    private function iua(): void
    {
        config(['fhir.auth.token_url' => self::TOKEN, 'fhir.auth.client_id' => 'fit-app', 'fhir.auth.client_secret' => 's3cret', 'fhir.auth.scope' => 'system/*.read system/*.write']);
    }

    /** AuditEvent déposés pendant le test. */
    private function audits(): array
    {
        return collect(Http::recorded())->map(fn ($pair) => $pair[0])
            ->filter(fn (Request $r) => $r->url() === self::BASE . '/AuditEvent')->map(fn (Request $r) => $r->data())->values()->all();
    }

    public function test_iua_token_is_obtained_cached_and_presented_as_bearer(): void
    {
        $this->iua();
        Http::fake([
            self::TOKEN => Http::response(['access_token' => 'jeton-1', 'token_type' => 'Bearer', 'expires_in' => 3600]),
            self::BASE . '/*' => Http::response(['resourceType' => 'Patient', 'id' => 'p1']),
        ]);
        $client = app(FhirClient::class);

        $client->read('Patient', 'p1');
        $client->read('Patient', 'p1');

        Http::assertSentCount(3); // un seul jeton pour deux lectures
        Http::assertSent(fn (Request $r) => $r->url() === self::TOKEN && $r['grant_type'] === 'client_credentials' && $r['scope'] === 'system/*.read system/*.write'
            && $r->hasHeader('Authorization', 'Basic ' . base64_encode('fit-app:s3cret')));
        Http::assertSent(fn (Request $r) => str_starts_with($r->url(), self::BASE . '/Patient') && $r->hasHeader('Authorization', 'Bearer jeton-1'));
    }

    public function test_expired_token_is_renewed_once_on_401(): void
    {
        $this->iua();
        Http::fake([
            self::TOKEN => Http::sequence()->push(['access_token' => 'ancien', 'token_type' => 'Bearer', 'expires_in' => 3600])->push(['access_token' => 'nouveau', 'token_type' => 'bearer', 'expires_in' => 3600]),
            self::BASE . '/Patient/p1' => Http::sequence()->push(['resourceType' => 'OperationOutcome'], 401)->push(['resourceType' => 'Patient', 'id' => 'p1']),
        ]);

        $this->assertSame('p1', app(FhirClient::class)->read('Patient', 'p1')['id']);
        Http::assertSent(fn (Request $r) => str_ends_with($r->url(), '/Patient/p1') && $r->hasHeader('Authorization', 'Bearer nouveau'));
    }

    public function test_refused_token_request_is_an_explicit_error(): void
    {
        $this->iua();
        Http::fake([self::TOKEN => Http::response(['error' => 'invalid_client'], 401)]);

        $this->expectException(FhirException::class);
        $this->expectExceptionMessage('Jeton d\'accès IUA refusé');
        app(FhirClient::class)->read('Patient', 'p1');
    }

    public function test_patient_read_and_query_are_audited_with_balp_profiles(): void
    {
        config(['fhir.audit.enabled' => true]);
        $this->actingAs(User::factory()->create(['name' => 'Dr Audit']));
        Http::fake([
            self::BASE . '/AuditEvent' => Http::response(['resourceType' => 'AuditEvent', 'id' => 'a1'], 201),
            self::BASE . '/Patient/p1' => Http::response(['resourceType' => 'Patient', 'id' => 'p1']),
            self::BASE . '/Observation?*' => Http::response(['resourceType' => 'Bundle', 'type' => 'searchset']),
        ]);
        $client = app(FhirClient::class);

        $client->read('Patient', 'p1');
        $client->search('Observation', ['patient' => 'Patient/p1,Patient/p2', 'category' => 'laboratory']);

        [$read, $query] = $this->audits();
        $this->assertSame([self::BALP . 'PatientRead'], $read['meta']['profile']);
        $this->assertSame(['R', '0', 'read'], [$read['action'], $read['outcome'], $read['subtype'][0]['code']]);
        $this->assertSame(['110152', '110153', 'IRCP'], array_map(fn ($a) => $a['type']['coding'][0]['code'], $read['agent']), 'lecture : FIT destinataire, serveur source');
        $this->assertSame('Dr Audit', $read['agent'][2]['who']['display']);
        $this->assertTrue($read['agent'][2]['requestor']);
        $this->assertSame([['reference' => 'Patient/p1'], '1'], [$read['entity'][1]['what'], $read['entity'][1]['role']['code']]);
        $requestId = $read['entity'][2]['what']['identifier']['value'];
        Http::assertSent(fn (Request $r) => str_ends_with($r->url(), '/Patient/p1') && $r->hasHeader('X-Request-Id', $requestId));

        $this->assertSame([self::BALP . 'PatientQuery'], $query['meta']['profile']);
        $this->assertSame(['E', '24', 'Patient/p1'], [$query['action'], $query['entity'][0]['role']['code'], $query['entity'][1]['what']['reference']]);
        $this->assertStringStartsWith('GET ' . self::BASE . '/Observation?patient=', base64_decode($query['entity'][0]['query']));
        $this->assertSame(['110153', '110152'], [$query['agent'][0]['type']['coding'][0]['code'], $query['agent'][1]['type']['coding'][0]['code']]);
    }

    public function test_conditional_create_failure_and_mhd_publication_are_audited(): void
    {
        config(['fhir.audit.enabled' => true]);
        Http::fake([
            self::BASE . '/AuditEvent' => Http::response(['resourceType' => 'AuditEvent'], 201),
            self::BASE . '/Patient?*' => Http::response(['resourceType' => 'Patient', 'id' => 'fit-1'], 201),
            self::BASE . '/ServiceRequest?*' => Http::response(['resourceType' => 'OperationOutcome', 'issue' => [['severity' => 'error', 'code' => 'invalid']]], 422),
            self::BASE => Http::response(['resourceType' => 'Bundle', 'type' => 'transaction-response', 'entry' => [['response' => ['location' => 'List/10/_history/1']]]]),
        ]);
        $client = app(FhirClient::class);

        $client->conditionalUpdate(['resourceType' => 'Patient', 'name' => [['family' => 'X']]], ['identifier' => 'sys|1']);
        try {
            $client->conditionalUpdate(['resourceType' => 'ServiceRequest', 'subject' => ['reference' => 'Patient/fit-1']], ['identifier' => 'sys|2']);
        } catch (FhirException) {
        }
        $client->transaction(['resourceType' => 'Bundle', 'type' => 'transaction',
            'meta' => ['profile' => ['https://profiles.ihe.net/ITI/MHD/StructureDefinition/IHE.MHD.UnContained.Comprehensive.ProvideBundle']],
            'entry' => [['resource' => ['resourceType' => 'List', 'subject' => ['reference' => 'Patient/fit-1']]]]]);

        [$create, $failed, $mhd] = $this->audits();
        $this->assertSame([[self::BALP . 'PatientCreate'], 'C', 'Patient/fit-1'], [$create['meta']['profile'], $create['action'], $create['entity'][0]['what']['reference']], 'mise à jour conditionnelle ayant créé le Patient');
        $this->assertArrayNotHasKey('meta', $failed, 'échange refusé : AuditEvent sans profil de succès');
        $this->assertSame(['4', 'U'], [$failed['outcome'], $failed['action']]);
        $this->assertSame(['https://profiles.ihe.net/ITI/MHD/StructureDefinition/IHE.MHD.ProvideBundle.Audit.Source'], $mhd['meta']['profile']);
        $this->assertSame(['110106', 'ITI-65', 'R'], [$mhd['type']['code'], $mhd['subtype'][0]['code'], $mhd['action']]);
        $this->assertSame(['Patient/fit-1', 'List/10', '20'], [$mhd['entity'][0]['what']['reference'], $mhd['entity'][1]['what']['reference'], $mhd['entity'][1]['role']['code']]);
    }

    public function test_audit_failure_never_breaks_the_exchange_and_metadata_is_not_audited(): void
    {
        config(['fhir.audit.enabled' => true]);
        Http::fake([
            self::BASE . '/AuditEvent' => Http::response('indisponible', 503),
            self::BASE . '/metadata' => Http::response(['resourceType' => 'CapabilityStatement', 'fhirVersion' => '4.0.1']),
            self::BASE . '/Patient/p1' => Http::response(['resourceType' => 'Patient', 'id' => 'p1']),
        ]);
        $client = app(FhirClient::class);

        $this->assertSame('4.0.1', $client->capabilities()['fhirVersion']);
        $this->assertCount(0, $this->audits(), 'CapabilityStatement sans donnée de patient');
        $this->assertSame('p1', $client->read('Patient', 'p1')['id']);
        $this->assertCount(1, $this->audits());
    }
}
