<?php

namespace App\Http\Controllers\Passports;

use App\Http\Controllers\Controller;
use App\Models\DocumentSignatureRequest;
use App\Models\Player;
use App\Services\Documents\DocumentSignatureService;
use App\Services\Passports\MedicalSummary;
use App\Services\Passports\PassportAccess;
use App\Services\Passports\PassportAttestations;
use App\Services\Passports\TransferPassport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

final class PassportDocumentSignatureController extends Controller
{
    public function __construct(
        private readonly PassportAccess $access,
        private readonly MedicalSummary $medical,
        private readonly TransferPassport $transfer,
        private readonly PassportAttestations $attestations,
    ) {
    }

    public function medical(Request $request, int $player, DocumentSignatureService $signatures)
    {
        $model = $this->player($player);
        abort_unless($this->attestations->canAttest($request->user(), $model), 403);
        $data = $request->validate([
            'purpose' => 'required|in:' . implode(',', array_keys(MedicalSummary::PURPOSES)),
            'provider' => 'required|in:signotec_document,adobe_sign,globalsign_dss',
        ]);

        $summary = $this->medical->build($model, $data['purpose'], $request->user()->name);
        $attestation = $this->attestations->status($model, $summary);
        abort_unless(($attestation['state'] ?? null) === 'valid', 409);

        $bytes = Pdf::loadView('passports.medical.pdf', [
            'summary' => $summary,
            'sections' => MedicalSummary::SECTIONS,
            'attestation' => $attestation,
        ])->setPaper('a4')->setOption('isRemoteEnabled', false)->output();

        $attestationId = data_get($attestation, 'attestation.id');
        $versionSha256 = hash('sha256', implode('|', [
            (string) $attestationId,
            (string) data_get($attestation, 'hash'),
            $data['purpose'],
        ]));

        return $this->createRequest($request, $signatures, $data['provider'], $model, $bytes, [
            'workflow' => 'medical_passport.final_document',
            'type' => 'medical_passport_pdf',
            'filename' => 'passeport-medical-ips-' . $model->id . '.pdf',
            'purpose' => $data['purpose'],
            'attestation_id' => $attestationId,
            'version_sha256' => $versionSha256,
        ]);
    }

    public function transfer(Request $request, int $player, DocumentSignatureService $signatures)
    {
        $model = $this->player($player);
        abort_unless($this->access->canViewTransfer($request->user(), $model), 403);
        abort_unless($request->user()->isClubUser() || $request->user()->isAssociationUser(), 403);

        $provider = $request->validate([
            'provider' => 'required|in:signotec_document,adobe_sign,globalsign_dss',
        ])['provider'];

        $passport = $this->transfer->build($model);
        $version = $passport;
        unset($version['document']['generated_at']);
        $versionSha256 = hash('sha256', json_encode($version, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $bytes = Pdf::loadView('passports.transfer.pdf', ['passport' => $passport])
            ->setPaper('a4')->setOption('isRemoteEnabled', false)->output();

        return $this->createRequest($request, $signatures, $provider, $model, $bytes, [
            'workflow' => 'transfer_passport.final_document',
            'type' => 'transfer_passport_pdf',
            'filename' => 'passeport-transfert-' . $model->id . '.pdf',
            'version_sha256' => $versionSha256,
        ]);
    }

    public function sync(Request $request, int $player, DocumentSignatureRequest $signature, DocumentSignatureService $signatures)
    {
        $model = $this->player($player);
        $this->authorizeRequest($request, $model, $signature, true);
        $updated = $signatures->sync($signature);

        return back()->with('status', 'Statut de signature synchronisé : ' . ucfirst($updated->status) . '.');
    }

    public function download(Request $request, int $player, DocumentSignatureRequest $signature)
    {
        $model = $this->player($player);
        $this->authorizeRequest($request, $model, $signature);
        return app(\App\Services\Documents\DocumentSignatureStorage::class)
            ->download($signature, $signature->document_type . '-' . $model->id . '-signed.pdf');
    }

    private function createRequest(Request $request, DocumentSignatureService $signatures, string $provider, Player $player, string $bytes, array $document)
    {
        $sha256 = hash('sha256', $bytes);
        $versionSha256 = $document['version_sha256'] ?? $sha256;
        $reference = strtoupper(str_replace('_pdf', '', $document['type'])) . '-' . $player->id . '-' . substr($versionSha256, 0, 16);

        $existing = DocumentSignatureRequest::query()
            ->where('workflow', $document['workflow'])
            ->where('document_reference', $reference)
            ->whereIn('status', ['pending', 'sent', 'signed'])
            ->exists();

        if ($existing) {
            return back()->with('status', 'Une demande existe déjà pour cette version du passeport.');
        }

        try {
            $signature = $signatures->createRequest($provider, [
                'type' => $document['type'],
                'reference' => $reference,
                'sha256' => $sha256,
                'player_id' => $player->id,
                'filename' => $document['filename'],
                'name' => $document['filename'],
                'bytes' => $bytes,
                'purpose' => $document['purpose'] ?? null,
                'attestation_id' => $document['attestation_id'] ?? null,
                'version_sha256' => $versionSha256,
            ], [
                'type' => 'user',
                'id' => $request->user()->id,
                'role' => $request->user()->role,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
            ], $document['workflow']);
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['signature' => $e->getMessage()]);
        }

        return back()->with('status', $signature->status === 'sent'
            ? 'Passeport envoyé au fournisseur de signature.'
            : 'Demande de signature numérique créée.');
    }

    private function authorizeRequest(Request $request, Player $player, DocumentSignatureRequest $signature, bool $manage = false): void
    {
        abort_unless((int) data_get($signature->metadata, 'document.player_id') === (int) $player->id, 404);

        if ($signature->workflow === 'medical_passport.final_document') {
            abort_unless($this->access->canViewMedical($request->user(), $player), 403);
            if ($manage) {
                abort_unless($this->attestations->canAttest($request->user(), $player), 403);
            }
            return;
        }

        if ($signature->workflow === 'transfer_passport.final_document') {
            abort_unless($this->access->canViewTransfer($request->user(), $player), 403);
            if ($manage) {
                abort_unless($request->user()->isClubUser() || $request->user()->isAssociationUser(), 403);
            }
            return;
        }

        abort(404);
    }

    private function player(int $id): Player
    {
        return Player::withoutGlobalScopes()->with('club.association')->findOrFail($id);
    }
}
