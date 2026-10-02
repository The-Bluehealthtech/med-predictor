<?php

namespace Tests\Feature;

use App\Models\NationalSelection;
use App\Models\User;
use App\Services\Dtn\ApiAbilities;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

/**
 * API des sélections nationales : la DTN consulte les fiches et convoque, le
 * club répond par l'état de départ et reçoit l'état de retour. Chaque appel
 * exige le droit du jeton, la permission RBAC de l'espace et le périmètre ;
 * la partie médicale exige selections:medical et un rôle médical.
 */
class NationalSelectionApiTest extends TestCase
{
    use DatabaseTransactions;

    private const FEDERATION = [ApiAbilities::DTN_PLAYERS_READ, ApiAbilities::DTN_SELECTIONS_READ, ApiAbilities::DTN_SELECTIONS_WRITE];
    private const CLUB = [ApiAbilities::CLUB_SELECTIONS_READ, ApiAbilities::CLUB_SELECTIONS_WRITE];

    private int $associationId;
    private int $clubId;
    private int $otherClubId;
    private int $playerId;

    protected function setUp(): void
    {
        parent::setUp();

        if (!Schema::hasTable('national_selections')) {
            (require base_path('database/migrations/2026_10_02_090000_create_national_selections_tables.php'))->up();
        }

        $this->artisan('role-eval:generate-demo', ['--seed' => 11, '--clubs' => 4])->assertExitCode(0);
        $competitionId = DB::table('matches')->max('competition_id');
        $clubs = DB::table('matches')->where('competition_id', $competitionId)->pluck('home_club_id')->unique()->sort()->values();
        $this->clubId = (int) $clubs[0];
        $this->otherClubId = (int) $clubs[1];
        $this->playerId = (int) DB::table('match_participations')
            ->join('teams', 'teams.id', '=', 'match_participations.team_id')
            ->where('teams.club_id', $this->clubId)->value('match_participations.player_id');
        // Deux fédérations propres au test (la base de test peut ne pas en contenir)
        $this->associationId = (int) DB::table('associations')->insertGetId(['name' => 'Fédération test', 'country' => 'Tunisie', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('associations')->insert(['name' => 'Autre fédération test', 'country' => 'Maroc', 'created_at' => now(), 'updated_at' => now()]);
    }

    private function user(string $role, array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => $role, 'tenant_id' => 1, 'club_id' => null, 'association_id' => null, 'status' => 'active',
        ], $attributes));
    }

    private function dtn(): User
    {
        return $this->user('dtn', ['association_id' => $this->associationId]);
    }

    private function convokeViaApi(User $dtn): int
    {
        Sanctum::actingAs($dtn, self::FEDERATION);

        return $this->postJson('/api/v1/dtn/selections', [
            'player_id' => $this->playerId,
            'association_id' => $this->associationId,
            'team_label' => 'Équipe nationale A',
            'event_type' => 'qualifier',
            'event_name' => 'Fenêtre de novembre',
            'start_date' => now()->addDays(10)->toDateString(),
            'end_date' => now()->addDays(18)->toDateString(),
        ])->assertCreated()->assertJsonPath('data.status', NationalSelection::STATUS_CONVOKED)->json('data.id');
    }

    public function test_full_cycle_through_the_api(): void
    {
        $dtn = $this->dtn();

        // La DTN consulte les fiches joueurs avant de convoquer
        Sanctum::actingAs($dtn, self::FEDERATION);
        $this->getJson('/api/v1/dtn/players?per_page=5')->assertOk()->assertJsonStructure(['data' => [['id', 'name', 'club']], 'meta']);
        $profile = $this->getJson("/api/v1/dtn/players/{$this->playerId}")->assertOk()->json('data');
        $this->assertGreaterThan(0, $profile['data']['season']['matches']);
        $this->assertArrayNotHasKey('medical', $profile);

        $id = $this->convokeViaApi($dtn);

        // Le club reçoit la convocation et l'état de départ pré-rempli
        $coach = $this->user('club_admin', ['club_id' => $this->clubId]);
        Sanctum::actingAs($coach, self::CLUB);
        $this->getJson('/api/v1/club/selections')->assertOk()->assertJsonPath('data.0.id', $id);
        $this->getJson("/api/v1/club/selections/{$id}")->assertOk()
            ->assertJsonPath('data.departure_report.status', 'draft')
            ->assertJsonPath('data.medical_included', false);
        $this->putJson("/api/v1/club/selections/{$id}/departure", [
            'availability' => 'available_limited', 'load_recommendation' => '60 minutes maximum', 'send' => true,
        ])->assertOk()->assertJsonPath('data.status', NationalSelection::STATUS_DEPARTURE_SENT);

        // La DTN envoie l'état de retour
        Sanctum::actingAs($dtn, self::FEDERATION);
        $this->getJson("/api/v1/dtn/selections/{$id}")->assertOk()
            ->assertJsonPath('data.departure_report.content.load_recommendation', '60 minutes maximum');
        $this->putJson("/api/v1/dtn/selections/{$id}/return", [
            'matches' => 2, 'minutes' => 150, 'avg_rating' => 7.0, 'staff_evaluation' => 8,
            'fatigue_level' => 'medium', 'injury_risk' => 'low', 'incidents' => 'Aucun', 'send' => true,
        ])->assertOk()->assertJsonPath('data.status', NationalSelection::STATUS_RETURN_SENT);

        // Le club reçoit les données de retour et accuse réception
        Sanctum::actingAs($coach, self::CLUB);
        $back = $this->getJson('/api/v1/club/selections?status=return_sent')->assertOk()->json('data.0');
        $this->assertSame('Aucun', $back['return_report']['content']['incidents']);
        $this->assertEqualsWithDelta(74.0, $back['performance']['index'], 0.01);
        $this->postJson("/api/v1/club/selections/{$id}/acknowledge")->assertOk()->assertJsonPath('data.status', NationalSelection::STATUS_CLOSED);
    }

    public function test_token_abilities_and_rbac_separate_the_two_spaces(): void
    {
        $dtn = $this->dtn();
        $coach = $this->user('club_admin', ['club_id' => $this->clubId]);
        $id = $this->convokeViaApi($dtn);

        // Jeton club sur l'espace fédération, et inversement : refusé par le droit du jeton
        Sanctum::actingAs($coach, self::CLUB);
        $this->getJson('/api/v1/dtn/players')->assertForbidden();
        $this->postJson('/api/v1/dtn/selections', ['player_id' => $this->playerId])->assertForbidden();
        Sanctum::actingAs($dtn, self::FEDERATION);
        $this->getJson('/api/v1/club/selections')->assertForbidden();
        $this->putJson("/api/v1/club/selections/{$id}/departure", ['send' => true])->assertForbidden();

        // Même avec le bon droit, le compte doit avoir la permission RBAC de l'espace
        Sanctum::actingAs($coach, self::FEDERATION);
        $this->getJson('/api/v1/dtn/players')->assertForbidden();
        Sanctum::actingAs($dtn, self::CLUB);
        $this->getJson("/api/v1/club/selections/{$id}")->assertForbidden();

        // Lecture seule : pas d'écriture
        Sanctum::actingAs($coach, [ApiAbilities::CLUB_SELECTIONS_READ]);
        $this->getJson("/api/v1/club/selections/{$id}")->assertOk();
        $this->putJson("/api/v1/club/selections/{$id}/departure", ['send' => true])->assertForbidden();
        $this->assertSame(NationalSelection::STATUS_CONVOKED, NationalSelection::findOrFail($id)->status);

        // Périmètre : autre club, autre fédération
        Sanctum::actingAs($this->user('club_admin', ['club_id' => $this->otherClubId]), self::CLUB);
        $this->getJson("/api/v1/club/selections/{$id}")->assertNotFound();
        $this->getJson('/api/v1/club/selections')->assertOk()->assertJsonCount(0, 'data');
        $otherAssociation = (int) DB::table('associations')->where('id', '!=', $this->associationId)->max('id');
        Sanctum::actingAs($this->user('dtn', ['association_id' => $otherAssociation]), self::FEDERATION);
        $this->getJson("/api/v1/dtn/selections/{$id}")->assertNotFound();

        // Sans jeton
        $this->app['auth']->forgetGuards();
        $this->getJson('/api/v1/dtn/players')->assertUnauthorized();
    }

    public function test_medical_data_needs_the_medical_ability_and_a_medical_role(): void
    {
        $id = $this->convokeViaApi($this->dtn());
        $doctor = $this->user('club_medical', ['club_id' => $this->clubId]);

        // Sans le droit médical, la partie médicale est ignorée à l'écriture
        Sanctum::actingAs($doctor, self::CLUB);
        $this->putJson("/api/v1/club/selections/{$id}/departure", [
            'medical' => ['current_injuries' => 'NE DOIT PAS ETRE ENREGISTRE'], 'fitness_status' => 'unfit',
        ])->assertOk();
        $this->assertEmpty(NationalSelection::findOrFail($id)->departure->medical);

        // Avec le droit médical et un rôle médical
        Sanctum::actingAs($doctor, [...self::CLUB, ApiAbilities::MEDICAL]);
        $this->putJson("/api/v1/club/selections/{$id}/departure", [
            'medical' => ['current_injuries' => 'Gêne aux ischio-jambiers'], 'fitness_status' => 'fit_with_restrictions', 'send' => true,
        ])->assertOk();
        $this->getJson("/api/v1/club/selections/{$id}")->assertJsonMissingPath('data.departure_report.medical');
        $this->getJson("/api/v1/club/selections/{$id}?include_medical=1")
            ->assertJsonPath('data.departure_report.medical.current_injuries', 'Gêne aux ischio-jambiers')
            ->assertJsonPath('data.medical_included', true);

        // Médecin de la fédération : visible ; DTN non médical même avec le droit : invisible
        Sanctum::actingAs($this->user('association_medical', ['association_id' => $this->associationId]), [...self::FEDERATION, ApiAbilities::MEDICAL]);
        $this->getJson("/api/v1/dtn/selections/{$id}?include_medical=1")
            ->assertJsonPath('data.departure_report.medical.current_injuries', 'Gêne aux ischio-jambiers');
        Sanctum::actingAs($this->dtn(), [...self::FEDERATION, ApiAbilities::MEDICAL]);
        $this->getJson("/api/v1/dtn/selections/{$id}?include_medical=1")
            ->assertJsonMissingPath('data.departure_report.medical')
            ->assertJsonPath('data.departure_report.fitness_status', 'fit_with_restrictions');

        // Un rôle non médical ne peut pas obtenir de jeton médical
        $this->assertNotContains(ApiAbilities::MEDICAL, ApiAbilities::forSpace('club', $this->user('club_admin', ['club_id' => $this->clubId]), true));
        $this->assertContains(ApiAbilities::MEDICAL, ApiAbilities::forSpace('club', $doctor, true));
    }
}
