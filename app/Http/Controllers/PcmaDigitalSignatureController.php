<?php

namespace App\Http\Controllers;

use App\Models\DocumentSignatureRequest;
use App\Models\PCMA;
use App\Services\Documents\DocumentSignatureService;
use App\Services\MedicalRecordAccess;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class PcmaDigitalSignatureController extends Controller
{
    public function store(Request $request, PCMA $pcma, DocumentSignatureService $signatures): RedirectResponse
    {
        app(MedicalRecordAccess::class)->record($request->user(), $pcma);
        abort_unless($pcma->is_signed, 409, 'Le PCMA doit être signé médicalement avant la certification numérique du document.');
        abort_unless((int) $pcma->assessor_id === (int) $request->user()->id || $request->user()->isSystemAdmin(), 403);

        $data = $request->validate(['provider' => 'required|string|in:signotec_document,adobe_sign,globalsign_dss']);
        $pcma->loadMissing(['player', 'athlete', 'assessor']);
        $pdfBytes = Pdf::loadView('pcma.pdf', [
            'pcma' => $pcma,
            'formData' => $this->formData($pcma),
            'isDraft' => false,
            'generatedAt' => $pcma->updated_at,
        ])->setOption('isRemoteEnabled', false)->output();
        $sha256 = hash('sha256', $pdfBytes);
        $reference = 'PCMA-'.$pcma->id.'-'.substr($sha256, 0, 16);

        $existing = DocumentSignatureRequest::query()
            ->where('workflow', 'pcma.final_document')
            ->where('document_reference', $reference)
            ->where('signer_id', $request->user()->id)
            ->whereIn('status', ['pending', 'sent', 'signed'])
            ->latest('id')->first();
        if ($existing) {
            return back()->with('info', 'Une demande de signature numérique existe déjà pour cette version du PCMA.');
        }

        try {
            $signatureRequest = $signatures->createRequest($data['provider'], [
                'type' => 'pcma_pdf',
                'reference' => $reference,
                'sha256' => $sha256,
                'pcma_id' => $pcma->id,
                'version_updated_at' => optional($pcma->updated_at)->toIso8601String(),
                'filename' => 'PCMA-'.$pcma->id.'.pdf',
                'name' => 'PCMA '.$pcma->id.' - '.($pcma->player?->name ?? $pcma->athlete?->name ?? 'Joueur'),
                'bytes' => $pdfBytes,
            ], [
                'type' => 'user',
                'id' => $request->user()->id,
                'role' => 'doctor',
                'name' => $request->user()->name,
                'email' => $request->user()->email,
            ], 'pcma.final_document');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        if ($signatureRequest->status === 'error') {
            return back()->with('error', 'La demande a été créée mais le fournisseur n’a pas accepté l’envoi. Vérifiez Configuration des API.');
        }

        return back()->with('success', $signatureRequest->status === 'sent'
            ? 'Document envoyé au fournisseur de signature.'
            : 'Demande de signature numérique créée pour cette version figée du PCMA.');
    }

    public function sync(Request $request, PCMA $pcma, DocumentSignatureRequest $signature, DocumentSignatureService $signatures): RedirectResponse
    {
        app(MedicalRecordAccess::class)->record($request->user(), $pcma);
        $this->authorizeSignature($pcma, $signature);
        abort_unless((int) $pcma->assessor_id === (int) $request->user()->id || $request->user()->isSystemAdmin(), 403);

        $updated = $signatures->sync($signature);

        return back()->with('success', 'Statut de signature synchronisé : '.ucfirst($updated->status).'.');
    }

    public function download(Request $request, PCMA $pcma, DocumentSignatureRequest $signature)
    {
        app(MedicalRecordAccess::class)->record($request->user(), $pcma);
        $this->authorizeSignature($pcma, $signature);
        $path = data_get($signature->metadata, 'signed_path');
        abort_unless(is_string($path) && $path && !str_contains($path, '..'), 404);
        abort_unless(\Illuminate\Support\Facades\Storage::disk('local')->exists($path), 404);

        return \Illuminate\Support\Facades\Storage::disk('local')->download($path, 'PCMA-'.$pcma->id.'-signed.pdf');
    }

    private function authorizeSignature(PCMA $pcma, DocumentSignatureRequest $signature): void
    {
        abort_unless($signature->workflow === 'pcma.final_document'
            && (int) data_get($signature->metadata, 'document.pcma_id') === (int) $pcma->id, 404);
    }

    private function formData(PCMA $pcma): array
    {
        $data = is_array($pcma->result_json) ? $pcma->result_json : [];
        unset($data['draft_token'], $data['signature_data']);
        foreach (['type', 'status', 'notes', 'medical_history', 'physical_examination',
            'cardiovascular_investigations', 'final_statement', 'scat_assessment', 'anatomical_annotations'] as $field) {
            if ($pcma->$field !== null) $data[$field] = $pcma->$field;
        }
        return $data;
    }
}
