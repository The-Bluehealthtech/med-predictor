<?php

namespace Database\Seeders;

use App\Models\PerformanceMetric;
use App\Models\Player;
use App\Services\Fit\FitScoreService;
use App\Services\Fit\FitSnapshotService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FitScoreTestFixtureSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('fit_score_snapshots')) {
            throw new \RuntimeException('Migration FIT manquante.');
        }

        $player = Player::withoutGlobalScopes()->findOrFail(1);
        $userId = DB::table('users')->orderBy('id')->value('id');
        if (!$userId) {
            throw new \RuntimeException('Utilisateur de test absent.');
        }

        $definitions = [
            'physical' => ['endurance', 72, '%'],
            'technical' => ['passing', 76, '%'],
            'tactical' => ['positioning', 7.1, 'score'],
            'mental' => ['concentration', 79, '%'],
            'social' => ['team_cohesion', 82, '%'],
        ];
        $scoreService = app(FitScoreService::class);
        $missing = $scoreService->diagnose($player)['missing_axes'];
        $created = 0;

        DB::transaction(function () use (
            $player, $userId, $definitions, $missing, &$created
        ): void {
            foreach ($definitions as $axis => [$name, $value, $unit]) {
                if (!in_array($axis, $missing, true)) {
                    continue;
                }

                $metric = PerformanceMetric::firstOrCreate(
                    [
                        'player_id' => $player->id,
                        'metric_type' => $axis,
                        'metric_name' => $name,
                        'notes' => 'FIT_SYNTHETIC_TEST_V1',
                    ],
                    [
                        'metric_value' => $value,
                        'metric_unit' => $unit,
                        'measurement_date' => now(),
                        'data_source' => 'manual',
                        'confidence_score' => 0.75,
                        'is_verified' => true,
                        'verified_at' => now(),
                        'verified_by' => null,
                        'created_by' => $userId,
                        'metadata' => ['source' => 'synthetic_demo'],
                    ]
                );

                $created += (int) $metric->wasRecentlyCreated;
            }
            $result = app(FitSnapshotService::class)
                ->createSnapshot($player, 30, now());

            if (!$result['snapshot']->is_complete) {
                throw new \RuntimeException(
                    'Le scénario FIT reste incomplet : transaction annulée.'
                );
            }

            $this->command?->info(
                "Métriques de test créées : {$created}; "
                . "FIT : {$result['snapshot']->fit_score}/100; "
                . 'snapshot '
                . ($result['created'] ? 'créé.' : 'déjà présent.')
            );
        });
    }
}
