<?php

namespace App\Http\Controllers;

use App\Models\GameMatch;
use App\Models\MatchSheet;

class MatchSheetController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        abort_unless($user && ($user->isSystemAdmin() || $user->isAssociationUser() || $user->isClubUser()), 403);

        $scope = function ($query) use ($user): void {
            if ($user->isClubUser()) {
                abort_unless($user->club_id, 403);
                $query->where(fn ($q) => $q->where('home_club_id', $user->club_id)
                    ->orWhere('away_club_id', $user->club_id));
            } elseif ($user->isAssociationUser()) {
                abort_unless($user->association_id, 403);
                $query->whereHas('competition', fn ($q) => $q->where('association_id', $user->association_id));
            }
        };

        $matches = GameMatch::query();
        $scope($matches);
        $matchSheets = MatchSheet::query()
            ->whereHas('match', $scope)
            ->with(['match.homeTeam.club', 'match.awayTeam.club', 'match.competition'])
            ->latest()->get();
        $upcomingMatches = (clone $matches)->where('match_date', '>=', now())
            ->where('status', 'scheduled')
            ->with(['homeTeam.club', 'awayTeam.club', 'competition'])
            ->orderBy('match_date')->limit(10)->get();
        $recentMatches = (clone $matches)->where('match_date', '<', now())
            ->with(['homeTeam.club', 'awayTeam.club', 'competition'])
            ->orderByDesc('match_date')->limit(10)->get();

        return view('match-sheet.index', compact('matchSheets', 'upcomingMatches', 'recentMatches'));
    }

    public function show(GameMatch $gameMatch)
    {
        $match = $gameMatch;
        $user = auth()->user();
        abort_unless($user && ($user->isSystemAdmin() || $user->isAssociationUser() || $user->isClubUser()), 403);
        if ($user->isClubUser()) {
            abort_unless($user->club_id && in_array($user->club_id, [$match->home_club_id, $match->away_club_id]), 403);
        } elseif ($user->isAssociationUser()) {
            abort_unless($user->association_id && $match->competition?->association_id === $user->association_id, 403);
        }

        $match->load(['homeTeam.club', 'awayTeam.club', 'competition', 'events.player']);
        $sheet = MatchSheet::query()->where('match_id', $match->id)->firstOrFail();

        return view('match-sheet.demo-show', compact('match', 'sheet'));
    }
}
