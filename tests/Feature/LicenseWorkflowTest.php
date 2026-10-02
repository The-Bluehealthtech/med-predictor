<?php

namespace Tests\Feature;

use App\Http\Controllers\Licensing\LicenseApprovalController;
use App\Http\Controllers\PlayerLicenseWorkflowController;
use App\Models\PlayerLicense;
use App\Models\User;
use App\Services\Licensing\FifaIdRegistry;
use App\Services\Modules\ModuleCatalog;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Circuit de licence : le club demande, la fédération examine, vérifie
 * l'identité auprès de FIFA ID (facultatif) et décide ; le club complète.
 */
class LicenseWorkflowTest extends TestCase
{
    use DatabaseTransactions;

    private int $associationId;

    private int $clubId;

    private int $playerId;

    protected function setUp(): void
    {
        parent::setUp();
        Route::middleware(['web', 'auth'])->group(function () {
            Route::get('/_t/licenses', [PlayerLicenseWorkflowController::class, 'index'])->name('modules.licenses.index');
            Route::get('/_t/licenses/players/{player}/request', [PlayerLicenseWorkflowController::class, 'create'])->name('player-licenses.request.create');
            Route::post('/_t/licenses/players/{player}/request', [PlayerLicenseWorkflowController::class, 'store'])->name('player-licenses.request.store');
            Route::get('/_t/licenses/officials/{official}/request', [PlayerLicenseWorkflowController::class, 'createOfficial'])->name('official-licenses.request.create');
            Route::post('/_t/licenses/officials/{official}/request', [PlayerLicenseWorkflowController::class, 'storeOfficial'])->name('official-licenses.request.store');
            Route::post('/_t/licenses/requests/{license}/respond', [PlayerLicenseWorkflowController::class, 'respond'])->name('player-licenses.respond');
            Route::get('/_t/licenses/requests/{license}', [PlayerLicenseWorkflowController::class, 'show'])->name('player-licenses.show');
            Route::post('/_t/licenses/requests/{license}/documents', [PlayerLicenseWorkflowController::class, 'addDocuments'])->name('player-licenses.documents');
            Route::get('/_t/documents/{document}', [\App\Http\Controllers\Licensing\LicenseDocumentController::class, 'show'])->name('licenses.document');
            Route::get('/_t/notifications', [\App\Http\Controllers\NotificationCenterController::class, 'index'])->name('notifications.index');
            Route::get('/_t/notifications/{id}/open', [\App\Http\Controllers\NotificationCenterController::class, 'open'])->name('notifications.open');
            Route::get('/_t/approval', [LicenseApprovalController::class, 'index'])->name('licenses.validation');
            Route::get('/_t/approval/{license}', [LicenseApprovalController::class, 'show'])->name('licenses.review');
            Route::post('/_t/approval/{license}/identity', [LicenseApprovalController::class, 'verifyIdentity'])->name('licenses.verify-identity');
            Route::post('/_t/approval/{license}/decision', [LicenseApprovalController::class, 'decide'])->name('licenses.decide');
            Route::get('/_t/approval/cards/{license}', [LicenseApprovalController::class, 'card'])->name('licenses.card');
            Route::post('/_t/approval/cards/batch', [LicenseApprovalController::class, 'cardsBatch'])->name('licenses.cards.batch');
            Route::post('/_t/approval/{license}/integrity', [LicenseApprovalController::class, 'recordIntegrityReview'])->name('licenses.integrity-review');
            Route::post('/_t/legacy-fraud', [\App\Http\Controllers\LicenseController::class, 'checkAllLicenses'])->name('licenses.fraud-detection.test-disabled');
        });
        app('router')->getRoutes()->refreshNameLookups();

        $this->associationId = (int) DB::table('associations')->insertGetId(['name' => 'Fédération Licences Test', 'country' => 'Tunisie', 'created_at' => now(), 'updated_at' => now()]);
        $this->clubId = (int) DB::table('clubs')->insertGetId(['name' => 'Club Licences Test', 'association_id' => $this->associationId, 'created_at' => now(), 'updated_at' => now()]);
        $this->playerId = (int) DB::table('players')->insertGetId(['name' => 'Samir Licence', 'first_name' => 'Samir', 'last_name' => 'Licence',
            'date_of_birth' => '2001-04-12', 'gender' => 'male', 'fifa_connect_id' => 'FIFA123TEST', 'club_id' => $this->clubId, 'association_id' => $this->associationId, 'created_at' => now(), 'updated_at' => now()]);
    }

    private function user(string $role, array $extra = []): User
    {
        return User::factory()->create(array_merge(['role' => $role, 'status' => 'active', 'tenant_id' => 1], $extra));
    }

    private function club(): User
    {
        return $this->user('club_admin', ['club_id' => $this->clubId]);
    }

    private function federation(): User
    {
        return $this->user('association_admin', ['association_id' => $this->associationId]);
    }

    /** PCMA du joueur : signé « apte » par défaut ; $synthetic = bilan de démonstration. */
    private function pcma(array $overrides = [], bool $synthetic = false): void
    {
        $teamId = DB::table('teams')->value('id') ?? DB::table('teams')->insertGetId(['name' => 'Équipe PCMA test', 'club_id' => $this->clubId, 'created_at' => now(), 'updated_at' => now()]);
        $athleteId = DB::table('athletes')->insertGetId(['name' => 'Samir Licence', 'dob' => '2001-04-12', 'nationality' => 'TN', 'team_id' => $teamId, 'created_at' => now(), 'updated_at' => now()]);
        $doctor = $this->user('club_medical', ['club_id' => $this->clubId]);
        DB::table('pcmas')->insert(array_merge([
            'athlete_id' => $athleteId, 'assessor_id' => $doctor->id, 'player_id' => $this->playerId, 'type' => 'pcma', 'status' => 'completed',
            'result_json' => json_encode($synthetic ? ['synthetic_demo' => true] : ['overall_score' => 90]),
            'final_statement' => json_encode(['overall_decision' => 'FIT']), 'is_signed' => true, 'signed_at' => now(),
            'assessment_date' => now()->subMonth()->toDateString(), 'created_at' => now(), 'updated_at' => now(),
        ], $overrides));
    }

    private function documents(array $types = ['identity', 'photo', 'medical', 'contract']): array
    {
        return collect($types)->mapWithKeys(fn ($t) => [$t => UploadedFile::fake()->create("{$t}.pdf", 40, 'application/pdf')])->all();
    }

    private function season(): string
    {
        $scale = app(\App\Services\Licensing\LicenseScale::class);

        return $scale->season($scale->settings($this->associationId))['label'];
    }

    /** Licence joueur FIFA Connect : Football, niveau pro, enregistrement, saison en cours. */
    private function payload(array $overrides = []): array
    {
        return array_merge(['discipline' => 'Football', 'level' => 'pro', 'registration_nature' => 'Registration', 'season' => $this->season(),
            'notes' => 'Nouveau contrat', 'documents' => $this->documents()], $overrides);
    }

    private function request(?User $club = null): PlayerLicense
    {
        $this->actingAs($club ?? $this->club())->post("/_t/licenses/players/{$this->playerId}/request", $this->payload())->assertSessionHasNoErrors()->assertRedirect();

        return PlayerLicense::query()->where('player_id', $this->playerId)->latest('id')->firstOrFail();
    }

    public function test_club_request_then_complement_then_approval(): void
    {
        $this->pcma();
        $this->actingAs($this->club())->get('/_t/licenses')->assertOk()->assertSee('Demander une licence')->assertSee('Samir Licence');
        $license = $this->request();
        $this->assertSame('pending', $license->status);

        $this->actingAs($this->club())->post("/_t/licenses/players/{$this->playerId}/request", $this->payload())->assertSessionHas('error');

        $federation = $this->federation();
        $this->actingAs($federation)->get('/_t/approval')->assertOk()->assertSee('Samir Licence')->assertSee('Examiner')->assertSee('non connecté');

        $this->actingAs($federation)->post("/_t/approval/{$license->id}/decision", ['decision' => 'request_info'])->assertSessionHas('error');
        $this->actingAs($federation)->post("/_t/approval/{$license->id}/decision", ['decision' => 'request_info', 'message' => 'Joindre le contrat signé'])
            ->assertRedirect(route('licenses.validation'));
        $this->assertSame('justification_requested', $license->fresh()->status);

        $this->actingAs($this->club())->get('/_t/licenses')->assertSee('Joindre le contrat signé')->assertSee('Compléter la demande');
        $this->actingAs($this->club())->get("/_t/licenses/requests/{$license->id}")->assertOk()->assertSee('Envoyer le complément à la fédération')->assertSee('Complément demandé au club');
        $this->actingAs($this->club())->post("/_t/licenses/requests/{$license->id}/respond", ['club_response' => 'Contrat transmis par courrier'])->assertRedirect();
        $this->assertSame('pending', $license->fresh()->status);
        $this->assertSame('Contrat transmis par courrier', $license->fresh()->club_response);

        $this->actingAs($federation)->get("/_t/approval/{$license->id}")->assertOk()->assertSee('Contrat transmis par courrier')->assertSee('Anti-fraude · identité et âge');
        $this->actingAs($federation)->post("/_t/approval/{$license->id}/decision", ['decision' => 'approve'])->assertRedirect();
        $fresh = $license->fresh();
        $this->assertSame('active', $fresh->status);
        $this->assertSame($federation->id, (int) $fresh->approved_by);
    }

    public function test_refusal_needs_a_reason_and_clubs_cannot_approve(): void
    {
        $license = $this->request();
        $this->actingAs($this->club())->get('/_t/approval')->assertForbidden();
        $this->actingAs($this->club())->post("/_t/approval/{$license->id}/decision", ['decision' => 'approve'])->assertForbidden();

        $federation = $this->federation();
        $this->actingAs($federation)->post("/_t/approval/{$license->id}/decision", ['decision' => 'reject'])->assertSessionHas('error');
        $this->actingAs($federation)->post("/_t/approval/{$license->id}/decision", ['decision' => 'reject', 'message' => 'Joueur déjà licencié ailleurs']);
        $this->assertSame('revoked', $license->fresh()->status);
        $this->assertSame('Joueur déjà licencié ailleurs', $license->fresh()->rejection_reason);
    }

    public function test_fifa_id_check_is_optional_and_never_simulated(): void
    {
        $license = $this->request();
        $federation = $this->federation();

        config(['services.fifa_id.url' => null, 'services.fifa_id.token' => null]);
        $this->assertSame('not_configured', app(FifaIdRegistry::class)->verify($license->player)['status']);
        $this->actingAs($federation)->get("/_t/approval/{$license->id}")->assertSee('Registre non connecté')->assertDontSee('Vérifier l\'identité auprès de FIFA ID');

        config(['services.fifa_id.url' => 'https://fifa-id.test/api', 'services.fifa_id.token' => 'secret']);
        Http::fake([
            'fifa-id.test/api/persons/FIFA123TEST' => Http::response(['firstName' => 'Samir', 'lastName' => 'Licence', 'dateOfBirth' => '2001-04-12']),
            'fifa-id.test/api/persons/FIFA999OTHER' => Http::response(['firstName' => 'Karim', 'lastName' => 'Autre', 'dateOfBirth' => '1999-01-01']),
            'fifa-id.test/api/persons/*' => Http::response([], 404),
        ]);
        $this->actingAs($federation)->get("/_t/approval/{$license->id}")->assertSee('Vérifier l\'identité auprès de FIFA ID');
        $this->actingAs($federation)->post("/_t/approval/{$license->id}/identity")->assertRedirect(route('licenses.review', $license));
        $this->assertSame('match', $license->fresh()->identity_check_status);

        DB::table('players')->where('id', $this->playerId)->update(['fifa_connect_id' => 'FIFA999OTHER']);
        $check = app(FifaIdRegistry::class)->verify($license->player->fresh());
        $this->assertSame('mismatch', $check['status']);
        $this->assertEqualsCanonicalizing(['first_name', 'last_name', 'date_of_birth'], $check['differences']);

        DB::table('players')->where('id', $this->playerId)->update(['fifa_connect_id' => 'INCONNU']);
        $this->assertSame('not_found', app(FifaIdRegistry::class)->verify($license->player->fresh())['status']);

        DB::table('players')->where('id', $this->playerId)->update(['fifa_connect_id' => null]);
        $this->assertSame('missing_id', app(FifaIdRegistry::class)->verify($license->player->fresh())['status']);
    }

    public function test_modules_cards_launch_the_right_side_of_the_process(): void
    {
        $catalog = app(ModuleCatalog::class);
        $clubCards = $catalog->visibleFor($this->club())->pluck('name')->all();
        $this->assertContains('Demande de licence', $clubCards);
        $this->assertNotContains('Approbation des licences', $clubCards, 'le club ne voit pas l\'approbation');

        $federationCards = $catalog->visibleFor($this->federation())->pluck('name')->all();
        $this->assertContains('Approbation des licences', $federationCards);

        $coach = $this->user('coach', ['club_id' => $this->clubId]);
        $this->assertNotContains('Demande de licence', $catalog->visibleFor($coach)->pluck('name')->all());
    }

    public function test_required_documents_notifications_and_secure_downloads(): void
    {
        $this->pcma();
        $club = $this->club();
        $federation = $this->federation();

        // Pièce exigée manquante (contrat pour une licence professionnelle) : dépôt refusé.
        $this->actingAs($club)->post("/_t/licenses/players/{$this->playerId}/request", $this->payload(['documents' => $this->documents(['identity', 'photo', 'medical'])]))->assertSessionHas('error');
        $this->assertSame(0, PlayerLicense::query()->where('player_id', $this->playerId)->count());

        $license = $this->request($club);
        $this->assertSame(4, $license->documents()->count());
        $this->assertSame(['submitted'], $license->events()->pluck('action')->all());

        // La fédération est notifiée du dépôt, avec un lien vers le dossier.
        $notification = $federation->notifications()->latest()->first();
        $this->assertNotNull($notification, 'notification envoyée à la fédération');
        $this->assertStringContainsString('Nouvelle demande de licence', $notification->data['message']);
        $this->assertSame(route('licenses.review', $license), $notification->data['url']);
        $this->actingAs($federation)->get("/_t/notifications/{$notification->id}/open")->assertRedirect(route('licenses.review', $license));
        $this->assertNotNull($notification->fresh()->read_at, 'ouverte = lue');
        $this->actingAs($federation)->get('/_t/notifications')->assertOk()->assertSee('Nouvelle demande de licence');

        // Pièces consultables par le club et la fédération, pas par un autre club.
        $document = $license->documents()->where('document_type', 'medical')->first();
        $this->actingAs($club)->get("/_t/documents/{$document->id}")->assertOk()->assertHeader('Content-Type', 'application/pdf')->assertHeader('Content-Security-Policy', 'sandbox');
        $this->actingAs($federation)->get("/_t/documents/{$document->id}")->assertOk();
        $otherClub = (int) DB::table('clubs')->insertGetId(['name' => 'Autre club', 'association_id' => $this->associationId + 1000, 'created_at' => now(), 'updated_at' => now()]);
        $this->actingAs($this->user('club_admin', ['club_id' => $otherClub]))->get("/_t/documents/{$document->id}")->assertForbidden();
        $this->actingAs($federation)->get("/_t/approval/{$license->id}")->assertSee('Toutes les pièces exigées')->assertSee('medical.pdf')->assertSee('Demande envoyée à la fédération');

        // Décision : le club (responsable et auteur de la demande) est notifié.
        $this->actingAs($federation)->post("/_t/approval/{$license->id}/decision", ['decision' => 'approve'])->assertRedirect();
        $clubNotification = $club->notifications()->latest()->first();
        $this->assertStringContainsString('Licence approuvée pour Samir Licence', $clubNotification->data['message']);
        $this->assertSame(['submitted', 'approved'], $license->events()->pluck('action')->all());
    }

    public function test_approval_is_blocked_while_required_documents_are_missing(): void
    {
        $this->pcma(); // joueur senior : PCMA exigé pour une licence amateur
        $license = PlayerLicense::query()->create(['player_id' => $this->playerId, 'club_id' => $this->clubId, 'registration_type' => 'Player', 'discipline' => 'Football',
            'level' => 'amateur', 'registration_nature' => 'Registration', 'license_type' => 'amateur', 'season' => $this->season(),
            'status' => 'pending', 'approval_status' => 'pending', 'expiry_date' => now()->addMonths(6)->toDateString()]);
        $federation = $this->federation();

        $this->actingAs($federation)->post("/_t/approval/{$license->id}/decision", ['decision' => 'approve'])->assertSessionHas('error');
        $this->assertSame('pending', $license->fresh()->status);
        $this->actingAs($federation)->get("/_t/approval/{$license->id}")->assertSee('Manquante')->assertSee('Impossible tant que des pièces exigées manquent');

        // Le club ajoute les pièces manquantes depuis son suivi ; l'approbation devient possible.
        $club = $this->club();
        foreach (['identity', 'photo', 'medical'] as $type) {
            $this->actingAs($club)->post("/_t/licenses/requests/{$license->id}/documents", ['documents' => [$type => UploadedFile::fake()->image("{$type}.jpg")]])->assertSessionHas('success');
        }
        $this->assertSame(3, $license->events()->where('action', 'document_added')->count());
        $this->actingAs($federation)->post("/_t/approval/{$license->id}/decision", ['decision' => 'approve']);
        $this->assertSame('active', $license->fresh()->status);
    }

    public function test_pcma_is_required_for_professionals_and_senior_amateurs(): void
    {
        $federation = $this->federation();
        $license = $this->request(); // professionnelle, joueur de 25 ans, toutes pièces fournies

        // Aucun PCMA : approbation bloquée, motif explicite.
        $this->actingAs($federation)->get("/_t/approval/{$license->id}")->assertSee('Aucun PCMA enregistré')->assertSee('Impossible sans PCMA valide');
        $this->actingAs($federation)->post("/_t/approval/{$license->id}/decision", ['decision' => 'approve'])->assertSessionHas('error');
        $this->assertSame('pending', $license->fresh()->status);

        // Un bilan de démonstration ou non signé ne compte pas.
        $this->pcma([], true);
        $this->assertFalse(app(\App\Services\Licensing\PcmaRequirement::class)->check($license->fresh())['status']['valid']);
        $this->pcma(['is_signed' => false, 'signed_at' => null, 'assessment_date' => now()->toDateString()]);
        $this->assertSame('unsigned', app(\App\Services\Licensing\PcmaRequirement::class)->check($license->fresh())['status']['state']);

        // PCMA signé « apte » mais trop ancien : toujours bloqué.
        $this->pcma(['assessment_date' => now()->subMonths(13)->toDateString(), 'created_at' => now()->addSecond()]);
        DB::table('pcmas')->where('player_id', $this->playerId)->where('is_signed', false)->delete();
        $this->assertSame('expired', app(\App\Services\Licensing\PcmaRequirement::class)->check($license->fresh())['status']['state']);

        // PCMA signé « apte » récent : l'approbation passe.
        $this->pcma(['assessment_date' => now()->toDateString()]);
        $this->actingAs($federation)->post("/_t/approval/{$license->id}/decision", ['decision' => 'approve']);
        $this->assertSame('active', $license->fresh()->status);
    }

    public function test_pcma_is_not_required_for_young_amateurs(): void
    {
        DB::table('players')->where('id', $this->playerId)->update(['date_of_birth' => now()->subYears(16)->toDateString()]);
        $requirement = app(\App\Services\Licensing\PcmaRequirement::class);
        $player = \App\Models\Player::query()->find($this->playerId);

        $this->assertFalse($requirement->requirement($player, 'Football', 'amateur')['required']);
        $this->assertTrue($requirement->requirement($player, 'Football', 'pro')['required'], 'un professionnel mineur reste soumis au PCMA');
        $this->assertFalse($requirement->requirement($player, 'Futsal', 'amateur')['required']);

        DB::table('players')->where('id', $this->playerId)->update(['date_of_birth' => null]);
        $this->assertTrue($requirement->requirement($player->fresh(), 'Football', 'amateur')['required'], 'âge inconnu : PCMA exigé par précaution');
    }

    public function test_official_and_manager_licences_follow_fifa_connect_roles(): void
    {
        $officialId = (int) DB::table('club_officials')->insertGetId(['club_id' => $this->clubId, 'international_first_name' => 'Nabil', 'international_last_name' => 'Président',
            'gender' => 'male', 'date_of_birth' => '1970-01-01', 'nationality' => 'TN', 'registration_type' => 'OrganisationOfficial', 'organisation_official_role' => 'President',
            'status' => 'active', 'discipline' => 'Football', 'registration_valid_from' => now()->toDateString(), 'created_at' => now(), 'updated_at' => now()]);
        $club = $this->club();

        $this->actingAs($club)->get('/_t/licenses')->assertSee('Licences des officiels d')->assertSee('Nabil Président');
        $this->actingAs($club)->get("/_t/licenses/officials/{$officialId}/request")->assertOk()->assertSee('OrganisationOfficial')->assertSee('Président');
        $this->actingAs($club)->post("/_t/licenses/officials/{$officialId}/request", ['discipline' => 'Football', 'season' => $this->season(),
            'documents' => $this->documents(['identity', 'photo'])])->assertSessionHasNoErrors()->assertRedirect();

        $license = PlayerLicense::query()->where('club_official_id', $officialId)->firstOrFail();
        $this->assertNull($license->player_id);
        $this->assertSame('OrganisationOfficial', $license->registration_type);
        $this->assertSame('President', $license->organisation_official_role);
        $this->assertSame($this->season(), $license->season);
        $this->assertStringContainsString('Dirigeant · Président', \App\Services\Licensing\LicenseWorkflow::describe($license));

        // Pas de PCMA pour un officiel : la fédération approuve dès que les pièces sont là.
        $federation = $this->federation();
        $this->actingAs($federation)->get("/_t/approval/{$license->id}")->assertOk()->assertSee('Nabil Président')->assertDontSee('Aptitude médicale (PCMA)');
        $this->actingAs($federation)->post("/_t/approval/{$license->id}/decision", ['decision' => 'approve'])->assertRedirect();
        $this->assertSame('active', $license->fresh()->status);
    }

    public function test_a_licence_lasts_one_season_at_most(): void
    {
        $this->pcma();
        $license = $this->request();
        $scale = app(\App\Services\Licensing\LicenseScale::class);
        $season = $scale->season($scale->settings($this->associationId));

        $this->assertSame($season['end']->toDateString(), $license->expiry_date->toDateString());
        $this->assertSame('Football', $license->discipline);
        $this->assertSame('pro', $license->level);
        $this->assertSame('male', $license->gender);
        $this->assertSame('SENIOR', $license->age_category);
        $this->actingAs($this->club())->post("/_t/licenses/players/{$this->playerId}/request", $this->payload(['season' => '1999-2000']))->assertSessionHas('error');
    }

    public function test_federation_can_preview_and_batch_print_only_approved_player_cards(): void
    {
        $this->pcma();
        $license = $this->request();
        $federation = $this->federation();

        $this->actingAs($federation)->get("/_t/approval/cards/{$license->id}")->assertNotFound();
        $this->actingAs($federation)->post("/_t/approval/{$license->id}/decision", ['decision' => 'approve'])->assertRedirect();

        $this->actingAs($federation)->get("/_t/approval?tab=active")
            ->assertOk()->assertSee('Imprimer la sélection')->assertSee('Carte');
        $this->actingAs($federation)->get("/_t/approval/cards/{$license->id}")
            ->assertOk()->assertSee('Carte de licence')->assertSee('CR80')->assertSee('Samir')->assertSee('Club Licences Test');
        $this->actingAs($federation)->post('/_t/approval/cards/batch', ['license_ids' => [$license->id]])
            ->assertOk()->assertSee('Impression en batch')->assertSee('1 carte');
    }

    public function test_club_cannot_open_federation_license_cards(): void
    {
        $license = PlayerLicense::query()->create([
            'player_id' => $this->playerId, 'club_id' => $this->clubId, 'status' => 'active',
            'approval_status' => 'approved', 'season' => $this->season(), 'expiry_date' => now()->addMonths(6),
        ]);

        $this->actingAs($this->club())->get("/_t/approval/cards/{$license->id}")->assertForbidden();
        $this->actingAs($this->club())->post('/_t/approval/cards/batch', ['license_ids' => [$license->id]])->assertForbidden();
    }
    public function test_legacy_fraud_scoring_endpoint_is_disabled(): void
    {
        $this->actingAs($this->federation())
            ->postJson('/_t/legacy-fraud')
            ->assertStatus(410)
            ->assertJson(['error' => 'legacy_fraud_detection_disabled']);
    }

    public function test_federation_integrity_reviews_are_append_only_and_audited(): void
    {
        $license = $this->request();
        $federation = $this->federation();
        $payload = [
            'photo_status' => 'coherent',
            'signature_status' => 'insufficient',
            'identity_status' => 'coherent',
            'age_status' => 'uncertain',
            'notes' => 'Contrôle visuel réalisé, justificatif âge à compléter.',
        ];

        $this->actingAs($federation)->post("/_t/approval/{$license->id}/integrity", $payload)
            ->assertRedirect(route('licenses.review', $license))->assertSessionHas('success');
        $this->assertDatabaseHas('license_integrity_reviews', [
            'player_license_id' => $license->id, 'reviewer_id' => $federation->id,
            'photo_status' => 'coherent', 'age_status' => 'uncertain',
        ]);
        $this->actingAs($federation)->post("/_t/approval/{$license->id}/integrity", array_merge($payload, ['age_status' => 'coherent']));
        $this->assertSame(2, DB::table('license_integrity_reviews')->where('player_license_id', $license->id)->count());
        $this->assertSame(2, $license->events()->where('action', 'integrity_review')->count());

        $this->actingAs($this->club())->post("/_t/approval/{$license->id}/integrity", $payload)->assertForbidden();
        $this->actingAs($federation)->get("/_t/approval/{$license->id}")
            ->assertOk()->assertSee('Historique des revues')->assertSee('Contrôle visuel réalisé');
    }

}
