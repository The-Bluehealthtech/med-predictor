<?php

namespace App\Services\Fit;

use App\Models\FitScoreSnapshot;
use App\Models\Player;
use Illuminate\Support\Carbon;

class FitSnapshotService
{
    public const CALCULATION_VERSION = 'fit_v1';

    public function __construct(
        private readonly FitScoreService $fitScoreService
    ) {
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

        /*
         * Seul un snapshot complet, calculé avec la même version,
         * peut servir de référence pour une évolution FIT.
         */
        $previous = null;

        if ($result['complete'] && $result['overall_score'] !== null) {
            $previous = FitScoreSnapshot::query()
                ->where('player_id', $player->id)
                ->where('calculation_version', self::CALCULATION_VERSION)
                ->where('is_complete', true)
                ->whereNotNull('fit_score')
                ->where('snapshot_at', '<', $snapshotAt)
                ->orderByDesc('snapshot_at')
                ->first();
        }

        $snapshot = FitScoreSnapshot::create([
            'player_id' => $player->id,
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
            'calculation_version' => self::CALCULATION_VERSION,
            'evidence' => [
                'axes' => $result['axes'],
            ],
            'generated_by_user_id' => $generatedByUserId,
        ]);

        return [
            'snapshot' => $snapshot,
            'evolution' => $this->calculateEvolution(
                $snapshot,
                $previous
            ),
        ];
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
