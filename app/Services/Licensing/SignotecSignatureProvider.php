<?php

namespace App\Services\Licensing;

use Illuminate\Support\Facades\Http;

/**
 * Adaptateur FIT vers le bridge interne qui encapsule la Biometrics API signotec.
 * Le bridge est un composant FIT ; ce n'est pas un endpoint REST fourni par signotec.
 */
final class SignotecSignatureProvider
{
    public function isConfigured(): bool
    {
        return filled(config('services.signotec.bridge_url'))
            && filled(config('services.signotec.bridge_token'));
    }

    public function status(): array
    {
        return [
            'status' => $this->isConfigured() ? 'ready' : 'sdk_required',
            'label' => $this->isConfigured()
                ? 'signotec Biometrics API connectée'
                : 'signotec prête côté FIT · SDK/licence à connecter',
            'provider' => 'signotec',
            'mode' => 'licensed_sdk_bridge',
        ];
    }

    public function compare(string $referenceId, string $candidateId): array
    {
        if (!$this->isConfigured()) {
            return ['status' => 'sdk_required'] + $this->status();
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
