<?php

namespace Tests\Feature;

use App\Models\FhirDocument;
use App\Models\FhirPatientLink;
use App\Models\Player;
use App\Models\User;
use App\Services\Fhir\FhirException;
use App\Services\Fhir\IpsDocumentSharing;
use App\Services\Passports\IpsFhirBundle;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Partage de l'IPS selon IHE sIPS : RxNorm et alerte AMA dans l'IPS, validation contre
 * Bundle-uv-ips, publication MHD ITI-65 (UnContained Comprehensive), recherche ITI-67,
 * lecture ITI-68 limitée au serveur de FIT et aux Patients du joueur.
 */
class FhirIpsSharingTest extends TestCase
{
    use DatabaseTransactions;

    private const BASE = 'http://fit-fhir.test/fhir';

    private Player $player;

    private User $doctor;

    protected function setUp(): void
    {
        parent::setUp();
        config(['fhir.base_url' => self::BASE, 'fhir.document_sharing.source_oid' => '1.3.6.1.4.1.99999.1']);
        $id = DB::table('players')->insertGetId(['name' => 'Samir Ben Ali', 'first_name' => 'Samir', 'last_name' => 'Ben Ali', 'gender' => 'male',
            'date_of_birth' => '2000-05-14', 'created_at' => now(), 'updated_at' => now()]);
        $this->player = Player::withoutGlobalScopes()->findOrFail($id);
        $this->doctor = User::factory()->create(['role' => 'club_medical', 'status' => 'active']);
        $this->actingAs($this->doctor);
        FhirPatientLink::query()->create(['player_id' => $id, 'role' => 'fit', 'patient_id' => 'fit-1', 'status' => 'linked']);
    }

    private function summary(): array
    {
        return [
            'document' => ['id' => 'b9c1f0a2-0000-4000-8000-000000000001', 'purpose' => 'general', 'purpose_label' => 'Suivi médical', 'generated_at' => now(), 'author' => 'Dr Test', 'custodian' => 'Club FHIR'],
            'patient' => ['id' => $this->player->id, 'name' => 'Samir Ben Ali', 'birth_date' => '2000-05-14', 'fifa_connect_id' => null],
            'sections' => [
                'medications' => [['label' => 'Salbutamol 100 µg', 'detail' => '2 bouffées', 'status' => 'active', 'date' => '2026-09-01', 'rxcui' => '435',
                    'antidoping' => ['version' => '2025', 'categories' => ['S3. BÊTA-2 AGONISTES']]]],
                'problems' => [['label' => 'Asthme', 'code' => 'CIM-11 CA23', 'date' => '2026-01-10', 'detail' => null]],
            ],
        ];
    }

    private function ips(): array
    {
        return app(IpsFhirBundle::class)->build($this->summary(), ['state' => 'valid', 'attestation' => (object) ['id' => 7, 'signed_at' => now(), 'signer_name' => 'Dr Test']]);
    }

    private function attestation(): array
    {
        return ['state' => 'valid', 'attestation' => (object) ['id' => 7, 'signed_at' => now(), 'signer_name' => 'Dr Test']];
    }

    public function test_ips_medications_carry_rxnorm_and_the_wada_alert_as_text(): void
    {
        $statement = collect($this->ips()['entry'])->firstWhere('resource.resourceType', 'MedicationStatement')['resource'];

        $this->assertSame([['system' => 'http://www.nlm.nih.gov/research/umls/rxnorm', 'code' => '435', 'display' => 'Salbutamol 100 µg']], $statement['medicationCodeableConcept']['coding']);
        $this->assertStringContainsString('liste des interdictions AMA 2025 : S3. BÊTA-2 AGONISTES', $statement['note'][0]['text']);
    }

    public function test_publication_requires_server_source_oid_and_valid_attestation(): void
    {
        $sharing = app(IpsDocumentSharing::class);
        $this->assertSame([], $sharing->blockers($this->attestation()));

        config(['fhir.base_url' => null, 'fhir.document_sharing.source_oid' => 'FIT']);
        $blockers = $sharing->blockers(['state' => 'outdated']);
        $this->assertCount(3, $blockers);
        $this->assertStringContainsString('FIT_FHIR_SOURCE_OID', $blockers[1]);
    }

    public function test_provide_bundle_follows_mhd_uncontained_comprehensive_and_sips(): void
    {
        $ips = $this->ips();
        $previous = FhirDocument::query()->create(['player_id' => $this->player->id, 'purpose' => 'general', 'document_reference_id' => 'dr-old',
            'master_identifier' => 'urn:uuid:old', 'published_at' => now()->subDay()]);

        $bundle = app(IpsDocumentSharing::class)->provideBundle($ips, 'fit-1', $this->attestation(), $previous);
        [$submission, $reference, $document] = array_column($bundle['entry'], 'resource');
        $mhd = 'https://profiles.ihe.net/ITI/MHD/StructureDefinition/';

        $this->assertSame(['transaction', [$mhd . 'IHE.MHD.UnContained.Comprehensive.ProvideBundle']], [$bundle['type'], $bundle['meta']['profile']]);
        $this->assertSame([$mhd . 'IHE.MHD.UnContained.Comprehensive.SubmissionSet'], $submission['meta']['profile']);
        $this->assertSame('urn:oid:1.3.6.1.4.1.99999.1', collect($submission['extension'])->firstWhere('url', $mhd . 'ihe-sourceId')['valueIdentifier']['value']);
        $this->assertSame('60591-5', collect($submission['extension'])->firstWhere('url', $mhd . 'ihe-designationType')['valueCodeableConcept']['coding'][0]['code']);
        $this->assertSame(['Patient/fit-1', $bundle['entry'][1]['fullUrl']], [$submission['subject']['reference'], $submission['entry'][0]['item']['reference']]);

        $this->assertSame([$mhd . 'IHE.MHD.UnContained.Comprehensive.DocumentReference'], $reference['meta']['profile']);
        $this->assertSame($ips['identifier']['value'], $reference['masterIdentifier']['value'], 'uniqueId = Bundle.identifier');
        $this->assertSame(['system' => 'urn:ietf:rfc:3986', 'code' => 'http://hl7.org/fhir/uv/ips/StructureDefinition/Bundle-uv-ips'], $reference['content'][0]['format'], 'formatCode imposé par sIPS');
        $attachment = $reference['content'][0]['attachment'];
        $this->assertSame([$bundle['entry'][2]['fullUrl'], 'application/fhir+json', 'fr-FR'], [$attachment['url'], $attachment['contentType'], $attachment['language']]);
        $json = json_encode($ips, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $this->assertSame([strlen($json), base64_encode(sha1($json, true))], [$attachment['size'], $attachment['hash']]);
        $this->assertSame('R', $reference['securityLabel'][0]['coding'][0]['code']);
        $this->assertSame([['code' => 'replaces', 'target' => ['reference' => 'DocumentReference/dr-old']]], $reference['relatesTo']);
        $this->assertSame('Dr Test', $reference['authenticator']['display']);
        foreach (['facilityType', 'practiceSetting', 'sourcePatientInfo'] as $field) {
            $this->assertArrayHasKey($field, $reference['context'], "context.{$field} obligatoire (Comprehensive)");
        }
        $this->assertSame($ips, $document);
    }

    public function test_invalid_ips_is_never_published(): void
    {
        Http::fake([self::BASE . '/Bundle/$validate*' => Http::response(['resourceType' => 'OperationOutcome',
            'issue' => [['severity' => 'error', 'code' => 'invariant', 'diagnostics' => 'Bundle.entry[0]: Composition.section: minimum required = 1']]], 422)]);

        try {
            app(IpsDocumentSharing::class)->publish($this->player, $this->ips(), $this->attestation(), 'general', $this->doctor);
            $this->fail('IPS non conforme publié');
        } catch (FhirException $e) {
            $this->assertStringContainsString('Composition.section', implode(' ', $e->issues()));
        }
        Http::assertNotSent(fn (Request $r) => $r->url() === self::BASE);
        $this->assertFalse(FhirDocument::query()->where('player_id', $this->player->id)->exists());
    }

    public function test_publication_records_the_document_and_supersedes_the_previous_one(): void
    {
        $previous = FhirDocument::query()->create(['player_id' => $this->player->id, 'purpose' => 'general', 'document_reference_id' => 'dr-old',
            'master_identifier' => 'urn:uuid:old', 'published_at' => now()->subDay()]);
        Http::fake([
            self::BASE . '/Bundle/$validate*' => Http::response(['resourceType' => 'OperationOutcome', 'issue' => [['severity' => 'information', 'code' => 'informational', 'diagnostics' => 'All OK']]]),
            self::BASE . '/DocumentReference/dr-old' => Http::sequence()
                ->push(['resourceType' => 'DocumentReference', 'id' => 'dr-old', 'status' => 'current'])
                ->push(['resourceType' => 'DocumentReference', 'id' => 'dr-old', 'status' => 'superseded']),
            self::BASE => Http::response(['resourceType' => 'Bundle', 'type' => 'transaction-response', 'entry' => [
                ['response' => ['status' => '201 Created', 'location' => 'List/10/_history/1']],
                ['response' => ['status' => '201 Created', 'location' => 'DocumentReference/dr-new/_history/1']],
                ['response' => ['status' => '201 Created', 'location' => 'Bundle/12/_history/1']],
            ]]),
        ]);

        $document = app(IpsDocumentSharing::class)->publish($this->player, $this->ips(), $this->attestation(), 'general', $this->doctor);

        $this->assertSame(['dr-new', 'current', $previous->id, 7], [$document->document_reference_id, $document->status, $document->replaces_id, $document->attestation_id]);
        $this->assertSame('superseded', $previous->fresh()->status);
        Http::assertSent(fn (Request $r) => $r->method() === 'PUT' && $r->url() === self::BASE . '/DocumentReference/dr-old' && $r->data()['status'] === 'superseded');
        Http::assertSent(fn (Request $r) => $r->method() === 'POST' && $r->url() === self::BASE && ($r->data()['type'] ?? null) === 'transaction');
        $this->assertTrue(DB::table('audit_logs')->where('action', 'ips_publish')->where('model_id', $this->player->id)->exists());
    }

    public function test_find_searches_ips_documents_of_fit_and_linked_patients(): void
    {
        FhirPatientLink::query()->create(['player_id' => $this->player->id, 'role' => 'external', 'patient_id' => 'emr-7', 'status' => 'linked']);
        FhirPatientLink::query()->create(['player_id' => $this->player->id, 'role' => 'external', 'patient_id' => 'lab-3', 'status' => 'rejected']);
        FhirDocument::query()->create(['player_id' => $this->player->id, 'purpose' => 'general', 'document_reference_id' => 'dr-fit', 'master_identifier' => 'urn:uuid:x', 'published_at' => now()]);
        Http::fake([self::BASE . '/DocumentReference?*' => Http::response(['resourceType' => 'Bundle', 'entry' => [
            ['resource' => ['resourceType' => 'DocumentReference', 'id' => 'dr-fit', 'date' => '2026-10-01', 'subject' => ['reference' => 'Patient/fit-1']]],
            ['resource' => ['resourceType' => 'DocumentReference', 'id' => 'dr-emr', 'date' => '2026-09-01', 'author' => [['display' => 'Hôpital Charles Nicolle']], 'subject' => ['reference' => 'Patient/emr-7'],
                'content' => [['attachment' => ['title' => 'Patient Summary']]]]],
        ]])]);

        $documents = app(IpsDocumentSharing::class)->find($this->player);

        $this->assertSame([['dr-fit', true], ['dr-emr', false]], array_map(fn ($d) => [$d['id'], $d['own']], $documents));
        $this->assertSame('Hôpital Charles Nicolle', $documents[1]['author']);
        Http::assertSent(fn (Request $r) => str_contains(urldecode($r->url()), 'patient=Patient/fit-1,Patient/emr-7')
            && str_contains(urldecode($r->url()), 'format=urn:ietf:rfc:3986|http://hl7.org/fhir/uv/ips/StructureDefinition/Bundle-uv-ips')
            && str_contains($r->url(), 'status=current'));
    }

    public function test_retrieve_reads_only_fit_server_documents_of_the_player(): void
    {
        Http::fake([
            self::BASE . '/DocumentReference/dr-ok' => Http::response(['resourceType' => 'DocumentReference', 'id' => 'dr-ok', 'subject' => ['reference' => 'Patient/fit-1'],
                'content' => [['attachment' => ['url' => self::BASE . '/Bundle/12']]]]),
            self::BASE . '/DocumentReference/dr-other' => Http::response(['resourceType' => 'DocumentReference', 'id' => 'dr-other', 'subject' => ['reference' => 'Patient/someone'],
                'content' => [['attachment' => ['url' => 'Bundle/13']]]]),
            self::BASE . '/DocumentReference/dr-ext' => Http::response(['resourceType' => 'DocumentReference', 'id' => 'dr-ext', 'subject' => ['reference' => 'Patient/fit-1'],
                'content' => [['attachment' => ['url' => 'https://elsewhere.example/Bundle/1']]]]),
            self::BASE . '/Bundle/12' => Http::response($this->ips()),
        ]);
        $sharing = app(IpsDocumentSharing::class);

        $ips = $sharing->retrieve($this->player, 'dr-ok');
        $sections = collect($ips['sections'])->pluck('items', 'title');
        $this->assertSame(['Salbutamol 100 µg (RxNorm 435)'], $sections['Medication summary']);
        $this->assertSame(['Asthme (CIM-11 CA23)'], $sections['Problem list']);
        $this->assertTrue(DB::table('audit_logs')->where('action', 'ips_retrieve')->exists());

        foreach (['dr-other' => 403, 'dr-ext' => 422] as $id => $status) {
            try {
                $sharing->retrieve($this->player, $id);
                $this->fail("document {$id} lu");
            } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
                $this->assertSame($status, $e->getStatusCode());
            }
        }
        Http::assertNotSent(fn (Request $r) => str_contains($r->url(), 'elsewhere.example'));
    }
}
