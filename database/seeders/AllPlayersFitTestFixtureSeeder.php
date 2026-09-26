<?php

namespace Database\Seeders;

use App\Models\FitScoreSnapshot;
use App\Models\PerformanceMetric;
use App\Models\Player;
use App\Services\Fit\FitScoreService;
use App\Services\Fit\FitSnapshotService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AllPlayersFitTestFixtureSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('fit_score_snapshots')) {
            throw new \RuntimeException('Migration FIT manquante.');
        }

        $ids = DB::table('player_real_time_health')
            ->where('notes', 'like', 'synthetic_demo%')
            ->distinct()->pluck('player_id')->map(fn ($id) => (int) $id);
        $complete = FitScoreSnapshot::query()
            ->whereIn('player_id', $ids)
            ->where('calculation_version', FitSnapshotService::CALCULATION_VERSION)
            ->where('is_complete', true)
            ->whereNotNull('fit_score')
            ->distinct()->pluck('player_id')->map(fn ($id) => (int) $id);
        $pending = $ids->diff($complete)->values();
        $userId = DB::table('users')->orderBy('id')->value('id');

        if (!$userId) {
            throw new \RuntimeException('Utilisateur de test absent.');
        }
        if ($pending->isEmpty()) {
            $this->command?->info('FIT complet pour tous les joueurs de test.');
            return;
        }

        $definitions = [
            'physical' => ['endurance', 72, '%'],
            'technical' => ['passing', 76, '%'],
            'tactical' => ['positioning', 7.1, 'score'],
            'mental' => ['concentration', 79, '%'],
            'social' => ['team_cohesion', 82, '%'],
        ];
        $scoreService = app(FitScoreService::class);
        $available = [];
        $recent = PerformanceMetric::query()
            ->whereIn('player_id', $pending)
            ->verified()
            ->whereBetween('measurement_date', [now()->subDays(30), now()])
            ->get();
        foreach ($recent as $metric) {
            if ($scoreService->isMetricEligibleForFit($metric)) {
                $available[$metric->player_id][$metric->metric_type] = true;
            }
        }

        $players = Player::withoutGlobalScopes()
            ->whereIn('id', $pending)->get()->keyBy('id');
        $inserted = 0;
        $createdAt = now();
        foreach ($pending->chunk(100) as $batch) {
            $rows = [];
            foreach ($batch as $id) {
                foreach ($definitions as $axis => [$name, $value, $unit]) {
                    if (isset($available[$id][$axis])) {
                        continue;
                    }
                    $rows[] = [
                        'player_id' => $id,
                        'metric_type' => $axis,
                        'metric_name' => $name,
                        'metric_value' => $value,
                        'metric_unit' => $unit,
                        'measurement_date' => $createdAt,
                        'data_source' => 'manual',
                        'confidence_score' => 0.75,
                        'is_verified' => true,
                        'verified_at' => $createdAt,
                        'verified_by' => null,
                        'created_by' => $userId,
                        'notes' => 'FIT_SYNTHETIC_TEST_V1',
                        'metadata' => json_encode(['source' => 'synthetic_demo'], JSON_THROW_ON_ERROR),
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ];
                }
            }
            foreach (array_chunk($rows, 500) as $chunk) {
                DB::table('performance_metrics')->insert($chunk);
                $inserted += count($chunk);
            }
        }

        $snapshots = 0;
        foreach ($pending as $id) {
            $player = $players->get($id);
            if (!$player) {
                throw new \RuntimeException("Joueur de test {$id} absent.");
            }
            $result = app(FitSnapshotService::class)
                ->createSnapshot($player, 30, now());
            if (!$result['snapshot']->is_complete) {
                throw new \RuntimeException("FIT incomplet pour le joueur {$id}.");
            }
            $snapshots += (int) $result['created'];
            if ($snapshots > 0 && $snapshots % 100 === 0) {
                $this->command?->info("Snapshots FIT créés : {$snapshots}/{$pending->count()}");
            }
        }
        $this->command?->info(
            "Métriques de test : {$inserted}; snapshots FIT créés : {$snapshots}."
        );
    }
}
