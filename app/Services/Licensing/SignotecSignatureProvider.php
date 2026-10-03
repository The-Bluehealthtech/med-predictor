<?php

namespace App\Services\Licensing;

use App\Services\ApiConnectorState;
use Illuminate\Support\Facades\Http;

/**
 * Adaptateur FIT vers le bridge interne qui encapsule la Biometrics API signotec.
 * Le bridge est un composant FIT ; ce n'est pas un endpoint REST fourni par signotec.
 */
final class SignotecSignatureProvider
{
    public function __construct(private readonly ApiConnectorState $connectorState)
    {
    }

    public function isConfigured(): bool
    {
        return filled(config('services.signotec.bridge_url'))
            && filled(config('services.signotec.bridge_token'));
    }

    public function isEnabled(): bool
    {
        return $this->connectorState->enabled('signotec', false);
    }

    public function status(): array
    {
        $configured = $this->isConfigured();
        $enabled = $this->isEnabled();
        $status = !$configured ? 'sdk_required' : ($enabled ? 'ready' : 'disabled');

        return [
            'status' => $status,
            'label' => match ($status) {
                'ready' => 'signotec Biometrics API activée',
                'disabled' => 'signotec configurée · désactivée',
                default => 'signotec prête côté FIT · SDK/licence à connecter',
            },
            'provider' => 'signotec',
            'configured' => $configured,
            'enabled' => $enabled,
            'mode' => 'licensed_sdk_bridge',
        ];
    }

    public function testConnection(): array
    {
        if (!$this->isConfigured()) {
            return ['ok' => false, 'message' => 'Bridge/licence signotec non configuré.'];
        }

        try {
            $response = Http::timeout((int) config('services.signotec.timeout', 15))
                ->acceptJson()
                ->withToken((string) config('services.signotec.bridge_token'))
                ->get(rtrim((string) config('services.signotec.bridge_url'), '/') . '/health');
        } catch (\Throwable $e) {
            report($e);
            return ['ok' => false, 'message' => 'Bridge signotec injoignable.'];
        }

        return $response->successful()
            ? ['ok' => true, 'message' => 'Bridge signotec disponible.']
            : ['ok' => false, 'message' => 'Bridge signotec répond HTTP ' . $response->status() . '.'];
    }

    public function compare(string $referenceId, string $candidateId): array
    {
        if (!$this->isConfigured()) {
            return ['status' => 'sdk_required'] + $this->status();
        }
        if (!$this->isEnabled()) {
            return ['status' => 'disabled', 'provider' => 'signotec'];
        }

        try {
            $response = Http::timeout((int) config('services.signotec.timeout', 15))
                ->acceptJson()
                ->withToken((string) config('services.signotec.bridge_token'))
                ->post(rtrim((string) config('services.signotec.bridge_url'), '/') . '/v1/signatures/compare', [
                    'reference_id' => $referenceId,
                    'candidate_id' => $candidateId,
                ]);
        } catch (\Throwable $e) {
            report($e);
            return ['status' => 'error', 'provider' => 'signotec'];
        }

        if (!$response->successful()) {
            return ['status' => 'error', 'provider' => 'signotec', 'http_status' => $response->status()];
        }

        $data = (array) $response->json();
        if (!isset($data['score']) || !is_numeric($data['score'])) {
            return ['status' => 'error', 'provider' => 'signotec', 'message' => 'Réponse de comparaison invalide'];
        }

        return [
            'status' => 'completed',
            'provider' => 'signotec',
            'score' => max(0.0, min(100.0, (float) $data['score'])),
            'threshold' => isset($data['threshold']) && is_numeric($data['threshold']) ? (float) $data['threshold'] : null,
            'classification' => isset($data['classification']) ? (string) $data['classification'] : null,
            'provider_reference' => isset($data['reference']) ? (string) $data['reference'] : null,
            'checked_at' => now()->toIso8601String(),
        ];
    }
}
