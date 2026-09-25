<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Services\FifaConnectService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FIFATestController extends Controller
{
    public function __construct(
        private readonly FifaConnectService $fifaConnectService
    ) {
    }

    public function show(Request $request): View
    {
        $players = Player::query()
            ->with(['club', 'association'])
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->limit(100)
            ->get();

        $selectedPlayer = null;

        if ($request->filled('player_id')) {
            $playerId = filter_var(
                $request->input('player_id'),
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1]]
            );

            abort_unless($playerId !== false, 404);

            $selectedPlayer = Player::query()
                ->with(['club', 'association'])
                ->findOrFail((int) $playerId);
        }

        return view('fifa.portal', [
            'players' => $players,
            'selectedPlayer' => $selectedPlayer,
            'connectivity' => $this->fifaConnectService->checkConnectivity(),
        ]);
    }
}
