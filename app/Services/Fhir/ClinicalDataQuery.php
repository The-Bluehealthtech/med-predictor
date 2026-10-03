<?php

namespace App\Services\Fhir;

use App\Models\FhirPatientLink;
use App\Models\Player;
use App\Services\Audit\Auditor;
use Illuminate\Support\Carbon;

/**
 * Données cliniques des établissements (EMR, LIS, RIS, PACS) lues sur le serveur FHIR de FIT
 * pour le Patient de FIT et les Patients rattachés au joueur.
 *  - IHE QEDm, Clinical Data Consumer (PCC-44) : Observation (patient + category), Condition,
 *    AllergyIntolerance, MedicationStatement, Immunization, DiagnosticReport (patient + category).
 *  - Imagerie : ImagingStudy (FHIR R4) ; images ouvertes dans la visionneuse du PACS par
 *    IHE RAD Invoke Image Display (IID) quand elle est configurée.
 * Lecture seule : rien n'est intégré au dossier FIT sans décision médicale.
 */
final class ClinicalDataQuery
{
    private const V2_0074 = 'http://terminology.hl7.org/CodeSystem/v2-0074';

    private const OBSERVATION_CATEGORY = 'http://terminology.hl7.org/CodeSystem/observation-category';

    /** Onglet => [libellé, ressource, critères de recherche QEDm]. */
    public const CATEGORIES = [
        'lab' => ['Résultats de laboratoire', 'Observation', ['category' => self::OBSERVATION_CATEGORY . '|laboratory']],
        'reports' => ['Comptes rendus', 'DiagnosticReport', []],
        'imaging' => ['Imagerie', 'ImagingStudy', []],
        'vitals' => ['Constantes', 'Observation', ['category' => self::OBSERVATION_CATEGORY . '|vital-signs']],
        'problems' => ['Problèmes', 'Condition', []],
        'allergies' => ['Allergies', 'AllergyIntolerance', []],
        'medications' => ['Traitements', 'MedicationStatement', []],
        'immunizations' => ['Vaccinations', 'Immunization', []],
    ];

    public function __construct(private readonly FhirClient $client, private readonly Auditor $auditor)
    {
    }

    /** Patient de FIT et Patients des sources rattachés. */
    public function patientIds(Player $player): array
    {
        return FhirPatientLink::query()->where('player_id', $player->id)->where('status', 'linked')->whereNotNull('patient_id')
            ->orderByDesc('role')->orderBy('id')->pluck('patient_id')->map(fn ($id) => (string) $id)->unique()->values()->all();
    }

    /** @return list<array{id:string, date:?string, label:string, value:?string, detail:?string, status:?string, codes:list<string>, source:?string, patient:string, viewer:?string}> */
    public function fetch(Player $player, string $category): array
    {
        abort_unless(isset(self::CATEGORIES[$category]), 404);
        app(\App\Services\Privacy\PlayerConsents::class)->assertExternalSharing($player);
        $patients = $this->patientIds($player);
        if ($patients === []) {
            return [];
        }
        [, $type, $criteria] = self::CATEGORIES[$category];
        $bundle = $this->client->search($type, $criteria + [
            'patient' => implode(',', array_map(fn ($id) => 'Patient/' . $id, $patients)),
            '_count' => 100,
        ]);
        $this->auditor->record(['event_type' => 'data_access', 'module' => 'fhir', 'action' => 'clinical_data_query',
            'description' => 'Données cliniques des établissements consultées (IHE QEDm)', 'model' => $player, 'sensitive' => true,
            'metadata' => ['category' => $category, 'patients' => count($patients)]]);

        return collect($bundle['entry'] ?? [])->map(fn ($e) => $e['resource'] ?? [])
            ->filter(fn ($r) => ($r['resourceType'] ?? null) === $type)
            ->map(fn ($r) => $this->item($r))
            ->sortByDesc(fn ($i) => $i['date'] ?? '')->values()->all();
    }

    private function item(array $r): array
    {
        $type = $r['resourceType'];
        $concept = match ($type) {
            'MedicationStatement' => $r['medicationCodeableConcept'] ?? null,
            'Immunization' => $r['vaccineCode'] ?? null,
            'ImagingStudy' => null,
            default => $r['code'] ?? null,
        };
        $date = $r['effectiveDateTime'] ?? $r['effectivePeriod']['start'] ?? $r['issued'] ?? $r['started'] ?? $r['recordedDate']
            ?? $r['onsetDateTime'] ?? $r['occurrenceDateTime'] ?? $r['dateAsserted'] ?? null;

        return [
            'id' => (string) ($r['id'] ?? ''),
            'date' => $date,
            'label' => $type === 'ImagingStudy' ? $this->imagingLabel($r) : $this->text($concept),
            'value' => $this->value($r),
            'detail' => $this->detail($r),
            'status' => $r['status'] ?? ($r['clinicalStatus']['coding'][0]['code'] ?? null),
            'codes' => $this->codes($concept),
            'source' => $r['meta']['source'] ?? collect($r['performer'] ?? [])->map(fn ($p) => $p['display'] ?? ($p['actor']['display'] ?? null))->filter()->first(),
            'patient' => (string) ($r['subject']['reference'] ?? $r['patient']['reference'] ?? ''),
            'viewer' => $type === 'ImagingStudy' ? $this->viewerUrl($r) : null,
            'study_uid' => $type === 'ImagingStudy' ? $this->studyUid($r) : null,
        ];
    }

    private function value(array $r): ?string
    {
        if (isset($r['valueQuantity'])) {
            $q = $r['valueQuantity'];

            return trim(($q['comparator'] ?? '') . ($q['value'] ?? '') . ' ' . ($q['unit'] ?? $q['code'] ?? ''));
        }
        if (isset($r['valueCodeableConcept'])) {
            return $this->text($r['valueCodeableConcept']);
        }
        foreach (['valueString', 'valueBoolean', 'valueInteger', 'valueDateTime'] as $key) {
            if (array_key_exists($key, $r)) {
                return is_bool($r[$key]) ? ($r[$key] ? 'oui' : 'non') : (string) $r[$key];
            }
        }
        if (!empty($r['component'])) {
            return collect($r['component'])->map(fn ($c) => $this->text($c['code'] ?? []) . ' ' . ($this->value($c) ?? ''))->implode(' · ');
        }

        return null;
    }

    private function detail(array $r): ?string
    {
        $parts = [];
        if (!empty($r['referenceRange'][0])) {
            $range = $r['referenceRange'][0];
            $parts[] = 'Référence : ' . ($range['text'] ?? trim(($range['low']['value'] ?? '') . ' – ' . ($range['high']['value'] ?? '') . ' ' . ($range['high']['unit'] ?? $range['low']['unit'] ?? '')));
        }
        if (!empty($r['interpretation'][0])) {
            $parts[] = 'Interprétation : ' . $this->text($r['interpretation'][0]);
        }
        if (!empty($r['conclusion'])) {
            $parts[] = 'Conclusion : ' . $r['conclusion'];
        }
        if (!empty($r['category'])) {
            $categories = collect($r['category'])->map(fn ($c) => collect($c['coding'] ?? [])->firstWhere('system', self::V2_0074)['code'] ?? null)->filter()->implode(', ');
            if ($categories !== '' && ($r['resourceType'] ?? null) === 'DiagnosticReport') {
                $parts[] = 'Catégorie : ' . $categories;
            }
        }
        if (!empty($r['presentedForm'])) {
            $parts[] = count($r['presentedForm']) . ' document(s) joint(s) au compte rendu';
        }
        if (($r['resourceType'] ?? null) === 'ImagingStudy') {
            $parts[] = ($r['numberOfSeries'] ?? '?') . ' série(s), ' . ($r['numberOfInstances'] ?? '?') . ' image(s)';
        }
        foreach ($r['note'] ?? [] as $note) {
            if (!empty($note['text'])) {
                $parts[] = $note['text'];
            }
        }

        return $parts ? implode(' · ', $parts) : null;
    }

    private function imagingLabel(array $r): string
    {
        $modalities = collect($r['modality'] ?? [])->pluck('code')->merge(collect($r['series'] ?? [])->pluck('modality.code'))->filter()->unique()->implode(', ');
        $label = config('medical_imaging.modalities')[$modalities] ?? $modalities;

        return trim(($r['description'] ?? 'Examen d\'imagerie') . ($label ? ' (' . $label . ')' : ''));
    }

    /** UID d'étude DICOM de l'ImagingStudy (identifier urn:dicom:uid, valeur urn:oid:…). */
    private function studyUid(array $r): ?string
    {
        $uid = collect($r['identifier'] ?? [])->firstWhere('system', 'urn:dicom:uid')['value'] ?? null;
        $uid = $uid ? preg_replace('/^urn:oid:/', '', (string) $uid) : null;

        return $uid && preg_match('/^[0-9]+(\.[0-9]+){1,63}$/', $uid) && strlen($uid) <= 64 ? $uid : null;
    }

    /** IHE RAD IID : requestType=STUDY & studyUID, vers la visionneuse configurée (FIT_IID_VIEWER_URL). */
    private function viewerUrl(array $r): ?string
    {
        $viewer = (string) config('fhir.imaging.iid_viewer_url');
        $uid = $this->studyUid($r);
        if ($viewer === '' || !$uid || !str_starts_with($viewer, 'https://')) {
            return null;
        }

        return rtrim($viewer, '/') . '/IHEInvokeImageDisplay?' . http_build_query(['requestType' => 'STUDY', 'studyUID' => $uid]);
    }

    private function text(?array $concept): string
    {
        if (!$concept) {
            return '—';
        }

        return (string) ($concept['text'] ?? ($concept['coding'][0]['display'] ?? ($concept['coding'][0]['code'] ?? '—')));
    }

    private function codes(?array $concept): array
    {
        return collect($concept['coding'] ?? [])->map(fn ($c) => isset($c['code']) ? $this->systemName($c['system'] ?? '') . ' ' . $c['code'] : null)->filter()->values()->all();
    }

    private function systemName(string $system): string
    {
        return match ($system) {
            'http://loinc.org' => 'LOINC',
            'http://id.who.int/icd/release/11/mms' => 'CIM-11',
            'http://www.nlm.nih.gov/research/umls/rxnorm' => 'RxNorm',
            'http://hl7.org/fhir/sid/cvx' => 'CVX',
            'http://snomed.info/sct' => 'SNOMED CT',
            'http://unitsofmeasure.org' => 'UCUM',
            default => $system,
        };
    }

    /** Date affichable (jour, ou jour et heure si fournie). */
    public static function displayDate(?string $value): string
    {
        if (!$value) {
            return '—';
        }

        return strlen($value) > 10 ? Carbon::parse($value)->format('d/m/Y H:i') : Carbon::parse($value)->format('d/m/Y');
    }
}
