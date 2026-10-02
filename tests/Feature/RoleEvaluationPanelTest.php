<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Section « Évaluation joueur — rôle et apport » du cockpit : état du club en
 * langage clair, parcours de mise à jour réservé aux comptes autorisés.
 */
class RoleEvaluationPanelTest extends TestCase
{
    use DatabaseTransactions;

    private int $clubId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('role-eval:generate-demo', ['--seed' => 7, '--clubs' => 4])->assertExitCode(0);
        $competitionId = DB::table('matches')->max('competition_id');
        $this->clubId = (int) DB::table('matches')->where('competition_id', $competitionId)->min('home_club_id');
    }

    private function user(string $role, array $attributes = []): User
    {
        return User::factory()->create(array_merge(['role' => $role, 'tenant_id' => $role === 'system_admin' ? null : 1, 'club_id' => null, 'status' => 'active'], $attributes));
    }

    public function test_locked_state_and_guided_steps_for_an_authorised_account(): void
    {
        DB::table('role_config_versions')->update(['status' => 'draft']);
        $page = $this->actingAs($this->user('system_admin'))->get(route('modules.coach-cockpit', ['club_id' => $this->clubId]))->assertOk();
        $page->assertSee('data-role-eval-state="locked"', false)->assertSee('Calcul impossible pour l')
            ->assertSee('Grille de pondération')->assertSee('Publier une grille')->assertSee('Données de performance')->assertSee('Calcul des scores')
            ->assertSee('Comment lire le score ?')->assertDontSee('dry-run')->assertDontSee('PostgreSQL');

        $teamIds = DB::table('teams')->where('club_id', $this->clubId)->pluck('id');
        $played = DB::table('match_participations')->whereIn('team_id', $teamIds)->distinct()->count('player_id');
        $page->assertSee("sur <b>{$played}</b> ayant joué", false);
    }

    public function test_coverage_reflects_the_club_once_a_grid_is_published(): void
    {
        $configId = DB::table('role_config_versions')->insertGetId(['label' => 'Grille test', 'status' => 'published', 'published_at' => now(), 'created_at' => now(), 'updated_at' => now()]);
        $page = $this->actingAs($this->user('system_admin'))->get(route('modules.coach-cockpit', ['club_id' => $this->clubId]))->assertOk();
        $page->assertSee('Publiée : Grille test')->assertSee('Calculer les scores de l');
        $this->assertStringContainsString('name="config_version" value="' . $configId . '"', $page->getContent()) ;
        $this->assertMatchesRegularExpression('/data-role-eval-state="(to_compute|partial|up_to_date)"/', $page->getContent());
    }

    public function test_accounts_without_the_permission_see_the_status_but_no_actions(): void
    {
        $page = $this->actingAs($this->user('club_admin', ['club_id' => $this->clubId]))->get(route('modules.coach-cockpit'))->assertOk();
        $page->assertSee('Évaluation joueur — rôle et apport')->assertSee('joueur(s) évalué(s)')
            ->assertSee('réservée aux comptes autorisés')->assertDontSee('Calculer les scores de l')->assertDontSee('Importer un fichier de données');
    }
}
