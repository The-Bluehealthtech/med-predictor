<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PerformanceMetric;
use App\Models\Player;
use App\Services\Fit\FitScoreService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PerformanceMetricRecordingController extends Controller
{
    public function __construct(
        private readonly FitScoreService $fitScoreService
    ) {
    }

    public function __invoke(
        Request $request,
        int $player
    ): JsonResponse {
        // Resolve the player through the tenant scope before accepting data.
        $scopedPlayer = Player::query()->findOrFail($player);
        $user = $request->user();

        $validated = $request->validate([
            'metric_type' => [
                'required',
                Rule::in(array_keys(PerformanceMetric::getMetricTypes())),
            ],
            'metric_name' => [
                'required',
                'string',
                'max:255',
            ],
            'metric_value' => [
                'required',
                'numeric',
            ],
            'metric_unit' => [
                'required',
                'string',
                'max:50',
            ],
            'measurement_date' => [
                'required',
                'date',
                'before_or_equal:now',
            ],
            'data_source' => [
                'required',
                Rule::in([PerformanceMetric::SOURCE_MANUAL]),
            ],
            'confidence_score' => [
                'required',
                'numeric',
                'between:0,1',
            ],
            'notes' => [
                'nullable',
                'string',
            ],
            'metadata' => [
                'nullable',
                'array',
            ],
        ]);

        $candidate = new PerformanceMetric([
            'player_id' => $scopedPlayer->id,
            'metric_type' => $validated['metric_type'],
            'metric_name' => $validated['metric_name'],
            'metric_value' => $validated['metric_value'],
            'metric_unit' => $validated['metric_unit'],
            'measurement_date' => $validated['measurement_date'],
            'data_source' => $validated['data_source'],
            'confidence_score' => $validated['confidence_score'],
            'notes' => $validated['notes'] ?? null,
            'metadata' => $validated['metadata'] ?? null,
        ]);

        if (!$this->fitScoreService->isMetricEligibleForFit($candidate)) {
            abort(
                422,
                'Metric is not eligible for canonical FIT scoring.'
            );
        }

        $performanceMetric = DB::transaction(
            function () use ($candidate, $user): PerformanceMetric {
                $candidate->forceFill([
                    'is_verified' => false,
                    'verified_by' => null,
                    'verified_at' => null,
                    'created_by' => $user->id,
                    'updated_by' => null,
                ]);

                $candidate->save();

                return $candidate->fresh();
            }
        );

        return response()->json([
            'data' => [
                'id' => $performanceMetric->id,
                'player_id' => $performanceMetric->player_id,
                'metric_type' => $performanceMetric->metric_type,
                'metric_name' => $performanceMetric->metric_name,
                'metric_value' => $performanceMetric->metric_value,
                'metric_unit' => $performanceMetric->metric_unit,
                'measurement_date' =>
                    $performanceMetric->measurement_date?->toISOString(),
                'data_source' => $performanceMetric->data_source,
                'confidence_score' =>
                    $performanceMetric->confidence_score,
                'is_verified' => $performanceMetric->is_verified,
                'created_by' => $performanceMetric->created_by,
            ],
        ], 201);
    }
}
