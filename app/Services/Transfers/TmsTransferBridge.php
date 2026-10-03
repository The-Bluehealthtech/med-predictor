<?php

namespace App\Services\Transfers;

use App\Services\ApiConnectorState;
use Illuminate\Support\Facades\Http;

final class TmsTransferBridge
{
    public function __construct(private readonly ApiConnectorState $state)
    {
    }

    public function isConfigured(): bool
    {
        return filled(config('services.fifa_tms.bridge_url'))
            && filled(config('services.fifa_tms.bridge_token'))
            && !config('services.fifa_tms.mock_mode', false);
    }

    public function isEnabled(): bool
    {
        return $this->state->enabled('fifa_tms', false);
    }

    public function isReady(): bool
    {
        return $this->isConfigured() && $this->isEnabled();
    }

    public function testConnection(): array
    {
        if (!$this->isConfigured()) {
            return ['ok' => false, 'message' => 'Bridge SDK FIFA TMS non configuré.'];
        }

        try {
            $response = $this->client()->get('/health');
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => 'Bridge TMS inaccessible.'];
        }

        return [
            'ok' => $response->successful(),
            'message' => $response->successful()
                ? 'Bridge SDK FIFA TMS joignable.'
                : 'Bridge TMS a répondu avec une erreur.',
        ];
    }

    public function fetchTransfer(string $tmsTransferId): array
    {
        if (!$this->isReady()) {
            return [
                'success' => false,
                'code' => $this->isConfigured() ? 'disabled' : 'not_configured',
                'error' => $this->isConfigured()
                    ? 'Connecteur FIFA TMS désactivé dans FIT.'
                    : 'Bridge SDK FIFA TMS non configuré.',
            ];
        }

        try {
            $response = $this->client()->get('/transfers/'.rawurlencode($tmsTransferId));
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'code' => 'transport_error',
                'error' => 'Bridge TMS inaccessible.',
            ];
        }

        if (!$response->successful()) {
            return [
                'success' => false,
                'code' => 'provider_error',
                'error' => 'Le bridge TMS a refusé la requête.',
            ];
        }

        $data = $response->json();
        if (!is_array($data)) {
            return [
                'success' => false,
                'code' => 'invalid_response',
                'error' => 'Réponse TMS invalide.',
            ];
        }

        return [
            'success' => true,
            'data' => [
                'tms_transfer_id' => (string) ($data['tms_transfer_id'] ?? $tmsTransferId),
                'status' => isset($data['status']) ? (string) $data['status'] : null,
                'itc_status' => isset($data['itc_status']) ? (string) $data['itc_status'] : null,
                'itc_id' => isset($data['itc_id']) ? (string) $data['itc_id'] : null,
                'updated_at' => $data['updated_at'] ?? null,
            ],
        ];
    }

    private function client()
    {
        return Http::baseUrl(rtrim((string) config('services.fifa_tms.bridge_url'), '/'))
            ->withToken((string) config('services.fifa_tms.bridge_token'))
            ->acceptJson()
            ->timeout((int) config('services.fifa_tms.timeout', 15));
    }
}
