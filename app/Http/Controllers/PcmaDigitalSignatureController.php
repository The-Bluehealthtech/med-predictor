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
            $signatures->createRequest($data['provider'], [
                'type' => 'pcma_pdf',
                'reference' => $reference,
                'sha256' => $sha256,
                'pcma_id' => $pcma->id,
                'version_updated_at' => optional($pcma->updated_at)->toIso8601String(),
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

        return back()->with('success', 'Demande de signature numérique créée pour cette version figée du PCMA.');
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
