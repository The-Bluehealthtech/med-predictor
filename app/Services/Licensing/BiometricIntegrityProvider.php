<?php

namespace App\Services\Licensing;

use Illuminate\Support\Facades\Http;

final class BiometricIntegrityProvider
{
    public function __construct(private readonly AwsRekognitionFaceMatcher $faceMatcher)
    {
    }

    public const STATUS_LABELS = [
        'not_configured' => 'Fournisseur biométrique non connecté',
        'ready' => 'Fournisseur biométrique configuré',
        'error' => 'Fournisseur biométrique indisponible',
    ];

    public function isConfigured(): bool
    {
        return $this->faceMatcher->isConfigured()
            || (filled(config('services.biometric_integrity.url')) && filled(config('services.biometric_integrity.token')));
    }

    public function status(): array
    {
        $face = $this->faceMatcher->status();
        $signatureReady = filled(config('services.biometric_integrity.url'))
            && filled(config('services.biometric_integrity.token'))
            && (bool) config('services.biometric_integrity.signature_match', false);
        $status = ($face['status'] === 'ready' || $signatureReady) ? 'ready' : 'not_configured';

        return [
            'status' => $status,
            'label' => self::STATUS_LABELS[$status],
            'provider' => $face['status'] === 'ready' ? 'AWS Rekognition' : (config('services.biometric_integrity.provider') ?: null),
            'face' => $face,
            'signature' => [
                'status' => $signatureReady ? 'ready' : 'not_configured',
                'provider' => config('services.biometric_integrity.provider') ?: null,
            ],
            'capabilities' => [
                'face_match' => $face['status'] === 'ready',
                'signature_match' => $signatureReady,
            ],
        ];
    }

    public function compare(string $capability, array $payload): array
    {
        if (!$this->isConfigured()) {
            return [
                'status' => 'not_configured',
                'label' => self::STATUS_LABELS['not_configured'],
            ];
        }

        if (!in_array($capability, ['face_match', 'signature_match'], true)
            || !config("services.biometric_integrity.{$capability}", false)) {
            return ['status' => 'unsupported', 'label' => 'Comparaison non activée chez le fournisseur'];
        }

        try {
            $response = Http::timeout((int) config('services.biometric_integrity.timeout', 15))
                ->acceptJson()
                ->withToken((string) config('services.biometric_integrity.token'))
                ->post(rtrim((string) config('services.biometric_integrity.url'), '/') . '/compare/' . $capability, $payload);
        } catch (\Throwable $e) {
            return ['status' => 'error', 'label' => self::STATUS_LABELS['error']];
        }

        if (!$response->successful()) {
            return ['status' => 'error', 'label' => self::STATUS_LABELS['error']];
        }

        $data = (array) $response->json();
        $score = $data['score'] ?? null;
        if (!is_numeric($score)) {
            return ['status' => 'error', 'label' => 'Réponse biométrique invalide'];
        }

        return [
            'status' => 'completed',
            'score' => max(0.0, min(1.0, (float) $score)),
            'threshold' => isset($data['threshold']) && is_numeric($data['threshold'])
                ? (float) $data['threshold'] : null,
            'provider_reference' => isset($data['reference']) ? (string) $data['reference'] : null,
            'model' => isset($data['model']) ? (string) $data['model'] : null,
            'evaluated_at' => now()->toIso8601String(),
        ];
    }
}
