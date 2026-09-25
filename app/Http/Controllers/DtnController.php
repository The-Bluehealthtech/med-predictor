<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Competition;
use App\Models\Player;
use App\Models\PlayerPerformance;
use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DtnController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        abort_unless($user, 401);

        abort_unless(
            $user->isSystemAdmin() || $user->isAssociationUser(),
            403
        );

        $stats = [
            'players' => Player::query()->count(),
            'clubs' => Club::query()->count(),
            'teams' => Team::query()->count(),
            'competitions' => Competition::query()->count(),
            'performance_records' => PlayerPerformance::query()->count(),
            'players_with_performance' => PlayerPerformance::query()
                ->distinct('player_id')
                ->count('player_id'),
        ];

        $recentPerformances = PlayerPerformance::query()
            ->with('player')
            ->orderByDesc('performance_date')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        return view('dtn.index-canonical', compact(
            'stats',
            'recentPerformances'
        ));
    }
}
