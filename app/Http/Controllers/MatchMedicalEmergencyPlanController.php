<?php

namespace App\Http\Controllers;

use App\Models\MatchMedicalEmergencyPlan;
use App\Models\MatchModel;
use App\Models\User;
use App\Models\FifaConnect\MatchRecord as ConnectMatchRecord;
use App\Models\DocumentSignatureRequest;
use App\Services\Documents\DocumentSignatureService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

final class MatchMedicalEmergencyPlanController extends Controller
{
    private const MEDICAL_CLUB_ROLES = ['club_medical','team_doctor','doctor','medical_staff'];
    private const ASSOCIATION_VIEW_ROLES = ['association_admin','association_medical'];
    private const ASSOCIATION_EDIT_ROLES = ['association_admin','association_medical'];

    public function show(Request $request, MatchModel $match)
    {
        $this->authorizeView($request, $match);
        $match->loadMissing(['competition','homeTeam.club','awayTeam.club']);
        $plan = MatchMedicalEmergencyPlan::query()->firstOrCreate(
            ['match_id'=>$match->id],
            $this->defaults($match)
        );

        $match->loadMissing(['rosters.players.player','medicalIncidents.player','medicalIncidents.doctor']);
        $connectMatch = $this->connectMatch($match);
        if ($connectMatch && blank($plan->connect_match_fifa_id)) {
            $plan->forceFill(['connect_match_fifa_id' => $connectMatch->match_fifa_id])->save();
        }
        $connectPeople = $this->connectPeople($connectMatch);
        $signatureProviders = collect(app(DocumentSignatureService::class)->allStatuses());
        $signatureRequests = Schema::hasTable('document_signature_requests')
            ? DocumentSignatureRequest::query()->where('workflow','matchday_medical_plan.final_document')->latest('id')->get()
                ->filter(fn ($item) => (int) data_get($item->metadata,'document.plan_id') === (int) $plan->id)->values()
            : collect();

        return view('matches.medical-emergency-plan', [
            'match'=>$match,
            'plan'=>$plan,
            'canEdit'=>$this->canEdit($request, $match),
            'canValidate'=>$this->canValidate($request, $match),
            'canDocumentIncident'=>$this->canDocumentIncident($request, $match),
            'eligibleLeaders'=>$this->eligibleLeaders($match),
            'matchPlayers'=>$match->rosters->flatMap->players->pluck('player')->filter()->unique('id')->sortBy('name')->values(),
            'incidents'=>$match->medicalIncidents->sortByDesc('created_at'),
            'roleDefinitions'=>$this->roleDefinitions(),
            'connectMatch'=>$connectMatch,
            'connectPeople'=>$connectPeople,
            'signatureProviders'=>$signatureProviders,
            'signatureRequests'=>$signatureRequests,
            'canRequestSignature'=>$this->canRequestSignature($request, $match, $plan),
        ]);
    }

    public function update(Request $request, MatchModel $match)
    {
        abort_unless($this->canEdit($request, $match), 403);
        $plan = MatchMedicalEmergencyPlan::query()->firstOrCreate(
            ['match_id'=>$match->id],
            $this->defaults($match)
        );

        $data = $request->validate([
            'stadium'=>'nullable|string|max:255',
            'nearest_hospital'=>'required|string|max:255',
            'nearest_hospital_phone'=>'required|string|max:64',
            'ambulance_contact'=>'required|string|max:128',
            'team_leader_user_id'=>'nullable|exists:users,id',
            'team_leader_name'=>'required|string|max:255',
            'team_leader_phone'=>'required|string|max:64',
            'role_assignments'=>'nullable|array',
            'role_assignments.*'=>'nullable|string|max:255',
            'connect_role_assignments'=>'nullable|array',
            'connect_role_assignments.*'=>'nullable|string|max:255',
            'equipment_checklist'=>'nullable|array',
            'equipment_checklist.*'=>'boolean',
            'timeline_checklist'=>'nullable|array',
            'timeline_checklist.*'=>'boolean',
            'notes'=>'nullable|string|max:4000',
        ]);

        $connectMatch = $this->connectMatch($match);
        $validConnectIds = $this->connectPeople($connectMatch)->pluck('person_fifa_id');
        foreach (($data['connect_role_assignments'] ?? []) as $role => $personFifaId) {
            if (filled($personFifaId) && !$validConnectIds->contains($personFifaId)) {
                throw ValidationException::withMessages([
                    'connect_role_assignments.'.$role => 'Cette identité FIFA Connect n’appartient pas aux officiels ou staffs du match lié.',
                ]);
            }
        }

        $plan->fill($data);
        if ($connectMatch) $plan->connect_match_fifa_id = $connectMatch->match_fifa_id;
        $plan->status = $this->isReady($plan) ? 'ready' : 'draft';
        $plan->prepared_at = now();
        $plan->prepared_by = $request->user()->id;
        $plan->save();

        return back()->with('success', $plan->status === 'ready'
            ? 'Plan d’urgence médical prêt pour validation.'
            : 'Plan d’urgence médical enregistré en brouillon.');
    }

    public function validatePlan(Request $request, MatchModel $match)
    {
        abort_unless($this->canValidate($request, $match), 403);
        $plan = MatchMedicalEmergencyPlan::query()->where('match_id',$match->id)->firstOrFail();
        abort_unless($this->isReady($plan), 422, 'Le plan est incomplet.');

        $plan->forceFill([
            'status'=>'validated',
            'validated_at'=>now(),
            'validated_by'=>$request->user()->id,
        ])->save();

        return back()->with('success','Plan d’urgence médical validé.');
    }

    private function authorizeView(Request $request, MatchModel $match): void
    {
        abort_unless($this->canView($request, $match), 403);
    }

    private function canView(Request $request, MatchModel $match): bool
    {
        $user=$request->user();
        if (!$user) return false;
        if ($user->isSystemAdmin() || $user->role === 'admin') return true;

        $associationId=$match->competition?->association_id;
        if (in_array($user->role,self::ASSOCIATION_VIEW_ROLES,true)) {
            return $user->association_id && (int)$user->association_id === (int)$associationId;
        }

        if (in_array($user->role,self::MEDICAL_CLUB_ROLES,true)) {
            return $user->club_id && in_array((int)$user->club_id,[(int)$match->home_club_id,(int)$match->away_club_id],true);
        }

        return false;
    }

    private function canEdit(Request $request, MatchModel $match): bool
    {
        $user=$request->user();
        if (!$user) return false;
        if ($user->isSystemAdmin() || $user->role === 'admin') return true;

        return in_array($user->role,self::ASSOCIATION_EDIT_ROLES,true)
            && $user->association_id
            && (int)$user->association_id === (int)$match->competition?->association_id;
    }

    private function canValidate(Request $request, MatchModel $match): bool
    {
        $user=$request->user();
        return $user
            && $user->role === 'association_medical'
            && $user->association_id
            && (int)$user->association_id === (int)$match->competition?->association_id;
    }

    private function canDocumentIncident(Request $request, MatchModel $match): bool
    {
        $user=$request->user();
        if (!$user) return false;
        if ($user->isSystemAdmin() || $user->role === 'admin') return true;
        if ($user->role === 'association_medical') {
            return $user->association_id
                && (int)$user->association_id === (int)$match->competition?->association_id;
        }

        return in_array($user->role,self::MEDICAL_CLUB_ROLES,true)
            && $user->club_id
            && in_array((int)$user->club_id,[(int)$match->home_club_id,(int)$match->away_club_id],true);
    }

    private function isReady(MatchMedicalEmergencyPlan $plan): bool
    {
        $roles=$plan->role_assignments ?? [];
        $equipment=$plan->equipment_checklist ?? [];
        $timeline=$plan->timeline_checklist ?? [];

        return filled($plan->nearest_hospital)
            && filled($plan->nearest_hospital_phone)
            && filled($plan->ambulance_contact)
            && filled($plan->team_leader_name)
            && collect(['black','red','orange','blue','green','white'])->every(fn($role)=>filled($roles[$role]??null))
            && collect($this->equipmentKeys())->every(fn($key)=>(bool)($equipment[$key]??false))
            && collect($this->timelineKeys())->every(fn($key)=>(bool)($timeline[$key]??false));
    }

    private function defaults(MatchModel $match): array
    {
        return [
            'protocol_name'=>'FIFA Emergency Care Protocols',
            'protocol_version'=>'v3 - March 2025',
            'stadium'=>$match->stadium ?: $match->venue,
            'role_assignments'=>array_fill_keys(['black','red','orange','blue','green','white'],null),
            'connect_role_assignments'=>array_fill_keys(['black','red','orange','blue','green','white'],null),
            'equipment_checklist'=>array_fill_keys($this->equipmentKeys(),false),
            'timeline_checklist'=>array_fill_keys($this->timelineKeys(),false),
        ];
    }

    private function equipmentKeys(): array
    {
        return ['evacuation_set_1','evacuation_set_2','aed_1','aed_2','oxygen_1','oxygen_2','splints','emergency_bag'];
    }

    private function timelineKeys(): array
    {
        return ['h_minus_2_team_present','h_minus_2_equipment_checked','h_minus_90_simulation','h_minus_60_infirmary_ready','halftime_team_present','post_match_until_last_player'];
    }

    private function eligibleLeaders(MatchModel $match)
    {
        return User::query()
            ->whereIn('role',['association_medical','club_medical','team_doctor','doctor','medical_staff'])
            ->where(function($q)use($match){
                $q->where('association_id',$match->competition?->association_id)
                    ->orWhereIn('club_id',array_filter([$match->home_club_id,$match->away_club_id]));
            })
            ->orderBy('name')
            ->get(['id','name','role','club_id','association_id']);
    }


    private function connectMatch(MatchModel $match): ?ConnectMatchRecord
    {
        if (!Schema::hasTable('fifa_connect_matches')) return null;

        return ConnectMatchRecord::query()
            ->with(['officials','teams.officials'])
            ->where('match_id', $match->id)
            ->first();
    }

    private function connectPeople(?ConnectMatchRecord $connectMatch)
    {
        if (!$connectMatch) return collect();

        $rows = collect();
        foreach ($connectMatch->officials as $official) {
            $rows->push([
                'person_fifa_id' => $official->person_fifa_id,
                'name' => trim(($official->international_first_name ?? '').' '.($official->international_last_name ?? '')) ?: $official->person_fifa_id,
                'role' => $official->role_description ?: $official->role,
                'source' => 'MatchOfficial',
                'team' => null,
            ]);
        }
        foreach ($connectMatch->teams as $team) {
            foreach ($team->officials as $official) {
                $rows->push([
                    'person_fifa_id' => $official->person_fifa_id,
                    'name' => trim(($official->international_first_name ?? '').' '.($official->international_last_name ?? '')) ?: $official->person_fifa_id,
                    'role' => $official->role_description ?: $official->role,
                    'source' => 'TeamOfficial',
                    'team' => $team->international_name ?: $team->international_short_name,
                ]);
            }
        }

        return $rows->filter(fn ($row) => filled($row['person_fifa_id']))
            ->unique('person_fifa_id')->sortBy('name')->values();
    }

    private function canRequestSignature(Request $request, MatchModel $match, MatchMedicalEmergencyPlan $plan): bool
    {
        $user = $request->user();
        if (!$user || $plan->status !== 'validated') return false;
        if ($user->isSystemAdmin() || $user->role === 'admin') return true;

        return $user->role === 'association_medical'
            && $user->association_id
            && (int) $user->association_id === (int) $match->competition?->association_id;
    }

    private function roleDefinitions(): array
    {
        return [
            'black'=>'Responsable d’équipe',
            'red'=>'Évaluation / compressions',
            'orange'=>'Voies aériennes / rachis cervical',
            'green'=>'DAE / mallette urgence',
            'blue'=>'Oxygène / relais compressions',
            'white'=>'Évacuation / attelles',
        ];
    }
}
