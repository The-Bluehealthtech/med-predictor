<?php

namespace App\Services\Fhir;

use App\Models\Athlete;
use App\Models\Document;
use App\Models\FhirOrder;
use App\Models\MedicalFile;
use App\Models\Player;
use App\Models\User;
use App\Models\Visit;
use App\Services\Audit\Auditor;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

/**
 * Intégration d'un compte rendu d'établissement (DiagnosticReport du serveur FHIR) au dossier
 * FIT, sur décision du médecin : document joint à la visite de la prescription (à défaut, la
 * dernière visite du joueur). Le PDF fourni par l'établissement (presentedForm) est conservé
 * tel quel ; sinon FIT génère un PDF récapitulatif du compte rendu et de ses résultats, avec
 * leur provenance. Un compte rendu n'est intégré qu'une fois.
 */
final class ReportIntegration
{
    public function __construct(private readonly FhirClient $client, private readonly ClinicalDataQuery $query, private readonly Auditor $auditor)
    {
    }

    /** Identifiants des comptes rendus déjà intégrés pour ce joueur. */
    public function integrated(Player $player): array
    {
        $visits = Visit::query()->whereIn('athlete_id', Athlete::query()->where('player_id', $player->id)->select('id'))->pluck('id');

        return Document::query()->whereIn('visit_id', $visits)->get(['metadata'])
            ->map(fn ($d) => $d->metadata['diagnostic_report_id'] ?? null)->filter()->map(fn ($id) => (string) $id)->values()->all();
    }

    public function integrate(Player $player, string $reportId, User $doctor): Document
    {
        app(\App\Services\Privacy\PlayerConsents::class)->assertExternalSharing($player);
        abort_if(in_array($reportId, $this->integrated($player), true), 409, 'Ce compte rendu est déjà intégré au dossier.');
        $report = $this->client->read('DiagnosticReport', $reportId);
        $subject = Str::after((string) ($report['subject']['reference'] ?? ''), 'Patient/');
        abort_unless(($report['resourceType'] ?? null) === 'DiagnosticReport' && in_array($subject, $this->query->patientIds($player), true), 403, 'Ce compte rendu ne concerne pas ce joueur.');

        $order = FhirOrder::query()->where('player_id', $player->id)->get()->first(fn ($o) => in_array($reportId, $o->report_ids ?? [], true));
        $visit = $order?->visit ?? Visit::query()->whereIn('athlete_id', Athlete::query()->where('player_id', $player->id)->select('id'))->latest('visit_date')->first();
        abort_unless($visit, 422, 'Aucune visite du joueur à laquelle joindre le compte rendu.');

        $label = $report['code']['text'] ?? ($report['code']['coding'][0]['display'] ?? 'Compte rendu');
        $source = $report['meta']['source'] ?? collect($report['performer'] ?? [])->pluck('display')->filter()->first();
        $presented = $this->presentedForm($report);
        [$bytes, $mime, $name] = $presented ?? [$this->summaryPdf($player, $report, $label, $source), 'application/pdf', 'compte-rendu-' . $reportId . '.pdf'];
        $category = collect($report['category'] ?? [])->flatMap(fn ($c) => $c['coding'] ?? [])->firstWhere('system', 'http://terminology.hl7.org/CodeSystem/v2-0074')['code'] ?? null;

        $file = MedicalFile::query()->create([
            'owner_type' => 'visit_document', 'field' => 'fhir_report', 'file_name' => $name, 'mime_type' => $mime, 'size' => strlen($bytes),
            'sha256' => hash('sha256', $bytes), 'content_base64' => base64_encode($bytes), 'uploaded_by' => $doctor->id,
        ]);
        $document = Document::query()->create([
            'visit_id' => $visit->id,
            'document_type' => match ($category) { 'LAB' => 'lab_result', 'RAD' => 'radiology', default => 'medical_report' },
            'file_name' => $name, 'file_path' => $file->ref(), 'file_size' => strlen($bytes), 'mime_type' => $mime,
            'description' => mb_substr($label . ($source ? ' — ' . $source : ''), 0, 2000),
            'uploaded_by' => $doctor->id, 'status' => 'pending',
            'metadata' => ['source' => 'fhir', 'diagnostic_report_id' => $reportId, 'order_id' => $order?->id, 'origin' => $source,
                'issued' => $report['issued'] ?? null, 'integrated_by' => $doctor->id, 'integrated_at' => now()->toIso8601String(),
                'presented_form' => $presented !== null], // false : récapitulatif généré par FIT
        ]);
        $this->auditor->record(['event_type' => 'data_modification', 'module' => 'fhir', 'action' => 'diagnostic_report_integrate',
            'description' => 'Compte rendu d\'établissement intégré au dossier par le médecin', 'model' => $player, 'sensitive' => true,
            'metadata' => ['diagnostic_report' => $reportId, 'document_id' => $document->id, 'visit_id' => $visit->id]]);

        return $document;
    }

    /** PDF (ou image) fourni par l'établissement : contenu inclus, ou Binary du serveur de FIT. @return array{0:string,1:string,2:string}|null */
    private function presentedForm(array $report): ?array
    {
        foreach ($report['presentedForm'] ?? [] as $attachment) {
            $type = (string) ($attachment['contentType'] ?? '');
            if (!in_array($type, ['application/pdf', 'image/jpeg', 'image/png'], true)) {
                continue;
            }
            $bytes = isset($attachment['data']) ? base64_decode((string) $attachment['data'], true) : null;
            if (!$bytes && isset($attachment['url'])) {
                $url = (string) $attachment['url'];
                $base = rtrim((string) config('fhir.base_url'), '/') . '/';
                $url = str_starts_with($url, $base) ? substr($url, strlen($base)) : $url;
                if (preg_match('~^Binary/([A-Za-z0-9\-.]{1,64})$~', $url, $m)) { // serveur de FIT uniquement
                    $binary = $this->client->read('Binary', $m[1]);
                    $bytes = base64_decode((string) ($binary['data'] ?? ''), true) ?: null;
                }
            }
            if ($bytes) {
                $extension = ['application/pdf' => 'pdf', 'image/jpeg' => 'jpg', 'image/png' => 'png'][$type];

                return [$bytes, $type, Str::slug($attachment['title'] ?? 'compte-rendu-etablissement') . '.' . $extension];
            }
        }

        return null;
    }

    /** PDF récapitulatif : compte rendu et résultats (Observation) avec leurs codes et leur provenance. */
    private function summaryPdf(Player $player, array $report, string $label, ?string $source): string
    {
        $results = [];
        foreach (array_slice($report['result'] ?? [], 0, 50) as $reference) {
            if (preg_match('~^Observation/([A-Za-z0-9\-.]{1,64})$~', (string) ($reference['reference'] ?? ''), $m)) {
                try {
                    $observation = $this->client->read('Observation', $m[1]);
                    $results[] = [
                        'label' => $observation['code']['text'] ?? ($observation['code']['coding'][0]['display'] ?? '—'),
                        'codes' => collect($observation['code']['coding'] ?? [])->map(fn ($c) => ($c['system'] ?? '') === 'http://loinc.org' ? 'LOINC ' . $c['code'] : null)->filter()->implode(', '),
                        'value' => isset($observation['valueQuantity']) ? trim(($observation['valueQuantity']['value'] ?? '') . ' ' . ($observation['valueQuantity']['unit'] ?? '')) : ($observation['valueString'] ?? null),
                        'range' => $observation['referenceRange'][0]['text'] ?? (isset($observation['referenceRange'][0]) ? trim(($observation['referenceRange'][0]['low']['value'] ?? '') . ' – ' . ($observation['referenceRange'][0]['high']['value'] ?? '')) : null),
                        'interpretation' => $observation['interpretation'][0]['text'] ?? ($observation['interpretation'][0]['coding'][0]['display'] ?? null),
                    ];
                } catch (FhirException) {
                    $results[] = ['label' => 'Résultat non lisible (' . $reference['reference'] . ')', 'codes' => null, 'value' => null, 'range' => null, 'interpretation' => null];
                }
            }
        }

        return Pdf::loadView('clinical.report-pdf', ['player' => $player, 'report' => $report, 'label' => $label, 'source' => $source, 'results' => $results])
            ->setOption('isRemoteEnabled', false)->output();
    }
}
