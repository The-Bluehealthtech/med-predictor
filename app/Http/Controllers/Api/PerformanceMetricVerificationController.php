<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PerformanceMetric;
use App\Models\Player;
use App\Services\Fit\FitScoreService;
use App\Services\Fit\FitSnapshotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerformanceMetricVerificationController extends Controller
{
    public function __construct(
        private readonly FitScoreService $fitScoreService,
        private readonly FitSnapshotService $fitSnapshotService
    ) {
    }

    public function __invoke(
        Request $request,
        int $player,
        int $metric
    ): JsonResponse {
        // Player is tenant-scoped. Never resolve the metric globally first.
        $scopedPlayer = Player::query()->findOrFail($player);
        $user = $request->user();

        [$performanceMetric, $alreadyVerified] = DB::transaction(
            function () use ($scopedPlayer, $metric, $user): array {
                $performanceMetric = PerformanceMetric::query()
                    ->where('player_id', $scopedPlayer->id)
                    ->whereKey($metric)
                    ->lockForUpdate()
                    ->firstOrFail();

                if (!$this->fitScoreService->isMetricEligibleForFit(
                    $performanceMetric
                )) {
                    abort(
                        422,
                        'Metric is not eligible for canonical FIT scoring.'
                    );
                }

                $alreadyVerified = (bool) $performanceMetric->is_verified;

                if (!$alreadyVerified) {
                    $performanceMetric->forceFill([
                        'is_verified' => true,
                        'verified_by' => $user->id,
                        'verified_at' => now(),
                        'updated_by' => $user->id,
                    ])->save();

                    $performanceMetric = $performanceMetric->fresh();
                }

                /*
                 * Persist the canonical calculation attempt immediately.
                 * Incomplete axes remain null; no score is fabricated.
                 *
                 * This is deliberately inside the same transaction so a
                 * newly verified metric and its FIT snapshot stay atomic.
                 * createSnapshot() is signature-idempotent.
                 */
                $this->fitSnapshotService->createSnapshot(
                    $scopedPlayer,
                    generatedByUserId: $user->id
                );

                return [$performanceMetric, $alreadyVerified];
            }
        );

        return response()->json([
            'data' => [
                'id' => $performanceMetric->id,
                'player_id' => $performanceMetric->player_id,
                'is_verified' => $performanceMetric->is_verified,
                'verified_by' => $performanceMetric->verified_by,
                'verified_at' => $performanceMetric->verified_at?->toISOString(),
            ],
            'meta' => [
                'already_verified' => $alreadyVerified,
            ],
        ]);
    }
}
