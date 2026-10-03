<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\Document;
use App\Models\FhirOrder;
use App\Models\FhirPatientLink;
use App\Models\Player;
use App\Models\User;
use App\Models\Visit;
use App\Services\Fhir\FhirOrders;
use App\Services\Fhir\ReportIntegration;
use App\Services\MedicalFileStore;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Prescriptions d'examens transmises au serveur FHIR (ServiceRequest), comptes rendus
 * rattachés (DiagnosticReport.basedOn), notifications, abonnement rest-hook et intégration
 * d'un compte rendu au dossier FIT.
 */
class FhirOrdersTest extends TestCase
{
    use DatabaseTransactions;
    use \Tests\Concerns\GrantsSharingConsent;

    private const BASE = 'http://fit-fhir.test/fhir';

    private Player $player;

    private Visit $visit;

    private User $doctor;

    private User $secretary;

    protected function setUp(): void
    {
        parent::setUp();
        config(['fhir.base_url' => self::BASE, 'fhir.webhook_secret' => 'secret-de-test']);
        foreach (['clinical.external-data' => '/_t/ext/{player}', 'secretary.dashboard' => '/_t/sec'] as $name => $uri) {
            if (!Route::has($name)) {
                Route::get($uri, fn () => 'ok')->name($name);
            }
        }
        if (!Route::has('api.fhir.notify')) {
            Route::post('/api/fhir/notify', \App\Http\Controllers\Fhir\SubscriptionNotificationController::class)->name('api.fhir.notify');
        }
        app('router')->getRoutes()->refreshNameLookups();

        $associationId = (int) DB::table('associations')->insertGetId(['name' => 'Fédération Examens', 'country' => 'Tunisie', 'created_at' => now(), 'updated_at' => now()]);
        $clubId = (int) DB::table('clubs')->insertGetId(['name' => 'Club Examens', 'association_id' => $associationId, 'created_at' => now(), 'updated_at' => now()]);
        $id = DB::table('players')->insertGetId(['name' => 'Samir Examen', 'first_name' => 'Samir', 'last_name' => 'Examen', 'date_of_birth' => '2000-05-14',
            'club_id' => $clubId, 'association_id' => $associationId, 'created_at' => now(), 'updated_at' => now()]);
        $this->player = Player::withoutGlobalScopes()->findOrFail($id);
        $teamId = DB::table('teams')->value('id') ?? DB::table('teams')->insertGetId(['name' => 'Équipe Examens', 'club_id' => $clubId, 'created_at' => now(), 'updated_at' => now()]);
        $athleteId = (int) DB::table('athletes')->insertGetId(['name' => 'Samir Examen', 'dob' => '2000-05-14', 'nationality' => 'TN', 'team_id' => $teamId, 'player_id' => $id, 'created_at' => now(), 'updated_at' => now()]);
        $this->doctor = User::factory()->create(['role' => 'club_medical', 'club_id' => $clubId, 'status' => 'active', 'tenant_id' => 1, 'name' => 'Dr Prescripteur']);
        $this->secretary = User::factory()->create(['role' => 'secretary', 'club_id' => $clubId, 'status' => 'active', 'tenant_id' => 1]);
        $appointment = Appointment::create(['athlete_id' => $athleteId, 'created_by' => $this->secretary->id, 'doctor_id' => $this->doctor->id, 'appointment_date' => now(), 'appointment_type' => 'consultation', 'status' => 'Terminé']);
        $this->visit = Visit::create(['athlete_id' => $athleteId, 'appointment_id' => $appointment->id, 'doctor_id' => $this->doctor->id, 'visit_date' => now(), 'visit_type' => 'consultation', 'status' => 'Terminé']);
        FhirPatientLink::query()->create(['player_id' => $id, 'role' => 'fit', 'patient_id' => 'fit-1', 'status' => 'linked']);
        $this->grantSharingConsent($this->player); // partage hors du club consenti (IHE PCF)
    }

    private function sentOrder(string $module = 'laboratory', string $request = 'sr-1'): FhirOrder
    {
        return FhirOrder::query()->create(['visit_id' => $this->visit->id, 'player_id' => $this->player->id, 'module' => $module,
            'service_request_id' => $request, 'status' => 'active', 'sent_at' => now(), 'requested_by' => $this->doctor->id]);
    }

    private function searchset(array $resources): array
    {
        return ['resourceType' => 'Bundle', 'type' => 'searchset', 'entry' => array_map(fn ($r) => ['resource' => $r], $resources)];
    }

    public function test_only_lab_and_imaging_prescriptions_become_service_requests(): void
    {
        Http::fake([self::BASE . '/ServiceRequest?*' => Http::sequence()->push(['resourceType' => 'ServiceRequest', 'id' => 'sr-lab'])->push(['resourceType' => 'ServiceRequest', 'id' => 'sr-mri'])]);

        $orders = app(FhirOrders::class)->dispatch($this->visit, $this->player, ['scat', 'laboratory', 'physiotherapy', 'mri'], 'NFS, CRP ; IRM genou droit', $this->doctor);

        $this->assertSame([['laboratory', 'sr-lab', 'active'], ['mri', 'sr-mri', 'active']], array_map(fn ($o) => [$o->module, $o->fresh()->service_request_id, $o->fresh()->status], $orders));
        Http::assertSent(function (Request $r) use ($orders) {
            $data = $r->data();

            return $r->method() === 'PUT' && $r->url() === self::BASE . '/ServiceRequest?identifier=' . rawurlencode(FhirOrders::IDENTIFIER_SYSTEM . '|' . $orders[0]->id)
                && [$data['status'], $data['intent'], $data['subject']['reference'], $data['requester']['display']] === ['active', 'order', 'Patient/fit-1', 'Dr Prescripteur']
                && $data['category'][0]['coding'][0] === ['system' => 'http://terminology.hl7.org/CodeSystem/v2-0074', 'code' => 'LAB', 'display' => 'Laboratory']
                && $data['code']['text'] === 'Examen de laboratoire — NFS, CRP ; IRM genou droit';
        });
        Http::assertSent(fn (Request $r) => str_contains($r->url(), '/ServiceRequest?') && ($r->data()['category'][0]['coding'][0]['code'] ?? null) === 'RAD');
        $this->assertTrue(DB::table('audit_logs')->where('action', 'service_request_send')->exists());
    }

    public function test_orders_wait_for_the_server_and_failures_are_kept(): void
    {
        Http::fake([self::BASE . '/*' => Http::response(['resourceType' => 'OperationOutcome', 'issue' => [['severity' => 'error', 'code' => 'invalid', 'diagnostics' => 'ServiceRequest.subject: required']]], 422)]);
        config(['fhir.base_url' => null]);
        [$order] = app(FhirOrders::class)->dispatch($this->visit, $this->player, ['imaging'], null, $this->doctor);
        $this->assertSame('pending', $order->fresh()->status);
        Http::assertNothingSent();

        config(['fhir.base_url' => self::BASE]);
        app(FhirOrders::class)->retry($order->fresh());
        $this->assertSame('error', $order->fresh()->status);
        $this->assertStringContainsString('ServiceRequest.subject: required', $order->fresh()->error);
    }

    public function test_final_report_marks_results_received_and_notifies_once(): void
    {
        $lab = $this->sentOrder('laboratory', 'sr-1');
        $mri = $this->sentOrder('mri', 'sr-2');
        Http::fake([self::BASE . '/DiagnosticReport?*' => Http::response($this->searchset([
            ['resourceType' => 'DiagnosticReport', 'id' => 'dr-9', 'status' => 'final', 'issued' => '2026-10-02T09:00:00Z', 'basedOn' => [['reference' => 'ServiceRequest/sr-1']]],
            ['resourceType' => 'DiagnosticReport', 'id' => 'dr-10', 'status' => 'preliminary', 'basedOn' => [['reference' => self::BASE . '/ServiceRequest/sr-2']]],
        ]))]);
        $orders = app(FhirOrders::class);

        $this->assertSame(1, $orders->sync());
        $this->assertSame(['results_received', ['dr-9']], [$lab->fresh()->status, $lab->fresh()->report_ids]);
        $this->assertSame(['active', ['dr-10']], [$mri->fresh()->status, $mri->fresh()->report_ids], 'compte rendu préliminaire rattaché, prescription toujours en attente');
        Http::assertSent(fn (Request $r) => str_contains(urldecode($r->url()), 'based-on=ServiceRequest/sr-1,ServiceRequest/sr-2'));

        $this->assertSame(1, $this->doctor->notifications()->count());
        $this->assertSame(1, $this->secretary->notifications()->count());
        $notification = $this->secretary->notifications()->first()->data;
        $this->assertSame('Compte rendu reçu : Examen de laboratoire — Samir Examen', $notification['message']);

        $this->assertSame(0, $orders->sync(), 'idempotent');
        $this->assertSame(1, $this->doctor->notifications()->count());
    }

    public function test_webhook_requires_the_shared_secret_and_triggers_a_sync(): void
    {
        $order = $this->sentOrder();
        Http::fake([self::BASE . '/DiagnosticReport?*' => Http::response($this->searchset([
            ['resourceType' => 'DiagnosticReport', 'id' => 'dr-1', 'status' => 'final', 'basedOn' => [['reference' => 'ServiceRequest/sr-1']]],
        ]))]);

        $this->postJson('/api/fhir/notify')->assertUnauthorized();
        $this->postJson('/api/fhir/notify', [], ['Authorization' => 'Bearer mauvais'])->assertUnauthorized();
        $this->postJson('/api/fhir/notify', [], ['Authorization' => 'Bearer secret-de-test'])->assertOk()->assertJson(['status' => 'ok', 'completed' => 1]);
        $this->assertSame('results_received', $order->fresh()->status);
    }

    public function test_subscription_is_an_r4_rest_hook_without_payload(): void
    {
        $subscription = app(FhirOrders::class)->subscription('https://fit.tbhc.uk/api/fhir/notify');

        $this->assertSame(['Subscription', 'requested', 'DiagnosticReport?based-on:missing=false', 'rest-hook', 'https://fit.tbhc.uk/api/fhir/notify', ['Authorization: Bearer secret-de-test']],
            [$subscription['resourceType'], $subscription['status'], $subscription['criteria'], $subscription['channel']['type'], $subscription['channel']['endpoint'], $subscription['channel']['header']]);
        $this->assertArrayNotHasKey('payload', $subscription['channel'], 'notification sans contenu : FIT relit lui-même');
    }

    public function test_doctor_integrates_a_report_into_the_visit_once(): void
    {
        $this->actingAs($this->doctor);
        $order = $this->sentOrder();
        $order->update(['status' => 'results_received', 'report_ids' => ['dr-9']]);
        Http::fake([
            self::BASE . '/DiagnosticReport/dr-9' => Http::response(['resourceType' => 'DiagnosticReport', 'id' => 'dr-9', 'status' => 'final', 'subject' => ['reference' => 'Patient/fit-1'],
                'meta' => ['source' => 'lis-institut-pasteur'], 'code' => ['text' => 'Bilan sanguin'],
                'category' => [['coding' => [['system' => 'http://terminology.hl7.org/CodeSystem/v2-0074', 'code' => 'LAB']]]],
                'presentedForm' => [['contentType' => 'application/pdf', 'title' => 'Bilan sanguin', 'data' => base64_encode('%PDF-1.4 compte rendu du laboratoire')]]]),
            self::BASE . '/DiagnosticReport/dr-other' => Http::response(['resourceType' => 'DiagnosticReport', 'id' => 'dr-other', 'subject' => ['reference' => 'Patient/someone']]),
        ]);
        $integration = app(ReportIntegration::class);

        $document = $integration->integrate($this->player, 'dr-9', $this->doctor);

        $this->assertSame([$this->visit->id, 'lab_result', 'Bilan sanguin — lis-institut-pasteur'], [$document->visit_id, $document->document_type, $document->description]);
        $this->assertSame('%PDF-1.4 compte rendu du laboratoire', app(MedicalFileStore::class)->read($document->file_path)['bytes'], 'PDF de l\'établissement conservé tel quel');
        $this->assertSame(['fhir', 'dr-9', true], [$document->metadata['source'], $document->metadata['diagnostic_report_id'], $document->metadata['presented_form']]);
        $this->assertSame(['dr-9'], $integration->integrated($this->player));

        foreach (['dr-9' => 409, 'dr-other' => 403] as $id => $status) {
            try {
                $integration->integrate($this->player, $id, $this->doctor);
                $this->fail("compte rendu {$id} intégré");
            } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
                $this->assertSame($status, $e->getStatusCode());
            }
        }
    }

    public function test_report_without_document_gets_a_fit_summary_pdf_with_its_results(): void
    {
        $this->actingAs($this->doctor);
        Http::fake([
            self::BASE . '/DiagnosticReport/dr-5' => Http::response(['resourceType' => 'DiagnosticReport', 'id' => 'dr-5', 'status' => 'final', 'subject' => ['reference' => 'Patient/fit-1'],
                'code' => ['text' => 'Hémogramme'], 'conclusion' => 'Normal', 'result' => [['reference' => 'Observation/o-1']]]),
            self::BASE . '/Observation/o-1' => Http::response(['resourceType' => 'Observation', 'id' => 'o-1', 'code' => ['coding' => [['system' => 'http://loinc.org', 'code' => '718-7', 'display' => 'Hémoglobine']]],
                'valueQuantity' => ['value' => 14.2, 'unit' => 'g/dL'], 'referenceRange' => [['low' => ['value' => 13.5], 'high' => ['value' => 17.5]]]]),
        ]);

        $document = app(ReportIntegration::class)->integrate($this->player, 'dr-5', $this->doctor);

        $this->assertSame(['medical_report', 'compte-rendu-dr-5.pdf', false], [$document->document_type, $document->file_name, $document->metadata['presented_form']]);
        $this->assertStringStartsWith('%PDF-', app(MedicalFileStore::class)->read($document->file_path)['bytes']);
        Http::assertSent(fn (Request $r) => $r->url() === self::BASE . '/Observation/o-1');
    }
}
