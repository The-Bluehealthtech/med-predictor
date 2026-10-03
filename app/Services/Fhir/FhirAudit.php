<?php

namespace App\Services\Fhir;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Traçabilité des échanges FHIR de FIT, rôle IHE BALP « Audit Creator » (AuditEvent FHIR R4
 * déposé sur le serveur FHIR de FIT, qui tient l'acteur Audit Record Repository, ITI-20).
 * Profils officiels IHE BALP 1.1.4 : Create / Read / Query / Update et leurs variantes Patient ;
 * publication de documents : profil IHE MHD ProvideBundle.Audit.Source (ITI-65). Un échange en
 * échec est tracé sans profil (outcome 4 ou 8). L'audit n'interrompt jamais l'échange audité.
 */
final class FhirAudit
{
    private const BALP = 'https://profiles.ihe.net/ITI/BALP/StructureDefinition/IHE.BasicAudit.';

    private const DCM = 'http://dicom.nema.org/resources/ontology/DCM';

    private const ENTITY_TYPE = 'http://terminology.hl7.org/CodeSystem/audit-entity-type';

    private const OBJECT_ROLE = 'http://terminology.hl7.org/CodeSystem/object-role';

    private const PARTICIPATION = 'http://terminology.hl7.org/CodeSystem/v3-ParticipationType';

    /** interaction => [action, profil BALP, rôle DICOM de FIT, rôle DICOM du serveur, rôle de l'utilisateur]. */
    private const INTERACTIONS = [
        'create' => ['C', 'Create', '110153', '110152', 'INF'],
        'update' => ['U', 'Update', '110153', '110152', 'INF'],
        'read' => ['R', 'Read', '110152', '110153', 'IRCP'],
        'search' => ['E', 'Query', '110153', '110152', 'IRCP'],
    ];

    public function __construct(private readonly IuaToken $token)
    {
    }

    public function enabled(): bool
    {
        return (bool) config('fhir.audit.enabled', true) && (string) config('fhir.base_url') !== '';
    }

    /** @param array{interaction:string, type:string, id?:?string, url:string, patient?:?string, resource?:array, response:array, status:int, ok:bool, request_id:string} $exchange */
    public function record(array $exchange): void
    {
        if (!$this->enabled()) {
            return;
        }
        $event = $this->event($exchange);
        if (!$event) {
            return;
        }
        try {
            $http = Http::acceptJson()->timeout(5)->withOptions(['allow_redirects' => false])
                ->withBody(json_encode($event, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR), FhirClient::MIME);
            if ($this->token->enabled()) {
                $http = $http->withToken($this->token->get());
            }
            $response = $http->post(rtrim((string) config('fhir.base_url'), '/') . '/AuditEvent');
            if (!$response->successful()) {
                Log::warning('AuditEvent FHIR non enregistré', ['status' => $response->status(), 'request_id' => $exchange['request_id']]);
            }
        } catch (\Throwable $e) {
            Log::warning('AuditEvent FHIR non enregistré', ['error' => $e->getMessage(), 'request_id' => $exchange['request_id']]);
        }
    }

    /** AuditEvent de l'échange, ou null s'il n'y a rien à tracer. */
    public function event(array $x): ?array
    {
        $interaction = $x['interaction'];
        if ($interaction === 'update' && ($x['status'] ?? 0) === 201) {
            $interaction = 'create'; // mise à jour conditionnelle ayant créé la ressource
        }
        if ($interaction === 'transaction') {
            return $this->isMhd($x['resource'] ?? []) ? $this->provideBundle($x) : null;
        }
        if (!isset(self::INTERACTIONS[$interaction])) {
            return null;
        }
        [$action, $profile, $fitRole, $serverRole, $userRole] = self::INTERACTIONS[$interaction];
        $patient = $this->patient($x, $interaction);
        $entities = [];
        if ($interaction === 'search') {
            $entities[] = [
                'type' => $this->coding(self::ENTITY_TYPE, '2', 'System Object'),
                'role' => $this->coding(self::OBJECT_ROLE, '24', 'Query'),
                'description' => 'GET ' . $x['url'],
                'query' => base64_encode('GET ' . $x['url'] . "\nAccept: " . FhirClient::MIME . "\nX-Request-Id: " . $x['request_id']),
            ];
        } else {
            $id = $x['id'] ?? ($x['response']['id'] ?? null);
            $entities[] = array_filter([
                'what' => ['reference' => $x['type'] . ($id ? '/' . $id : '')],
                'type' => $this->coding(self::ENTITY_TYPE, '2', 'System Object'),
                'role' => $this->coding(self::OBJECT_ROLE, '4', 'Domain Resource'),
            ]);
        }
        if ($patient) {
            $entities[] = $this->patientEntity($patient);
        }
        $entities[] = $this->requestEntity($x['request_id']);

        $event = [
            'resourceType' => 'AuditEvent',
            'type' => $this->coding('http://terminology.hl7.org/CodeSystem/audit-event-type', 'rest', 'Restful Operation'),
            'subtype' => [$this->coding('http://hl7.org/fhir/restful-interaction', $interaction, $interaction)],
            'action' => $action,
            'recorded' => now()->toIso8601String(),
            'outcome' => $this->outcome($x),
            'agent' => $this->agents($fitRole, $serverRole, $userRole),
            'source' => $this->source(),
            'entity' => $entities,
        ];
        if ($x['ok']) {
            $event = ['meta' => ['profile' => [self::BALP . ($patient ? 'Patient' : '') . $profile]]] + $event;
        }

        return $event;
    }

    /** ITI-65 côté Document Source : profil IHE MHD ProvideBundle.Audit.Source. */
    private function provideBundle(array $x): array
    {
        $submission = collect($x['resource']['entry'] ?? [])->firstWhere('resource.resourceType', 'List')['resource'] ?? [];
        $location = (string) ($x['response']['entry'][0]['response']['location'] ?? '');
        $list = preg_match('~^(List/[A-Za-z0-9\-.]{1,64})~', $location, $m) ? $m[1] : 'List';
        $event = [
            'resourceType' => 'AuditEvent',
            'type' => $this->coding(self::DCM, '110106', 'Export'),
            'subtype' => [$this->coding('urn:ihe:event-type-code', 'ITI-65', 'Provide Document Bundle')],
            'action' => 'R',
            'recorded' => now()->toIso8601String(),
            'outcome' => $this->outcome($x),
            'agent' => $this->agents('110153', '110152', 'INF'),
            'source' => $this->source(),
            'entity' => array_values(array_filter([
                isset($submission['subject']['reference']) ? $this->patientEntity($submission['subject']['reference']) : null,
                ['what' => ['reference' => $list], 'type' => $this->coding(self::ENTITY_TYPE, '2', 'System Object'), 'role' => $this->coding(self::OBJECT_ROLE, '20', 'Job')],
                $this->requestEntity($x['request_id']),
            ])),
        ];

        return $x['ok'] ? ['meta' => ['profile' => ['https://profiles.ihe.net/ITI/MHD/StructureDefinition/IHE.MHD.ProvideBundle.Audit.Source']]] + $event : $event;
    }

    private function isMhd(array $bundle): bool
    {
        return collect($bundle['meta']['profile'] ?? [])->contains(fn ($p) => str_contains((string) $p, '/ITI/MHD/StructureDefinition/') && str_ends_with((string) $p, 'ProvideBundle'));
    }

    private function patient(array $x, string $interaction): ?string
    {
        $reference = $x['patient'] ?? null;
        if (!$reference && $x['type'] === 'Patient' && $interaction !== 'search') {
            $id = $x['id'] ?? ($x['response']['id'] ?? null);
            $reference = $id ? 'Patient/' . $id : null;
        }
        foreach ([$x['resource'] ?? [], $x['response'] ?? []] as $resource) {
            $reference ??= $resource['subject']['reference'] ?? $resource['patient']['reference'] ?? null;
        }
        if (!is_string($reference) || $reference === '') {
            return null;
        }

        return str_contains($reference, 'Patient/') ? 'Patient/' . preg_replace('~^.*Patient/~', '', $reference) : (preg_match('/^[A-Za-z0-9\-.]{1,64}$/', $reference) ? 'Patient/' . $reference : null);
    }

    /** Agents : FIT (client), serveur FHIR, et l'utilisateur connecté qui a déclenché l'échange. */
    private function agents(string $fitRole, string $serverRole, string $userRole): array
    {
        $display = fn (string $code) => $code === '110153' ? 'Source Role ID' : 'Destination Role ID';
        $app = (string) config('app.url');
        $agents = [
            ['type' => ['coding' => [$this->coding(self::DCM, $fitRole, $display($fitRole))]], 'who' => ['display' => 'FIT — ' . (parse_url($app, PHP_URL_HOST) ?: 'application')],
                'requestor' => false, 'network' => ['address' => $app ?: 'FIT', 'type' => '5']],
            ['type' => ['coding' => [$this->coding(self::DCM, $serverRole, $display($serverRole))]], 'who' => ['display' => 'Serveur FHIR de FIT'],
                'requestor' => false, 'network' => ['address' => (string) config('fhir.base_url'), 'type' => '5']],
        ];
        if ($user = auth()->user()) {
            $agents[] = ['type' => ['coding' => [$this->coding(self::PARTICIPATION, $userRole, $userRole === 'IRCP' ? 'information recipient' : 'informant')]],
                'who' => ['identifier' => ['system' => 'https://fit.tbhc.uk/fhir/sid/user', 'value' => (string) $user->getAuthIdentifier()], 'display' => (string) $user->name],
                'requestor' => true];
        }

        return $agents;
    }

    private function source(): array
    {
        return ['site' => parse_url((string) config('app.url'), PHP_URL_HOST) ?: 'FIT', 'observer' => ['display' => 'FIT'],
            'type' => [$this->coding('http://terminology.hl7.org/CodeSystem/security-source-type', '4', 'Application Server')]];
    }

    private function patientEntity(string $reference): array
    {
        return ['what' => ['reference' => $reference], 'type' => $this->coding(self::ENTITY_TYPE, '1', 'Person'), 'role' => $this->coding(self::OBJECT_ROLE, '1', 'Patient')];
    }

    private function requestEntity(string $requestId): array
    {
        return ['what' => ['identifier' => ['value' => $requestId]], 'type' => $this->coding('https://profiles.ihe.net/ITI/BALP/CodeSystem/BasicAuditEntityType', 'XrequestId', 'X-Request-Id')];
    }

    /** 0 succès, 4 échec mineur (refus du serveur, 4xx), 8 échec grave (5xx). */
    private function outcome(array $x): string
    {
        return $x['ok'] ? '0' : (($x['status'] ?? 500) >= 500 ? '8' : '4');
    }

    private function coding(string $system, string $code, string $display): array
    {
        return ['system' => $system, 'code' => $code, 'display' => $display];
    }
}
