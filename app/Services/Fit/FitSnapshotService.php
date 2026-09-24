<?php

namespace App\Services\Fit;

use App\Models\FitScoreSnapshot;
use App\Models\Player;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;

class FitSnapshotService
{
    public const CALCULATION_VERSION = 'fit_v1';

    public function __construct(
        private readonly FitScoreService $fitScoreService
    ) {
    }

    public function latestForPlayer(Player $player): array
    {
        if (!Schema::hasTable('fit_score_snapshots')) {
            return [
                'snapshot' => null,
                'previous_snapshot' => null,
                'latest_attempt' => null,
                'missing_axes' => null,
                'evolution' => [
                    'previous_snapshot_id' => null,
                    'previous_score' => null,
                    'current_score' => null,
                    'points' => null,
                    'percent' => null,
                ],
            ];
        }

        $baseQuery = FitScoreSnapshot::query()
            ->where('player_id', $player->id)
            ->where('calculation_version', self::CALCULATION_VERSION)
            ->where('snapshot_at', '<=', now());

        $latestAttempt = (clone $baseQuery)
            ->orderByDesc('snapshot_at')
            ->orderByDesc('id')
            ->first();

        $snapshots = (clone $baseQuery)
            ->where('is_complete', true)
            ->whereNotNull('fit_score')
            ->orderByDesc('snapshot_at')
            ->orderByDesc('id')
            ->limit(2)
            ->get();

        $current = $snapshots->get(0);
        $previous = $snapshots->get(1);

        return [
            'snapshot' => $current,
            'previous_snapshot' => $previous,
            'latest_attempt' => $latestAttempt,
            'missing_axes' => $latestAttempt
                ? $this->missingAxes($latestAttempt)
                : null,
            'evolution' => $current
                ? $this->calculateEvolution($current, $previous)
                : [
                    'previous_snapshot_id' => null,
                    'previous_score' => null,
                    'current_score' => null,
                    'points' => null,
                    'percent' => null,
                ],
        ];
    }

    private function missingAxes(FitScoreSnapshot $snapshot): array
    {
        $axes = [
            'physical' => 'physical_score',
            'technical' => 'technical_score',
            'tactical' => 'tactical_score',
            'mental' => 'mental_score',
            'social' => 'social_score',
        ];

        $missing = [];

        foreach ($axes as $axis => $field) {
            if ($snapshot->{$field} === null) {
                $missing[] = $axis;
            }
        }

        return $missing;
    }

    public function createSnapshot(
        Player $player,
        int $days = 30,
        ?Carbon $snapshotAt = null,
        ?int $generatedByUserId = null
    ): array {
        $snapshotAt ??= now();

        $result = $this->fitScoreService->calculate(
            $player,
            $days,
            $snapshotAt
        );

        $inputSignature = $this->buildInputSignature(
            $player,
            $days,
            $result
        );

        /*
         * L'unicité player + version + signature garantit qu'un même
         * ensemble d'entrées ne crée pas plusieurs snapshots.
         */
        $snapshot = FitScoreSnapshot::firstOrCreate(
            [
                'player_id' => $player->id,
                'calculation_version' => self::CALCULATION_VERSION,
                'input_signature' => $inputSignature,
            ],
            [
                'tenant_id' => $player->tenant_id,
                'snapshot_at' => $snapshotAt,
                'physical_score' => $result['physical_score'],
                'technical_score' => $result['technical_score'],
                'tactical_score' => $result['tactical_score'],
                'mental_score' => $result['mental_score'],
                'social_score' => $result['social_score'],
                'fit_score' => $result['overall_score'],
                'confidence_score' => $result['confidence'],
                'is_complete' => $result['complete'],
                'window_days' => $days,
                'evidence' => [
                    'axes' => $result['axes'],
                ],
                'generated_by_user_id' => $generatedByUserId,
            ]
        );

        /*
         * L'évolution appartient au snapshot réellement persisté.
         * Si firstOrCreate a retrouvé un ancien snapshot identique,
         * on ne compare donc pas avec la nouvelle heure de recalcul.
         */
        $previous = null;

        if ($snapshot->is_complete && $snapshot->fit_score !== null) {
            $previous = FitScoreSnapshot::query()
                ->where('player_id', $player->id)
                ->where('calculation_version', self::CALCULATION_VERSION)
                ->where('is_complete', true)
                ->whereNotNull('fit_score')
                ->where('snapshot_at', '<', $snapshot->snapshot_at)
                ->orderByDesc('snapshot_at')
                ->orderByDesc('id')
                ->first();
        }

        return [
            'snapshot' => $snapshot,
            'created' => $snapshot->wasRecentlyCreated,
            'evolution' => $this->calculateEvolution(
                $snapshot,
                $previous
            ),
        ];
    }

    private function buildInputSignature(
        Player $player,
        int $days,
        array $result
    ): string {
        /*
         * snapshot_at et generated_by_user_id sont volontairement exclus :
         * ils ne changent pas les données sportives ayant produit le score.
         */
        $payload = [
            'player_id' => (int) $player->id,
            'calculation_version' => self::CALCULATION_VERSION,
            'window_days' => $days,
            'physical_score' => $result['physical_score'],
            'technical_score' => $result['technical_score'],
            'tactical_score' => $result['tactical_score'],
            'mental_score' => $result['mental_score'],
            'social_score' => $result['social_score'],
            'overall_score' => $result['overall_score'],
            'confidence' => $result['confidence'],
            'complete' => $result['complete'],
            'axes' => $result['axes'],
        ];

        return hash(
            'sha256',
            json_encode(
                $payload,
                JSON_THROW_ON_ERROR
                | JSON_PRESERVE_ZERO_FRACTION
                | JSON_UNESCAPED_SLASHES
            )
        );
    }

    private function calculateEvolution(
        FitScoreSnapshot $current,
        ?FitScoreSnapshot $previous
    ): array {
        if (
            !$current->is_complete
            || $current->fit_score === null
            || $previous === null
            || $previous->fit_score === null
        ) {
            return [
                'previous_snapshot_id' => null,
                'previous_score' => null,
                'current_score' => $current->fit_score,
                'points' => null,
                'percent' => null,
            ];
        }

        $currentScore = (float) $current->fit_score;
        $previousScore = (float) $previous->fit_score;
        $delta = $currentScore - $previousScore;

        return [
            'previous_snapshot_id' => $previous->id,
            'previous_score' => $previousScore,
            'current_score' => $currentScore,
            'points' => round($delta, 1),
            'percent' => $previousScore > 0
                ? round(($delta / $previousScore) * 100, 1)
                : null,
        ];
    }
}
