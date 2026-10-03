<?php

namespace App\Services\Fhir;

use App\Models\FhirDocument;
use App\Models\FhirPatientLink;
use App\Models\Player;
use App\Models\User;
use App\Services\Audit\Auditor;
use Illuminate\Support\Str;

/**
 * Partage de l'IPS du passeport médical selon IHE sIPS 1.0.0 :
 *  - Content Creator groupé avec MHD Document Source (ITI-65, option Comprehensive,
 *    références non contenues) : l'IPS attesté est validé contre Bundle-uv-ips ($validate),
 *    puis publié avec sa SubmissionSet et sa DocumentReference ;
 *  - Content Consumer groupé avec MHD Document Consumer (ITI-67 recherche, ITI-68 lecture),
 *    option « View » : IPS des établissements pour le Patient de FIT et les Patients rattachés.
 * Métadonnées : correspondance Composition → DocumentReference du cœur FHIR R4 ; formatCode
 * imposé par sIPS (urn:ietf:rfc:3986 | profil Bundle-uv-ips).
 */
final class IpsDocumentSharing
{
    public const IPS_BUNDLE = 'http://hl7.org/fhir/uv/ips/StructureDefinition/Bundle-uv-ips';

    private const MHD = 'https://profiles.ihe.net/ITI/MHD/StructureDefinition/';

    private const RFC3986 = 'urn:ietf:rfc:3986';

    private const LOINC = 'http://loinc.org';

    public function __construct(private readonly FhirClient $client, private readonly PatientIdentity $identity, private readonly Auditor $auditor)
    {
    }

    /** Conditions de publication non remplies (vide = publication possible). */
    public function blockers(array $attestation): array
    {
        $blockers = [];
        if (!$this->client->configured()) {
            $blockers[] = 'Serveur FHIR de FIT non installé.';
        }
        if (!preg_match('/^[0-2](\.(0|[1-9][0-9]*))+$/', (string) config('fhir.document_sharing.source_oid'))) {
            $blockers[] = 'OID de FIT comme source documentaire non configuré (FIT_FHIR_SOURCE_OID).';
        }
        if (($attestation['state'] ?? 'none') !== 'valid') {
            $blockers[] = 'Le passeport doit être attesté par un médecin, sans modification depuis.';
        }

        return $blockers;
    }

    /**
     * Validation ($validate, profil Bundle-uv-ips) puis ITI-65. Renvoie le document enregistré.
     *
     * @throws FhirException si le serveur refuse l'IPS ou la transaction (issues de l'OperationOutcome)
     */
    public function publish(Player $player, array $ips, array $attestation, string $purpose, User $user): FhirDocument
    {
        abort_unless($this->blockers($attestation) === [], 422, implode(' ', $this->blockers($attestation)));

        $outcome = $this->client->validate($ips, self::IPS_BUNDLE);
        $errors = collect($outcome['issue'] ?? [])->whereIn('severity', ['error', 'fatal']);
        if ($errors->isNotEmpty()) {
            throw new FhirException('IPS non conforme au profil Bundle-uv-ips : publication refusée.', 422, $outcome);
        }

        $patientId = $this->identity->fitPatientId($player) ?? $this->identity->feed($player);
        $previous = FhirDocument::query()->where(['player_id' => $player->id, 'kind' => 'ips', 'status' => 'current'])->latest('published_at')->first();
        $transaction = $this->provideBundle($ips, $patientId, $attestation, $previous);

        $response = $this->client->transaction($transaction);
        $location = (string) ($response['entry'][1]['response']['location'] ?? '');
        abort_unless(preg_match('~DocumentReference/([A-Za-z0-9\-.]{1,64})~', $location, $m), 502, 'Réponse ITI-65 sans DocumentReference.');

        $document = FhirDocument::query()->create([
            'player_id' => $player->id, 'kind' => 'ips', 'purpose' => $purpose, 'document_reference_id' => $m[1],
            'master_identifier' => $ips['identifier']['value'], 'status' => 'current', 'replaces_id' => $previous?->id,
            'attestation_id' => $attestation['attestation']?->id, 'published_by' => $user->id, 'published_at' => now(),
        ]);
        if ($previous) {
            $this->supersede($previous);
        }
        $this->auditor->record(['event_type' => 'data_modification', 'module' => 'fhir', 'action' => 'ips_publish',
            'description' => 'IPS du passeport médical publié (IHE sIPS / MHD ITI-65)', 'model' => $player, 'sensitive' => true,
            'metadata' => ['document_reference' => $m[1], 'replaces' => $previous?->document_reference_id]]);

        return $document;
    }

    /** Transaction ITI-65 : SubmissionSet, DocumentReference, document FHIR (profil UnContained Comprehensive). */
    public function provideBundle(array $ips, string $patientId, array $attestation, ?FhirDocument $previous = null): array
    {
        $composition = collect($ips['entry'])->firstWhere('resource.resourceType', 'Composition')['resource'];
        $author = collect($ips['entry'])->firstWhere('fullUrl', $composition['author'][0]['reference'] ?? null)['resource'] ?? null;
        $json = json_encode($ips, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        $patient = ['reference' => 'Patient/' . $patientId];
        $documentUrl = 'urn:uuid:' . Str::uuid();
        $referenceUrl = 'urn:uuid:' . Str::uuid();
        $submissionUrl = 'urn:uuid:' . Str::uuid();
        $type = $composition['type'];
        $sharing = config('fhir.document_sharing');

        $documentReference = array_filter([
            'resourceType' => 'DocumentReference',
            'meta' => ['profile' => [self::MHD . 'IHE.MHD.UnContained.Comprehensive.DocumentReference']],
            'masterIdentifier' => $this->uniqueId($ips['identifier']['value']),
            'status' => 'current',
            'type' => $type,
            'category' => [$type],
            'subject' => $patient,
            'date' => $composition['date'],
            'author' => $author ? [['display' => $author['name'] ?? 'FIT']] : null,
            'authenticator' => isset($composition['attester'][0]['party']['display']) ? ['display' => $composition['attester'][0]['party']['display']] : null,
            'description' => $composition['title'] ?? null,
            'securityLabel' => [['coding' => [['system' => 'http://terminology.hl7.org/CodeSystem/v3-Confidentiality', 'code' => $composition['confidentiality'] ?? 'R']]]],
            'relatesTo' => $previous ? [['code' => 'replaces', 'target' => ['reference' => 'DocumentReference/' . $previous->document_reference_id]]] : null,
            'content' => [[
                'attachment' => [
                    'contentType' => FhirClient::MIME,
                    'language' => $sharing['language'],
                    'url' => $documentUrl,
                    'size' => strlen($json),
                    'hash' => base64_encode(sha1($json, true)),
                    'title' => $composition['title'] ?? 'International Patient Summary',
                    'creation' => $composition['date'],
                ],
                'format' => ['system' => self::RFC3986, 'code' => self::IPS_BUNDLE],
            ]],
            'context' => [
                'facilityType' => ['text' => $sharing['facility_type']],
                'practiceSetting' => ['text' => $sharing['practice_setting']],
                'sourcePatientInfo' => $patient,
            ],
        ], fn ($v) => $v !== null);

        $submissionSet = [
            'resourceType' => 'List',
            'meta' => ['profile' => [self::MHD . 'IHE.MHD.UnContained.Comprehensive.SubmissionSet']],
            'extension' => [
                ['url' => self::MHD . 'ihe-designationType', 'valueCodeableConcept' => $type],
                ['url' => self::MHD . 'ihe-sourceId', 'valueIdentifier' => ['value' => 'urn:oid:' . $sharing['source_oid']]],
            ],
            'identifier' => [$this->uniqueId('urn:uuid:' . Str::uuid(), 'usual')],
            'status' => 'current',
            'mode' => 'working',
            'title' => 'Publication IPS — passeport médical FIT',
            'code' => ['coding' => [['system' => 'https://profiles.ihe.net/ITI/MHD/CodeSystem/MHDlistTypes', 'code' => 'submissionset']]],
            'subject' => $patient,
            'date' => now()->toIso8601String(),
            'entry' => [['item' => ['reference' => $referenceUrl]]],
        ];

        return [
            'resourceType' => 'Bundle',
            'meta' => ['profile' => [self::MHD . 'IHE.MHD.UnContained.Comprehensive.ProvideBundle']],
            'type' => 'transaction',
            'entry' => [
                ['fullUrl' => $submissionUrl, 'resource' => $submissionSet, 'request' => ['method' => 'POST', 'url' => 'List']],
                ['fullUrl' => $referenceUrl, 'resource' => $documentReference, 'request' => ['method' => 'POST', 'url' => 'DocumentReference']],
                ['fullUrl' => $documentUrl, 'resource' => $ips, 'request' => ['method' => 'POST', 'url' => 'Bundle']],
            ],
        ];
    }

    /**
     * ITI-67 : IPS courants du joueur (Patient de FIT et Patients rattachés).
     *
     * @return list<array{id:string, date:?string, title:?string, author:?string, own:bool, source_patient:string}>
     */
    public function find(Player $player): array
    {
        $patients = $this->patientIds($player);
        if ($patients === []) {
            return [];
        }
        $own = FhirDocument::query()->where('player_id', $player->id)->pluck('document_reference_id')->all();
        $bundle = $this->client->search('DocumentReference', [
            'patient' => implode(',', array_map(fn ($id) => 'Patient/' . $id, $patients)),
            'status' => 'current',
            'format' => self::RFC3986 . '|' . self::IPS_BUNDLE,
            '_sort' => '-date',
            '_count' => 50,
        ]);

        return collect($bundle['entry'] ?? [])->map(fn ($e) => $e['resource'] ?? [])
            ->filter(fn ($r) => ($r['resourceType'] ?? null) === 'DocumentReference' && !empty($r['id']))
            ->map(fn ($r) => [
                'id' => (string) $r['id'],
                'date' => $r['date'] ?? null,
                'title' => $r['content'][0]['attachment']['title'] ?? ($r['description'] ?? null),
                'author' => collect($r['author'] ?? [])->pluck('display')->filter()->implode(', ') ?: null,
                'own' => in_array((string) $r['id'], $own, true),
                'source_patient' => (string) ($r['subject']['reference'] ?? ''),
            ])->values()->all();
    }

    /** ITI-68 : IPS d'une DocumentReference du joueur, résumé pour affichage (option View). */
    public function retrieve(Player $player, string $documentReferenceId): array
    {
        $reference = $this->client->read('DocumentReference', $documentReferenceId);
        $subject = Str::after((string) ($reference['subject']['reference'] ?? ''), 'Patient/');
        abort_unless(in_array($subject, $this->patientIds($player), true), 403, 'Ce document ne concerne pas ce joueur.');
        $url = (string) ($reference['content'][0]['attachment']['url'] ?? '');
        $base = rtrim((string) config('fhir.base_url'), '/') . '/';
        if (str_starts_with($url, $base)) {
            $url = substr($url, strlen($base));
        }
        // Seul le serveur FHIR de FIT est lu (aucune URL externe).
        abort_unless(preg_match('~^(Bundle|Binary)/([A-Za-z0-9\-.]{1,64})$~', $url, $m), 422, 'Emplacement du document non pris en charge.');
        $resource = $this->client->read($m[1], $m[2]);
        if ($m[1] === 'Binary') {
            $resource = json_decode(base64_decode((string) ($resource['data'] ?? ''), true) ?: '[]', true) ?: [];
        }
        abort_unless(($resource['resourceType'] ?? null) === 'Bundle' && ($resource['type'] ?? null) === 'document', 422, 'Le document n\'est pas un IPS FHIR.');
        $this->auditor->record(['event_type' => 'data_access', 'module' => 'fhir', 'action' => 'ips_retrieve',
            'description' => 'IPS consulté (IHE sIPS / MHD ITI-68)', 'model' => $player, 'sensitive' => true, 'metadata' => ['document_reference' => $documentReferenceId]]);

        return $this->view($resource) + ['reference' => ['id' => $documentReferenceId, 'date' => $reference['date'] ?? null,
            'author' => collect($reference['author'] ?? [])->pluck('display')->filter()->implode(', ') ?: null]];
    }

    /** Sections de l'IPS et libellés de leurs entrées (le XHTML narratif d'une source n'est jamais rendu tel quel). */
    public function view(array $bundle): array
    {
        $byUrl = collect($bundle['entry'] ?? [])->keyBy('fullUrl')->map(fn ($e) => $e['resource'] ?? []);
        $composition = $byUrl->first(fn ($r) => ($r['resourceType'] ?? null) === 'Composition') ?? [];
        $sections = collect($composition['section'] ?? [])->map(fn ($section) => [
            'title' => $section['title'] ?? ($section['code']['coding'][0]['display'] ?? 'Section'),
            'items' => collect($section['entry'] ?? [])->map(fn ($ref) => $this->label($byUrl->get($ref['reference'] ?? '') ?? []))->filter()->values()->all(),
            'text' => isset($section['text']['div']) ? trim(html_entity_decode(strip_tags($section['text']['div']))) : null,
        ])->all();

        return ['title' => $composition['title'] ?? 'International Patient Summary', 'date' => $composition['date'] ?? null,
            'status' => $composition['status'] ?? null, 'sections' => $sections];
    }

    private function label(array $resource): ?string
    {
        $concept = $resource['code'] ?? $resource['medicationCodeableConcept'] ?? $resource['vaccineCode'] ?? null;
        if (!$concept) {
            return null;
        }
        $text = $concept['text'] ?? ($concept['coding'][0]['display'] ?? ($concept['coding'][0]['code'] ?? null));
        $codes = collect($concept['coding'] ?? [])->map(fn ($c) => isset($c['code']) ? $this->systemName($c['system'] ?? '') . ' ' . $c['code'] : null)->filter()->implode(', ');
        $value = $resource['valueString'] ?? (isset($resource['valueQuantity']['value']) ? $resource['valueQuantity']['value'] . ' ' . ($resource['valueQuantity']['unit'] ?? '') : null);

        return trim($text . ($value ? ' : ' . $value : '') . ($codes ? ' (' . $codes . ')' : ''));
    }

    private function systemName(string $system): string
    {
        return match ($system) {
            'http://id.who.int/icd/release/11/mms' => 'CIM-11',
            'http://www.nlm.nih.gov/research/umls/rxnorm' => 'RxNorm',
            self::LOINC => 'LOINC',
            'http://hl7.org/fhir/sid/cvx' => 'CVX',
            'http://snomed.info/sct' => 'SNOMED CT',
            'http://hl7.org/fhir/uv/ips/CodeSystem/absent-unknown-uv-ips' => 'IPS',
            default => $system,
        };
    }

    /** Patient de FIT et Patients des sources rattachés au joueur. */
    private function patientIds(Player $player): array
    {
        return FhirPatientLink::query()->where('player_id', $player->id)->where('status', 'linked')->whereNotNull('patient_id')
            ->orderByDesc('role')->orderBy('id') // Patient de FIT (« fit ») d'abord
            ->pluck('patient_id')->map(fn ($id) => (string) $id)->unique()->values()->all();
    }

    private function uniqueId(string $value, ?string $use = null): array
    {
        return array_filter(['use' => $use, 'type' => ['coding' => [['system' => 'https://profiles.ihe.net/ITI/MHD/CodeSystem/IHE.MHD.MHDIdentifierType', 'code' => 'uniqueId']]],
            'system' => self::RFC3986, 'value' => $value]);
    }

    /** Le document remplacé passe à « superseded » (rôle du Document Recipient, émulé sur HAPI). */
    private function supersede(FhirDocument $previous): void
    {
        $previous->update(['status' => 'superseded']);
        try {
            $reference = $this->client->read('DocumentReference', $previous->document_reference_id);
            if (($reference['status'] ?? null) === 'current') {
                $reference['status'] = 'superseded';
                $this->client->update($reference);
            }
        } catch (FhirException) {
            // Le nouveau document porte relatesTo « replaces » : la relation reste lisible.
        }
    }
}
