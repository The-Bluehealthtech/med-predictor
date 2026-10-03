<?php

namespace App\Http\Controllers;

use App\Models\ClubOfficial;
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
        $match->loadMissing(['competition','homeTeam.club','awayTeam.club','matchSheet']);
        $plan = MatchMedicalEmergencyPlan::query()->firstOrCreate(
            ['match_id'=>$match->id],
            $this->defaults($match)
        );
        $contactContext = $this->contactContext($match);
        $this->hydratePlanContacts($plan, $contactContext);

        $match->loadMissing(['rosters.team.club','rosters.players.player','medicalIncidents.player','medicalIncidents.doctor']);
        $connectMatch = $this->connectMatch($match);
        if ($connectMatch && blank($plan->connect_match_fifa_id)) {
            $plan->forceFill(['connect_match_fifa_id' => $connectMatch->match_fifa_id])->save();
        }
        $connectPeople = $this->connectPeople($match);
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
            'contactContext'=>$contactContext,
            'matchPlayers'=>$this->matchSheetPlayers($match),
            'incidents'=>$match->medicalIncidents->sortByDesc('created_at'),
            'roleDefinitions'=>$this->roleDefinitions(),
            'mechanismOptions'=>$this->mechanismOptions(),
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
            'team_leader_club_official_id'=>'nullable|exists:club_officials,id',
            'team_leader_person_fifa_id'=>'nullable|string|max:255',
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
        $identifiedPeople = $this->connectPeople($match);
        $validConnectIds = $identifiedPeople->pluck('person_fifa_id');
        $homeLeaders = $this->eligibleLeaders($match);
        if (filled($data['team_leader_club_official_id'] ?? null)) {
            $leader = $homeLeaders->firstWhere('club_official_id', (int) $data['team_leader_club_official_id']);
            if (!$leader) {
                throw ValidationException::withMessages(['team_leader_club_official_id'=>'Ce responsable n’est pas déclaré dans le staff actif du club recevant.']);
            }
            $data['team_leader_name'] = $leader['name'];
            $data['team_leader_person_fifa_id'] = $leader['person_fifa_id'] ?: null;
            if (filled($leader['phone'] ?? null)) $data['team_leader_phone'] = $leader['phone'];
            $data['team_leader_user_id'] = null;
        } elseif (filled($data['team_leader_person_fifa_id'] ?? null)) {
            $leader = $homeLeaders->firstWhere('person_fifa_id', $data['team_leader_person_fifa_id']);
            if (!$leader) {
                throw ValidationException::withMessages(['team_leader_person_fifa_id'=>'Ce responsable n’est pas déclaré dans le staff actif du club recevant.']);
            }
            $data['team_leader_club_official_id'] = $leader['club_official_id'];
            $data['team_leader_name'] = $leader['name'];
            if (filled($leader['phone'] ?? null)) $data['team_leader_phone'] = $leader['phone'];
            $data['team_leader_user_id'] = null;
        }
        foreach (($data['connect_role_assignments'] ?? []) as $role => $personFifaId) {
            if (filled($personFifaId) && !$validConnectIds->contains($personFifaId)) {
                throw ValidationException::withMessages([
                    'connect_role_assignments.'.$role => 'Cette identité FIFA Connect n’est pas déclarée comme officiel actif dans un des clubs du match.',
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
        $match->loadMissing(['competition','homeTeam.club','matchSheet']);
        $context = $this->contactContext($match);

        return [
            'protocol_name'=>'FIFA Emergency Care Protocols',
            'protocol_version'=>'v3 - March 2025',
            'stadium'=>$context['stadium'],
            'nearest_hospital'=>$context['hospital'],
            'nearest_hospital_phone'=>$context['hospital_phone'],
            'ambulance_contact'=>$context['ambulance'],
            'team_leader_name'=>$context['leader_name'],
            'team_leader_phone'=>$context['leader_phone'],
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
        if (!Schema::hasTable('club_officials')) return collect();

        return ClubOfficial::query()
            ->with('club:id,name')
            ->where('club_id', $match->home_club_id)
            ->where('status', 'active')
            ->orderBy('international_last_name')
            ->orderBy('international_first_name')
            ->get()
            ->map(fn (ClubOfficial $official) => [
                'club_official_id' => $official->id,
                'person_fifa_id' => $official->person_fifa_id,
                'name' => $official->fullName(),
                'role' => $official->roleLabel(),
                'team' => $official->club?->name,
                'phone' => $official->phone,
            ])->values();
    }

    private function contactContext(MatchModel $match): array
    {
        $competition = $match->competition;
        $homeClub = $match->homeTeam?->club;
        $fdm = $match->matchSheet;
        $pick = fn (...$values) => collect($values)->first(fn ($value) => filled($value));

        return [
            'stadium' => $pick($fdm?->stadium_venue, $match->stadium, $homeClub?->stadium_name, $homeClub?->stadium, $competition?->main_stadium, $match->venue),
            'stadium_source' => filled($fdm?->stadium_venue) ? 'FDM' : (filled($match->stadium) ? 'Match' : (filled($homeClub?->stadium_name) || filled($homeClub?->stadium) ? 'Club recevant' : 'Compétition')),
            'hospital' => $pick($homeClub?->matchday_hospital_name, $competition?->matchday_hospital_name),
            'hospital_phone' => $pick($homeClub?->matchday_hospital_phone, $competition?->matchday_hospital_phone),
            'hospital_source' => filled($homeClub?->matchday_hospital_name) ? 'Club recevant' : (filled($competition?->matchday_hospital_name) ? 'Compétition' : 'Non configuré'),
            'ambulance' => $pick($homeClub?->matchday_ambulance_contact, $competition?->matchday_ambulance_contact),
            'ambulance_source' => filled($homeClub?->matchday_ambulance_contact) ? 'Club recevant' : (filled($competition?->matchday_ambulance_contact) ? 'Compétition' : 'Non configuré'),
            'leader_name' => $pick($homeClub?->matchday_medical_contact_name, $competition?->matchday_medical_contact_name, $competition?->responsible_person),
            'leader_phone' => $pick($homeClub?->matchday_medical_contact_phone, $competition?->matchday_medical_contact_phone, $competition?->contact_phone, $homeClub?->phone),
            'leader_source' => filled($homeClub?->matchday_medical_contact_name) ? 'Club recevant' : (filled($competition?->matchday_medical_contact_name) || filled($competition?->responsible_person) ? 'Compétition' : 'Non configuré'),
            'home_club' => $homeClub,
            'competition' => $competition,
        ];
    }

    private function hydratePlanContacts(MatchMedicalEmergencyPlan $plan, array $context): void
    {
        $changes = [];
        foreach (['stadium'=>'stadium','nearest_hospital'=>'hospital','nearest_hospital_phone'=>'hospital_phone','ambulance_contact'=>'ambulance','team_leader_name'=>'leader_name','team_leader_phone'=>'leader_phone'] as $field=>$key) {
            if (blank($plan->{$field}) && filled($context[$key] ?? null)) $changes[$field] = $context[$key];
        }
        if ($changes) $plan->forceFill($changes)->save();
    }


    private function connectMatch(MatchModel $match): ?ConnectMatchRecord
    {
        if (!Schema::hasTable('fifa_connect_matches')) return null;

        return ConnectMatchRecord::query()
            ->with(['officials','teams.officials'])
            ->where('match_id', $match->id)
            ->first();
    }

    private function connectPeople(MatchModel $match)
    {
        if (!Schema::hasTable('club_officials')) return collect();

        return ClubOfficial::query()
            ->with('club:id,name')
            ->whereIn('club_id', array_filter([$match->home_club_id, $match->away_club_id]))
            ->where('status', 'active')
            ->whereNotNull('person_fifa_id')
            ->where('person_fifa_id', '!=', '')
            ->orderBy('international_last_name')
            ->orderBy('international_first_name')
            ->get()
            ->map(fn (ClubOfficial $official) => [
                'club_official_id' => $official->id,
                'person_fifa_id' => $official->person_fifa_id,
                'name' => $official->fullName(),
                'role' => $official->roleLabel(),
                'role_connect_id' => $official->roleCode(),
                'source' => $official->source ?: 'FIT',
                'team' => $official->club?->name,
                'club_id' => $official->club_id,
                'phone' => $official->phone,
            ])
            ->unique(fn ($row) => $row['club_id'].'|'.$row['person_fifa_id'].'|'.$row['role_connect_id'])
            ->values();
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

    private function matchSheetPlayers(MatchModel $match)
    {
        return $match->rosters
            ->flatMap(function ($roster) {
                return $roster->players->map(function ($rosterPlayer) use ($roster) {
                    $player = $rosterPlayer->player;
                    if (!$player) return null;

                    return [
                        'id' => $player->id,
                        'name' => $player->name ?: trim(($player->first_name ?? '').' '.($player->last_name ?? '')),
                        'jersey_number' => $rosterPlayer->jersey_number,
                        'club_name' => $roster->team?->club?->name ?: $roster->team?->name,
                        'team_name' => $roster->team?->name,
                    ];
                });
            })
            ->filter()
            ->unique('id')
            ->sortBy(fn ($row) => [$row['club_name'] ?? '', $row['jersey_number'] ?? 999, $row['name']])
            ->values();
    }

    private function mechanismOptions(): array
    {
        return [
            'collapse_non_contact' => 'Effondrement sans contact',
            'player_collision' => 'Collision avec un joueur',
            'ground_collision' => 'Impact avec le sol',
            'direct_blow' => 'Choc / coup direct',
            'twist_non_contact' => 'Torsion sans contact',
            'twist_contact' => 'Torsion avec contact',
            'hyperextension_flexion' => 'Hyperextension / hyperflexion',
            'fall' => 'Chute',
            'sprint_overload' => 'Sprint / surcharge / effort',
            'other' => 'Autre mécanisme',
        ];
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
