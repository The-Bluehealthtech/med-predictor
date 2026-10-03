<?php

namespace App\Services\Fhir;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Client REST HL7 FHIR R4 du serveur dédié à FIT (config/fhir.php). Échanges en
 * application/fhir+json ; toute erreur du serveur remonte avec son OperationOutcome.
 * Aucun contenu médical n'est journalisé.
 */
final class FhirClient
{
    public const MIME = 'application/fhir+json';

    public function configured(): bool
    {
        return (string) config('fhir.base_url') !== '';
    }

    /** CapabilityStatement du serveur (GET [base]/metadata). */
    public function capabilities(): array
    {
        return $this->send(fn (PendingRequest $http) => $http->get($this->url('metadata')));
    }

    public function read(string $type, string $id): array
    {
        return $this->send(fn (PendingRequest $http) => $http->get($this->url($type . '/' . rawurlencode($id))));
    }

    /** Recherche (search-type) : Bundle searchset. */
    public function search(string $type, array $parameters = []): array
    {
        return $this->send(fn (PendingRequest $http) => $http->get($this->url($type), $parameters));
    }

    public function create(array $resource): array
    {
        return $this->send(fn (PendingRequest $http) => $http->withBody($this->json($resource), self::MIME)->post($this->url($resource['resourceType'])));
    }

    /** Mise à jour d'une ressource connue (PUT [type]/[id]). */
    public function update(array $resource): array
    {
        return $this->send(fn (PendingRequest $http) => $http->withBody($this->json($resource), self::MIME)
            ->put($this->url($resource['resourceType'] . '/' . rawurlencode((string) $resource['id']))));
    }

    /** Mise à jour conditionnelle (PUT [type]?critères), ex. ['identifier' => 'système|valeur']. */
    public function conditionalUpdate(array $resource, array $criteria): array
    {
        return $this->send(fn (PendingRequest $http) => $http->withBody($this->json($resource), self::MIME)
            ->put($this->url($resource['resourceType']) . '?' . http_build_query($criteria)));
    }

    /** Bundle transaction (ex. ITI-65 Provide Document Bundle). */
    public function transaction(array $bundle): array
    {
        return $this->send(fn (PendingRequest $http) => $http->withBody($this->json($bundle), self::MIME)->post($this->url('')));
    }

    /** $validate d'une ressource, éventuellement contre un profil : OperationOutcome. */
    public function validate(array $resource, ?string $profile = null): array
    {
        $query = $profile ? '?' . http_build_query(['profile' => $profile]) : '';

        return $this->send(fn (PendingRequest $http) => $http->withBody($this->json($resource), self::MIME)
            ->post($this->url($resource['resourceType'] . '/$validate') . $query), [200, 400, 422]);
    }

    private function send(callable $call, array $accepted = []): array
    {
        if (!$this->configured()) {
            throw new FhirException('Serveur FHIR de FIT non configuré (FIT_FHIR_BASE_URL).');
        }
        try {
            /** @var Response $response */
            $response = $call(Http::accept(self::MIME)->timeout((int) config('fhir.timeout', 20))->withOptions(['allow_redirects' => false]));
        } catch (ConnectionException $e) {
            throw new FhirException('Serveur FHIR injoignable.');
        }
        $body = $response->json();
        if ($response->successful() || in_array($response->status(), $accepted, true)) {
            return is_array($body) ? $body : [];
        }

        throw new FhirException('Le serveur FHIR a refusé la requête (HTTP ' . $response->status() . ').', $response->status(),
            is_array($body) && ($body['resourceType'] ?? null) === 'OperationOutcome' ? $body : null);
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
