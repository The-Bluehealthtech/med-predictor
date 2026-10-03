<?php

namespace App\Http\Controllers\ClubOfficials;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\ClubOfficial;
use App\Services\ClubOfficials\ClubOfficials;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/** Module « Dirigeants et staff » : fiches au format FIFA Connect des officiels de chaque club. */
class ClubOfficialController extends Controller
{
    public function __construct(private readonly ClubOfficials $officials)
    {
    }

    public function index(Request $request)
    {
        $user = $request->user();
        abort_unless($user->isSystemAdmin() || $user->isClubUser() || $user->isAssociationUser(), 403);
        if ($user->isClubUser() && $user->club_id) {
            return redirect()->route('club-officials.club', $user->club_id);
        }
        $clubs = $this->officials->clubsFor($user)->select('clubs.*')
            ->addSelect(['officials_count' => ClubOfficial::query()->selectRaw('count(*)')->whereColumn('club_id', 'clubs.id')->where('status', 'active')])
            ->paginate(30);

        return view('club-officials.index', compact('clubs'));
    }

    public function club(Request $request, Club $club)
    {
        abort_unless($this->officials->canView($request->user(), $club), 403);
        $all = ClubOfficial::query()->where('club_id', $club->id)->orderByDesc('is_head_coach')->orderBy('international_last_name')->get();

        return view('club-officials.club', [
            'club' => $club,
            'staff' => $all->where('registration_type', ClubOfficial::TEAM_OFFICIAL)->values(),
            'board' => $all->where('registration_type', ClubOfficial::ORGANISATION_OFFICIAL)->values(),
            'canManage' => $this->officials->canManage($request->user(), $club),
            'xsdInstalled' => $this->officials->xsdInstalled(),
        ]);
    }

    public function create(Request $request, Club $club)
    {
        abort_unless($this->officials->canManage($request->user(), $club), 403);
        $type = $request->query('type') === ClubOfficial::ORGANISATION_OFFICIAL ? ClubOfficial::ORGANISATION_OFFICIAL : ClubOfficial::TEAM_OFFICIAL;

        return view('club-officials.form', $this->formData($club, new ClubOfficial(['registration_type' => $type, 'status' => 'active', 'discipline' => 'Football', 'registration_valid_from' => today()])));
    }

    public function syncConnect(Request $request, Club $club, \App\Services\ClubOfficials\SyncFifaConnectOfficials $sync)
    {
        abort_unless($this->officials->canManage($request->user(), $club), 403);

        $result = $sync->sync($club, $request->user());
        if (($result['available'] ?? false) === false) {
            return back()->with('status', 'Les tables FIFA Connect ne sont pas disponibles.');
        }
        if (($result['missing_club_fifa_id'] ?? false) === true) {
            return back()->with('status', 'Le club doit avoir un OrganisationFIFAId / FIFA Club ID avant la synchronisation.');
        }

        return back()->with(
            'status',
            sprintf(
                'Synchronisation FIFA Connect : %d créé(s), %d mis à jour, %d ignoré(s) car données obligatoires incomplètes.',
                $result['created'],
                $result['updated'],
                $result['skipped']
            )
        );
    }

    public function store(Request $request, Club $club)
    {
        abort_unless($this->officials->canManage($request->user(), $club), 403);
        $official = new ClubOfficial;
        $official->club_id = $club->id;
        $official->created_by = $request->user()->id;
        $this->save($request, $official);

        return redirect()->route('club-officials.show', [$club, $official])->with('status', 'Fiche enregistrée.');
    }

    public function show(Request $request, Club $club, ClubOfficial $official)
    {
        $this->authorizeView($request, $club, $official);

        $signatureService = app(\App\Services\Documents\DocumentSignatureService::class);
        $signatureProviders = \Illuminate\Support\Facades\Schema::hasTable('system_settings')
            ? collect($signatureService->allStatuses())
            : collect($signatureService->providers())->map(fn($provider,$slug)=>$provider+['slug'=>$slug,'enabled'=>false,'status'=>$provider['configured']?'disabled':'not_configured']);
        $signatureRequests = \Illuminate\Support\Facades\Schema::hasTable('document_signature_requests')
            ? \App\Models\DocumentSignatureRequest::query()->where('workflow','club_official.profile_document')->latest('id')->get()
                ->filter(fn($item)=>(int)data_get($item->metadata,'document.official_id')===(int)$official->id)->values()
            : collect();
        $canSign = in_array($request->user()->role, ['club_admin','association_admin'], true) && $this->officials->canManage($request->user(), $club);

        return view('club-officials.show', compact('club','official','signatureProviders','signatureRequests','canSign') + ['canManage' => $this->officials->canManage($request->user(), $club)]);
    }

    public function edit(Request $request, Club $club, ClubOfficial $official)
    {
        abort_unless((int) $official->club_id === (int) $club->id && $this->officials->canManage($request->user(), $club), 403);

        return view('club-officials.form', $this->formData($club, $official));
    }

    public function update(Request $request, Club $club, ClubOfficial $official)
    {
        abort_unless((int) $official->club_id === (int) $club->id && $this->officials->canManage($request->user(), $club), 403);
        $this->save($request, $official);

        return redirect()->route('club-officials.show', [$club, $official])->with('status', 'Fiche mise à jour.');
    }

    public function pdf(Request $request, Club $club, ClubOfficial $official)
    {
        $this->authorizeView($request, $club, $official);
        $response = Pdf::loadView('club-officials.pdf', ['club' => $club, 'official' => $official])->setPaper('a4')
            ->setOption('isRemoteEnabled', false)->download('fiche-fifa-connect-' . $official->id . '.pdf');
        $response->headers->set('Cache-Control', 'private, no-store');

        return $response;
    }

    private function authorizeView(Request $request, Club $club, ClubOfficial $official): void
    {
        abort_unless((int) $official->club_id === (int) $club->id, 404);
        abort_unless($this->officials->canView($request->user(), $club), 403);
    }

    private function save(Request $request, ClubOfficial $official): void
    {
        $type = $request->input('registration_type');
        // Codes pays FIFA Connect : ISO 3166 sur deux lettres majuscules.
        foreach (['nationality', 'second_nationality', 'country_of_birth'] as $field) {
            if ($request->filled($field)) {
                $request->merge([$field => strtoupper(trim((string) $request->input($field)))]);
            }
        }
        $data = $request->validate($this->officials->rules((string) $type));
        $data['is_head_coach'] = $type === ClubOfficial::TEAM_OFFICIAL && ($data['team_official_role'] ?? null) === 'Coach' && (bool) ($data['is_head_coach'] ?? false);
        // Un diplôme d'entraîneur ou de staff est une certification FIFA Connect de type TeamOfficial.
        $data['certification_type'] = !empty($data['certification_name']) ? ($type === ClubOfficial::TEAM_OFFICIAL ? 'TeamOfficial' : 'OrganisationOfficial') : null;
        if ($type === ClubOfficial::TEAM_OFFICIAL) {
            $data['organisation_official_role'] = null;
        } else {
            $data['team_official_role'] = null;
        }

        DB::transaction(function () use ($official, $data) {
            $official->fill($data);
            $official->save();
            if ($official->is_head_coach) {
                // Un seul entraîneur principal par club.
                ClubOfficial::query()->where('club_id', $official->club_id)->where('id', '!=', $official->id)->update(['is_head_coach' => false]);
            }
        });
    }

    private function formData(Club $club, ClubOfficial $official): array
    {
        return [
            'club' => $club,
            'official' => $official,
            'teamRoles' => $this->officials->roles(ClubOfficial::TEAM_OFFICIAL),
            'boardRoles' => $this->officials->roles(ClubOfficial::ORGANISATION_OFFICIAL),
            'xsdInstalled' => $this->officials->xsdInstalled(),
        ];
    }
}
