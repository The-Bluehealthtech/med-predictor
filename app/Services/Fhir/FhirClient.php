<?php

namespace App\Services\Fhir;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Client REST HL7 FHIR R4 du serveur dédié à FIT (config/fhir.php). Échanges en
 * application/fhir+json ; toute erreur du serveur remonte avec son OperationOutcome.
 *  - Autorisation : jeton OAuth2 IHE IUA (ITI-71) quand un serveur d'autorisation est configuré.
 *  - Traçabilité : chaque échange portant des données de patient est journalisé sur le serveur
 *    en AuditEvent IHE BALP (MHD pour ITI-65), avec l'identifiant de requête X-Request-Id.
 * Aucun contenu médical n'est journalisé dans les journaux applicatifs.
 */
final class FhirClient
{
    public const MIME = 'application/fhir+json';

    public function __construct(private readonly IuaToken $token, private readonly FhirAudit $audit)
    {
    }

    public function configured(): bool
    {
        return (string) config('fhir.base_url') !== '';
    }

    /** CapabilityStatement du serveur (GET [base]/metadata) : aucune donnée de patient, non audité. */
    public function capabilities(): array
    {
        return $this->send(fn (PendingRequest $http) => $http->get($this->url('metadata')));
    }

    public function read(string $type, string $id): array
    {
        $url = $this->url($type . '/' . rawurlencode($id));

        return $this->send(fn (PendingRequest $http) => $http->get($url), [], ['interaction' => 'read', 'type' => $type, 'id' => $id, 'url' => $url]);
    }

    /** Recherche (search-type) : Bundle searchset. */
    public function search(string $type, array $parameters = []): array
    {
        $url = $this->url($type);

        return $this->send(fn (PendingRequest $http) => $http->get($url, $parameters), [], [
            'interaction' => 'search', 'type' => $type, 'url' => $url . ($parameters ? '?' . http_build_query($parameters) : ''),
            'patient' => isset($parameters['patient']) ? explode(',', (string) $parameters['patient'])[0] : null,
        ]);
    }

    public function create(array $resource): array
    {
        $url = $this->url($resource['resourceType']);

        return $this->send(fn (PendingRequest $http) => $http->withBody($this->json($resource), self::MIME)->post($url), [],
            ['interaction' => 'create', 'type' => $resource['resourceType'], 'resource' => $resource, 'url' => $url]);
    }

    /** Mise à jour d'une ressource connue (PUT [type]/[id]). */
    public function update(array $resource): array
    {
        $url = $this->url($resource['resourceType'] . '/' . rawurlencode((string) $resource['id']));

        return $this->send(fn (PendingRequest $http) => $http->withBody($this->json($resource), self::MIME)->put($url), [],
            ['interaction' => 'update', 'type' => $resource['resourceType'], 'id' => (string) $resource['id'], 'resource' => $resource, 'url' => $url]);
    }

    /** Mise à jour conditionnelle (PUT [type]?critères), ex. ['identifier' => 'système|valeur']. */
    public function conditionalUpdate(array $resource, array $criteria): array
    {
        $url = $this->url($resource['resourceType']) . '?' . http_build_query($criteria);

        return $this->send(fn (PendingRequest $http) => $http->withBody($this->json($resource), self::MIME)->put($url), [],
            ['interaction' => 'update', 'type' => $resource['resourceType'], 'resource' => $resource, 'url' => $url]);
    }

    /** Bundle transaction (ex. ITI-65 Provide Document Bundle). */
    public function transaction(array $bundle): array
    {
        return $this->send(fn (PendingRequest $http) => $http->withBody($this->json($bundle), self::MIME)->post($this->url('')), [],
            ['interaction' => 'transaction', 'type' => 'Bundle', 'resource' => $bundle, 'url' => $this->url('')]);
    }

    /** $validate d'une ressource, éventuellement contre un profil : OperationOutcome (rien n'est enregistré, non audité). */
    public function validate(array $resource, ?string $profile = null): array
    {
        $query = $profile ? '?' . http_build_query(['profile' => $profile]) : '';

        return $this->send(fn (PendingRequest $http) => $http->withBody($this->json($resource), self::MIME)
            ->post($this->url($resource['resourceType'] . '/$validate') . $query), [200, 400, 422]);
    }

    private function send(callable $call, array $accepted = [], ?array $audit = null): array
    {
        if (!$this->configured()) {
            throw new FhirException('Serveur FHIR de FIT non configuré (FIT_FHIR_BASE_URL).');
        }
        $requestId = (string) Str::uuid();
        try {
            /** @var Response $response */
            $response = $call($this->http($requestId));
            if ($response->status() === 401 && $this->token->enabled()) {
                // Jeton expiré ou révoqué : un seul renouvellement.
                $this->token->forget();
                $response = $call($this->http($requestId));
            }
        } catch (ConnectionException $e) {
            throw new FhirException('Serveur FHIR injoignable.');
        }
        $body = $response->json();
        $ok = $response->successful() || in_array($response->status(), $accepted, true);
        if ($audit) {
            $this->audit->record($audit + ['status' => $response->status(), 'ok' => $response->successful(), 'response' => is_array($body) ? $body : [], 'request_id' => $requestId]);
        }
        if ($ok) {
            return is_array($body) ? $body : [];
        }

        throw new FhirException('Le serveur FHIR a refusé la requête (HTTP ' . $response->status() . ').', $response->status(),
            is_array($body) && ($body['resourceType'] ?? null) === 'OperationOutcome' ? $body : null);
    }

    private function http(string $requestId): PendingRequest
    {
        $http = Http::accept(self::MIME)->timeout((int) config('fhir.timeout', 20))->withOptions(['allow_redirects' => false])
            ->withHeaders(['X-Request-Id' => $requestId]);

        return $this->token->enabled() ? $http->withToken($this->token->get()) : $http;
    }

    private function url(string $path): string
    {
        return rtrim((string) config('fhir.base_url'), '/') . ($path === '' ? '' : '/' . ltrim($path, '/'));
    }

    private function json(array $resource): string
    {
        return json_encode($resource, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }
}
