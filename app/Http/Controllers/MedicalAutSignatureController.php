<?php

namespace App\Http\Controllers;

use App\Models\DocumentSignatureRequest;
use App\Models\HealthRecord;
use App\Models\TUERequest;
use App\Services\Documents\DocumentSignatureService;
use App\Services\MedicalRecordAccess;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

final class MedicalAutSignatureController extends Controller
{
    public function store(Request $request, HealthRecord $record, TUERequest $aut, DocumentSignatureService $signatures)
    {
        $this->authorizeAut($request, $record, $aut, true);
        $provider = $request->validate([
            'provider' => 'required|in:signotec_document,adobe_sign,globalsign_dss',
        ])['provider'];

        $fields = $aut->aut_form_data['fields'] ?? [];
        $bytes = Pdf::loadView('health-records.aut-pdf', [
            'healthRecord' => $record,
            'item' => $aut,
            'fields' => $fields,
            'sections' => config('medical_aut.sections'),
            'source' => json_decode(file_get_contents(config('medical_aut.source_directory').'/fifa-aut-fr-2024-text.json'), true),
            'preview' => false,
            'generatedAt' => $aut->updated_at ?: $aut->created_at,
        ])->setPaper('a4')->setOption('isRemoteEnabled', false)->output();

        $versionSha256 = hash('sha256', json_encode([
            'aut_id' => $aut->id,
            'player_id' => $aut->player_id,
            'health_record_id' => $aut->health_record_id,
            'form' => $aut->aut_form_data,
            'supporting_documents' => $aut->supporting_documents,
            'source_sha256' => config('medical_aut.source_sha256'),
            'form_version' => config('medical_aut.form_version'),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $reference = 'AUT-'.$aut->id.'-'.substr($versionSha256, 0, 16);

        if (DocumentSignatureRequest::query()->where('workflow', 'medical_aut.submission_document')
            ->where('document_reference', $reference)
            ->whereIn('status', ['pending', 'sent', 'signed'])->exists()) {
            return back()->with('success', 'Une demande de signature existe déjà pour cette version de l’AUT.');
        }

        try {
            $signature = $signatures->createRequest($provider, [
                'type' => 'medical_aut_pdf',
                'reference' => $reference,
                'sha256' => hash('sha256', $bytes),
                'version_sha256' => $versionSha256,
                'health_record_id' => $record->id,
                'aut_id' => $aut->id,
                'player_id' => $aut->player_id,
                'filename' => 'AUT-FIFA-'.$aut->id.'.pdf',
                'name' => 'AUT FIFA #'.$aut->id,
                'bytes' => $bytes,
            ], [
                'type' => 'user',
                'id' => $request->user()->id,
                'role' => $request->user()->role,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
            ], 'medical_aut.submission_document');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with($signature->status === 'error' ? 'error' : 'success',
            $signature->status === 'sent'
                ? 'AUT envoyée au fournisseur de signature.'
                : 'Demande de signature numérique créée pour cette version figée de l’AUT.');
    }

    public function sync(Request $request, HealthRecord $record, TUERequest $aut, DocumentSignatureRequest $signature, DocumentSignatureService $signatures)
    {
        $this->authorizeAut($request, $record, $aut, true);
        $this->authorizeSignature($aut, $signature);
        $updated = $signatures->sync($signature);

        return back()->with('success', 'Statut de signature synchronisé : '.ucfirst($updated->status).'.');
    }

    public function download(Request $request, HealthRecord $record, TUERequest $aut, DocumentSignatureRequest $signature)
    {
        $this->authorizeAut($request, $record, $aut, false);
        $this->authorizeSignature($aut, $signature);
        $path = data_get($signature->metadata, 'signed_path');
        abort_unless(is_string($path) && $path !== '' && !str_contains($path, '..'), 404);
        abort_unless(Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->download($path, 'AUT-FIFA-'.$aut->id.'-signed.pdf');
    }

    private function authorizeAut(Request $request, HealthRecord $record, TUERequest $aut, bool $manage): void
    {
        app(MedicalRecordAccess::class)->authorize($request->user(), $record->player, null);
        abort_unless((int) $aut->health_record_id === (int) $record->id && (int) $aut->player_id === (int) $record->player_id, 404);

        if ($manage) {
            abort_unless((int) $aut->physician_id === (int) $request->user()->id, 403);
            abort_unless($request->user()->hasAnyRole(['doctor', 'team_doctor', 'club_medical', 'association_medical']), 403);
        }
    }

    private function authorizeSignature(TUERequest $aut, DocumentSignatureRequest $signature): void
    {
        abort_unless(
            $signature->workflow === 'medical_aut.submission_document'
            && (int) data_get($signature->metadata, 'document.aut_id') === (int) $aut->id,
            404
        );
    }
}
