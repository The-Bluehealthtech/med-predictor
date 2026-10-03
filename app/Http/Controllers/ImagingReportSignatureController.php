<?php

namespace App\Http\Controllers;

use App\Models\DocumentSignatureRequest;
use App\Models\HealthRecord;
use App\Models\ImagingReport;
use App\Models\ImagingStudy;
use App\Services\Documents\DocumentSignatureService;
use App\Services\MedicalRecordAccess;
use Illuminate\Http\Request;

final class ImagingReportSignatureController extends Controller
{
    public function store(
        Request $request,
        HealthRecord $healthRecord,
        ImagingStudy $study,
        ImagingReport $report,
        DocumentSignatureService $signatures
    ) {
        $this->authorizeReport($request, $healthRecord, $study, $report, true);
        $data = $request->validate([
            'provider' => 'required|in:signotec_document,adobe_sign,globalsign_dss',
        ]);

        $study->loadMissing('player');
        $report->loadMissing('validator');
        $bytes = app('dompdf.wrapper')
            ->loadView('health-records.imaging.report-pdf', compact('study', 'report'))
            ->output();

        $versionSha256 = hash('sha256', json_encode([
            'study_id' => $study->id,
            'report_id' => $report->id,
            'version' => $report->version,
            'validated_at' => optional($report->validated_at)->toIso8601String(),
            'validated_by' => $report->validated_by,
            'patient_snapshot' => $report->patient_snapshot,
            'technique' => $report->technique,
            'quality' => $report->quality,
            'findings' => $report->findings,
            'conclusion' => $report->conclusion,
            'reference_images' => $report->reference_images,
            'age_review' => $report->age_review,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $reference = 'IMAGING-'.$report->id.'-V'.$report->version.'-'.substr($versionSha256, 0, 16);

        if (DocumentSignatureRequest::query()
            ->where('workflow', 'imaging_report.final_document')
            ->where('document_reference', $reference)
            ->whereIn('status', ['pending', 'sent', 'signed'])
            ->exists()) {
            return back()->with('success', 'Une demande de signature existe déjà pour cette version du compte rendu.');
        }

        try {
            $signature = $signatures->createRequest($data['provider'], [
                'type' => 'imaging_report_pdf',
                'reference' => $reference,
                'sha256' => hash('sha256', $bytes),
                'version_sha256' => $versionSha256,
                'health_record_id' => $healthRecord->id,
                'study_id' => $study->id,
                'report_id' => $report->id,
                'version' => $report->version,
                'filename' => 'FIT-imaging-'.$study->id.'-v'.$report->version.'.pdf',
                'name' => 'Compte rendu imagerie '.$study->id.' v'.$report->version,
                'bytes' => $bytes,
            ], [
                'type' => 'user',
                'id' => $request->user()->id,
                'role' => $request->user()->role,
                'name' => $request->user()->name,
                'email' => $request->user()->email,
            ], 'imaging_report.final_document');
        } catch (\InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with($signature->status === 'error' ? 'error' : 'success',
            $signature->status === 'sent'
                ? 'Compte rendu envoyé au fournisseur de signature.'
                : 'Demande de signature numérique créée pour cette version du compte rendu.');
    }

    public function sync(
        Request $request,
        HealthRecord $healthRecord,
        ImagingStudy $study,
        ImagingReport $report,
        DocumentSignatureRequest $signature,
        DocumentSignatureService $signatures
    ) {
        $this->authorizeReport($request, $healthRecord, $study, $report, true);
        $this->authorizeSignature($report, $signature);
        $updated = $signatures->sync($signature);

        return back()->with('success', 'Statut de signature synchronisé : '.ucfirst($updated->status).'.');
    }

    public function download(
        Request $request,
        HealthRecord $healthRecord,
        ImagingStudy $study,
        ImagingReport $report,
        DocumentSignatureRequest $signature
    ) {
        $this->authorizeReport($request, $healthRecord, $study, $report, false);
        $this->authorizeSignature($report, $signature);
        return app(\App\Services\Documents\DocumentSignatureStorage::class)
            ->download($signature, 'FIT-imaging-'.$study->id.'-v'.$report->version.'-signed.pdf');
    }

    private function authorizeReport(
        Request $request,
        HealthRecord $record,
        ImagingStudy $study,
        ImagingReport $report,
        bool $manage
    ): void {
        app(MedicalRecordAccess::class)->authorize($request->user(), $record->player, null);
        abort_unless((int) $study->player_id === (int) $record->player_id, 404);
        abort_unless((int) $report->study_id === (int) $study->id, 404);
        abort_unless($report->status === 'validated', 409);

        if ($manage) {
            abort_unless($request->user()->hasAnyRole(['doctor', 'team_doctor', 'club_medical', 'association_medical']), 403);
        }
    }

    private function authorizeSignature(ImagingReport $report, DocumentSignatureRequest $signature): void
    {
        abort_unless(
            $signature->workflow === 'imaging_report.final_document'
            && (int) data_get($signature->metadata, 'document.report_id') === (int) $report->id,
            404
        );
    }
}
