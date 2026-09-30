<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * IMPORTANT (voir tests/Feature/RoleEvaluationSchemaTest.php et
 * RoleEvaluationImportCommandTest.php, Livrables 1 et 2, même règle) :
 * DatabaseTransactions, JAMAIS RefreshDatabase — phpunit.xml pointe
 * DB_DATABASE sur database/database.sqlite, c'est-à-dire la base de
 * développement elle-même. RefreshDatabase la viderait, ce qui est interdit
 * par le mandat. Chaque test est enveloppé dans une transaction annulée en
 * fin de test : rien n'est conservé, y compris les données générées par
 * `role-eval:generate-demo` elle-même (dont la propre transaction interne
 * s'exécute imbriquée, via SAVEPOINT SQLite — cf. rapport Livrable 2 pour
 * la même mécanique de dry-run).
 *
 * Prérequis : nécessite que les migrations du Livrable 1 aient été
 * exécutées sur la base de test (position_catalog, match_participations,
 * match_team_stats, import_batches...). Si ce n'est pas encore le cas, ces
 * tests échoueront avec le message explicite de
 * DemoDataGenerator::assertPrerequisiteTablesExist() plutôt que
 * silencieusement.
 */
class GenerateDemoDataCommandTest extends TestCase
{
    use DatabaseTransactions;

    public function test_dry_run_does_not_persist_anything(): void
    {
        $before = [
            'clubs' => DB::table('clubs')->count(),
            'teams' => DB::table('teams')->count(),
            'players' => DB::table('players')->count(),
            'matches' => DB::table('matches')->count(),
            'import_batches' => DB::table('import_batches')->count(),
        ];

        $exitCode = $this->artisan('role-eval:generate-demo', ['--seed' => 1, '--clubs' => 4, '--dry-run' => true])
            ->run();

        $this->assertSame(0, $exitCode);

        $after = [
            'clubs' => DB::table('clubs')->count(),
            'teams' => DB::table('teams')->count(),
            'players' => DB::table('players')->count(),
            'matches' => DB::table('matches')->count(),
            'import_batches' => DB::table('import_batches')->count(),
        ];

        $this->assertSame($before, $after, 'un --dry-run ne doit rien laisser en base');
    }

    public function test_generation_creates_only_is_demo_rows_with_expected_volume(): void
    {
        // 4 clubs -> double round-robin -> 2*(4-1)=6 matchs/équipe, 12 matchs au total.
        $exitCode = $this->artisan('role-eval:generate-demo', ['--seed' => 42, '--clubs' => 4])->run();
        $this->assertSame(0, $exitCode);

        $batch = DB::table('import_batches')->where('batch_type', 'demo_generation')->latest('id')->first();
        $this->assertNotNull($batch);
        $this->assertSame('completed', $batch->status);

        $matches = DB::table('matches')->where('import_batch_id', $batch->id)->get();
        $this->assertCount(12, $matches);
        foreach ($matches as $m) {
            $this->assertEquals(1, $m->is_demo);
        }

        $teams = DB::table('teams')->where('import_batch_id', $batch->id)->get();
        $this->assertCount(4, $teams);
        foreach ($teams as $t) {
            $this->assertEquals(1, $t->is_demo);
        }

        $players = DB::table('players')->where('import_batch_id', $batch->id)->get();
        $this->assertCount(4 * 20, $players); // 4 clubs * ~20 joueurs (gabarit exact)
        foreach ($players as $p) {
            $this->assertEquals(1, $p->is_demo);
        }

        $participations = DB::table('match_participations')->where('import_batch_id', $batch->id)->get();
        foreach ($participations as $part) {
            $this->assertEquals(1, $part->is_demo);
        }

        // Aucun produit cartésien : le nombre de participations par match
        // (les deux équipes confondues, ~11 titulaires + quelques
        // remplaçants entrés par équipe) reste dans une fourchette réaliste,
        // très loin de (joueurs de démo) x (matchs de démo) = 80 x 12 = 960.
        $participationsPerMatch = count($participations) / count($matches);
        $this->assertGreaterThanOrEqual(20, $participationsPerMatch);
        $this->assertLessThanOrEqual(40, $participationsPerMatch);
        $this->assertLessThan(
            count($players) * count($matches),
            count($participations),
            'le nombre de participations doit rester loin du produit cartésien joueurs x matchs'
        );

        $playerStats = DB::table('player_match_detailed_stats')->where('import_batch_id', $batch->id)->get();
        $this->assertCount(count($participations), $playerStats, 'une ligne de stats par participation, ni plus ni moins');

        $teamStats = DB::table('match_team_stats')->where('import_batch_id', $batch->id)->get();
        $this->assertCount(12 * 2, $teamStats); // une ligne par équipe par match

        $events = DB::table('match_events')->where('import_batch_id', $batch->id)->get();
        $this->assertNotEmpty($events);
        foreach ($events as $e) {
            $this->assertContains($e->type, ['goal', 'yellow_card', 'red_card', 'substitution'], 'aucun type hors du CHECK existant sur match_events.type');
            $this->assertEquals(1, $e->is_demo);
        }
    }

    public function test_score_is_coherent_with_goal_events(): void
    {
        $this->artisan('role-eval:generate-demo', ['--seed' => 7, '--clubs' => 4])->run();
        $batch = DB::table('import_batches')->where('batch_type', 'demo_generation')->latest('id')->first();

        $matches = DB::table('matches')->where('import_batch_id', $batch->id)->get();
        foreach ($matches as $m) {
            $homeGoals = DB::table('match_events')
                ->where('match_id', $m->id)->where('type', 'goal')->where('team_id', $m->home_team_id)->count();
            $awayGoals = DB::table('match_events')
                ->where('match_id', $m->id)->where('type', 'goal')->where('team_id', $m->away_team_id)->count();

            $this->assertSame((int) $m->home_score, $homeGoals, "match {$m->id} : score domicile incohérent avec les événements de but");
            $this->assertSame((int) $m->away_score, $awayGoals, "match {$m->id} : score extérieur incohérent avec les événements de but");
        }
    }

    public function test_team_stats_equal_sum_of_player_stats(): void
    {
        $this->artisan('role-eval:generate-demo', ['--seed' => 13, '--clubs' => 4])->run();
        $batch = DB::table('import_batches')->where('batch_type', 'demo_generation')->latest('id')->first();

        $teamStats = DB::table('match_team_stats')->where('import_batch_id', $batch->id)->get();
        foreach ($teamStats as $ts) {
            $sumShots = DB::table('player_match_detailed_stats')
                ->where('match_id', $ts->match_id)->where('team_id', $ts->team_id)->sum('shots_total');
            $sumGoals = DB::table('player_match_detailed_stats')
                ->where('match_id', $ts->match_id)->where('team_id', $ts->team_id)->sum('goals_scored');

            $this->assertSame((int) $ts->shots_total, (int) $sumShots);
            // goals_scored équipe inclut les buts contre son camp adverses,
            // jamais crédités à un joueur : la somme joueur peut donc être
            // inférieure ou égale, jamais supérieure.
            $this->assertLessThanOrEqual((int) $ts->goals_scored, (int) $sumGoals);
        }
    }

    public function test_existing_demo_dataset_is_never_touched(): void
    {
        $before = DB::table('player_match_detailed_stats')
            ->join('players', 'players.id', '=', 'player_match_detailed_stats.player_id')
            ->where('players.club_id', 22) // CS Sfaxien, jeu de démo actuel (49 joueurs / 10 matchs)
            ->count();
        $beforeMatches = DB::table('matches')->whereIn('id', range(1, 34))->get()->keyBy('id');

        $this->artisan('role-eval:generate-demo', ['--seed' => 5, '--clubs' => 4])->run();

        $after = DB::table('player_match_detailed_stats')
            ->join('players', 'players.id', '=', 'player_match_detailed_stats.player_id')
            ->where('players.club_id', 22)
            ->count();
        $afterMatches = DB::table('matches')->whereIn('id', range(1, 34))->get()->keyBy('id');

        $this->assertSame($before, $after, 'le jeu de démonstration actuel (club CS Sfaxien) ne doit jamais changer de volume');
        foreach ($beforeMatches as $id => $m) {
            $this->assertEquals((array) $m, (array) $afterMatches[$id], "le match existant #{$id} ne doit pas être modifié");
        }
    }
}
