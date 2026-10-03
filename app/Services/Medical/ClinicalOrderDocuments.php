<?php

namespace App\Services\Medical;

use App\Models\Document;
use App\Models\MedicalFile;
use App\Models\Player;
use App\Models\User;
use App\Models\Visit;
use App\Services\Audit\Auditor;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Documents des actes prescrits en consultation qui ne relèvent pas d'un module de FIT :
 * courrier d'adressage à un spécialiste et prescription de kinésithérapie. Établis au nom du
 * médecin connecté (identité FIT), joints à la visite, conservés en base. Le contenu clinique
 * vient uniquement de la saisie du médecin.
 */
final class ClinicalOrderDocuments
{
    public const SPECIALTIES = ['cardiologie' => 'Cardiologie', 'orthopedie' => 'Chirurgie orthopédique', 'medecine_sport' => 'Médecine du sport',
        'medecine_physique' => 'Médecine physique et de réadaptation', 'neurologie' => 'Neurologie', 'orl' => 'ORL', 'ophtalmologie' => 'Ophtalmologie',
        'dermatologie' => 'Dermatologie', 'radiologie' => 'Radiologie interventionnelle', 'autre' => 'Autre spécialité'];

    public function __construct(private readonly Auditor $auditor)
    {
    }

    /** @return list<Document> */
    public function generate(Visit $visit, Player $player, User $doctor, array $modules, array $data): array
    {
        $player->loadMissing('club');
        $documents = [];
        if (in_array('specialist', $modules, true) && filled($data['referral_reason'] ?? null)) {
            $specialty = ($data['referral_specialty'] ?? 'autre') === 'autre' && filled($data['referral_specialty_other'] ?? null)
                ? $data['referral_specialty_other'] : (self::SPECIALTIES[$data['referral_specialty'] ?? 'autre'] ?? 'Autre spécialité');
            $documents[] = $this->store($visit, $player, $doctor, 'referral', 'courrier-adressage', 'Courrier d’adressage — ' . $specialty,
                'medical-documents.referral-pdf', ['specialty' => $specialty, 'reason' => $data['referral_reason'], 'urgent' => ($data['referral_urgency'] ?? 'routine') === 'urgent'], 'specialist');
        }
        if (in_array('physiotherapy', $modules, true) && filled($data['physio_indication'] ?? null)) {
            $documents[] = $this->store($visit, $player, $doctor, 'prescription', 'prescription-kinesitherapie', 'Prescription de kinésithérapie',
                'medical-documents.physio-pdf', ['indication' => $data['physio_indication'], 'sessions' => $data['physio_sessions'] ?? null,
                    'frequency' => $data['physio_frequency'] ?? null, 'instructions' => $data['physio_instructions'] ?? null], 'physiotherapy');
        }

        return $documents;
    }

    private function store(Visit $visit, Player $player, User $doctor, string $type, string $slug, string $title, string $view, array $content, string $module): Document
    {
        $bytes = Pdf::loadView($view, ['player' => $player, 'doctor' => $doctor, 'visit' => $visit, 'title' => $title, 'issuedAt' => now()] + $content)
            ->setPaper('a4')->setOption('isRemoteEnabled', false)->output();
        $name = $slug . '-' . $visit->id . '.pdf';
        $file = MedicalFile::query()->create([
            'owner_type' => 'visit_document', 'field' => $type, 'file_name' => $name, 'mime_type' => 'application/pdf', 'size' => strlen($bytes),
            'sha256' => hash('sha256', $bytes), 'content_base64' => base64_encode($bytes), 'uploaded_by' => $doctor->id,
        ]);
        $document = Document::query()->create([
            'visit_id' => $visit->id, 'document_type' => $type, 'file_name' => $name, 'file_path' => $file->ref(), 'file_size' => strlen($bytes),
            'mime_type' => 'application/pdf', 'description' => $title, 'uploaded_by' => $doctor->id, 'status' => 'analyzed',
            'metadata' => ['source' => 'fit_prescription', 'module' => $module, 'issued_by' => $doctor->id, 'issued_at' => now()->toIso8601String(), 'sha256' => $file->sha256],
        ]);
        $this->auditor->record(['event_type' => 'data_modification', 'module' => 'medical', 'action' => 'clinical_order_document',
            'description' => $title . ' établi(e) en consultation', 'model' => $player, 'sensitive' => true,
            'metadata' => ['document_id' => $document->id, 'visit_id' => $visit->id, 'module' => $module]]);

        return $document;
    }
}
