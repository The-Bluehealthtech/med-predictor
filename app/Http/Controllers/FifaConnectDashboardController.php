<?php

namespace App\Http\Controllers;

use App\Models\Association;
use App\Models\Club;
use App\Models\Confederation;
use App\Models\Player;
use App\Services\FifaConnectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FifaConnectDashboardController extends Controller
{
    public function __construct(
        private readonly FifaConnectService $fifaConnectService
    ) {
    }

    public function index(Request $request): View
    {
        $connectivity = $this->fifaConnectService->checkConnectivity();

        $fifaStats = [
            'confederations' => [
                'total' => Confederation::query()->count(),
                'synced' => Confederation::query()
                    ->where('fifa_sync_status', 'synced')
                    ->count(),
            ],
            'associations' => [
                'total' => Association::query()->count(),
                'synced' => Association::query()
                    ->whereNotNull('fifa_connect_id')
                    ->count(),
            ],
            'clubs' => [
                'total' => Club::query()->count(),
                'synced' => Club::query()
                    ->whereNotNull('fifa_connect_id')
                    ->count(),
            ],
            'players' => [
                'total' => Player::query()->count(),
                'synced' => Player::query()
                    ->whereNotNull('fifa_connect_id')
                    ->count(),
            ],
        ];

        $confederations = Confederation::query()
            ->orderBy('name')
            ->get();

        $filteredConfederation = null;

        if ($request->filled('confederation_id')) {
            $id = filter_var(
                $request->input('confederation_id'),
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1]]
            );

            abort_unless($id !== false, 404);

            $filteredConfederation = Confederation::query()
                ->findOrFail((int) $id);
        }

        return view('modules.fifa.dashboard', compact(
            'connectivity',
            'fifaStats',
            'confederations',
            'filteredConfederation'
        ));
    }

    public function status(): JsonResponse
    {
        return response()->json(
            $this->fifaConnectService->checkConnectivity()
        );
    }
}
