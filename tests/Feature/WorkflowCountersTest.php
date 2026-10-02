<?php

namespace Tests\Feature;

use App\Models\NationalSelection;
use App\Models\User;
use App\Services\Analytics\PlayerFormAnalytics;
use App\Services\Modules\WorkflowCounters;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Compteurs « à faire » des parcours de /modules : ils suivent l'avancement
 * réel, restent dans le périmètre du compte et n'apparaissent qu'à l'étape vue.
 */
class WorkflowCountersTest extends TestCase
{
    use DatabaseTransactions;

    private int $associationId;
    private int $clubId;
    private int $playerId;

    protected function setUp(): void
    {
        parent::setUp();
        if (!Schema::hasTable('national_selections')) {
            (require base_path('database/migrations/2026_10_02_090000_create_national_selections_tables.php'))->up();
        }
        $this->artisan('role-eval:generate-demo', ['--seed' => 11, '--clubs' => 4])->assertExitCode(0);
        $competitionId = DB::table('matches')->max('competition_id');
        $this->clubId = (int) DB::table('matches')->where('competition_id', $competitionId)->min('home_club_id');
        $this->playerId = (int) DB::table('match_participations')->join('teams', 'teams.id', '=', 'match_participations.team_id')
            ->where('teams.club_id', $this->clubId)->value('match_participations.player_id');
        $this->associationId = (int) DB::table('associations')->insertGetId(['name' => 'Fédération test', 'country' => 'Tunisie', 'created_at' => now(), 'updated_at' => now()]);
    }

    private function user(string $role, array $attributes = []): User
    {
        return User::factory()->create(array_merge(['role' => $role, 'tenant_id' => 1, 'club_id' => null, 'association_id' => null, 'status' => 'active'], $attributes));
    }

    private function counters(User $user): array
    {
        Cache::flush();

        return app(WorkflowCounters::class)->forUser($user);
    }

    public function test_selection_counters_follow_the_workflow_in_each_space(): void
    {
        $dtn = $this->user('dtn', ['association_id' => $this->associationId]);
        $coach = $this->user('club_admin', ['club_id' => $this->clubId]);
        $this->actingAs($dtn)->post(route('dtn.selections.store'), [
            'player_id' => $this->playerId, 'association_id' => $this->associationId, 'team_label' => 'Équipe nationale A',
            'event_type' => 'qualifier', 'event_name' => 'Fenêtre test',
            'start_date' => now()->addDays(10)->toDateString(), 'end_date' => now()->addDays(18)->toDateString(),
        ])->assertRedirect();
        $selection = NationalSelection::query()->latest('id')->firstOrFail();

        // ② convoqué : la DTN attend le club, le club doit préparer l'état de départ
        $this->assertSame(1, $this->counters($dtn)['dtn_waiting_club']['count']);
        $this->assertSame(1, $this->counters($coach)['club_departures']['count']);
        $this->assertArrayNotHasKey('dtn_waiting_club', $this->counters($coach), 'aucun compteur de l\'autre espace');
        $this->assertArrayNotHasKey('club_departures', $this->counters($dtn));

        // ③ état de départ envoyé : la DTN doit rédiger le retour
        $this->actingAs($coach)->post(route('club.selections.departure', $selection), ['action' => 'send'])->assertRedirect();
        $this->assertSame(1, $this->counters($dtn)['dtn_returns']['count']);
        $this->assertArrayNotHasKey('club_departures', $this->counters($coach));

        // ④ retour envoyé : le club doit le lire ; ⑤ accusé : plus rien à faire
        $this->actingAs($dtn)->post(route('dtn.selections.return', $selection), ['matches' => 1, 'action' => 'send'])->assertRedirect();
        $this->assertSame(1, $this->counters($coach)['club_returns']['count']);
        $this->assertArrayNotHasKey('dtn_returns', $this->counters($dtn));
        $this->actingAs($coach)->post(route('club.selections.acknowledge', $selection))->assertRedirect();
        $this->assertArrayNotHasKey('club_returns', $this->counters($coach));

        // Un club d'une autre fédération ne voit rien
        $this->assertSame([], array_intersect_key($this->counters($this->user('club_admin', ['club_id' => 999999])), array_flip(['club_departures', 'club_returns'])));
    }

    public function test_performance_and_licence_counters_and_medical_privacy(): void
    {
        $coach = $this->user('club_admin', ['club_id' => $this->clubId]);
        $warnings = collect(app(PlayerFormAnalytics::class)->forClub($this->clubId, '10')['alerts'])->where('level', 'warning')->count();
        $counters = $this->counters($coach);
        $this->assertSame($warnings, $counters['perf_alerts']['count'] ?? 0);
        foreach (['clinic_today', 'clinic_waiting', 'clinic_aut', 'clinic_pcma'] as $key) {
            $this->assertArrayNotHasKey($key, $counters, 'aucun compteur médical pour un compte non médical');
        }

        // La base de test locale peut avoir une table licenses réduite : le compteur est alors ignoré sans erreur.
        if (Schema::hasColumns('licenses', ['club_id', 'association_id', 'license_type'])) {
            DB::table('clubs')->where('id', $this->clubId)->update(['association_id' => $this->associationId]);
            DB::table('licenses')->insert(['license_type' => 'player', 'applicant_name' => 'Test Licence', 'club_id' => $this->clubId,
                'association_id' => $this->associationId, 'status' => 'pending', 'created_at' => now(), 'updated_at' => now()]);
            $admin = $this->user('association_admin', ['association_id' => $this->associationId]);
            $this->assertGreaterThanOrEqual(1, $this->counters($admin)['licences_pending']['count']);
            $this->assertArrayNotHasKey('licences_pending', $this->counters($coach), 'la validation est réservée à la fédération');
        }
    }

    public function test_modules_page_shows_counters_only_on_visible_steps(): void
    {
        $dtn = $this->user('dtn', ['association_id' => $this->associationId]);
        $this->actingAs($dtn)->post(route('dtn.selections.store'), [
            'player_id' => $this->playerId, 'association_id' => $this->associationId, 'team_label' => 'Équipe nationale A',
            'event_type' => 'friendly', 'event_name' => 'Fenêtre test',
            'start_date' => now()->addDays(10)->toDateString(), 'end_date' => now()->addDays(18)->toDateString(),
        ])->assertRedirect();
        if (!Route::has('logout')) {
            Route::post('/logout', fn () => null)->name('logout');
            Route::getRoutes()->refreshNameLookups();
        }
        $cards = [
            ['name' => 'Convocations et retours', 'description' => 'x', 'icon' => 'flag', 'route' => 'dtn.index', 'status' => 'active', 'color' => 'indigo', 'group' => 'dtn', 'category' => 'selections'],
            ['name' => 'Convocations reçues', 'description' => 'x', 'icon' => 'mail', 'route' => 'club.selections.index', 'status' => 'active', 'color' => 'emerald', 'group' => 'club', 'category' => 'selections'],
        ];
        app()->setLocale('fr');
        Cache::flush();
        $this->actingAs($dtn);
        $html = view('modules.index', ['modules' => $cards])->render();
        $this->assertStringContainsString('data-todo="dtn_waiting_club"', $html);
        $this->assertStringContainsString('1 en attente du club', $html);
        $this->assertStringNotContainsString('data-todo="club_departures"', $html);

        $coach = $this->user('club_admin', ['club_id' => $this->clubId]);
        Cache::flush();
        $this->actingAs($coach);
        $html = view('modules.index', ['modules' => $cards])->render();
        $this->assertStringContainsString('data-todo="club_departures"', $html);
        $this->assertStringContainsString('1 à préparer', $html);
        $this->assertStringNotContainsString('data-todo="dtn_waiting_club"', $html);
    }
}
