<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\FhirSetupController;
use App\Models\Appointment;
use App\Models\FhirOrder;
use App\Models\FhirPatientLink;
use App\Models\User;
use App\Models\Visit;
use App\Services\Fhir\ReadinessCheck;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/** Mise en service de la chaîne FHIR : vérification, page d'administration, abonnement, renvoi des examens. */
class FhirSetupTest extends TestCase
{
    use DatabaseTransactions;

    private const BASE = 'http://fit-fhir.test/fhir';

    protected function setUp(): void
    {
        parent::setUp();
        if (!Route::has('admin.fhir-setup')) {
            Route::middleware(['web', 'auth'])->group(function () {
                Route::get('/admin/fhir-setup', [FhirSetupController::class, 'index'])->name('admin.fhir-setup');
                Route::post('/admin/fhir-setup/subscription', [FhirSetupController::class, 'installSubscription'])->name('admin.fhir-setup.subscription');
                Route::post('/admin/fhir-setup/resend', [FhirSetupController::class, 'resendOrders'])->name('admin.fhir-setup.resend');
            });
        }
        foreach (['modules.api-connectors.index' => '/_t/api', 'modules.index' => '/_t/modules', 'dashboard' => '/_t/dash'] as $name => $uri) {
            if (!Route::has($name)) {
                Route::get($uri, fn () => 'ok')->name($name);
            }
        }
        app('router')->getRoutes()->refreshNameLookups();
        config(['fhir.base_url' => self::BASE, 'fhir.webhook_secret' => str_repeat('s', 40), 'app.url' => 'https://fit.tbhc.uk']);
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'system_admin', 'status' => 'active', 'tenant_id' => 1]);
    }

    private function rows(): array
    {
        return collect(app(ReadinessCheck::class)->run())->keyBy('label')->all();
    }

    public function test_readiness_reports_blocking_configuration_points(): void
    {
        config(['fhir.base_url' => null, 'fhir.webhook_secret' => 'court', 'app.url' => 'http://localhost', 'fhir.document_sharing.source_oid' => null]);
        Http::fake();

        $rows = $this->rows();
        $this->assertSame('fail', $rows['FIT_FHIR_BASE_URL (adresse interne du serveur)']['state']);
        $this->assertSame('fail', $rows['APP_URL en HTTPS (point de notification)']['state']);
        $this->assertSame('fail', $rows['FIT_FHIR_WEBHOOK_SECRET']['state']);
        $this->assertSame('warn', $rows['FIT_FHIR_SOURCE_OID (publication des IPS)']['state'], 'non bloquant : publication des IPS désactivée');
        Http::assertNothingSent();
    }

    public function test_refused_iua_token_blocks_the_server_checks(): void
    {
        config(['fhir.auth.token_url' => 'https://auth.test/token', 'fhir.auth.client_id' => 'fit']);
        Http::fake(['https://auth.test/token' => Http::response(['error' => 'invalid_client'], 401)]);

        $rows = $this->rows();
        $this->assertSame('fail', $rows['Autorisation IUA (ITI-71)']['state']);
        $this->assertSame('fail', $rows['Serveur FHIR joignable']['state'], 'sans jeton, aucun échange avec le serveur');
        $this->assertStringContainsString('IUA', $rows['Serveur FHIR joignable']['detail']);
    }

    public function test_readiness_checks_server_conformance_and_subscription(): void
    {
        Http::fake([
            self::BASE . '/metadata' => Http::response(['resourceType' => 'CapabilityStatement', 'fhirVersion' => '4.0.1', 'rest' => [['mode' => 'server']]]),
            self::BASE . '/StructureDefinition*' => Http::response(['resourceType' => 'Bundle', 'total' => 1]),
            self::BASE . '/Subscription*' => Http::response(['resourceType' => 'Bundle', 'entry' => [['resource' => ['resourceType' => 'Subscription', 'status' => 'requested']]]]),
        ]);

        $rows = $this->rows();
        $this->assertSame('warn', $rows['Autorisation IUA (ITI-71)']['state'], 'non configurée : réseau privé seul');
        $this->assertSame('ok', $rows['Serveur FHIR joignable, version 4.0.1']['state']);
        $this->assertSame('ok', $rows['Guides IHE / HL7 installés']['state']);
        $this->assertSame('fail', $rows['Exigences SHALL des acteurs IHE']['state'], 'serveur sans aucune ressource déclarée');
        $this->assertSame(['fail', 'statut requested (attendu : active)'], [$rows['Abonnement des comptes rendus']['state'], $rows['Abonnement des comptes rendus']['detail']]);
        Http::assertSent(fn (Request $r) => str_starts_with($r->url(), self::BASE . '/Subscription?') && str_contains(urldecode($r->url()), 'url=https://fit.tbhc.uk/api/fhir/notify'));
    }

    public function test_setup_page_is_reserved_to_system_admins_and_lists_the_checks(): void
    {
        Http::fake([self::BASE . '/*' => Http::response(['resourceType' => 'OperationOutcome'], 503)]);

        $this->actingAs($this->admin())->get(route('admin.fhir-setup'))->assertOk()
            ->assertSee('Mise en service du serveur FHIR')->assertSee('Abonnement des comptes rendus')->assertSee('Installer l’abonnement des comptes rendus', false);
        $this->actingAs(User::factory()->create(['role' => 'association_admin', 'status' => 'active', 'tenant_id' => 1]))
            ->get(route('admin.fhir-setup'))->assertForbidden();
    }

    public function test_admin_installs_the_subscription_and_resends_pending_orders(): void
    {
        $clubId = (int) DB::table('clubs')->insertGetId(['name' => 'Club Setup', 'created_at' => now(), 'updated_at' => now()]);
        $playerId = (int) DB::table('players')->insertGetId(['name' => 'Joueur Setup', 'first_name' => 'Joueur', 'last_name' => 'Setup', 'club_id' => $clubId, 'created_at' => now(), 'updated_at' => now()]);
        $teamId = DB::table('teams')->value('id') ?? DB::table('teams')->insertGetId(['name' => 'Équipe Setup', 'club_id' => $clubId, 'created_at' => now(), 'updated_at' => now()]);
        $athleteId = (int) DB::table('athletes')->insertGetId(['name' => 'Joueur Setup', 'dob' => '2000-01-01', 'nationality' => 'TN', 'team_id' => $teamId, 'player_id' => $playerId, 'created_at' => now(), 'updated_at' => now()]);
        $admin = $this->admin();
        $appointment = Appointment::create(['athlete_id' => $athleteId, 'created_by' => $admin->id, 'appointment_date' => now(), 'appointment_type' => 'consultation', 'status' => 'Terminé']);
        $visit = Visit::create(['athlete_id' => $athleteId, 'appointment_id' => $appointment->id, 'visit_date' => now(), 'visit_type' => 'consultation', 'status' => 'Terminé']);
        FhirPatientLink::query()->create(['player_id' => $playerId, 'role' => 'fit', 'patient_id' => 'fit-9', 'status' => 'linked']);
        $order = FhirOrder::query()->create(['visit_id' => $visit->id, 'player_id' => $playerId, 'module' => 'laboratory', 'status' => 'pending']);
        Http::fake([
            self::BASE . '/Subscription?*' => Http::response(['resourceType' => 'Subscription', 'id' => 'sub-1']),
            self::BASE . '/ServiceRequest?*' => Http::response(['resourceType' => 'ServiceRequest', 'id' => 'sr-7']),
        ]);

        $this->actingAs($admin)->post(route('admin.fhir-setup.subscription'))->assertSessionHas('success', 'Abonnement installé : Subscription/sub-1 → https://fit.tbhc.uk/api/fhir/notify');
        Http::assertSent(fn (Request $r) => $r->method() === 'PUT' && ($r->data()['channel']['type'] ?? null) === 'rest-hook');

        $this->actingAs($admin)->post(route('admin.fhir-setup.resend'))->assertSessionHas('success', '1 demande(s) d’examen transmise(s), 0 en échec.');
        $this->assertSame(['active', 'sr-7'], [$order->fresh()->status, $order->fresh()->service_request_id]);
    }

    public function test_render_blueprint_describes_a_private_hapi_service_and_its_database(): void
    {
        $yaml = file_get_contents(base_path('fhir-server/render.yaml'));
        foreach (['type: pserv', 'name: fit-fhir', 'rootDir: fhir-server', 'name: fit-fhir-db', 'property: hostport', 'postgresMajorVersion: "17"'] as $expected) {
            $this->assertStringContainsString($expected, $yaml);
        }
        $this->assertStringContainsString('FROM hapiproject/hapi:v8.12.0-2', file_get_contents(base_path('fhir-server/Dockerfile')));
        $this->assertStringContainsString('server_address: http://${HAPI_HOSTPORT', file_get_contents(base_path('fhir-server/application.yaml')));
    }
}
