<?php

namespace Tests\Feature;

use App\Services\RoleEvaluationEngine\RoleFitEvaluator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Test de bout en bout de role-eval:compute (moteur de calcul rôle et
 * apport). DatabaseTransactions, jamais RefreshDatabase — même précaution
 * que les tests des Livrables 1 à 3 (phpunit.xml pointe la vraie base de
 * dev). Suppose que les migrations du Livrable 1 et le correctif FK
 * joueurs/players ont déjà été appliqués.
 *
 * Utilise role-eval:generate-demo (Livrable 3) pour produire des données
 * d'entrée réalistes plutôt que de fabriquer des lignes à la main : ce
 * moteur lit exactement ce que ce générateur écrit.
 */
class RoleEvaluationComputeCommandTest extends TestCase
{
    use DatabaseTransactions;

    /** Insère une version de configuration "draft" avec des poids égaux arbitraires couvrant toutes les familles. */
    private function seedDraftConfigVersion(): int
    {
        $now = now();
        $versionId = DB::table('role_config_versions')->insertGetId([
            'label' => 'Test',
            'description' => 'Poids arbitraires, test uniquement.',
            'status' => 'draft',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $cfg = config('role_evaluation_engine');
        $rows = [];
        foreach (DB::table('position_catalog')->pluck('family')->unique() as $family) {
            $familyType = $family === 'gardien' ? 'goalkeeper' : 'field';
            $dimensionKeys = array_keys($cfg['dimensions'][$familyType]);
            $weight = round(100 / count($dimensionKeys), 4);
            foreach ($dimensionKeys as $dimensionKey) {
                $rows[] = [
                    'role_config_version_id' => $versionId,
                    'position_family' => $family,
                    'dimension_key' => $dimensionKey,
                    'weight' => $weight,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }
        DB::table('role_config_weights')->insert($rows);

        return $versionId;
    }

    public function test_dry_run_writes_nothing(): void
    {
        $versionId = $this->seedDraftConfigVersion();
        Artisan::call('role-eval:generate-demo', ['--seed' => 1, '--clubs' => 4]);

        $before = DB::table('player_role_evaluations')->count();

        $exitCode = Artisan::call('role-eval:compute', [
            '--config-version' => $versionId,
            '--is-demo' => '1',
            '--dry-run' => true,
        ]);

        $this->assertSame(0, $exitCode);
        $this->assertSame($before, DB::table('player_role_evaluations')->count());
    }

    public function test_is_demo_is_required_and_never_guessed(): void
    {
        $versionId = $this->seedDraftConfigVersion();
        Artisan::call('role-eval:generate-demo', ['--seed' => 1, '--clubs' => 4]);

        $exitCode = Artisan::call('role-eval:compute', ['--config-version' => $versionId]);

        $this->assertNotSame(0, $exitCode, '--is-demo doit être obligatoire : sans lui, la commande doit échouer plutôt que deviner.');
        $this->assertSame(0, DB::table('player_role_evaluations')->count());
    }

    public function test_computed_rows_carry_is_demo_true_and_valid_french_family_names(): void
    {
        $versionId = $this->seedDraftConfigVersion();
        Artisan::call('role-eval:generate-demo', ['--seed' => 2, '--clubs' => 4]);

        $exitCode = Artisan::call('role-eval:compute', [
            '--config-version' => $versionId,
            '--is-demo' => '1',
        ]);
        $this->assertSame(0, $exitCode);

        $rows = DB::table('player_role_evaluations')->get();
        $this->assertGreaterThan(0, $rows->count(), 'Au moins un joueur devrait être évaluable sur ce petit jeu de démonstration.');

        $validFamilies = DB::table('position_catalog')->pluck('family')->unique()->all();
        foreach ($rows as $row) {
            $this->assertEquals(1, (int) $row->is_demo);
            $this->assertContains(
                $row->position_family_evaluated,
                $validFamilies,
                "position_family_evaluated doit toujours être une famille française de position_catalog, jamais une clé interne anglaise (régression : voir PositionFamilyTranslator)."
            );
            $this->assertSame(RoleFitEvaluator::MODEL_VERSION, $row->model_version);
            $this->assertNotNull($row->score);
        }

        // La ligne "famille jouée" (celle qui n'est pas une comparaison
        // transversale) porte toujours un role_fit_score de 0.
        $playedRows = $rows->filter(fn ($r) => (float) $r->role_fit_score === 0.0);
        $this->assertGreaterThan(0, $playedRows->count());
    }

    public function test_missing_weight_for_a_family_fails_explicitly_without_writing_anything(): void
    {
        // Version de configuration délibérément incomplète : une seule
        // ligne de poids, alors que toutes les familles/dimensions sont
        // requises. CalculatorConfigBuilder doit échouer bruyamment plutôt
        // que de calculer avec un poids manquant silencieusement traité
        // comme zéro.
        $now = now();
        $versionId = DB::table('role_config_versions')->insertGetId([
            'label' => 'Incomplete', 'status' => 'draft', 'created_at' => $now, 'updated_at' => $now,
        ]);
        DB::table('role_config_weights')->insert([
            'role_config_version_id' => $versionId,
            'position_family' => 'défenseur central',
            'dimension_key' => 'finition_creation',
            'weight' => 50,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        Artisan::call('role-eval:generate-demo', ['--seed' => 3, '--clubs' => 4]);

        $exitCode = Artisan::call('role-eval:compute', [
            '--config-version' => $versionId,
            '--is-demo' => '1',
        ]);

        $this->assertNotSame(0, $exitCode);
        $this->assertSame(0, DB::table('player_role_evaluations')->count());
        $this->assertStringContainsString('Poids manquant', Artisan::output());
    }

    public function test_existing_source_data_is_never_modified(): void
    {
        $versionId = $this->seedDraftConfigVersion();
        Artisan::call('role-eval:generate-demo', ['--seed' => 4, '--clubs' => 4]);

        $beforeParticipations = DB::table('match_participations')->count();
        $beforeStats = DB::table('player_match_detailed_stats')->count();
        $beforePlayers = DB::table('players')->count();

        Artisan::call('role-eval:compute', ['--config-version' => $versionId, '--is-demo' => '1']);

        $this->assertSame($beforeParticipations, DB::table('match_participations')->count());
        $this->assertSame($beforeStats, DB::table('player_match_detailed_stats')->count());
        $this->assertSame($beforePlayers, DB::table('players')->count(), 'role-eval:compute ne doit jamais créer ni modifier de joueur.');
    }

    public function test_role_fit_comparison_produces_a_signed_delta_against_neighboring_families(): void
    {
        // Seuils statistiques abaissés pour CE test seulement (jamais un
        // réglage de poids : min_reference_players/minutes ne sont pas des
        // poids, seulement le volume minimal d'échantillon requis pour
        // qu'une comparaison de famille voisine soit possible), afin de
        // vérifier la mécanique de comparaison sans générer des centaines
        // de joueurs de démonstration à chaque exécution des tests.
        config([
            'role_evaluation_engine.min_reference_players' => 3,
            'role_evaluation_engine.min_reference_minutes' => 10,
        ]);

        $versionId = $this->seedDraftConfigVersion();
        Artisan::call('role-eval:generate-demo', ['--seed' => 5, '--clubs' => 4]);

        $exitCode = Artisan::call('role-eval:compute', ['--config-version' => $versionId, '--is-demo' => '1']);
        $this->assertSame(0, $exitCode);

        $withNeighbor = DB::table('player_role_evaluations')->where('role_fit_score', '!=', 0)->count();
        $this->assertGreaterThan(0, $withNeighbor, 'Avec le seuil de référence abaissé, au moins une comparaison de famille voisine devrait aboutir.');

        $sample = DB::table('player_role_evaluations')->where('role_fit_score', '!=', 0)->first();
        $playedRow = DB::table('player_role_evaluations')
            ->where('player_id', $sample->player_id)
            ->where('role_fit_score', 0)
            ->first();
        $this->assertNotNull($playedRow, 'Un joueur avec une ligne de comparaison doit aussi avoir sa ligne "famille jouée".');
        $this->assertEqualsWithDelta(
            (float) $sample->score - (float) $playedRow->score,
            (float) $sample->role_fit_score,
            0.01,
            'role_fit_score doit être exactement l\'écart entre le score de la famille comparée et celui de la famille jouée.'
        );
    }
}
