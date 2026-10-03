<?php

namespace App\Http\Controllers\Licensing;

use App\Http\Controllers\Controller;
use App\Models\DocumentSignatureRequest;
use App\Models\PlayerLicense;
use App\Services\Documents\DocumentSignatureService;
use App\Services\Licensing\LicenseWorkflow;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

final class LicenseCardSignatureController extends Controller
{
    public function __construct(private readonly LicenseWorkflow $workflow)
    {
    }

    public function pdf(Request $request, PlayerLicense $license)
    {
        $this->authorizeLicense($request, $license, false);
        [$bytes] = $this->render($license);

        return response($bytes, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="licence-'.$license->id.'-CR80.pdf"',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    public function store(Request $request, PlayerLicense $license, DocumentSignatureService $signatures)
    {
        $this->authorizeLicense($request, $license, true);
        $provider = $request->validate([
            'provider' => 'required|in:signotec_document,adobe_sign,globalsign_dss',
        ])['provider'];

        [$bytes, $snapshot] = $this->render($license);
        $versionSha256 = hash('sha256', json_encode($snapshot, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $reference = 'LICENSE-CARD-'.$license->id.'-'.substr($versionSha256, 0, 16);

        if (DocumentSignatureRequest::query()
            ->where('workflow', 'license_card.approved_document')
            ->where('document_reference', $reference)
            ->whereIn('status', ['pending', 'sent', 'signed'])
            ->exists()) {
            return back()->with('success', 'Une demande de signature existe déjà pour cette version de la carte.');
        }

        try {
            $signature = $signatures->createRequest($provider, [
                'type' => 'license_card_pdf',
                'reference' => $reference,
                'sha256' => hash('sha256', $bytes),
                'version_sha256' => $versionSha256,
                'license_id' => $license->id,
                'player_id' => $license->player_id,
                'club_id' => $license->club_id,
                'filename' => 'licence-'.$license->id.'-CR80.pdf',
                'name' => 'Carte de licence '.$license->license_number,
                'bytes' => $bytes,
            ], $this->signer($request), 'license_card.approved_document');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with($signature->status === 'error' ? 'error' : 'success',
            $signature->status === 'sent'
                ? 'Carte envoyée au fournisseur de signature.'
                : 'Demande de signature créée pour cette version approuvée de la carte.');
    }

    public function sync(
        Request $request,
        PlayerLicense $license,
        DocumentSignatureRequest $signature,
        DocumentSignatureService $signatures
    ) {
        $this->authorizeLicense($request, $license, true);
        $this->authorizeSignature($license, $signature);
        $updated = $signatures->sync($signature);

        return back()->with('success', 'Statut de signature synchronisé : '.ucfirst($updated->status).'.');
    }

    public function download(Request $request, PlayerLicense $license, DocumentSignatureRequest $signature)
    {
        $this->authorizeLicense($request, $license, false);
        $this->authorizeSignature($license, $signature);
        return app(\App\Services\Documents\DocumentSignatureStorage::class)
            ->download($signature, 'licence-'.$license->id.'-CR80-signee.pdf');
    }

    private function authorizeLicense(Request $request, PlayerLicense $license, bool $sign): void
    {
        abort_unless($this->workflow->canApprove($request->user()) && $this->workflow->canAccess($request->user(), $license), 403);
        abort_unless($license->status === 'active' && !$license->club_official_id, 404);

        if ($sign) {
            abort_unless(in_array($request->user()->role, ['association_admin', 'association_registrar'], true), 403);
        }
    }

    private function authorizeSignature(PlayerLicense $license, DocumentSignatureRequest $signature): void
    {
        abort_unless(
            $signature->workflow === 'license_card.approved_document'
            && (int) data_get($signature->metadata, 'document.license_id') === (int) $license->id,
            404
        );
    }

    private function signer(Request $request): array
    {
        return [
            'type' => 'user',
            'id' => $request->user()->id,
            'role' => $request->user()->role,
            'name' => $request->user()->name,
            'email' => $request->user()->email,
        ];
    }

    private function render(PlayerLicense $license): array
    {
        $license->loadMissing(['player.passport', 'club.association', 'photo']);
        $snapshot = [
            'license_id' => $license->id,
            'license_number' => $license->license_number,
            'status' => $license->status,
            'approval_status' => $license->approval_status,
            'approved_at' => optional($license->approved_at)->toIso8601String(),
            'approved_by' => $license->approved_by,
            'issue_date' => optional($license->issue_date)->format('Y-m-d'),
            'expiry_date' => optional($license->expiry_date)->format('Y-m-d'),
            'season' => $license->season,
            'discipline' => $license->discipline,
            'level' => $license->level,
            'age_category' => $license->age_category,
            'player_id' => $license->player_id,
            'player_name' => $license->player?->full_name,
            'date_of_birth' => optional($license->player?->date_of_birth)->format('Y-m-d'),
            'nationality' => $license->player?->nationality,
            'fifa_connect_id' => $license->player?->fifa_connect_id,
            'club_id' => $license->club_id,
            'club_name' => $license->club?->name,
            'association_id' => $license->club?->association_id,
            'association_name' => $license->club?->association?->getDisplayName(),
        ];

        $bytes = Pdf::loadView('licenses.cards.pdf', [
            'license' => $license,
            'generatedAt' => $license->approved_at ?: $license->updated_at,
        ])->setPaper([0, 0, 242.65, 153.01])->setOption('isRemoteEnabled', false)->output();

        return [$bytes, $snapshot];
    }
}
