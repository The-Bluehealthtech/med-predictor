<?php

namespace Tests\Feature;

use App\Models\NationalSelection;
use App\Models\NationalSelectionReport;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Outil DTN : cycle convocation → état de départ → état de retour → clôture,
 * cloisonnement par club et par fédération, et partie médicale réservée aux
 * rôles médicaux.
 */
class DtnSelectionWorkflowTest extends TestCase
{
    use DatabaseTransactions;

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
        $this->associationId = (int) DB::table('associations')->value('id');
    }

    private function user(string $role, array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role' => $role,
            'tenant_id' => $role === 'system_admin' ? null : 1,
            'club_id' => null,
            'association_id' => null,
            'status' => 'active',
        ], $attributes));
    }

    private function convoke(User $dtn): NationalSelection
    {
        $this->actingAs($dtn)->post(route('dtn.selections.store'), [
            'player_id' => $this->playerId,
            'association_id' => $this->associationId,
            'team_label' => 'Équipe nationale A',
            'event_type' => 'qualifier',
            'event_name' => 'Fenêtre de novembre',
            'opponent' => 'Adversaire test',
            'start_date' => now()->addDays(10)->toDateString(),
            'end_date' => now()->addDays(18)->toDateString(),
        ])->assertRedirect();

        return NationalSelection::query()->latest('id')->firstOrFail();
    }

    public function test_full_cycle_with_prefilled_departure_and_performance_index(): void
    {
        $dtn = $this->user('dtn', ['association_id' => $this->associationId]);
        $selection = $this->convoke($dtn);

        $this->assertSame(NationalSelection::STATUS_CONVOKED, $selection->status);
        $this->assertSame($this->clubId, (int) $selection->club_id, 'le club est celui du joueur');
        $snapshot = $selection->departure->snapshot;
        $this->assertGreaterThan(0, $snapshot['season']['matches'], 'état de départ pré-rempli depuis les feuilles de match');
        $this->assertNotEmpty($snapshot['last_matches']);

        // Le club complète et envoie l'état de départ
        $coach = $this->user('club_admin', ['club_id' => $this->clubId]);
        $this->actingAs($coach)->post(route('dtn.selections.departure', $selection), [
            'availability' => 'available_limited', 'load_recommendation' => '60 minutes maximum', 'action' => 'send',
        ])->assertRedirect();
        $this->assertSame(NationalSelection::STATUS_DEPARTURE_SENT, $selection->fresh()->status);
        $this->assertSame('60 minutes maximum', $selection->fresh()->departure->content['load_recommendation']);

        // Le club ne peut plus le modifier ; la DTN ne peut pas écrire l'état de départ
        $this->actingAs($coach)->post(route('dtn.selections.departure', $selection), ['action' => 'save'])->assertForbidden();
        $this->actingAs($dtn)->post(route('dtn.selections.departure', $selection), ['action' => 'save'])->assertForbidden();
        // Le club ne peut pas écrire l'état de retour
        $this->actingAs($coach)->post(route('dtn.selections.return', $selection), ['action' => 'save'])->assertForbidden();

        // La DTN envoie l'état de retour
        $this->actingAs($dtn)->post(route('dtn.selections.return', $selection), [
            'matches' => 2, 'minutes' => 150, 'avg_rating' => 7.0, 'staff_evaluation' => 8,
            'fatigue_level' => 'medium', 'injury_risk' => 'low', 'incidents' => 'Aucun', 'action' => 'send',
        ])->assertRedirect();
        $this->assertSame(NationalSelection::STATUS_RETURN_SENT, $selection->fresh()->status);

        $page = $this->actingAs($coach)->get(route('dtn.selections.show', $selection))->assertOk();
        $this->assertEqualsWithDelta(74.0, $page->viewData('performance')['index'], 0.01, '0,6 × 70 + 0,4 × 80');

        // Le club accuse réception : clôture
        $this->actingAs($coach)->post(route('dtn.selections.acknowledge', $selection))->assertRedirect();
        $this->assertSame(NationalSelection::STATUS_CLOSED, $selection->fresh()->status);
        $this->assertSame(NationalSelectionReport::STATUS_ACKNOWLEDGED, $selection->fresh()->returnReport->status);
    }

    public function test_medical_section_is_only_for_medical_roles(): void
    {
        $dtn = $this->user('dtn', ['association_id' => $this->associationId]);
        $selection = $this->convoke($dtn);

        // Un non-médical du club ne peut pas écrire la partie médicale
        $coach = $this->user('club_admin', ['club_id' => $this->clubId]);
        $this->actingAs($coach)->post(route('dtn.selections.departure', $selection), [
            'medical' => ['current_injuries' => 'NE DOIT PAS ETRE ENREGISTRE'], 'fitness_status' => 'unfit', 'action' => 'save',
        ]);
        $this->assertEmpty($selection->fresh()->departure->medical);
        $this->assertNull($selection->fresh()->departure->fitness_status);

        // Le médecin du club la renseigne
        $doctor = $this->user('club_medical', ['club_id' => $this->clubId]);
        $this->actingAs($doctor)->post(route('dtn.selections.departure', $selection), [
            'medical' => ['current_injuries' => 'Gêne aux ischio-jambiers'], 'fitness_status' => 'fit_with_restrictions', 'action' => 'send',
        ])->assertRedirect();
        $departure = $selection->fresh()->departure;
        $this->assertSame('Gêne aux ischio-jambiers', $departure->medical['current_injuries']);
        $this->assertArrayNotHasKey('medical', $departure->toArray(), 'jamais sérialisée');

        // Visible par le médecin de la fédération, invisible pour le DTN, l'entraîneur et l'admin système
        $fedDoctor = $this->user('association_medical', ['association_id' => $this->associationId]);
        $this->actingAs($fedDoctor)->get(route('dtn.selections.show', $selection))->assertOk()->assertSee('Gêne aux ischio-jambiers');
        foreach ([$dtn, $coach, $this->user('system_admin')] as $viewer) {
            $response = $this->actingAs($viewer)->get(route('dtn.selections.show', $selection))->assertOk();
            $response->assertDontSee('Gêne aux ischio-jambiers');
            $response->assertSee('Apte avec restrictions');
            $this->assertNull($response->viewData('departureMedical'));
        }
    }

    public function test_selections_are_partitioned_by_club_and_federation(): void
    {
        $selection = $this->convoke($this->user('dtn', ['association_id' => $this->associationId]));
        $otherAssociation = (int) DB::table('associations')->where('id', '!=', $this->associationId)->value('id');

        $this->actingAs($this->user('club_admin', ['club_id' => $this->otherClubId]))
            ->get(route('dtn.selections.show', $selection))->assertNotFound();
        $this->actingAs($this->user('dtn', ['association_id' => $otherAssociation]))
            ->get(route('dtn.selections.show', $selection))->assertNotFound();

        $list = $this->actingAs($this->user('club_admin', ['club_id' => $this->clubId]))->get(route('dtn.index'))->assertOk();
        $this->assertTrue($list->viewData('todo')->contains('id', $selection->id), 'le club doit préparer l\'état de départ');
        $this->assertFalse($list->viewData('canConvoke'), 'un club ne convoque pas');
    }

    public function test_demo_command_creates_three_stages_once(): void
    {
        $players = DB::table('match_participations')->join('teams', 'teams.id', '=', 'match_participations.team_id')
            ->where('teams.club_id', $this->clubId)->distinct()->limit(3)->pluck('match_participations.player_id')->implode(',');
        $admin = $this->user('system_admin');
        $args = ['--association' => $this->associationId, '--players' => $players, '--dtn-user' => $admin->id, '--club-user' => $admin->id];

        $this->artisan('dtn:demo-selections', $args)->assertExitCode(0);
        $this->artisan('dtn:demo-selections', $args)->assertExitCode(0);

        $demo = NationalSelection::query()->where('is_demo', true)->where('club_id', $this->clubId)->get();
        $this->assertCount(3, $demo, 'rejouable sans doublon');
        $this->assertEqualsCanonicalizing(
            [NationalSelection::STATUS_CLOSED, NationalSelection::STATUS_DEPARTURE_SENT, NationalSelection::STATUS_CONVOKED],
            $demo->pluck('status')->all()
        );
        $closed = $demo->firstWhere('status', NationalSelection::STATUS_CLOSED);
        $this->assertSame(NationalSelectionReport::STATUS_ACKNOWLEDGED, $closed->returnReport->status);
        $this->assertNotEmpty($closed->departure->snapshot['last_matches']);
    }

    public function test_only_the_federation_side_can_convoke_and_players_are_refused(): void
    {
        $this->actingAs($this->user('club_admin', ['club_id' => $this->clubId]))->get(route('dtn.selections.create'))->assertForbidden();
        $this->actingAs($this->user('player'))->get(route('dtn.index'))->assertForbidden();

        $this->app['auth']->forgetGuards();
        $this->get(route('dtn.index'))->assertRedirect(route('login'));
    }
}
