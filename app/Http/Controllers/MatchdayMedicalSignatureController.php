<?php

namespace App\Http\Controllers;

use App\Models\DocumentSignatureRequest;
use App\Models\MatchMedicalEmergencyPlan;
use App\Models\MatchModel;
use App\Services\Documents\DocumentSignatureService;
use App\Services\Documents\DocumentSignatureStorage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class MatchdayMedicalSignatureController extends Controller
{
    public function store(
        Request $request,
        MatchModel $match,
        DocumentSignatureService $signatures
    ): RedirectResponse {
        $plan = MatchMedicalEmergencyPlan::query()->where('match_id', $match->id)->firstOrFail();
        $this->authorizeSigner($request, $match, $plan);
        abort_unless($plan->status === 'validated', 409, 'Le plan médical doit être validé avant sa signature numérique.');

        $data = $request->validate([
            'provider' => 'required|string|in:signotec_document,adobe_sign,globalsign_dss',
        ]);

        $match->loadMissing(['competition','homeTeam','awayTeam']);
        $pdfBytes = Pdf::loadView('matches.medical-emergency-plan-pdf', [
            'match' => $match,
            'plan' => $plan,
        ])->setOption('isRemoteEnabled', false)->output();

        $sha256 = hash('sha256', $pdfBytes);
        $reference = 'MATCHDAY-MEDICAL-'.$plan->id.'-'.substr($sha256, 0, 16);

        $existing = DocumentSignatureRequest::query()
            ->where('workflow', 'matchday_medical_plan.final_document')
            ->where('document_reference', $reference)
            ->where('signer_id', $request->user()->id)
            ->whereIn('status', ['pending','sent','signed'])
            ->latest('id')->first();

        if ($existing) {
            return back()->with('info', 'Une demande de signature existe déjà pour cette version du plan.');
        }

        try {
            $signature = $signatures->createRequest($data['provider'], [
                'type' => 'matchday_medical_plan_pdf',
                'reference' => $reference,
                'sha256' => $sha256,
                'plan_id' => $plan->id,
                'match_id' => $match->id,
                'match_fifa_id' => $plan->connect_match_fifa_id,
                'signer_fifa_connect_id' => $request->user()->fifa_connect_id,
                'version_updated_at' => optional($plan->updated_at)->toIso8601String(),
                'filename' => 'Medical-Matchday-'.$match->id.'.pdf',
                'name' => 'Medical Matchday '.$match->id.' - '.($match->homeTeam?->name ?? 'Home').' vs '.($match->awayTeam?->name ?? 'Away'),
                'bytes' => $pdfBytes,
            ], [
                'type' => 'user',
                'id' => $request->user()->id,
                'role' => $request->user()->role,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
            ], 'matchday_medical_plan.final_document');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with($signature->status === 'error' ? 'error' : 'success',
            $signature->status === 'sent'
                ? 'Plan Medical Matchday envoyé au fournisseur de signature.'
                : 'Demande de signature créée pour cette version figée du plan.'
        );
    }

    public function sync(
        Request $request,
        MatchModel $match,
        DocumentSignatureRequest $signature,
        DocumentSignatureService $signatures
    ): RedirectResponse {
        $plan = MatchMedicalEmergencyPlan::query()->where('match_id', $match->id)->firstOrFail();
        $this->authorizeSignature($plan, $signature);
        $this->authorizeSigner($request, $match, $plan);

        $updated = $signatures->sync($signature);

        return back()->with('success', 'Statut de signature synchronisé : '.ucfirst($updated->status).'.');
    }

    public function download(
        Request $request,
        MatchModel $match,
        DocumentSignatureRequest $signature,
        DocumentSignatureStorage $storage
    ) {
        $plan = MatchMedicalEmergencyPlan::query()->where('match_id', $match->id)->firstOrFail();
        $this->authorizeSignature($plan, $signature);
        $this->authorizeView($request, $match);

        return $storage->download($signature, 'Medical-Matchday-'.$match->id.'-signed.pdf');
    }

    private function authorizeSignature(MatchMedicalEmergencyPlan $plan, DocumentSignatureRequest $signature): void
    {
        abort_unless(
            $signature->workflow === 'matchday_medical_plan.final_document'
            && (int) data_get($signature->metadata, 'document.plan_id') === (int) $plan->id,
            404
        );
    }

    private function authorizeSigner(Request $request, MatchModel $match, MatchMedicalEmergencyPlan $plan): void
    {
        $user = $request->user();
        abort_unless($user, 403);
        if ($user->isSystemAdmin() || $user->role === 'admin') return;

        abort_unless(
            $user->role === 'association_medical'
            && $user->association_id
            && (int) $user->association_id === (int) $match->competition?->association_id,
            403
        );
    }

    private function authorizeView(Request $request, MatchModel $match): void
    {
        $user = $request->user();
        abort_unless($user, 403);
        if ($user->isSystemAdmin() || $user->role === 'admin') return;

        $associationId = $match->competition?->association_id;
        if (in_array($user->role, ['association_admin','association_medical'], true)) {
            abort_unless($user->association_id && (int) $user->association_id === (int) $associationId, 403);
            return;
        }

        abort_unless(
            $user->club_id
            && in_array((int) $user->club_id, [(int) $match->home_club_id,(int) $match->away_club_id], true),
            403
        );
    }
}
