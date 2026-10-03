<?php

namespace App\Http\Controllers;

use App\Models\MatchMedicalIncident;
use App\Models\MatchModel;
use App\Models\MatchRosterPlayer;
use App\Models\ClubOfficial;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
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
            'mechanism'=>'nullable|string|in:collapse_non_contact,player_collision,ground_collision,direct_blow,twist_non_contact,twist_contact,hyperextension_flexion,fall,sprint_overload,other',
            'contact'=>'nullable|boolean',
            'abcde_assessment'=>'nullable|array:a,b,c,d,e',
            'abcde_assessment.a'=>['nullable', Rule::in(array_keys($this->abcdeOptions()['a']))],
            'abcde_assessment.b'=>['nullable', Rule::in(array_keys($this->abcdeOptions()['b']))],
            'abcde_assessment.c'=>['nullable', Rule::in(array_keys($this->abcdeOptions()['c']))],
            'abcde_assessment.d'=>['nullable', Rule::in(array_keys($this->abcdeOptions()['d']))],
            'abcde_assessment.e'=>['nullable', Rule::in(array_keys($this->abcdeOptions()['e']))],
            'protocol_actions'=>'nullable|array',
            'protocol_actions.*'=>'nullable|boolean',
            'loss_of_consciousness'=>'nullable|boolean',
            'aed_used'=>'nullable|boolean',
            'oxygen_used'=>'nullable|boolean',
            'evacuated'=>'nullable|boolean',
            'evacuation_destination'=>'nullable|string|max:255',
            'doctor_user_id'=>'nullable|exists:users,id',
            'doctor_club_official_id'=>'required|exists:club_officials,id',
            'doctor_name'=>'nullable|string|max:255',
            'initial_diagnosis'=>['nullable', Rule::in(array_keys($this->initialDiagnosisOptions()))],
            'notes'=>'nullable|string|max:4000',
        ]);

        $professional = ClubOfficial::query()
            ->whereKey((int) $data['doctor_club_official_id'])
            ->whereIn('club_id', array_filter([$match->home_club_id, $match->away_club_id]))
            ->where('status','active')
            ->where(function ($q) {
                $q->whereIn('team_official_role', ['TeamDoctor','Physiotherapist'])
                  ->orWhereIn('role_description', ['TeamDoctor','Doctor','Physiotherapist','Médecin d’équipe','Médecin','Kinésithérapeute']);
            })->first();
        abort_unless($professional, 422, 'Le professionnel doit appartenir au staff médical identifié du match.');
        $data['doctor_name'] = $professional->fullName();
        unset($data['doctor_club_official_id']);

        $allowedDestinations = $this->evacuationDestinations($match);
        if (filled($data['evacuation_destination'] ?? null)) {
            abort_unless(in_array($data['evacuation_destination'], $allowedDestinations, true), 422, 'La destination doit provenir de la configuration Medical Matchday.');
        }

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

    private function evacuationDestinations(MatchModel $match): array
    {
        $match->loadMissing(['competition','homeTeam.club']);
        return collect([
            $match->homeTeam?->club?->matchday_hospital_name,
            $match->competition?->matchday_hospital_name,
        ])->filter()->unique()->values()->all();
    }

    private function abcdeOptions(): array
    {
        return [
            'a'=>['patent'=>'Voies aériennes libres','at_risk'=>'Voies aériennes à risque / menace','obstructed'=>'Obstruction suspectée / constatée','adjunct'=>'Dispositif de maintien des voies aériennes en place'],
            'b'=>['normal'=>'Respiration spontanée sans anomalie évidente','abnormal'=>'Respiration anormale / détresse suspectée','absent'=>'Respiration absente','assisted'=>'Ventilation / assistance respiratoire en cours'],
            'c'=>['stable'=>'Circulation sans anomalie évidente','compromised'=>'Circulation compromise / choc suspecté','major_bleeding'=>'Hémorragie majeure constatée','cpr'=>'RCP / compressions en cours'],
            'd'=>['alert'=>'Alerte / répond normalement','voice'=>'Répond à la voix','pain'=>'Répond à la douleur','unresponsive'=>'Sans réponse','neuro_abnormal'=>'Anomalie neurologique constatée'],
            'e'=>['no_finding'=>'Pas de lésion évidente à l’exposition','injury_found'=>'Lésion / traumatisme visible','temperature_risk'=>'Risque thermique / environnemental','multiple_findings'=>'Lésions multiples constatées'],
        ];
    }

    private function initialDiagnosisOptions(): array
    {
        return [
            'cardiac_arrest'=>'Arrêt cardiaque suspecté / confirmé cliniquement',
            'cervical_spine'=>'Traumatisme crânien / cervical suspecté',
            'concussion'=>'Commotion cérébrale suspectée',
            'fracture'=>'Fracture / lésion osseuse suspectée',
            'soft_tissue'=>'Lésion musculo-tendineuse / ligamentaire suspectée',
            'other_pending'=>'Autre / diagnostic à préciser après évaluation clinique',
        ];
    }

    private function canDocument(Request $request, MatchModel $match): bool
    {
        $user = $request->user();
        if (!$user) return false;
        if ($user->isSystemAdmin() || $user->role === 'admin') return true;

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
