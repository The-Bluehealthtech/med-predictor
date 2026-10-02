<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CoachCockpitTest extends TestCase
{
    use DatabaseTransactions;

    /** @var array<int, int> */
    private array $clubIds = [];

    protected function setUp(): void
    {
        parent::setUp();

        // Championnat démo de 4 clubs : double aller-retour, 6 matchs par équipe.
        $this->artisan('role-eval:generate-demo', ['--seed' => 7, '--clubs' => 4])->assertExitCode(0);
        $competitionId = DB::table('matches')->max('competition_id');
        $this->clubIds = DB::table('matches')->where('competition_id', $competitionId)
            ->pluck('home_club_id')->unique()->sort()->values()->map(fn ($id) => (int) $id)->all();
        $this->assertCount(4, $this->clubIds);
    }

    /** Un tenant_id est requis pour tout rôle hors system_admin (middleware TenantEnforcer). */
    private function actingAsRole(string $role, ?int $clubId = null): void
    {
        $user = new User();
        $user->forceFill([
            'id' => 900101, 'name' => 'Coach Cockpit Test', 'email' => 'coach-cockpit-test@example.invalid',
            'role' => $role, 'club_id' => $clubId, 'player_id' => null, 'association_id' => null, 'tenant_id' => 1, 'status' => 'active',
        ]);
        $user->exists = true;
        $this->actingAs($user);
    }

    public function test_admin_sees_the_cockpit_of_the_selected_team_with_model_inputs(): void
    {
        $this->actingAsRole('system_admin');
        $clubId = $this->clubIds[1];

        $response = $this->get(route('modules.coach-cockpit', ['club_id' => $clubId]));

        $response->assertOk();
        $response->assertSee('window.COACH_COCKPIT_DATA', false);
        $cockpit = $response->viewData('cockpit');
        $this->assertSame($clubId, $cockpit['club']['id']);
        $this->assertCount(6, $cockpit['matches']);
        $this->assertCount(4, $cockpit['table']);
        // 12 matchs : 3 points par match décidé, 2 par nul ; chaque nul est compté par les deux équipes.
        $this->assertSame(36, array_sum(array_column($cockpit['table'], 'pts')) + intdiv(array_sum(array_column($cockpit['table'], 'd')), 2));

        $squadIds = DB::table('players')->where('club_id', $clubId)->pluck('id')->map(fn ($id) => (int) $id)->all();
        $model = $cockpit['model'];
        $this->assertEqualsCanonicalizing(
            array_values(array_intersect($squadIds, array_unique(array_column($cockpit['pm'], 0)))),
            array_map('intval', array_keys($model['players'])),
            'chaque joueur ayant joué reçoit ses caractéristiques'
        );
        $first = reset($model['players']);
        $this->assertSame($model['fams'], array_keys($first), 'une entrée par famille de poste');
        foreach ($model['feats'] as $feature) {
            if (!str_starts_with($feature, 'pos_') && !str_starts_with($feature, 'opp_') && !str_starts_with($feature, 'own_') && $feature !== 'home') {
                $this->assertArrayHasKey($feature, $first['milieu relayeur'], "caractéristique {$feature} calculée");
            }
        }
        $this->assertCount(4, $model['opponents']);
    }

    public function test_team_selector_lists_every_team_with_match_data_for_an_admin(): void
    {
        $this->actingAsRole('system_admin');

        $clubs = $this->get(route('modules.coach-cockpit'))->assertOk()->viewData('clubs');

        foreach ($this->clubIds as $clubId) {
            $this->assertTrue($clubs->contains('id', $clubId));
        }
    }

    public function test_club_user_only_sees_their_own_club(): void
    {
        $own = $this->clubIds[2];
        $this->actingAsRole('club_admin', $own);

        $response = $this->get(route('modules.coach-cockpit'));
        $response->assertOk();
        $this->assertSame($own, $response->viewData('cockpit')['club']['id']);
        $this->assertSame([$own], $response->viewData('clubs')->pluck('id')->map(fn ($id) => (int) $id)->all());

        $this->get(route('modules.coach-cockpit', ['club_id' => $this->clubIds[0]]))->assertNotFound();
    }

    public function test_player_accounts_cannot_open_the_cockpit(): void
    {
        $this->actingAsRole('player');

        $this->get(route('modules.coach-cockpit'))->assertForbidden();
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('modules.coach-cockpit'))->assertRedirect(route('login'));
    }

    public function test_a_club_without_match_sheets_opens_on_its_club_sheet_with_season_profiles(): void
    {
        $this->actingAsRole('system_admin');
        $clubId = (int) DB::table('clubs')->insertGetId(['name' => 'Club Fiche Test', 'founded_year' => 1957, 'created_at' => now(), 'updated_at' => now()]);
        $playerId = (int) DB::table('players')->insertGetId(['name' => 'Karim Fiche', 'first_name' => 'Karim', 'last_name' => 'Fiche', 'club_id' => $clubId, 'created_at' => now(), 'updated_at' => now()]);
        foreach (['minutes_played' => 799, 'goals' => 0.25, 'passes_accuracy' => 0.9018] as $name => $value) {
            DB::table('external_player_performance_metrics')->insert(['player_id' => $playerId, 'metric_name' => $name, 'metric_value' => $value,
                'source' => 'TEST', 'season' => '2026/27', 'competition' => 'Ligue test', 'measured_at' => '2026-09-27', 'score_origin' => 'observed',
                'raw_data' => json_encode(['Position' => 'CDM', 'Nationality' => 'Tunisia']), 'created_at' => now(), 'updated_at' => now()]);
        }

        // Cas de la prod : un score saisi dans le module compétitions, sans feuille de match.
        $competitionId = (int) DB::table('matches')->max('competition_id');
        $opponent = $this->clubIds[0];
        DB::table('matches')->insert(['competition_id' => $competitionId, 'home_club_id' => $clubId, 'away_club_id' => $opponent,
            'home_score' => 1, 'away_score' => 0, 'match_date' => '2026-09-20', 'created_at' => now(), 'updated_at' => now()]);

        $clubs = $this->get(route('modules.coach-cockpit'))->assertOk()->viewData('clubs');
        $this->assertTrue($clubs->contains(fn ($c) => $c->id === $clubId && !$c->has_matches), 'tous les clubs de la base sont proposés');

        $response = $this->get(route('modules.coach-cockpit', ['club_id' => $clubId]))->assertOk();
        $this->assertNull($response->viewData('cockpit'));
        $sheet = $response->viewData('sheet');
        $this->assertSame(1, $sheet['summary']['profiles']);
        $this->assertSame('CDM', $sheet['squad'][0]['position']);
        $response->assertSee('data-club-sheet', false)->assertSee('Club Fiche Test')->assertSee('Fondé en 1957')
            ->assertSee('Karim Fiche')->assertSee('90 %')->assertSee('Ligue test 2026/27')->assertSee('fiche club');

        // Seuils minimums : 1 club importé sur 6, le milieu défensif compte 1 régulier, Karim a assez de minutes.
        $t = $sheet['thresholds'];
        $this->assertSame(1, $t['clubs_imported']);
        $this->assertTrue($t['club_imported']);
        $cdm = collect($t['families'])->firstWhere('family', 'milieu défensif');
        $this->assertGreaterThanOrEqual(1, $cdm['regulars']);
        $this->assertSame(1, $cdm['own']);
        $response->assertSee('data-thresholds', false)->assertSee('Seuils minimums')->assertSee('1 / 6 minimum')->assertSee('score possible');
    }
}
