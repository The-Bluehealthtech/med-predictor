<?php

namespace App\Http\Controllers\Licensing;

use App\Http\Controllers\Controller;
use App\Models\LicenseBiometricCheck;
use App\Models\LicenseIntegrityReview;
use App\Models\PlayerLicense;
use App\Models\User;
use App\Services\AgeVerificationService;
use App\Services\Licensing\AwsRekognitionFaceMatcher;
use App\Services\Licensing\BiometricIntegrityProvider;
use App\Services\Licensing\FifaIdRegistry;
use App\Services\Licensing\LicenseFaceEvidence;
use App\Services\Licensing\LicenseWorkflow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function __construct(
        private readonly LicenseWorkflow $workflow,
        private readonly FifaIdRegistry $registry,
        private readonly AgeVerificationService $ageVerification,
        private readonly BiometricIntegrityProvider $biometricIntegrity,
        private readonly AwsRekognitionFaceMatcher $faceMatcher,
        private readonly LicenseFaceEvidence $faceEvidence,
    ) {
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
            'tab' => $tab, 'counts' => $counts, 'licenses' => $licenses, 'registryConnected' => $this->registry->isConfigured() && $this->registry->isEnabled(),
        ]);
    }

    public function show(Request $request, PlayerLicense $license)
    {
        $this->authorizeLicense($request->user(), $license);
        $license->load([
            'player.club', 'player.passport', 'clubOfficial', 'club.association', 'photo',
            'documents:id,player_license_id,document_type,original_name,mime_type,size,created_at',
            'events.user:id,name',
            'integrityReviews.reviewer:id,name',
            'biometricChecks.reviewer:id,name',
        ]);

        $ageVerification = ['coverage' => 'Insuffisant', 'flags' => [], 'confirmed' => []];
        if ($license->player) {
            try {
                $ageVerification = $this->ageVerification->assess($license->player, false);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return view('licenses.approval.show', [
            'license' => $license,
            'registryConnected' => $this->registry->isConfigured() && $this->registry->isEnabled(),
            'missing' => $this->workflow->missingDocuments($license),
            'pcma' => app(\App\Services\Licensing\PcmaRequirement::class)->check($license),
            'required' => $this->workflow->requiredDocuments($license),
            'ageVerification' => $ageVerification,
            'biometricProvider' => $this->biometricIntegrity->status(),

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
        $signatureService = app(\App\Services\Documents\DocumentSignatureService::class);
        $signatureProviders = \Illuminate\Support\Facades\Schema::hasTable('system_settings')
            ? collect($signatureService->allStatuses())
            : collect($signatureService->providers())->map(fn($provider,$slug)=>$provider+['slug'=>$slug,'enabled'=>false,'status'=>$provider['configured']?'disabled':'not_configured']);
        $signatureRequests = \Illuminate\Support\Facades\Schema::hasTable('document_signature_requests')
            ? \App\Models\DocumentSignatureRequest::query()->where('workflow','license_card.approved_document')->latest('id')->get()
                ->filter(fn($item)=>(int)data_get($item->metadata,'document.license_id')===(int)$license->id)->values()
            : collect();
        $canSignCard = in_array($request->user()->role, ['association_admin','association_registrar'], true);

        return view('licenses.cards.show', compact('license','signatureProviders','signatureRequests','canSignCard'));
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

    public function compareFaces(Request $request, PlayerLicense $license)
    {
        $user = $request->user();
        $this->authorizeLicense($user, $license);
        abort_if((bool) $license->club_official_id, 404);

        if (!$this->faceMatcher->isConfigured()) {
            return redirect()->route('licenses.review', $license)
                ->with('info', 'AWS Rekognition CompareFaces n’est pas encore configuré sur le serveur.');
        }
        if (!$this->faceMatcher->isEnabled()) {
            return redirect()->route('licenses.review', $license)
                ->with('info', 'AWS Rekognition CompareFaces est configuré mais désactivé dans Configuration des API.');
        }

        $sources = $this->faceEvidence->collect($license);
        if (count($sources) < 2) {
            return redirect()->route('licenses.review', $license)
                ->with('info', 'Au moins deux photos locales exploitables sont nécessaires pour la comparaison biométrique.');
        }

        $referenceKey = array_key_first($sources);
        $reference = $sources[$referenceKey];
        $checks = [];
        foreach ($sources as $targetKey => $target) {
            if ($targetKey === $referenceKey) {
                continue;
            }
            $result = $this->faceMatcher->compare($reference['bytes'], $target['bytes']);
            $checks[] = [
                'target_key' => $targetKey,
                'target_label' => $target['label'],
                'result' => $result,
            ];
        }

        DB::transaction(function () use ($license, $user, $referenceKey, $reference, $checks) {
            foreach ($checks as $check) {
                $result = $check['result'];
                $license->biometricChecks()->create([
                    'reviewer_id' => $user->id,
                    'capability' => 'face_match',
                    'provider' => 'aws_rekognition',
                    'source_reference' => $referenceKey,
                    'target_reference' => $check['target_key'],
                    'score' => $result['score'] ?? null,
                    'threshold' => $result['threshold'] ?? null,
                    'status' => $result['status'] ?? 'error',
                    'metadata' => [
                        'source_label' => $reference['label'],
                        'target_label' => $check['target_label'],
                        'matched_above_threshold' => $result['matched_above_threshold'] ?? null,
                        'source_face_confidence' => $result['source_face_confidence'] ?? null,
                        'target_faces_unmatched' => $result['target_faces_unmatched'] ?? null,
                        'request_id' => $result['request_id'] ?? null,
                    ],
                    'checked_at' => now(),
                ]);
            }
            $license->events()->create([
                'user_id' => $user->id,
                'action' => 'biometric_face_check',
                'message' => count($checks) . ' comparaison(s) faciale(s) AWS Rekognition enregistrée(s).',
            ]);
        });

        return redirect()->route('licenses.review', $license)
            ->with('success', count($checks) . ' comparaison(s) faciale(s) terminée(s).');
    }

    public function recordIntegrityReview(Request $request, PlayerLicense $license)
    {
        $user = $request->user();
        $this->authorizeLicense($user, $license);
        abort_if((bool) $license->club_official_id, 404);
        $statuses = implode(',', array_keys(LicenseIntegrityReview::STATUSES));
        $data = $request->validate([
            'photo_status' => 'required|in:' . $statuses,
            'signature_status' => 'required|in:' . $statuses,
            'identity_status' => 'required|in:' . $statuses,
            'age_status' => 'required|in:' . $statuses,
            'notes' => 'nullable|string|max:2000',
        ]);
        $license->loadMissing(['player.passport', 'documents']);
        $player = $license->player;
        $passport = $player?->passport;
        $ageReview = ['coverage' => 'Insuffisant', 'flags' => [], 'confirmed' => []];
        if ($player) {
            try {
                $ageReview = $this->ageVerification->assess($player, false);
            } catch (\Throwable $e) {
                report($e);
            }
        }
        DB::transaction(function () use ($license, $user, $data, $player, $passport, $ageReview) {
            $review = $license->integrityReviews()->create([
                'reviewer_id' => $user->id,
                'photo_status' => $data['photo_status'],
                'signature_status' => $data['signature_status'],
                'identity_status' => $data['identity_status'],
                'age_status' => $data['age_status'],
                'notes' => $data['notes'] ?? null,
                'reviewed_at' => now(),
                'evidence' => [
                    'mode' => 'human_review',
                    'photo_sources' => [
                        'license_photo' => (bool) $license->photo,
                        'player_profile' => (bool) $player?->player_picture_url,
                        'passport' => (bool) $passport?->photo_url,
                        'submitted_photo_document_id' => $license->documents->where('document_type', 'photo')->sortByDesc('id')->first()?->id,
                        'identity_document_id' => $license->documents->where('document_type', 'identity')->sortByDesc('id')->first()?->id,
                    ],
                    'signature_sources' => [
                        'passport_signature' => (bool) $passport?->signature_url,
                        'identity_document_id' => $license->documents->where('document_type', 'identity')->sortByDesc('id')->first()?->id,
                        'contract_document_id' => $license->documents->where('document_type', 'contract')->sortByDesc('id')->first()?->id,
                    ],
                    'date_of_birth_sources' => [
                        'fit' => $player?->date_of_birth?->toDateString(),
                        'passport' => $passport?->fifa_date_of_birth?->toDateString(),
                        'fifa_registry' => data_get($license->identity_check, 'registry.date_of_birth'),
                    ],
                    'age_verification' => $ageReview,
                    'identity_check_status' => $license->identity_check_status,
                ],
            ]);
            $license->events()->create([
                'user_id' => $user->id,
                'action' => 'integrity_review',
                'message' => 'Revue anti-fraude #' . $review->id . ' enregistrée.',
            ]);
        });

        return redirect()->route('licenses.review', $license)->with('success', 'Revue anti-fraude enregistrée.');
    }

    private function authorizeLicense(User $user, PlayerLicense $license): void
    {
        abort_unless($this->workflow->canApprove($user) && $this->workflow->canAccess($user, $license), 403);
    }
}
