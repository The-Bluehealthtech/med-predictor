<?php

namespace Tests\Feature;

use App\Http\Controllers\DashboardController;
use App\Models\Player;
use App\Models\User;
use App\Services\Dashboard\GeneralDashboard;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Tableau de bord général affiché après la connexion : blocs réels ou masqués,
 * droits par domaine, journal d'activité.
 */
class GeneralDashboardTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        // routes/testing.php garde une cible /dashboard minimale ; le vrai contrôleur est monté ici.
        Route::middleware('web')->get('/_test/general-dashboard', [DashboardController::class, 'index']);
    }

    private function user(string $role, array $extra = []): User
    {
        $clubId = $extra['club_id'] ?? null;

        return User::factory()->create(array_merge(['role' => $role, 'status' => 'active', 'tenant_id' => 1, 'club_id' => $clubId], $extra));
    }

    public function test_admin_sees_every_domain_and_no_invented_figures(): void
    {
        $admin = $this->user('system_admin');
        $response = $this->actingAs($admin)->get('/_test/general-dashboard')->assertOk();

        $board = $response->viewData('board');
        $this->assertSame(['clinique', 'performance', 'selections', 'administration'], array_keys($board['domains']));
        $kpis = collect($board['kpis'])->keyBy('key');
        $this->assertSame(DB::table('players')->where('status', 'active')->count(), $kpis['players']['value']);
        $this->assertNull($kpis['completeness']['value'], 'aucune règle de complétude : non disponible, jamais estimé');
        $this->assertGreaterThan(0, $kpis['modules']['value']);
        $this->assertSame(count(config('fit_modules.modules')), array_sum($board['modules']), "l'admin voit tout le catalogue");
        $performance = array_column($board['cards']['performance']['metrics'], 0);
        $this->assertNotContains('Points d\'attention sur la forme', $performance, 'compteur réservé aux comptes club : retiré pour un admin');

        $response->assertSee('data-general-dashboard', false)->assertSee('Accéder aux modules')
            ->assertSee(route('modules.index'), false)->assertSee(route('modules.index', ['section' => 'clinique']), false)
            ->assertSee('Non disponible')->assertSee('data-activity-ready="0"', false);
    }

    public function test_club_account_sees_its_scope_without_the_clinic(): void
    {
        $clubId = (int) DB::table('clubs')->insertGetId(['name' => 'Club Tableau Test', 'created_at' => now(), 'updated_at' => now()]);
        $user = $this->user('club_admin', ['club_id' => $clubId]);

        $board = $this->actingAs($user)->get('/_test/general-dashboard')->assertOk()->viewData('board');

        $this->assertArrayNotHasKey('clinique', $board['domains']);
        $this->assertArrayNotHasKey('clinique', $board['cards']);
        $this->assertNotContains('pcma_pending', array_column($board['kpis'], 'key'));
        $this->assertArrayNotHasKey('clinique', $board['modules']);
        $this->assertSame('Votre club', $board['scope']);
        $this->assertSame(0, collect($board['kpis'])->firstWhere('key', 'players')['value']);
    }

    public function test_player_is_sent_to_the_player_portal(): void
    {
        $this->actingAs($this->user('player'))->get('/_test/general-dashboard')->assertRedirect(route('test.portail.joueur.simple'));
    }

    public function test_actions_of_a_signed_in_user_are_journaled_and_cli_writes_are_not(): void
    {
        $clubId = (int) DB::table('clubs')->insertGetId(['name' => 'Club Journal Test', 'created_at' => now(), 'updated_at' => now()]);
        $before = DB::table('platform_activities')->count();

        Player::withoutGlobalScopes()->forceCreate(['name' => 'Sans Session', 'first_name' => 'Sans', 'last_name' => 'Session', 'club_id' => $clubId]);
        $this->assertSame($before, DB::table('platform_activities')->count(), 'sans utilisateur connecté (import, seed), rien n\'est journalisé');

        $user = $this->user('club_admin', ['club_id' => $clubId]);
        Auth::login($user);
        $player = Player::withoutGlobalScopes()->forceCreate(['name' => 'Avec Session', 'first_name' => 'Avec', 'last_name' => 'Session', 'club_id' => $clubId]);

        $row = DB::table('platform_activities')->orderByDesc('id')->first();
        $this->assertSame('administration', $row->domain);
        $this->assertSame('Joueur enregistré', $row->action);
        $this->assertSame((int) $player->id, (int) $row->subject_id);
        $this->assertSame($clubId, (int) $row->club_id);

        $board = app(GeneralDashboard::class)->forUser($user);
        $this->assertSame('Joueur enregistré', $board['activity']['recent'][0]['action']);
        $this->assertFalse($board['activity']['ready']);
    }

    public function test_curves_appear_only_with_thirty_days_of_real_history(): void
    {
        $admin = $this->user('system_admin');
        DB::table('platform_activities')->insert([
            ['user_id' => $admin->id, 'domain' => 'administration', 'action' => 'Licence : statut modifié', 'created_at' => now()->subDays(40)],
            ['user_id' => $admin->id, 'domain' => 'performance', 'action' => 'Export de statistiques joueurs importé', 'created_at' => now()->subDays(2)],
            ['user_id' => $admin->id, 'domain' => 'performance', 'action' => 'Scores « Rôle et apport » calculés', 'created_at' => now()],
        ]);

        $response = $this->actingAs($admin)->get('/_test/general-dashboard')->assertOk();
        $activity = $response->viewData('board')['activity'];

        $this->assertTrue($activity['ready']);
        $this->assertCount(GeneralDashboard::HISTORY_DAYS, $activity['daily']);
        $this->assertGreaterThanOrEqual(2, $activity['by_domain']['performance']);
        $response->assertSee('data-activity-ready="1"', false)->assertSee('Volume par domaine');
    }
}
