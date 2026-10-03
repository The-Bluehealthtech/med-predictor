<?php

namespace App\Http\Controllers;

use App\Models\MatchModel;
use App\Models\MatchSheet;
use Illuminate\Http\Request;

final class MatchdayPreparationController extends Controller
{
    private const MEDICAL_CLUB_ROLES = ['club_medical','team_doctor','doctor','medical_staff'];

    public function index(Request $request)
    {
        $user = $request->user();
        abort_unless($user, 403);

        $query = MatchSheet::query()
            ->with(['match.competition','match.homeTeam.club','match.awayTeam.club'])
            ->whereHas('match');

        if (!$user->isSystemAdmin() && $user->role !== 'admin') {
            if (str_starts_with((string) $user->role, 'association_')) {
                $query->whereHas('match.competition', fn ($q) => $q->where('association_id', $user->association_id));
            } else {
                $query->whereHas('match', fn ($q) => $q
                    ->where('home_club_id', $user->club_id)
                    ->orWhere('away_club_id', $user->club_id));
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('q')) {
            $term = '%'.$request->string('q').'%';
            $query->whereHas('match', fn ($q) => $q
                ->whereHas('homeTeam', fn ($t) => $t->where('name', 'like', $term))
                ->orWhereHas('awayTeam', fn ($t) => $t->where('name', 'like', $term))
                ->orWhereHas('competition', fn ($c) => $c->where('name', 'like', $term)));
        }

        $sheets = $query->orderByDesc('id')->paginate(20)->withQueryString();

        return view('competition-management.matches.matchday-selector', compact('sheets'));
    }

    public function show(Request $request, MatchModel $match)
    {
        abort_unless($this->canAccessMatch($request, $match), 403);

        $match->loadMissing([
            'competition',
            'homeTeam.club',
            'awayTeam.club',
            'matchSheet',
            'medicalEmergencyPlan',
            'officials',
        ]);

        $sheet = $match->matchSheet;
        $medicalPlan = $match->medicalEmergencyPlan;

        $sheetStatus = $sheet?->status ?: ($sheet ? 'draft' : 'missing');
        $officialsReady = (bool) (
            $sheet?->assigned_referee_id
            || $sheet?->main_referee_id
            || $match->officials->isNotEmpty()
            || filled($match->referee)
        );
        $venueReady = filled($match->stadium ?: $match->venue);
        $medicalStatus = $medicalPlan?->status ?: 'missing';
        $canOpenMedical = $this->canOpenMedical($request, $match);

        $readyCount = collect([
            in_array($sheetStatus, ['submitted','validated'], true),
            $officialsReady,
            $venueReady,
            in_array($medicalStatus, ['ready','validated'], true),
        ])->filter()->count();

        return view('competition-management.matches.matchday-preparation', [
            'match' => $match,
            'sheet' => $sheet,
            'sheetStatus' => $sheetStatus,
            'officialsReady' => $officialsReady,
            'venueReady' => $venueReady,
            'medicalStatus' => $medicalStatus,
            'canOpenMedical' => $canOpenMedical,
            'readyCount' => $readyCount,
            'totalCoreChecks' => 4,
        ]);
    }

    private function canAccessMatch(Request $request, MatchModel $match): bool
    {
        $user = $request->user();
        if (!$user) return false;
        if ($user->isSystemAdmin() || $user->role === 'admin') return true;

        if (str_starts_with((string) $user->role, 'association_')) {
            return $user->association_id
                && (int) $user->association_id === (int) $match->competition()->value('association_id');
        }

        return $user->club_id
            && in_array((int) $user->club_id, [(int) $match->home_club_id, (int) $match->away_club_id], true);
    }

    private function canOpenMedical(Request $request, MatchModel $match): bool
    {
        $user = $request->user();
        if (!$user) return false;
        if ($user->isSystemAdmin() || $user->role === 'admin') return true;

        if (in_array($user->role, ['association_admin','association_medical'], true)) {
            return $user->association_id
                && (int) $user->association_id === (int) $match->competition?->association_id;
        }

        return in_array($user->role, self::MEDICAL_CLUB_ROLES, true)
            && $user->club_id
            && in_array(
                (int) $user->club_id,
                [(int) $match->home_club_id, (int) $match->away_club_id],
                true
            );
    }
}
