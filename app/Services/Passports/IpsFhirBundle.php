<?php

namespace App\Services\Passports;

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Passeport médical exporté en HL7 FHIR R4 : Bundle de type « document »
 * structuré selon l'International Patient Summary (profils IPS uv).
 * Une section obligatoire vide reçoit l'entrée « aucune information » du
 * CodeSystem absent-unknown-uv-ips ; aucune donnée n'est déduite.
 */
final class IpsFhirBundle
{
    private const IPS = 'http://hl7.org/fhir/uv/ips/StructureDefinition/';
    private const LOINC = 'http://loinc.org';
    private const ABSENT = 'http://hl7.org/fhir/uv/ips/CodeSystem/absent-unknown-uv-ips';
    private const ICD11 = 'http://id.who.int/icd/release/11/mms';
    private const RXNORM = 'http://www.nlm.nih.gov/research/umls/rxnorm';

    /** Sections IPS exportées : clé du résumé => [code LOINC, titre]. */
    private const SECTIONS = [
        'allergies' => ['48765-2', 'Allergies and adverse reactions'],
        'problems' => ['11450-4', 'Problem list'],
        'medications' => ['10160-0', 'Medication summary'],
        'immunizations' => ['11369-6', 'Immunizations'],
        'results' => ['30954-2', 'Results'],
        'history' => ['11348-0', 'History of past illness'],
        'vital_signs' => ['8716-3', 'Vital signs'],
        'sport' => ['47420-5', 'Functional status (sport fitness, TUE, anti-doping)'],
    ];

    public function build(array $summary, array $attestation): array
    {
        $patientRef = 'urn:uuid:' . Str::uuid();
        $authorRef = 'urn:uuid:' . Str::uuid();
        $entries = [];
        $sections = [];

        foreach (self::SECTIONS as $key => [$loinc, $title]) {
            $items = $summary['sections'][$key] ?? [];
            $refs = [];
            foreach ($items as $item) {
                [$ref, $resource] = $this->resource($key, $item, $patientRef);
                $entries[] = ['fullUrl' => $ref, 'resource' => $resource];
                $refs[] = ['reference' => $ref];
            }
            if ($refs === [] && in_array($key, ['allergies', 'problems', 'medications'], true)) {
                [$ref, $resource] = $this->noInformation($key, $patientRef);
                $entries[] = ['fullUrl' => $ref, 'resource' => $resource];
                $refs[] = ['reference' => $ref];
            }
            if ($refs === []) {
                continue; // section recommandée ou optionnelle sans donnée : omise
            }
            $sections[] = [
                'title' => $title,
                'code' => ['coding' => [['system' => self::LOINC, 'code' => $loinc]]],
                'text' => ['status' => 'generated', 'div' => '<div xmlns="http://www.w3.org/1999/xhtml">' . e($title) . ' — ' . count($items) . ' élément(s) enregistré(s) dans FIT</div>'],
                'entry' => $refs,
            ];
        }

        $valid = ($attestation['state'] ?? 'none') === 'valid';
        $composition = [
            'resourceType' => 'Composition',
            'meta' => ['profile' => [self::IPS . 'Composition-uv-ips']],
            'status' => $valid ? 'final' : 'preliminary',
            'type' => ['coding' => [['system' => self::LOINC, 'code' => '60591-5', 'display' => 'Patient summary Document']]],
            'subject' => ['reference' => $patientRef],
            'date' => $summary['document']['generated_at']->toIso8601String(),
            'author' => [['reference' => $authorRef]],
            'title' => 'International Patient Summary — ' . $summary['document']['purpose_label'],
            'confidentiality' => 'R',
            'section' => $sections,
        ];
        if ($valid) {
            $a = $attestation['attestation'];
            $composition['attester'] = [['mode' => 'legal', 'time' => $a->signed_at->toIso8601String(), 'party' => ['display' => $a->signer_name]]];
        }

        $bundleEntries = array_merge([
            ['fullUrl' => 'urn:uuid:' . Str::uuid(), 'resource' => $composition],
            ['fullUrl' => $patientRef, 'resource' => $this->patient($summary['patient'])],
            ['fullUrl' => $authorRef, 'resource' => ['resourceType' => 'Organization', 'name' => $summary['document']['custodian'] ?: 'FIT — Football Intelligence & Tracking']],
        ], $entries);

        return [
            'resourceType' => 'Bundle',
            'meta' => ['profile' => [self::IPS . 'Bundle-uv-ips']],
            'identifier' => ['system' => 'urn:ietf:rfc:3986', 'value' => 'urn:uuid:' . $summary['document']['id']],
            'type' => 'document',
            'timestamp' => $summary['document']['generated_at']->toIso8601String(),
            'entry' => $bundleEntries,
        ];
    }

    private function patient(array $p): array
    {
        $parts = preg_split('/\s+/', trim((string) $p['name']), 2);
        $patient = [
            'resourceType' => 'Patient',
            'meta' => ['profile' => [self::IPS . 'Patient-uv-ips']],
            'name' => [['text' => $p['name'], 'given' => [$parts[0] ?? ''], 'family' => $parts[1] ?? ($parts[0] ?? '')]],
        ];
        if ($p['birth_date']) {
            $patient['birthDate'] = Carbon::parse($p['birth_date'])->toDateString();
        }
        if ($p['fifa_connect_id']) {
            $patient['identifier'] = [['type' => ['text' => 'FIFA Connect ID'], 'value' => (string) $p['fifa_connect_id']]];
        }

        return $patient;
    }

    private function date($value): ?string
    {
        return $value ? Carbon::parse($value)->toDateString() : null;
    }

    private function resource(string $section, array $item, string $patientRef): array
    {
        $ref = 'urn:uuid:' . Str::uuid();
        $subject = ['reference' => $patientRef];
        $text = trim($item['label'] . (!empty($item['detail']) ? ' — ' . $item['detail'] : ''));
        $resource = match ($section) {
            'allergies' => ['resourceType' => 'AllergyIntolerance', 'meta' => ['profile' => [self::IPS . 'AllergyIntolerance-uv-ips']],
                'clinicalStatus' => $this->concept('http://terminology.hl7.org/CodeSystem/allergyintolerance-clinical', 'active'),
                'code' => ['text' => $item['label']], 'patient' => $subject, 'recordedDate' => $this->date($item['date'] ?? null), 'note' => $item['detail'] ? [['text' => $item['detail']]] : null],
            'problems', 'history' => ['resourceType' => 'Condition', 'meta' => ['profile' => [self::IPS . 'Condition-uv-ips']],
                'clinicalStatus' => $this->concept('http://terminology.hl7.org/CodeSystem/condition-clinical', $section === 'history' ? 'resolved' : 'active'),
                'code' => $this->conditionCode($item), 'subject' => $subject, 'recordedDate' => $this->date($item['date'] ?? null), 'note' => $item['detail'] ? [['text' => $item['detail']]] : null],
            'medications' => ['resourceType' => 'MedicationStatement', 'meta' => ['profile' => [self::IPS . 'MedicationStatement-uv-ips']],
                'status' => in_array($item['status'] ?? null, ['active', 'enregistré'], true) ? 'active' : 'unknown',
                'medicationCodeableConcept' => $this->medicationCode($item), 'subject' => $subject,
                'effectiveDateTime' => $this->date($item['date'] ?? null), 'dosage' => $item['detail'] ? [['text' => $item['detail']]] : null,
                // Liste des interdictions de l'AMA : aucun système de codes FHIR officiel, mention en texte avec sa version.
                'note' => !empty($item['antidoping']['categories'])
                    ? [['text' => 'Alerte antidopage — liste des interdictions AMA ' . $item['antidoping']['version'] . ' : ' . implode(' ; ', $item['antidoping']['categories']) . ' (correspondance de substance, à vérifier par le médecin)']]
                    : null],
            'immunizations' => ['resourceType' => 'Immunization', 'meta' => ['profile' => [self::IPS . 'Immunization-uv-ips']],
                'status' => 'completed', 'vaccineCode' => ['text' => $item['label']], 'patient' => $subject, 'occurrenceDateTime' => $this->date($item['date'] ?? null)],
            default => ['resourceType' => 'Observation', 'status' => 'final',
                'category' => [$this->concept('http://terminology.hl7.org/CodeSystem/observation-category', $section === 'vital_signs' ? 'vital-signs' : ($section === 'results' ? 'laboratory' : 'survey'))],
                'code' => ['text' => $item['label']], 'subject' => $subject, 'effectiveDateTime' => $this->date($item['date'] ?? null), 'valueString' => $item['detail'] ?: $text],
        };

        return [$ref, array_filter($resource, fn ($v) => $v !== null)];
    }

    private function conditionCode(array $item): array
    {
        $code = ['text' => $item['label']];
        if (!empty($item['code']) && str_starts_with($item['code'], 'CIM-11 ')) {
            $code['coding'] = [['system' => self::ICD11, 'code' => substr($item['code'], 7), 'display' => $item['label']]];
        }

        return $code;
    }

    private function medicationCode(array $item): array
    {
        $code = ['text' => $item['label']];
        if (!empty($item['rxcui'])) {
            $code['coding'] = [['system' => self::RXNORM, 'code' => $item['rxcui'], 'display' => $item['label']]];
        }

        return $code;
    }

    private function noInformation(string $section, string $patientRef): array
    {
        $ref = 'urn:uuid:' . Str::uuid();
        $subject = ['reference' => $patientRef];
        [$code, $display] = match ($section) {
            'allergies' => ['no-allergy-info', 'No information about allergies'],
            'problems' => ['no-problem-info', 'No information about problems'],
            default => ['no-medication-info', 'No information about medications'],
        };
        $concept = ['coding' => [['system' => self::ABSENT, 'code' => $code, 'display' => $display]]];
        $resource = match ($section) {
            'allergies' => ['resourceType' => 'AllergyIntolerance', 'code' => $concept, 'patient' => $subject],
            'problems' => ['resourceType' => 'Condition', 'code' => $concept, 'subject' => $subject],
            default => ['resourceType' => 'MedicationStatement', 'status' => 'unknown', 'medicationCodeableConcept' => $concept, 'subject' => $subject],
        };

        return [$ref, $resource];
    }

    private function concept(string $system, string $code): array
    {
        return ['coding' => [['system' => $system, 'code' => $code]]];
    }
}
