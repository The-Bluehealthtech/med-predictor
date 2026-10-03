<?php

namespace App\Services\Fhir;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Jeton d'accès IHE IUA (Internet User Authorization) pour le serveur FHIR de FIT :
 * FIT agit comme Authorization Client et obtient un jeton OAuth 2.0 par la transaction ITI-71
 * (Get Access Token, flux « client credentials », RFC 6749 §4.4). Le jeton est mis en cache
 * jusqu'à son expiration ; il n'est jamais journalisé.
 */
final class IuaToken
{
    private const CACHE_KEY = 'fhir.iua.access_token';

    public function enabled(): bool
    {
        return (string) config('fhir.auth.token_url') !== '' && (string) config('fhir.auth.client_id') !== '';
    }

    public function get(): string
    {
        return Cache::get(self::CACHE_KEY) ?? $this->request();
    }

    public function forget(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    private function request(): string
    {
        $form = array_filter([
            'grant_type' => 'client_credentials',
            'scope' => config('fhir.auth.scope') ?: null,
            'resource' => config('fhir.auth.resource') ?: null, // RFC 8707 : serveur de ressources visé
        ]);
        try {
            $response = Http::asForm()->acceptJson()->timeout(10)->withOptions(['allow_redirects' => false])
                ->withBasicAuth((string) config('fhir.auth.client_id'), (string) config('fhir.auth.client_secret'))
                ->post((string) config('fhir.auth.token_url'), $form);
        } catch (ConnectionException) {
            throw new FhirException('Serveur d\'autorisation (IUA) injoignable.');
        }
        $token = $response->json('access_token');
        if (!$response->successful() || !is_string($token) || $token === '' || strcasecmp((string) $response->json('token_type'), 'Bearer') !== 0) {
            throw new FhirException('Jeton d\'accès IUA refusé par le serveur d\'autorisation (HTTP ' . $response->status() . ').', $response->status());
        }
        $ttl = max(30, (int) ($response->json('expires_in') ?? 300) - 30);
        Cache::put(self::CACHE_KEY, $token, $ttl);

        return $token;
    }
}
