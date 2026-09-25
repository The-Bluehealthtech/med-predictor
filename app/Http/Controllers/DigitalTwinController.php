<?php

namespace App\Http\Controllers;

use App\Models\Player;
use App\Models\PlayerPerformance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DigitalTwinController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        abort_unless($user, 401);

        $players = Player::query()
            ->orderBy('last_name')
            ->orderBy('first_name');

        if ($user->isPlayer()) {
            abort_unless($user->player_id, 403);
            $players->whereKey($user->player_id);
        } elseif (!(
            $user->isSystemAdmin()
            || $user->isClubUser()
            || $user->isAssociationUser()
        )) {
            abort(403);
        }

        $players = $players->get();

        $selectedPlayer = null;
        $baseline = null;
        $scenario = null;

        if ($request->filled('player_id')) {
            $playerId = filter_var(
                $request->input('player_id'),
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1]]
            );
            abort_unless($playerId !== false, 404);

            if ($user->isPlayer()) {
                abort_unless((int) $user->player_id === (int) $playerId, 403);
            }

            $selectedPlayer = Player::query()->findOrFail((int) $playerId);

            $baseline = PlayerPerformance::query()
                ->where('player_id', $selectedPlayer->id)
                ->orderByDesc('performance_date')
                ->orderByDesc('id')
                ->first();

            if ($baseline && $request->filled('adjustment_pct')) {
                $validated = $request->validate([
                    'adjustment_pct' => 'required|numeric|min:-20|max:20',
                ]);

                $adjustment = (float) $validated['adjustment_pct'];

                $scenario = [
                    'adjustment_pct' => $adjustment,
                    'scores' => collect([
                        'overall' => $baseline->overall_performance_score,
                        'physical' => $baseline->physical_score,
                        'technical' => $baseline->technical_score,
                        'tactical' => $baseline->tactical_score,
                        'mental' => $baseline->mental_score,
                        'social' => $baseline->social_score,
                    ])->map(
                        fn ($value) => $value === null
                            ? null
                            : round($this->clamp(
                                (float) $value * (1 + $adjustment / 100),
                                0,
                                100
                            ), 1)
                    )->all(),
                ];
            }
        }

        return view('analytics.digital-twin-canonical', compact(
            'players',
            'selectedPlayer',
            'baseline',
            'scenario'
        ));
    }

    private function clamp(float $value, float $min, float $max): float
    {
        return max($min, min($max, $value));
    }
}
