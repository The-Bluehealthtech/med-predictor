<?php

namespace App\Http\Controllers;

use App\Services\Analytics\PlayerFormAnalytics;
use App\Services\CoachCockpit\CoachCockpitData;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Analyse des performances : suivi individuel des joueurs d'un club à partir
 * des feuilles de match (forme, temps de jeu, charge, efficacité, alertes).
 * Les relevés player_performances (un seul point par joueur, origine non
 * documentée) ne sont plus utilisés pour décider.
 */
class PerformanceAnalyticsController extends Controller
{
    public function index(Request $request, PlayerFormAnalytics $analytics, CoachCockpitData $cockpit): View
    {
        $user = Auth::user();
        abort_if($user->isPlayer(), 403, 'Accès réservé au staff et aux administrateurs.');

        $filters = $request->validate([
            'club_id' => ['nullable', 'integer'],
            'window' => ['nullable', 'in:' . implode(',', array_keys(PlayerFormAnalytics::WINDOWS))],
            'position' => ['nullable', 'in:' . implode(',', array_keys(PlayerFormAnalytics::POSITIONS))],
            'min_matches' => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);

        $clubs = $this->clubsFor($user, $cockpit->availableClubs());
        if (isset($filters['club_id'])) {
            $clubId = (int) $filters['club_id'];
            abort_unless($clubs->contains('id', $clubId), 404, 'Club introuvable ou non autorisé.');
        } else {
            $clubId = $clubs->contains('id', (int) $user->club_id) ? (int) $user->club_id : $clubs->first()?->id;
        }

        $window = $filters['window'] ?? '10';
        $position = $filters['position'] ?? null;
        $minMatches = (int) ($filters['min_matches'] ?? 3);
        $data = $clubId ? $analytics->forClub($clubId, $window, $position, $minMatches) : null;
        $alerts = collect($data['alerts'] ?? []);

        return view('modules.performances.analytics-canonical', [
            'clubs' => $clubs,
            'clubId' => $clubId,
            'window' => $window,
            'position' => $position,
            'minMatches' => $minMatches,
            'data' => $data,
            'alerts' => $alerts,
            'windows' => PlayerFormAnalytics::WINDOWS,
            'positions' => PlayerFormAnalytics::POSITIONS,
        ]);
    }

    /** Périmètre : un compte club voit son club, une fédération ses clubs, l'admin système tous les clubs avec matchs. */
    private function clubsFor($user, Collection $clubs): Collection
    {
        if ($user->isSystemAdmin()) {
            return $clubs;
        }
        if ($user->isClubUser()) {
            return $clubs->where('id', (int) $user->club_id)->values();
        }
        if ($user->isAssociationUser()) {
            $ids = DB::table('clubs')->where('association_id', $user->association_id)->pluck('id')->map(fn ($id) => (int) $id);

            return $clubs->filter(fn ($c) => $ids->contains((int) $c->id))->values();
        }

        // Autres rôles du staff (DTN, officiels…) : mêmes clubs que le cockpit entraîneur.
        return $clubs;
    }
}
