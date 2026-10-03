<?php

namespace App\Http\Controllers\Passports;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Services\Passports\IpsFhirBundle;
use App\Services\Passports\MedicalSummary;
use App\Services\Passports\PassportAccess;
use App\Services\Passports\PassportAttestations;
use App\Services\Passports\TransferPassport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Module Passeports : passeport médical (résumé IPS HL7/IHE) et passeport de
 * transfert (format FIFA), en consultation et en PDF. Chaque consultation d'un
 * passeport médical est journalisée.
 */
class PassportsController extends Controller
{
    public function __construct(
        private readonly PassportAccess $access,
        private readonly MedicalSummary $medical,
        private readonly TransferPassport $transfer,
        private readonly PassportAttestations $attestations,
    ) {
    }

    public function medicalIndex(Request $request)
    {
        $user = $request->user();
        if ($user->isPlayer()) {
            abort_unless($user->player_id, 403);

            return redirect()->route('passports.medical.show', ['player' => $user->player_id, 'purpose' => 'player_share']);
        }
        abort_unless($user->hasAnyRole(['system_admin', 'association_medical', 'club_medical', 'doctor', 'team_doctor', 'medical_staff']), 403);

        return view('passports.index', ['kind' => 'medical'] + $this->search($request, $this->access->medicalPlayers($user)));
    }

    public function medicalShow(Request $request, int $player)
    {
        [$model, $purpose] = $this->medicalContext($request, $player);
        $summary = $this->medical->build($model, $purpose, $request->user()->name);
        $this->audit($request, $model, $purpose, 'view');

        $attestation = $this->attestations->status($model, $summary);

        return view('passports.medical.show', ['summary' => $summary, 'purposes' => MedicalSummary::PURPOSES, 'sections' => MedicalSummary::SECTIONS,
            'attestation' => $attestation, 'canAttest' => $this->attestations->canAttest($request->user(), $model)] +
            $this->signatureContext($model, 'medical_passport.final_document'));
    }

    /** Signature électronique simple : le médecin confirme par son mot de passe ; l'empreinte du contenu est conservée. */
    public function medicalAttest(Request $request, int $player)
    {
        [$model, $purpose] = $this->medicalContext($request, $player);
        abort_unless($this->attestations->canAttest($request->user(), $model), 403);
        $data = $request->validate([
            'password' => ['required', 'current_password'],
            'license' => ['nullable', 'string', 'max:60'],
            'confirm' => ['accepted'],
        ], ['password.current_password' => 'Mot de passe incorrect.', 'confirm.accepted' => 'Confirmez avoir vérifié le contenu du résumé.']);
        $summary = $this->medical->build($model, $purpose, $request->user()->name);
        $attestation = $this->attestations->attest($request->user(), $model, $summary, $purpose, $data['license'] ?? null, $request->ip());
        $this->audit($request, $model, $purpose, 'attest');

        return redirect()->route('passports.medical.show', ['player' => $model->id, 'purpose' => $purpose])
            ->with('status', 'Passeport médical attesté le ' . $attestation->signed_at->format('d/m/Y à H:i') . '.');
    }

    public function medicalPdf(Request $request, int $player)
    {
        [$model, $purpose] = $this->medicalContext($request, $player);
        $summary = $this->medical->build($model, $purpose, $request->user()->name);
        $this->audit($request, $model, $purpose, 'pdf');

        return $this->pdf('passports.medical.pdf', ['summary' => $summary, 'sections' => MedicalSummary::SECTIONS, 'attestation' => $this->attestations->status($model, $summary)],
            'passeport-medical-ips-' . $model->id . '.pdf');
    }

    /** Téléchargement du passeport médical en HL7 FHIR (Bundle IPS de type document). */
    public function medicalFhir(Request $request, int $player, IpsFhirBundle $fhir)
    {
        [$model, $purpose] = $this->medicalContext($request, $player);
        $summary = $this->medical->build($model, $purpose, $request->user()->name);
        $this->audit($request, $model, $purpose, 'fhir');

        return response()->json($fhir->build($summary, $this->attestations->status($model, $summary)), 200, [
            'Content-Type' => 'application/fhir+json',
            'Content-Disposition' => 'attachment; filename="passeport-medical-ips-' . $model->id . '.fhir.json"',
            'Cache-Control' => 'private, no-store',
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    public function transferIndex(Request $request)
    {
        $user = $request->user();
        if ($user->isPlayer()) {
            abort_unless($user->player_id, 403);

            return redirect()->route('passports.transfer.show', $user->player_id);
        }
        abort_unless($user->isSystemAdmin() || $user->isClubUser() || $user->isAssociationUser(), 403);

        return view('passports.index', ['kind' => 'transfer'] + $this->search($request, $this->access->transferPlayers($user)));
    }

    public function transferShow(Request $request, int $player)
    {
        $model = $this->player($player);
        abort_unless($this->access->canViewTransfer($request->user(), $model), 403);

        return view('passports.transfer.show', ['passport' => $this->transfer->build($model)] +
            $this->signatureContext($model, 'transfer_passport.final_document'));
    }

    public function transferPdf(Request $request, int $player)
    {
        $model = $this->player($player);
        abort_unless($this->access->canViewTransfer($request->user(), $model), 403);

        return $this->pdf('passports.transfer.pdf', ['passport' => $this->transfer->build($model)], 'passeport-transfert-' . $model->id . '.pdf');
    }

    private function medicalContext(Request $request, int $player): array
    {
        $model = $this->player($player);
        abort_unless($this->access->canViewMedical($request->user(), $model), 403);
        $purpose = $request->validate(['purpose' => ['nullable', 'in:' . implode(',', array_keys(MedicalSummary::PURPOSES))]])['purpose']
            ?? ($request->user()->isPlayer() ? 'player_share' : 'general');

        return [$model, $purpose];
    }

    private function player(int $id): Player
    {
        return Player::withoutGlobalScopes()->with('club.association')->findOrFail($id);
    }

    private function search(Request $request, $query): array
    {
        $term = trim((string) $request->query('q', ''));
        if ($term !== '') {
            $query->where(fn ($q) => $q->where('first_name', 'like', "%{$term}%")->orWhere('last_name', 'like', "%{$term}%")->orWhere('name', 'like', "%{$term}%"));
        }

        return ['players' => $query->with('club')->orderBy('last_name')->orderBy('first_name')->paginate(25)->withQueryString(), 'term' => $term];
    }

    private function pdf(string $view, array $data, string $filename)
    {
        $response = Pdf::loadView($view, $data)->setPaper('a4')->setOption('isRemoteEnabled', false)->download($filename);
        $response->headers->set('Cache-Control', 'private, no-store');
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        return $response;
    }

    private function signatureContext(Player $player, string $workflow): array
    {
        $providers = collect(app(\App\Services\Documents\DocumentSignatureService::class)->allStatuses());
        $requests = \Illuminate\Support\Facades\Schema::hasTable('document_signature_requests')
            ? \App\Models\DocumentSignatureRequest::query()
                ->where('workflow', $workflow)
                ->latest('id')
                ->get()
                ->filter(fn ($item) => (int) data_get($item->metadata, 'document.player_id') === (int) $player->id)
                ->values()
            : collect();

        return [
            'documentSignatureProviders' => $providers,
            'documentSignatureRequests' => $requests,
        ];
    }

    private function audit(Request $request, Player $player, string $purpose, string $action): void
    {
        Log::info('passeport médical consulté', [
            'action' => $action, 'user_id' => $request->user()->id, 'role' => $request->user()->role,
            'player_id' => $player->id, 'purpose' => $purpose, 'ip' => $request->ip(),
        ]);
    }
}
