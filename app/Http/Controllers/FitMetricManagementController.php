<?php

namespace App\Http\Controllers;

use App\Models\PerformanceMetric;
use App\Models\Player;
use App\Services\Fit\FitScoreService;
use App\Services\RBACService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FitMetricManagementController extends Controller
{
    public function __construct(
        private readonly FitScoreService $fitScoreService,
        private readonly RBACService $rbacService
    ) {
    }

    public function index(Request $request): View
    {
        $catalog = $this->fitScoreService->recordingCatalog();

        // Player carries the tenant scope. Never derive visibility
        // from PerformanceMetric directly.
        $players = Player::query()
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get([
                'id',
                'first_name',
                'last_name',
                'name',
                'fifa_connect_id',
            ]);

        $selectedPlayer = null;
        $metrics = null;

        if ($request->filled('player_id')) {
            $selectedPlayer = Player::query()
                ->findOrFail((int) $request->input('player_id'));

            $metrics = PerformanceMetric::query()
                ->where('player_id', $selectedPlayer->id)
                ->with([
                    'createdBy:id,name,email',
                    'verifiedBy:id,name,email',
                ])
                ->orderByDesc('measurement_date')
                ->orderByDesc('id')
                ->paginate(25)
                ->withQueryString();

            $metrics->getCollection()->transform(
                function (PerformanceMetric $metric): PerformanceMetric {
                    $metric->setAttribute(
                        'fit_eligible',
                        $this->fitScoreService
                            ->isMetricEligibleForFit($metric)
                    );

                    return $metric;
                }
            );
        }

        $authUser = $request->get('auth_user') ?? $request->user();

        $canVerify = $authUser
            ? $this->rbacService->userHasPermission(
                $authUser,
                'verify-performance-metrics'
            )
            : false;

        return view('performances.fit-metrics', [
            'catalog' => $catalog,
            'players' => $players,
            'selectedPlayer' => $selectedPlayer,
            'metrics' => $metrics,
            'canVerify' => $canVerify,
        ]);
    }
}
