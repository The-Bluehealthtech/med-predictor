<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PerformanceMetric;
use App\Models\Player;
use App\Services\Fit\FitScoreService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PerformanceMetricVerificationController extends Controller
{
    public function __construct(
        private readonly FitScoreService $fitScoreService
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

                if ($performanceMetric->is_verified) {
                    return [$performanceMetric, true];
                }

                $performanceMetric->forceFill([
                    'is_verified' => true,
                    'verified_by' => $user->id,
                    'verified_at' => now(),
                    'updated_by' => $user->id,
                ])->save();

                return [$performanceMetric->fresh(), false];
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
