<?php

namespace App\Http\Controllers;

use App\Models\MatchMedicalIncident;
use App\Models\MatchModel;
use App\Models\MatchRosterPlayer;
use Illuminate\Http\Request;

final class MatchMedicalIncidentController extends Controller
{
    private const MEDICAL_CLUB_ROLES = ['club_medical','team_doctor','doctor','medical_staff'];

    public function store(Request $request, MatchModel $match)
    {
        abort_unless($this->canDocument($request, $match), 403);

        $data = $request->validate([
            'incident_type'=>'required|in:cardiac_arrest,cervical_spine,fracture,concussion,other',
            'match_minute'=>'nullable|integer|min:0|max:180',
            'player_id'=>'nullable|exists:players,id',
            'mechanism'=>'nullable|string|max:255',
            'contact'=>'nullable|boolean',
            'abcde_assessment'=>'nullable|array',
            'abcde_assessment.*'=>'nullable|string|max:1000',
            'protocol_actions'=>'nullable|array',
            'protocol_actions.*'=>'nullable|boolean',
            'loss_of_consciousness'=>'nullable|boolean',
            'aed_used'=>'nullable|boolean',
            'oxygen_used'=>'nullable|boolean',
            'evacuated'=>'nullable|boolean',
            'evacuation_destination'=>'nullable|string|max:255',
            'doctor_user_id'=>'nullable|exists:users,id',
            'doctor_name'=>'required|string|max:255',
            'initial_diagnosis'=>'nullable|string|max:4000',
            'notes'=>'nullable|string|max:4000',
        ]);

        if (!empty($data['player_id'])) {
            abort_unless(
                $this->playerBelongsToMatch($match, (int)$data['player_id']),
                422,
                'Le joueur doit appartenir à la feuille de match.'
            );
        }

        foreach (['loss_of_consciousness','aed_used','oxygen_used','evacuated'] as $field) {
            $data[$field] = $request->boolean($field);
        }
        $data['contact'] = $request->has('contact') ? $request->boolean('contact') : null;
        $protocols = [
            'cardiac_arrest' => 'FIFA_SCA',
            'cervical_spine' => 'FIFA_HEAD_CERVICAL',
            'concussion' => 'FIFA_HEAD_CERVICAL',
        ];
        if (isset($protocols[$data['incident_type']])) {
            $data['protocol_code'] = $protocols[$data['incident_type']];
            $data['protocol_version'] = 'FIFA Emergency Care Protocols v3 - March 2025';
            $data['protocol_activated_at'] = now();
            $data['protocol_actions'] = collect($data['protocol_actions'] ?? [])
                ->map(fn ($value) => filter_var($value, FILTER_VALIDATE_BOOLEAN))
                ->all();
        } else {
            unset($data['protocol_actions']);
        }

        $data['match_id'] = $match->id;
        $data['created_by'] = $request->user()->id;

        MatchMedicalIncident::query()->create($data);

        return back()->with('success', 'Incident médical terrain enregistré.');
    }

    private function canDocument(Request $request, MatchModel $match): bool
    {
        $user = $request->user();
        if (!$user) return false;

        if ($user->role === 'association_medical') {
            return $user->association_id
                && (int)$user->association_id === (int)$match->competition?->association_id;
        }

        return in_array($user->role, self::MEDICAL_CLUB_ROLES, true)
            && $user->club_id
            && in_array(
                (int)$user->club_id,
                [(int)$match->home_club_id,(int)$match->away_club_id],
                true
            );
    }

    private function playerBelongsToMatch(MatchModel $match, int $playerId): bool
    {
        return MatchRosterPlayer::query()
            ->where('player_id', $playerId)
            ->whereHas('roster', fn ($q) => $q->where('match_id', $match->id))
            ->exists();
    }
}
