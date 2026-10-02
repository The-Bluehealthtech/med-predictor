<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

/**
 * Module Passeports : passeport médical (résumé IPS) et passeport de transfert
 * (format FIFA) — contenu fidèle aux données, sections vides honnêtes, PDF,
 * périmètre d'accès et journalisation des consultations médicales.
 */
class PassportsTest extends TestCase
{
    use DatabaseTransactions;

    private int $playerId;
    private int $clubId;
    private int $otherClubId;
    private int $associationId;

    protected function setUp(): void
    {
        parent::setUp();
        if (!\Illuminate\Support\Facades\Schema::hasTable('passport_attestations')) {
            (require base_path('database/migrations/2026_10_02_160000_create_passport_attestations_table.php'))->up();
        }
        $this->artisan('role-eval:generate-demo', ['--seed' => 7, '--clubs' => 4])->assertExitCode(0);
        $clubs = DB::table('teams')->whereIn('id', DB::table('match_participations')->distinct()->pluck('team_id'))->pluck('club_id')->unique()->sort()->values();
        [$this->clubId, $this->otherClubId] = [(int) $clubs[0], (int) $clubs[1]];
        $this->associationId = (int) DB::table('associations')->insertGetId(['name' => 'Fédération test', 'country' => 'Tunisie', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('clubs')->where('id', $this->clubId)->update(['association_id' => $this->associationId]);
        $this->playerId = (int) DB::table('players')->where('club_id', $this->clubId)->value('id');
        $teamId = (int) DB::table('teams')->where('club_id', $this->clubId)->value('id');
        $doctor = $this->user('club_medical', ['club_id' => $this->clubId]);

        DB::table('health_records')->insert([
            'user_id' => $doctor->id, 'player_id' => $this->playerId, 'record_date' => '2026-09-20', 'status' => 'active', 'blood_type' => 'A+',
            'allergies' => json_encode(['Pénicilline']), 'medications' => json_encode([['name' => 'Salbutamol', 'status' => 'active', 'dose' => '100 µg']]),
            'icd11_diagnoses' => json_encode([['code' => 'CA23', 'label' => 'Asthme', 'release' => '2026-01']]),
            'weight' => 74.5, 'height' => 181, 'laboratory_results' => 'Ferritine normale', 'created_at' => now(), 'updated_at' => now(),
        ]);
        $athleteId = (int) DB::table('athletes')->insertGetId(['name' => 'Joueur test', 'dob' => '2001-04-12', 'nationality' => 'TN', 'team_id' => $teamId, 'player_id' => $this->playerId, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('injuries')->insert([
            ['athlete_id' => $athleteId, 'date' => '2026-09-25', 'type' => 'strain', 'body_zone' => 'ischio_jambiers', 'severity' => 'moderate', 'description' => 'x', 'status' => 'open', 'diagnosed_by' => $doctor->id, 'created_at' => now(), 'updated_at' => now()],
            ['athlete_id' => $athleteId, 'date' => '2026-03-02', 'type' => 'sprain', 'body_zone' => 'cheville', 'severity' => 'mild', 'description' => 'x', 'status' => 'resolved', 'diagnosed_by' => $doctor->id, 'created_at' => now(), 'updated_at' => now()],
        ]);
        DB::table('immunisations')->insert(['athlete_id' => $athleteId, 'vaccine_code' => 'TDAP', 'vaccine_name' => 'Diphtérie-tétanos-coqueluche', 'date_administered' => '2025-01-10', 'status' => 'active', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('pcmas')->insert(['athlete_id' => $athleteId, 'player_id' => $this->playerId, 'assessor_id' => $doctor->id, 'type' => 'pcma', 'result_json' => '{}', 'status' => 'completed',
            'assessment_date' => '2026-08-01', 'final_statement' => json_encode(['overall_decision' => 'FIT']), 'is_signed' => true, 'created_at' => now(), 'updated_at' => now()]);
        DB::table('tue_requests')->insert(['physician_id' => $doctor->id, 'player_id' => $this->playerId, 'medication' => 'Salbutamol', 'status' => 'pending', 'request_date' => '2026-09-21', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('player_licenses')->insert(['player_id' => $this->playerId, 'club_id' => $this->clubId, 'season' => '2026-2027', 'license_number' => 'LIC-TEST-1', 'license_type' => 'player', 'contract_type' => 'professional', 'status' => 'active', 'issue_date' => '2026-07-01', 'expiry_date' => '2027-06-30', 'created_at' => now(), 'updated_at' => now()]);
    }

    private function user(string $role, array $attributes = []): User
    {
        return User::factory()->create(array_merge(['role' => $role, 'tenant_id' => 1, 'club_id' => null, 'association_id' => null, 'status' => 'active'], $attributes));
    }

    public function test_medical_passport_follows_ips_sections_and_recorded_data(): void
    {
        Log::spy();
        $doctor = $this->user('club_medical', ['club_id' => $this->clubId]);
        $page = $this->actingAs($doctor)->get(route('passports.medical.show', ['player' => $this->playerId, 'purpose' => 'transfer']))->assertOk();
        foreach (['International Patient Summary', 'Transfert du joueur', 'Allergies et intolérances', 'Pénicilline', 'Salbutamol', 'CIM-11 CA23', 'Asthme',
            'Blessure — ischio jambiers', 'Blessure guérie — cheville', 'Diphtérie-tétanos-coqueluche', 'Ferritine normale', '74.5 kg',
            'Apte · signé', 'AUT — Salbutamol', 'A+', 'Document non attesté'] as $expected) {
            $page->assertSee($expected, false);
        }
        $this->assertSame([], $page->viewData('summary')['sections']['allergies'] === [] ? ['vide'] : [], 'allergie enregistrée');
        Log::shouldHaveReceived('info')->withArgs(fn ($m, $c) => $m === 'passeport médical consulté' && $c['player_id'] === $this->playerId && $c['purpose'] === 'transfer');

        $pdf = $this->actingAs($doctor)->get(route('passports.medical.pdf', ['player' => $this->playerId, 'purpose' => 'selection']))->assertOk();
        $this->assertStringStartsWith('%PDF-', $pdf->getContent());
        $this->assertStringContainsString('no-store', $pdf->headers->get('Cache-Control'));
    }

    public function test_empty_sections_never_claim_absence_of_problems(): void
    {
        $other = (int) DB::table('players')->where('club_id', $this->clubId)->where('id', '!=', $this->playerId)->value('id');
        $page = $this->actingAs($this->user('club_medical', ['club_id' => $this->clubId]))->get(route('passports.medical.show', $other))->assertOk();
        $page->assertSee('Aucune information enregistrée.')->assertDontSee('Aucune allergie');
        $this->assertSame([], $page->viewData('summary')['sections']['allergies']);
    }

    public function test_medical_passport_access_is_limited_to_medical_scope_and_the_player(): void
    {
        $this->actingAs($this->user('club_medical', ['club_id' => $this->otherClubId]))->get(route('passports.medical.show', $this->playerId))->assertForbidden();
        $this->actingAs($this->user('club_admin', ['club_id' => $this->clubId]))->get(route('passports.medical.show', $this->playerId))->assertForbidden();
        $this->actingAs($this->user('club_admin', ['club_id' => $this->clubId]))->get(route('passports.medical.index'))->assertForbidden();

        // Le layout des comptes joueur pointe vers le tableau de bord joueur, absent des routes de test.
        if (!\Illuminate\Support\Facades\Route::has('player-dashboard')) {
            \Illuminate\Support\Facades\Route::get('/player-dashboard', fn () => null)->name('player-dashboard');
            \Illuminate\Support\Facades\Route::getRoutes()->refreshNameLookups();
        }
        $player = $this->user('player', ['player_id' => $this->playerId]);
        $this->actingAs($player)->get(route('passports.medical.index'))->assertRedirect(route('passports.medical.show', ['player' => $this->playerId, 'purpose' => 'player_share']));
        $this->actingAs($player)->get(route('passports.medical.show', $this->playerId))->assertOk()->assertSee('Partage par le joueur');
        $other = (int) DB::table('players')->where('id', '!=', $this->playerId)->value('id');
        $this->actingAs($player)->get(route('passports.medical.show', $other))->assertForbidden();
    }

    public function test_transfer_passport_lists_registrations_without_medical_data(): void
    {
        $coach = $this->user('club_admin', ['club_id' => $this->clubId]);
        $page = $this->actingAs($coach)->get(route('passports.transfer.show', $this->playerId))->assertOk()
            ->assertSee('Passeport joueur · format FIFA')->assertSee('LIC-TEST-1')->assertSee('Professionnel')->assertSee('2026-2027')->assertSee('Fédération test');
        $page->assertDontSee('Pénicilline')->assertDontSee('Salbutamol');
        $this->assertStringStartsWith('%PDF-', $this->actingAs($coach)->get(route('passports.transfer.pdf', $this->playerId))->assertOk()->getContent());

        $this->actingAs($this->user('club_admin', ['club_id' => $this->otherClubId]))->get(route('passports.transfer.show', $this->playerId))->assertForbidden();
        $this->actingAs($this->user('association_admin', ['association_id' => $this->associationId]))->get(route('passports.transfer.show', $this->playerId))->assertOk();
        $list = $this->actingAs($coach)->get(route('passports.transfer.index'))->assertOk();
        $this->assertTrue($list->viewData('players')->getCollection()->every(fn ($p) => (int) $p->club_id === $this->clubId));
    }

    public function test_physician_attests_and_attestation_expires_when_data_change(): void
    {
        $doctor = $this->user('club_medical', ['club_id' => $this->clubId]);
        $url = route('passports.medical.attest', ['player' => $this->playerId, 'purpose' => 'transfer']);
        $this->actingAs($doctor)->post($url, ['password' => 'mauvais', 'confirm' => '1'])->assertSessionHasErrors('password');
        $this->actingAs($doctor)->post($url, ['password' => 'password'])->assertSessionHasErrors('confirm');
        $this->actingAs($doctor)->post($url, ['password' => 'password', 'confirm' => '1', 'license' => 'ORD-123'])->assertRedirect();

        $page = $this->actingAs($doctor)->get(route('passports.medical.show', ['player' => $this->playerId, 'purpose' => 'transfer']))->assertOk();
        $page->assertSee('Document attesté')->assertSee('ORD-123');
        $this->assertSame('valid', $page->viewData('attestation')['state']);
        $fhir = $this->actingAs($doctor)->get(route('passports.medical.fhir', ['player' => $this->playerId, 'purpose' => 'transfer']))->assertOk()->json();
        $this->assertSame('final', $fhir['entry'][0]['resource']['status']);
        $this->assertSame('legal', $fhir['entry'][0]['resource']['attester'][0]['mode']);

        // Nouvelle donnée médicale : l'attestation devient périmée
        DB::table('health_records')->insert(['user_id' => $doctor->id, 'player_id' => $this->playerId, 'record_date' => '2026-09-28', 'status' => 'active',
            'allergies' => json_encode(['Arachide']), 'created_at' => now(), 'updated_at' => now()]);
        $after = $this->actingAs($doctor)->get(route('passports.medical.show', ['player' => $this->playerId, 'purpose' => 'transfer']))->assertOk();
        $after->assertSee('Attestation périmée');
        $this->assertSame('preliminary', $this->actingAs($doctor)->get(route('passports.medical.fhir', $this->playerId))->json('entry.0.resource.status'));

        // Ni l'admin système ni le joueur ne signent
        $this->actingAs($this->user('system_admin', ['tenant_id' => null]))->post($url, ['password' => 'password', 'confirm' => '1'])->assertForbidden();
        $this->actingAs($this->user('player', ['player_id' => $this->playerId]))->post($url, ['password' => 'password', 'confirm' => '1'])->assertForbidden();
    }

    public function test_fhir_ips_bundle_and_token_api(): void
    {
        $doctor = $this->user('club_medical', ['club_id' => $this->clubId]);
        $bundle = $this->actingAs($doctor)->get(route('passports.medical.fhir', ['player' => $this->playerId, 'purpose' => 'selection']))->assertOk();
        $this->assertStringContainsString('application/fhir+json', $bundle->headers->get('Content-Type'));
        $json = $bundle->json();
        $this->assertSame(['Bundle', 'document'], [$json['resourceType'], $json['type']]);
        $composition = $json['entry'][0]['resource'];
        $this->assertSame('60591-5', $composition['type']['coding'][0]['code']);
        $this->assertSame('preliminary', $composition['status']);
        $resources = collect($json['entry'])->pluck('resource');
        $this->assertTrue($resources->contains(fn ($r) => $r['resourceType'] === 'Condition' && ($r['code']['coding'][0]['system'] ?? null) === 'http://id.who.int/icd/release/11/mms' && $r['code']['coding'][0]['code'] === 'CA23'));
        $this->assertTrue($resources->contains(fn ($r) => $r['resourceType'] === 'AllergyIntolerance' && $r['code']['text'] === 'Pénicilline'));
        $this->assertTrue($resources->contains(fn ($r) => $r['resourceType'] === 'Patient'));

        // Joueur sans données : entrées « aucune information » des sections obligatoires
        $other = (int) DB::table('players')->where('club_id', $this->clubId)->where('id', '!=', $this->playerId)->value('id');
        $empty = collect($this->actingAs($doctor)->get(route('passports.medical.fhir', $other))->json('entry'))->pluck('resource');
        foreach (['no-allergy-info', 'no-problem-info', 'no-medication-info'] as $code) {
            $this->assertTrue($empty->contains(fn ($r) => json_encode($r) && str_contains(json_encode($r), $code)), $code);
        }

        // API à jeton : droit médical + accès au joueur
        \Laravel\Sanctum\Sanctum::actingAs($doctor, ['selections:medical']);
        $this->getJson("/api/v1/passports/medical/{$this->playerId}?purpose=transfer")->assertOk()->assertJsonPath('resourceType', 'Bundle');
        \Laravel\Sanctum\Sanctum::actingAs($doctor, ['club:selections:read']);
        $this->getJson("/api/v1/passports/medical/{$this->playerId}")->assertForbidden();
        \Laravel\Sanctum\Sanctum::actingAs($this->user('club_medical', ['club_id' => $this->otherClubId]), ['selections:medical']);
        $this->getJson("/api/v1/passports/medical/{$this->playerId}")->assertForbidden();
    }

    public function test_player_portal_shows_passport_links_by_right(): void
    {
        $player = \App\Models\Player::withoutGlobalScopes()->with('club')->findOrFail($this->playerId);
        $this->actingAs($this->user('player', ['player_id' => $this->playerId]));
        $html = view('passports.partials.portal-links', ['player' => $player])->render();
        $this->assertStringContainsString('Mon passeport médical', $html);
        $this->assertStringContainsString('Mon passeport de transfert', $html);

        $this->actingAs($this->user('club_admin', ['club_id' => $this->clubId]));
        $html = view('passports.partials.portal-links', ['player' => $player])->render();
        $this->assertStringNotContainsString('passeport médical', mb_strtolower($html));
        $this->assertStringContainsString('Passeport de transfert', $html);
    }
}
