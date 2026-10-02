<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Services\PlayerStatsImport\PlayerStatisticsFile;
use App\Services\PlayerStatsImport\PlayerStatisticsImporter;
use App\Services\RoleEvaluationEngine\PeriodStatsDataSource;
use App\Services\RoleEvaluationEngine\RoleFitEvaluator;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * « Rôle et apport » calculé depuis les profils de saison importés
 * (exports « Player statistics » en moyennes par match), sans feuille de match.
 */
class RoleEvaluationPeriodTest extends TestCase
{
    use DatabaseTransactions;

    private function publishedConfig(): int
    {
        $now = now();
        $id = DB::table('role_config_versions')->insertGetId(['label' => 'Test période', 'status' => 'published', 'published_at' => $now, 'created_at' => $now, 'updated_at' => $now]);
        $cfg = config('role_evaluation_engine');
        $rows = [];
        foreach (DB::table('position_catalog')->pluck('family')->unique() as $family) {
            $keys = array_keys($cfg['dimensions'][$family === 'gardien' ? 'goalkeeper' : 'field']);
            foreach ($keys as $key) {
                $rows[] = ['role_config_version_id' => $id, 'position_family' => $family, 'dimension_key' => $key, 'weight' => round(100 / count($keys), 4), 'created_at' => $now, 'updated_at' => $now];
            }
        }
        DB::table('role_config_weights')->insert($rows);

        return $id;
    }

    /**
     * Ligue synthétique importée comme des exports de saison : plusieurs clubs,
     * 8 matchs, moyennes à deux décimales, niveaux de joueurs différents.
     *
     * @return int[] joueurs du premier club
     */
    private function importLeague(int $clubs = 6): array
    {
        mt_srand(42);
        $positions = ['LCB', 'RCB', 'LB', 'RB', 'CDM', 'LCM', 'CAM', 'LAM', 'RAM', 'CF', 'LCB', 'RCB', 'CDM', 'CAM', 'CF', 'LB'];
        $first = [];
        for ($c = 0; $c < $clubs; $c++) {
            $club = Club::query()->forceCreate(['name' => "Club Période Test $c"]);
            $rows = [];
            foreach ($positions as $i => $position) {
                $level = mt_rand(60, 140) / 100;
                $per = fn (int $total) => number_format(round($total * $level * mt_rand(80, 120) / 100) / 8, 2, '.', '');
                $rate = fn (float $base) => (string) round(min(0.95, $base * (0.8 + 0.4 * $level / 1.4)), 4);
                $rows[] = ['Player' => "Joueur $c-$i", 'Position' => $position, 'Minutes played' => (string) mt_rand(560, 760),
                    'Goals' => $per(3), 'Assists' => $per(2), 'Shots on target' => $per(6), 'Key passes' => $per(7), 'Passes' => $per(300),
                    'Passes accurate, %' => $rate(0.8), 'Dribbles' => $per(12), 'Dribbles successful, %' => $rate(0.5),
                    'Tackles' => $per(20), 'Tackles successful, %' => $rate(0.6), 'Interceptions' => $per(15), 'Loose ball recoveries' => $per(40),
                    'Fouls' => $per(9), 'Challenges' => $per(80), 'Challenges won, %' => $rate(0.5), 'Aerial challenges' => $per(15), 'Aerial challenges won, %' => $rate(0.45),
                    'Long passes' => $per(25), 'Long passes accurate, %' => $rate(0.55), 'Crosses' => $per(10), 'Crosses accurate, %' => $rate(0.3),
                    'Progressive passes' => $per(40), 'Progressive passes accurate, %' => $rate(0.7), 'Chances created' => $per(4), 'Mistakes leading to chances' => $per(1)];
            }
            $file = ['rows' => $rows, 'metric_columns' => array_values(array_diff(array_keys($rows[0]), ['Player', 'Position'])), 'file_date' => now()];
            app(PlayerStatisticsImporter::class)->importPeriod($file, $club, ['source' => 'TEST', 'season' => '2026/27', 'competition' => 'Test', 'create_missing' => true]);
            $first = $first ?: DB::table('players')->where('club_id', $club->id)->pluck('id')->all();
        }

        return $first;
    }

    public function test_match_count_is_inferred_from_two_decimal_averages(): void
    {
        $source = new PeriodStatsDataSource;
        $this->assertSame([8, 'inferred'], $source->matches(null, 799, [48.38, 3.38, 5.5, 1.62, 0.62, 5.88, 6.62]));
        $this->assertSame([7, 'inferred'], $source->matches(null, 708, [37.57, 9.43, 2.71, 3.14, 3.86]));
        $this->assertSame([12, 'declared'], $source->matches(12.0, 900, [1.5]));
        $this->assertSame('estimated', $source->matches(null, 90, [])[1]);
    }

    public function test_period_profiles_produce_role_scores_without_match_sheets(): void
    {
        $versionId = $this->publishedConfig();
        $playerIds = $this->importLeague();

        $entries = collect((new PeriodStatsDataSource)->forPlayers($playerIds));
        $this->assertCount(count($playerIds), $entries);
        $this->assertSame(8, $entries->first()['average']['matches']);
        $this->assertSame('inferred', $entries->first()['period']['matches_origin']);
        $this->assertArrayHasKey('ground_duels_won', $entries->first()['average']['stats']);

        $exit = Artisan::call('role-eval:compute', ['--config-version' => $versionId, '--is-demo' => '0', '--source' => 'period', '--players' => implode(',', $playerIds)]);
        $this->assertSame(0, $exit, Artisan::output());

        $rows = DB::table('player_role_evaluations')->whereIn('player_id', $playerIds)->get();
        $played = $rows->where('role_fit_score', 0);
        $this->assertGreaterThan(count($playerIds) / 2, $played->unique('player_id')->count());
        foreach ($rows as $row) {
            $this->assertSame(RoleFitEvaluator::PERIOD_MODEL_VERSION, $row->model_version);
            $this->assertEquals(0, (int) $row->is_demo);
        }
    }

    public function test_period_source_refuses_demo_flag(): void
    {
        $versionId = $this->publishedConfig();
        $this->assertNotSame(0, Artisan::call('role-eval:compute', ['--config-version' => $versionId, '--is-demo' => '1', '--source' => 'period']));
    }

    public function test_real_al_hazem_export_is_read_into_period_profiles(): void
    {
        $path = getenv('HOME') . '/Desktop/images FIT/27.09.2026 - Al-Hazem SC - Player statistics.xlsx';
        if (!is_file($path)) {
            $this->markTestSkipped('Fichier réel absent.');
        }
        $versionId = $this->publishedConfig();
        $club = Club::query()->forceCreate(['name' => 'Al-Hazem SC (test)']);
        $file = app(PlayerStatisticsFile::class)->read($path, basename($path));
        app(PlayerStatisticsImporter::class)->importPeriod($file, $club, ['source' => 'TEST', 'season' => '2026/27', 'competition' => 'Saudi Pro League', 'create_missing' => true]);
        $playerIds = DB::table('players')->where('club_id', $club->id)->pluck('id')->all();

        $this->assertSame(0, Artisan::call('role-eval:compute', ['--config-version' => $versionId, '--is-demo' => '0', '--source' => 'period', '--players' => implode(',', $playerIds)]));
        // Un seul club : 1 à 4 joueurs par poste, la fiabilité ne peut pas être
        // établie et le moteur n'affiche pas de score (limite assumée) ; le
        // profil est néanmoins entièrement lu : poste, minutes, matchs déduits.
        $entries = collect((new PeriodStatsDataSource)->forPlayers($playerIds))->keyBy('id');
        $this->assertCount(24, $entries);
        $this->assertSame(8, $entries->first()['average']['matches']);
        $this->assertSame('CDM', $entries->first()['position']);
    }
}
