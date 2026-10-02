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
            ->with(['player:id,first_name,last_name,name,fifa_connect_id,date_of_birth', 'clubOfficial', 'club:id,name'])
            ->orderBy($tab === 'pending' ? 'updated_at' : 'approved_at', $tab === 'pending' ? 'asc' : 'desc')
            ->paginate(20)->withQueryString();

        return view('licenses.approval.index', [
            'tab' => $tab, 'counts' => $counts, 'licenses' => $licenses, 'registryConnected' => $this->registry->isConfigured(),
        ]);
    }

    public function show(Request $request, PlayerLicense $license)
    {
        $this->authorizeLicense($request->user(), $license);
        $license->load(['player.club', 'clubOfficial', 'club', 'documents:id,player_license_id,document_type,original_name,size,created_at', 'events.user:id,name']);

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

    private function authorizeLicense(User $user, PlayerLicense $license): void
    {
        abort_unless($this->workflow->canApprove($user) && $this->workflow->canAccess($user, $license), 403);
    }
}
