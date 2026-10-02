<?php

namespace App\Http\Controllers\Licensing;

use App\Http\Controllers\Controller;
use App\Models\PlayerLicense;
use App\Models\User;
use App\Services\Licensing\FifaIdRegistry;
use App\Services\Licensing\LicenseWorkflow;
use Illuminate\Http\Request;
use InvalidArgumentException;

/**
 * Côté fédération : file des demandes, examen d'un dossier, vérification
 * d'identité auprès de FIFA ID (facultative) et décision.
 */
class LicenseApprovalController extends Controller
{
    public const TABS = [
        'pending' => 'À examiner',
        'justification_requested' => 'Complément demandé',
        'active' => 'Approuvées',
        'revoked' => 'Refusées',
    ];

    public function __construct(private readonly LicenseWorkflow $workflow, private readonly FifaIdRegistry $registry)
    {
    }

    public function index(Request $request)
    {
        $user = $request->user();
        abort_unless($this->workflow->canApprove($user), 403);
        $tab = array_key_exists($request->query('tab'), self::TABS) ? $request->query('tab') : 'pending';

        $counts = [];
        foreach (array_keys(self::TABS) as $status) {
            $counts[$status] = $this->workflow->licenses($user)->where('status', $status)->count();
        }
        $licenses = $this->workflow->licenses($user)->where('status', $tab)->withCount('documents')
            ->with([
                'player:id,first_name,last_name,name,fifa_connect_id,date_of_birth,nationality,position,preferred_foot,player_picture,player_face_url,club_id',
                'clubOfficial',
                'club:id,name,association_id,logo_url,logo_image,logo_path',
            ])
            ->orderBy($tab === 'pending' ? 'updated_at' : 'approved_at', $tab === 'pending' ? 'asc' : 'desc')
            ->paginate(20)->withQueryString();

        return view('licenses.approval.index', [
            'tab' => $tab, 'counts' => $counts, 'licenses' => $licenses, 'registryConnected' => $this->registry->isConfigured(),
        ]);
    }

    public function show(Request $request, PlayerLicense $license)
    {
        $this->authorizeLicense($request->user(), $license);
        $license->load([
            'player.club', 'player.passport', 'clubOfficial', 'club.association', 'photo',
            'documents:id,player_license_id,document_type,original_name,mime_type,size,created_at',
            'events.user:id,name',
        ]);

        return view('licenses.approval.show', [
            'license' => $license,
            'registryConnected' => $this->registry->isConfigured(),
            'missing' => $this->workflow->missingDocuments($license),
            'pcma' => app(\App\Services\Licensing\PcmaRequirement::class)->check($license),
            'required' => $this->workflow->requiredDocuments($license),

            'requester' => $license->requested_by ? User::query()->find($license->requested_by, ['id', 'name']) : null,
            'decider' => $license->approved_by ? User::query()->find($license->approved_by, ['id', 'name']) : null,
        ]);
    }

    public function verifyIdentity(Request $request, PlayerLicense $license)
    {
        $this->authorizeLicense($request->user(), $license);
        $result = $this->registry->verifyLicense($license);
        $this->workflow->recordIdentityCheck($license, $result, $request->user());

        return redirect()->route('licenses.review', $license)->with($result['status'] === 'match' ? 'success' : 'info', $result['label'] . '.');
    }

    public function decide(Request $request, PlayerLicense $license)
    {
        $this->authorizeLicense($request->user(), $license);
        $data = $request->validate([
            'decision' => 'required|in:approve,request_info,reject',
            'message' => 'nullable|string|max:2000',
        ]);

        try {
            $this->workflow->decide($license, $request->user(), $data['decision'], $data['message'] ?? null);
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        $done = ['approve' => 'Licence approuvée : elle est active.', 'request_info' => 'Complément demandé au club.', 'reject' => 'Demande refusée.'][$data['decision']];

        return redirect()->route('licenses.validation')->with('success', $done);
    }

    public function card(Request $request, PlayerLicense $license)
    {
        $this->authorizeLicense($request->user(), $license);
        abort_unless($license->status === 'active' && !$license->club_official_id, 404);
        $license->load(['player.passport', 'club.association', 'photo']);

        return view('licenses.cards.show', ['license' => $license]);
    }

    public function cardsBatch(Request $request)
    {
        $user = $request->user();
        abort_unless($this->workflow->canApprove($user), 403);
        $data = $request->validate([
            'license_ids' => 'required|array|min:1|max:50',
            'license_ids.*' => 'integer|distinct',
        ]);
        $ids = collect($data['license_ids'])->map(fn ($id) => (int) $id)->values();
        $licenses = $this->workflow->licenses($user)
            ->whereIn('id', $ids)
            ->where('status', 'active')
            ->whereNull('club_official_id')
            ->with(['player.passport', 'club.association'])
            ->get();
        abort_unless($licenses->count() === $ids->count(), 403);

        return view('licenses.cards.batch', ['licenses' => $licenses]);
    }

    private function authorizeLicense(User $user, PlayerLicense $license): void
    {
        abort_unless($this->workflow->canApprove($user) && $this->workflow->canAccess($user, $license), 403);
    }
}
