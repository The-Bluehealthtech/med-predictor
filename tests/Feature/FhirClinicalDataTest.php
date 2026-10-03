<?php

namespace Tests\Feature;

use App\Models\FhirPatientLink;
use App\Models\Player;
use App\Models\User;
use App\Services\Fhir\ClinicalDataQuery;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Données cliniques des établissements (IHE QEDm, imagerie FHIR + IHE IID) lues pour le
 * Patient de FIT et les Patients rattachés, dans la page de consultation.
 */
class FhirClinicalDataTest extends TestCase
{
    use DatabaseTransactions;
    use \Tests\Concerns\GrantsSharingConsent;

    private const BASE = 'http://fit-fhir.test/fhir';

    private Player $player;

    private int $clubId;

    protected function setUp(): void
    {
        parent::setUp();
        config(['fhir.base_url' => self::BASE, 'fhir.imaging.iid_viewer_url' => 'https://pacs.example.org/viewer']);
        if (!\Illuminate\Support\Facades\Route::has('health-records.index')) { // route de web.php absente de routes/testing.php
            \Illuminate\Support\Facades\Route::get('/_t/health-records', fn () => 'ok')->name('health-records.index');
            \Illuminate\Support\Facades\Route::getRoutes()->refreshNameLookups();
        }
        $associationId = (int) DB::table('associations')->insertGetId(['name' => 'Fédération QEDm', 'country' => 'Tunisie', 'created_at' => now(), 'updated_at' => now()]);
        $this->clubId = (int) DB::table('clubs')->insertGetId(['name' => 'Club QEDm', 'association_id' => $associationId, 'created_at' => now(), 'updated_at' => now()]);
        $id = DB::table('players')->insertGetId(['name' => 'Samir Ben Ali', 'first_name' => 'Samir', 'last_name' => 'Ben Ali', 'date_of_birth' => '2000-05-14',
            'club_id' => $this->clubId, 'association_id' => $associationId, 'created_at' => now(), 'updated_at' => now()]);
        $this->player = Player::withoutGlobalScopes()->findOrFail($id);
        FhirPatientLink::query()->create(['player_id' => $id, 'role' => 'fit', 'patient_id' => 'fit-1', 'status' => 'linked']);
        FhirPatientLink::query()->create(['player_id' => $id, 'role' => 'external', 'patient_id' => 'lab-3', 'status' => 'linked']);
        FhirPatientLink::query()->create(['player_id' => $id, 'role' => 'external', 'patient_id' => 'emr-9', 'status' => 'rejected']);
        $this->grantSharingConsent($this->player); // partage hors du club consenti (IHE PCF)
    }

    private function doctor(?int $clubId = null): User
    {
        return User::factory()->create(['role' => 'club_medical', 'club_id' => $clubId ?? $this->clubId, 'status' => 'active', 'tenant_id' => 1]);
    }

    private function searchset(array $resources): array
    {
        return ['resourceType' => 'Bundle', 'type' => 'searchset', 'entry' => array_map(fn ($r) => ['resource' => $r], $resources)];
    }

    public function test_lab_results_use_qedm_patient_and_category_and_show_value_range_and_source(): void
    {
        $this->actingAs($this->doctor());
        Http::fake([self::BASE . '/Observation?*' => Http::response($this->searchset([[
            'resourceType' => 'Observation', 'id' => 'o1', 'status' => 'final', 'meta' => ['source' => 'lis-institut-pasteur'],
            'subject' => ['reference' => 'Patient/lab-3'], 'effectiveDateTime' => '2026-09-30T08:15:00+01:00',
            'code' => ['coding' => [['system' => 'http://loinc.org', 'code' => '718-7', 'display' => 'Hemoglobin [Mass/volume] in Blood']]],
            'valueQuantity' => ['value' => 14.2, 'unit' => 'g/dL', 'system' => 'http://unitsofmeasure.org', 'code' => 'g/dL'],
            'referenceRange' => [['low' => ['value' => 13.5, 'unit' => 'g/dL'], 'high' => ['value' => 17.5, 'unit' => 'g/dL']]],
            'interpretation' => [['coding' => [['system' => 'http://terminology.hl7.org/CodeSystem/v3-ObservationInterpretation', 'code' => 'N', 'display' => 'Normal']]]],
        ]]))]);

        [$item] = app(ClinicalDataQuery::class)->fetch($this->player, 'lab');

        $this->assertSame(['Hemoglobin [Mass/volume] in Blood', '14.2 g/dL', ['LOINC 718-7'], 'lis-institut-pasteur', 'Patient/lab-3'],
            [$item['label'], $item['value'], $item['codes'], $item['source'], $item['patient']]);
        $this->assertSame('Référence : 13.5 – 17.5 g/dL · Interprétation : Normal', $item['detail']);
        Http::assertSent(fn (Request $r) => str_contains(urldecode($r->url()), 'patient=Patient/fit-1,Patient/lab-3')
            && str_contains(urldecode($r->url()), 'category=http://terminology.hl7.org/CodeSystem/observation-category|laboratory'));
        $this->assertTrue(DB::table('audit_logs')->where('action', 'clinical_data_query')->where('model_id', $this->player->id)->exists());
    }

    public function test_reports_and_imaging_studies_with_iid_viewer_link(): void
    {
        $this->actingAs($this->doctor());
        Http::fake([
            self::BASE . '/DiagnosticReport?*' => Http::response($this->searchset([[
                'resourceType' => 'DiagnosticReport', 'id' => 'r1', 'status' => 'final', 'issued' => '2026-09-29T10:00:00Z',
                'category' => [['coding' => [['system' => 'http://terminology.hl7.org/CodeSystem/v2-0074', 'code' => 'RAD']]]],
                'code' => ['coding' => [['system' => 'http://loinc.org', 'code' => '24627-2', 'display' => 'Chest CT']]],
                'conclusion' => 'Pas d\'anomalie.', 'presentedForm' => [['contentType' => 'application/pdf', 'url' => 'Binary/9']],
                'subject' => ['reference' => 'Patient/fit-1'],
            ]])),
            self::BASE . '/ImagingStudy?*' => Http::response($this->searchset([[
                'resourceType' => 'ImagingStudy', 'id' => 'i1', 'status' => 'available', 'started' => '2026-09-29T09:30:00Z', 'description' => 'IRM genou droit',
                'identifier' => [['system' => 'urn:dicom:uid', 'value' => 'urn:oid:1.2.840.113619.2.55.3']], 'modality' => [['system' => 'http://dicom.nema.org/resources/ontology/DCM', 'code' => 'MR']],
                'numberOfSeries' => 4, 'numberOfInstances' => 120, 'subject' => ['reference' => 'Patient/fit-1'],
            ]])),
        ]);
        $query = app(ClinicalDataQuery::class);

        [$report] = $query->fetch($this->player, 'reports');
        $this->assertSame('Conclusion : Pas d\'anomalie. · Catégorie : RAD · 1 document(s) joint(s) au compte rendu', $report['detail']);

        [$study] = $query->fetch($this->player, 'imaging');
        $this->assertSame(['IRM genou droit (IRM)', '4 série(s), 120 image(s)'], [$study['label'], $study['detail']]);
        $this->assertSame('https://pacs.example.org/viewer/IHEInvokeImageDisplay?requestType=STUDY&studyUID=1.2.840.113619.2.55.3', $study['viewer']);

        config(['fhir.imaging.iid_viewer_url' => 'http://insecure.example/viewer']);
        $this->assertNull($query->fetch($this->player, 'imaging')[0]['viewer'], 'visionneuse HTTPS uniquement');
    }

    public function test_no_request_without_clinical_identity(): void
    {
        $this->actingAs($this->doctor());
        FhirPatientLink::query()->where('player_id', $this->player->id)->delete();
        Http::fake();

        $this->assertSame([], app(ClinicalDataQuery::class)->fetch($this->player, 'lab'));
        Http::assertNothingSent();
    }

    public function test_page_is_reserved_to_medical_staff_of_the_player(): void
    {
        Http::fake([self::BASE . '/Condition?*' => Http::response($this->searchset([[
            'resourceType' => 'Condition', 'id' => 'c1', 'recordedDate' => '2026-03-01', 'subject' => ['reference' => 'Patient/lab-3'],
            'clinicalStatus' => ['coding' => [['system' => 'http://terminology.hl7.org/CodeSystem/condition-clinical', 'code' => 'active']]],
            'code' => ['coding' => [['system' => 'http://id.who.int/icd/release/11/mms', 'code' => 'CA23', 'display' => 'Asthme']]],
        ]]))]);

        $this->actingAs($this->doctor())->get(route('clinical.external-data', ['player' => $this->player->id, 'tab' => 'problems']))
            ->assertOk()->assertSee('Asthme')->assertSee('CIM-11 CA23')->assertSee('Statut : active');
        // Autre club : le joueur est hors de portée (portée globale des joueurs), donc introuvable.
        $this->actingAs($this->doctor($this->clubId + 1000))->get(route('clinical.external-data', $this->player->id))->assertNotFound();
        $this->actingAs(User::factory()->create(['role' => 'secretary', 'club_id' => $this->clubId, 'status' => 'active', 'tenant_id' => 1]))
            ->get(route('clinical.external-data', $this->player->id))->assertForbidden();
    }
}
