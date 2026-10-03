<?php

namespace App\Http\Controllers\ClubOfficials;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\ClubOfficial;
use App\Models\DocumentSignatureRequest;
use App\Services\ClubOfficials\ClubOfficials;
use App\Services\Documents\DocumentSignatureService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

final class ClubOfficialSignatureController extends Controller
{
    public function __construct(private readonly ClubOfficials $officials)
    {
    }

    public function store(
        Request $request,
        Club $club,
        ClubOfficial $official,
        DocumentSignatureService $signatures
    ) {
        $this->authorizeOfficial($request, $club, $official, true);
        $provider = $request->validate([
            'provider' => 'required|in:signotec_document,adobe_sign,globalsign_dss',
        ])['provider'];

        $bytes = Pdf::loadView('club-officials.pdf', [
            'club' => $club,
            'official' => $official,
        ])->setPaper('a4')->setOption('isRemoteEnabled', false)->output();

        $versionSha256 = hash('sha256', json_encode($this->snapshot($club, $official),
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $reference = 'CLUB-OFFICIAL-'.$official->id.'-'.substr($versionSha256, 0, 16);

        if (DocumentSignatureRequest::query()
            ->where('workflow', 'club_official.profile_document')
            ->where('document_reference', $reference)
            ->where('signer_id', $request->user()->id)
            ->whereIn('status', ['pending', 'sent', 'signed'])
            ->exists()) {
            return back()->with('status', 'Vous avez déjà une demande de signature pour cette version de la fiche.');
        }

        try {
            $signature = $signatures->createRequest($provider, [
                'type' => 'club_official_pdf',
                'reference' => $reference,
                'sha256' => hash('sha256', $bytes),
                'version_sha256' => $versionSha256,
                'club_id' => $club->id,
                'official_id' => $official->id,
                'filename' => 'fiche-fifa-connect-'.$official->id.'.pdf',
                'name' => 'Fiche dirigeant/staff '.$official->fullName(),
                'bytes' => $bytes,
            ], [
                'type' => 'user',
                'id' => $request->user()->id,
                'role' => $request->user()->role,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
            ], 'club_official.profile_document');
        } catch (\InvalidArgumentException $e) {
            return back()->with('signature_error', $e->getMessage());
        }

        return back()->with($signature->status === 'error' ? 'signature_error' : 'status',
            $signature->status === 'sent'
                ? 'Fiche envoyée au fournisseur de signature.'
                : 'Demande de signature numérique créée pour cette version de la fiche.');
    }

    public function sync(
        Request $request,
        Club $club,
        ClubOfficial $official,
        DocumentSignatureRequest $signature,
        DocumentSignatureService $signatures
    ) {
        $this->authorizeOfficial($request, $club, $official, true);
        $this->authorizeSignature($official, $signature);
        $updated = $signatures->sync($signature);

        return back()->with('status', 'Statut de signature synchronisé : '.ucfirst($updated->status).'.');
    }

    public function download(
        Request $request,
        Club $club,
        ClubOfficial $official,
        DocumentSignatureRequest $signature
    ) {
        $this->authorizeOfficial($request, $club, $official, false);
        $this->authorizeSignature($official, $signature);
        $path = data_get($signature->metadata, 'signed_path');
        abort_unless(is_string($path) && $path !== '' && !str_contains($path, '..'), 404);
        abort_unless(Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->download($path, 'fiche-dirigeant-staff-'.$official->id.'-signee.pdf');
    }

    private function authorizeOfficial(Request $request, Club $club, ClubOfficial $official, bool $manage): void
    {
        abort_unless((int) $official->club_id === (int) $club->id, 404);
        abort_unless($this->officials->canView($request->user(), $club), 403);

        if ($manage) {
            abort_unless(in_array($request->user()->role, ['club_admin', 'association_admin'], true), 403);
            abort_unless($this->officials->canManage($request->user(), $club), 403);
        }
    }

    private function authorizeSignature(ClubOfficial $official, DocumentSignatureRequest $signature): void
    {
        abort_unless(
            $signature->workflow === 'club_official.profile_document'
            && (int) data_get($signature->metadata, 'document.official_id') === (int) $official->id,
            404
        );
    }

    private function snapshot(Club $club, ClubOfficial $official): array
    {
        return [
            'club_id' => $club->id,
            'club_name' => $club->name,
            'organisation_fifa_id' => $club->fifa_connect_id,
            'official_id' => $official->id,
            'person_fifa_id' => $official->person_fifa_id,
            'registration_type' => $official->registration_type,
            'role' => $official->roleCode(),
            'role_description' => $official->role_description,
            'is_head_coach' => $official->is_head_coach,

            'international_first_name' => $official->international_first_name,
            'international_last_name' => $official->international_last_name,
            'local_first_name' => $official->local_first_name,
            'local_last_name' => $official->local_last_name,
            'popular_name' => $official->popular_name,
            'gender' => $official->gender,
            'date_of_birth' => optional($official->date_of_birth)->format('Y-m-d'),
            'nationality' => $official->nationality,
            'second_nationality' => $official->second_nationality,
            'country_of_birth' => $official->country_of_birth,
            'place_of_birth' => $official->place_of_birth,
            'status' => $official->status,
            'discipline' => $official->discipline,
            'registration_valid_from' => optional($official->registration_valid_from)->format('Y-m-d'),
            'registration_valid_to' => optional($official->registration_valid_to)->format('Y-m-d'),
            'certification_type' => $official->certification_type,
            'certification_name' => $official->certification_name,
            'certification_number' => $official->certification_number,
            'certification_valid_from' => optional($official->certification_valid_from)->format('Y-m-d'),
            'certification_valid_to' => optional($official->certification_valid_to)->format('Y-m-d'),
        ];
    }
}
