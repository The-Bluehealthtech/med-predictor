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
        // Deux fédérations propres au test (la base de test peut ne pas en contenir)
        $this->associationId = (int) DB::table('associations')->insertGetId(['name' => 'Fédération test', 'country' => 'Tunisie', 'created_at' => now(), 'updated_at' => now()]);
        DB::table('associations')->insert(['name' => 'Autre fédération test', 'country' => 'Maroc', 'created_at' => now(), 'updated_at' => now()]);
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

        // Le club complète et envoie l'état de départ ; la DTN ne voit pas le brouillon
        $coach = $this->user('club_admin', ['club_id' => $this->clubId]);
        $this->actingAs($coach)->post(route('club.selections.departure', $selection), ['vigilance' => 'BROUILLON CLUB', 'action' => 'save'])->assertRedirect();
        $this->actingAs($dtn)->get(route('dtn.selections.show', $selection))->assertOk()->assertDontSee('BROUILLON CLUB')->assertSee('En attente de l');
        $this->actingAs($coach)->post(route('club.selections.departure', $selection), [
            'availability' => 'available_limited', 'load_recommendation' => '60 minutes maximum', 'action' => 'send',
        ])->assertRedirect();
        $this->assertSame(NationalSelection::STATUS_DEPARTURE_SENT, $selection->fresh()->status);
        $this->assertSame('60 minutes maximum', $selection->fresh()->departure->content['load_recommendation']);

        // Le club ne peut plus le modifier ; la DTN n'a pas accès à l'espace club
        $this->actingAs($coach)->post(route('club.selections.departure', $selection), ['action' => 'save'])->assertForbidden();
        $this->actingAs($dtn)->post(route('club.selections.departure', $selection), ['action' => 'save'])->assertRedirect(route('dashboard'));
        $this->assertSame(NationalSelection::STATUS_DEPARTURE_SENT, $selection->fresh()->status);
        // Le club n'a pas accès à l'espace fédération
        $this->actingAs($coach)->post(route('dtn.selections.return', $selection), ['action' => 'save'])->assertRedirect(route('dashboard'));
        $this->assertNull($selection->fresh()->returnReport);


        // Le brouillon de retour reste invisible pour le club
        $this->actingAs($dtn)->post(route('dtn.selections.return', $selection), ['matches' => 1, 'incidents' => 'BROUILLON DTN', 'action' => 'save']);
        $draft = $this->actingAs($coach)->get(route('club.selections.show', $selection))->assertOk()->assertDontSee('BROUILLON DTN');
        $this->assertNull($draft->viewData('returnReport'));
        $this->assertNull($draft->viewData('performance'));

        $this->actingAs($dtn)->post(route('dtn.selections.return', $selection), [
            'matches' => 2, 'minutes' => 150, 'avg_rating' => 7.0, 'staff_evaluation' => 8,
            'fatigue_level' => 'medium', 'injury_risk' => 'low', 'incidents' => 'Aucun', 'action' => 'send',
        ])->assertRedirect();
        $this->assertTrue($this->actingAs($coach)->get(route('club.selections.returns'))->viewData('groups')[0]['items']->contains('id', $selection->id), 'retour à lire');
        $page = $this->actingAs($coach)->get(route('club.selections.show', $selection))->assertOk()->assertSee('Aucun');
        $this->assertSame(NationalSelection::STATUS_RETURN_SENT, $selection->fresh()->status);
        $this->assertEqualsWithDelta(74.0, $page->viewData('performance')['index'], 0.01, '0,6 × 70 + 0,4 × 80');

        // Le club accuse réception : clôture
        $this->actingAs($coach)->post(route('club.selections.acknowledge', $selection))->assertRedirect();
        $this->assertSame(NationalSelection::STATUS_CLOSED, $selection->fresh()->status);
        $this->assertSame(NationalSelectionReport::STATUS_ACKNOWLEDGED, $selection->fresh()->returnReport->status);
    }

    public function test_medical_section_is_only_for_medical_roles(): void
    {
        $dtn = $this->user('dtn', ['association_id' => $this->associationId]);
        $selection = $this->convoke($dtn);

        // Un non-médical du club ne peut pas écrire la partie médicale
        $coach = $this->user('club_admin', ['club_id' => $this->clubId]);
        $this->actingAs($coach)->post(route('club.selections.departure', $selection), [
            'medical' => ['current_injuries' => 'NE DOIT PAS ETRE ENREGISTRE'], 'fitness_status' => 'unfit', 'action' => 'save',
        ]);
        $this->assertEmpty($selection->fresh()->departure->medical);
        $this->assertNull($selection->fresh()->departure->fitness_status);

        // Le médecin du club la renseigne
        $doctor = $this->user('club_medical', ['club_id' => $this->clubId]);
        $this->actingAs($doctor)->post(route('club.selections.departure', $selection), [
            'medical' => ['current_injuries' => 'Gêne aux ischio-jambiers'], 'fitness_status' => 'fit_with_restrictions', 'action' => 'send',
        ])->assertRedirect();
        $departure = $selection->fresh()->departure;
        $this->assertSame('Gêne aux ischio-jambiers', $departure->medical['current_injuries']);
        $this->assertArrayNotHasKey('medical', $departure->toArray(), 'jamais sérialisée');

        // Visible par le médecin de la fédération, invisible pour le DTN, l'entraîneur et l'admin système
        $fedDoctor = $this->user('association_medical', ['association_id' => $this->associationId]);
        $this->actingAs($fedDoctor)->get(route('dtn.selections.show', $selection))->assertOk()->assertSee('Gêne aux ischio-jambiers');
        $this->actingAs($doctor)->get(route('club.selections.show', $selection))->assertOk()->assertSee('Gêne aux ischio-jambiers');
        foreach ([[$dtn, 'dtn.selections.show'], [$coach, 'club.selections.show'], [$this->user('system_admin'), 'dtn.selections.show']] as [$viewer, $route]) {
            $response = $this->actingAs($viewer)->get(route($route, $selection))->assertOk();
            $response->assertDontSee('Gêne aux ischio-jambiers');
            $response->assertSee('Apte avec restrictions');
            $this->assertNull($response->viewData('departureMedical'));
        }
    }

    public function test_selections_are_partitioned_by_club_and_federation(): void
    {
        $selection = $this->convoke($this->user('dtn', ['association_id' => $this->associationId]));
        $otherAssociation = (int) DB::table('associations')->where('id', '!=', $this->associationId)->max('id');

        $this->actingAs($this->user('club_admin', ['club_id' => $this->otherClubId]))
            ->get(route('club.selections.show', $selection))->assertNotFound();
        $this->actingAs($this->user('dtn', ['association_id' => $otherAssociation]))
            ->get(route('dtn.selections.show', $selection))->assertNotFound();

        $list = $this->actingAs($this->user('club_admin', ['club_id' => $this->clubId]))->get(route('club.selections.index'))->assertOk();
        $this->assertTrue($list->viewData('groups')[0]['items']->contains('id', $selection->id), 'le club doit préparer l\'état de départ');
        $this->assertFalse($this->actingAs($this->user('club_admin', ['club_id' => $this->otherClubId]))
            ->get(route('club.selections.index'))->viewData('groups')[0]['items']->contains('id', $selection->id));
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
        $this->actingAs($this->user('association_medical', ['association_id' => $this->associationId]))->get(route('dtn.selections.create'))->assertForbidden();
        $this->actingAs($this->user('player'))->get(route('dtn.index'))->assertRedirect(route('dashboard'));
        $this->actingAs($this->user('player'))->get(route('club.selections.index'))->assertRedirect(route('dashboard'));

        $this->app['auth']->forgetGuards();
        $this->get(route('dtn.index'))->assertRedirect(route('login'));
    }

    public function test_the_two_spaces_are_separated_by_rbac_in_the_interface(): void
    {
        $dtn = $this->user('dtn', ['association_id' => $this->associationId]);
        $coach = $this->user('club_admin', ['club_id' => $this->clubId]);

        // Espace fédération : DTN oui, club non
        $this->actingAs($dtn)->get(route('dtn.index'))->assertOk()->assertSee('Direction technique nationale');
        $this->actingAs($dtn)->get(route('dtn.players.index'))->assertOk();
        $this->actingAs($dtn)->get(route('dtn.players.show', $this->playerId))->assertOk()->assertSee('Historique des sélections');
        $this->actingAs($dtn)->get(route('dtn.selections.create', ['player_id' => $this->playerId]))->assertOk();
        foreach (['dtn.index', 'dtn.players.index', 'dtn.selections.create', 'dtn.api-access'] as $route) {
            $this->actingAs($dtn)->get(route($route))->assertOk()->assertSee('Espace fédération')->assertDontSee('Espace club');
            $this->actingAs($coach)->get(route($route))->assertRedirect(route('dashboard'));
        }
        $this->actingAs($coach)->post(route('dtn.selections.store'), ['player_id' => $this->playerId])->assertRedirect(route('dashboard'));
        $this->assertSame(0, NationalSelection::query()->count());

        // Espace club : club oui, DTN non
        $this->actingAs($coach)->get(route('club.selections.index'))->assertOk()->assertSee('Convocations reçues');
        foreach (['club.selections.index', 'club.selections.returns', 'club.selections.api-access'] as $route) {
            $this->actingAs($coach)->get(route($route))->assertOk()->assertSee('Espace club')->assertDontSee('Espace fédération');
            $this->actingAs($dtn)->get(route($route))->assertRedirect(route('dashboard'));
        }

        // Une sélection s'ouvre dans l'espace de chacun, jamais dans celui de l'autre
        $selection = $this->convoke($dtn);
        $this->actingAs($dtn)->get(route('club.selections.show', $selection))->assertRedirect(route('dashboard'));
        $this->actingAs($coach)->get(route('dtn.selections.show', $selection))->assertRedirect(route('dashboard'));

        // Page des modules : une section par espace, visible seulement avec la permission de l'espace
        $cards = [
            ['name' => 'Convocations et retours', 'description' => 'x', 'icon' => 'x', 'route' => 'dtn.index', 'status' => 'active', 'color' => 'indigo', 'category' => 'dtn'],
            ['name' => 'Fiches joueurs', 'description' => 'x', 'icon' => 'x', 'route' => 'dtn.players.index', 'status' => 'active', 'color' => 'indigo', 'category' => 'dtn'],
            ['name' => 'Convocations reçues', 'description' => 'x', 'icon' => 'x', 'route' => 'club.selections.index', 'status' => 'active', 'color' => 'emerald', 'category' => 'club_selections'],
            ['name' => 'Retours de sélection', 'description' => 'x', 'icon' => 'x', 'route' => 'club.selections.returns', 'status' => 'active', 'color' => 'emerald', 'category' => 'club_selections'],
        ];
        if (!\Illuminate\Support\Facades\Route::has('logout')) {
            \Illuminate\Support\Facades\Route::post('/logout', fn () => null)->name('logout');
            \Illuminate\Support\Facades\Route::getRoutes()->refreshNameLookups();
        }
        $this->actingAs($dtn);
        $html = view('modules.index', ['modules' => $cards])->render();
        $this->assertStringContainsString('data-category="dtn"', $html);
        $this->assertStringNotContainsString('data-category="club_selections"', $html);
        $this->assertStringNotContainsString('Retours de sélection', $html);
        $this->actingAs($coach);
        $html = view('modules.index', ['modules' => $cards])->render();
        $this->assertStringContainsString('data-category="club_selections"', $html);
        $this->assertStringNotContainsString('data-category="dtn"', $html);
        $this->assertStringNotContainsString('Fiches joueurs', $html);
        $this->actingAs($this->user('system_admin'));
        $html = view('modules.index', ['modules' => $cards])->render();
        $this->assertStringContainsString('data-category="dtn"', $html);
        $this->assertStringContainsString('data-category="club_selections"', $html);

        // Visibilité calculée par DtnAccess
        $access = app(\App\Services\Dtn\DtnAccess::class);
        $this->assertTrue($access->isDtnSide($dtn) && !$access->isClubSide($dtn));
        $this->assertTrue($access->isClubSide($coach) && !$access->isDtnSide($coach));
    }

    public function test_each_space_creates_and_revokes_its_own_tokens(): void
    {
        $dtn = $this->user('dtn', ['association_id' => $this->associationId]);
        $this->actingAs($dtn)->post(route('dtn.api-access.store'), ['name' => 'Logiciel DTN', 'expires_in_days' => 90, 'with_medical' => 1])
            ->assertRedirect(route('dtn.api-access'))->assertSessionHas('plain_token');
        $this->actingAs($dtn)->get(route('dtn.api-access'))->assertOk()->assertSee('dtn:players:read')->assertSee('/dtn/selections/{id}/return');
        $token = $dtn->tokens()->firstOrFail();
        $this->assertSame('dtn-federation:Logiciel DTN', $token->name);
        $this->assertEqualsCanonicalizing(['dtn:players:read', 'dtn:selections:read', 'dtn:selections:write'], $token->abilities,
            'pas de droit médical pour un DTN non médical');
        $this->assertNotNull($token->expires_at);

        $doctor = $this->user('club_medical', ['club_id' => $this->clubId]);
        $this->actingAs($doctor)->post(route('club.selections.api-access.store'), ['name' => 'Logiciel médical', 'expires_in_days' => 30, 'with_medical' => 1]);
        $this->assertContains('selections:medical', $doctor->tokens()->firstOrFail()->abilities);
        $this->actingAs($doctor)->get(route('club.selections.api-access'))->assertOk()->assertSee('Logiciel médical')->assertSee('/club/selections/{id}/departure');

        // Un espace ne voit ni ne révoque les jetons de l'autre
        $this->actingAs($doctor)->delete(route('club.selections.api-access.destroy', $token->id))->assertNotFound();
        $this->actingAs($dtn)->delete(route('dtn.api-access.destroy', $token->id))->assertRedirect(route('dtn.api-access'));
        $this->assertSame(0, $dtn->tokens()->count());
    }
}
