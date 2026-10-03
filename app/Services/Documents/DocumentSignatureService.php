<?php

namespace App\Services\Documents;

use App\Models\DocumentSignatureRequest;
use App\Services\ApiConnectorState;
use InvalidArgumentException;

final class DocumentSignatureService
{
    public function __construct(private readonly ApiConnectorState $state)
    {
    }

    public function providers(): array
    {
        return [
            'signotec_document' => [
                'name' => 'signotec signoSign/Universal',
                'configured' => filled(config('services.signotec_document.base_url')) && filled(config('services.signotec_document.instance_token')),
                'variables' => ['SIGNOTEC_DOCUMENT_BASE_URL', 'SIGNOTEC_DOCUMENT_INSTANCE_TOKEN'],
                'capabilities' => ['handwritten', 'remote', 'advanced', 'qualified'],
            ],
            'adobe_sign' => [
                'name' => 'Adobe Acrobat Sign',
                'configured' => filled(config('services.adobe_sign.base_url')) && filled(config('services.adobe_sign.access_token')),
                'variables' => ['ADOBE_SIGN_BASE_URL', 'ADOBE_SIGN_ACCESS_TOKEN'],
                'capabilities' => ['remote', 'agreement', 'audit_trail'],
            ],
            'globalsign_dss' => [
                'name' => 'GlobalSign DSS',
                'configured' => filled(config('services.globalsign_dss.base_url')) && filled(config('services.globalsign_dss.token')),
                'variables' => ['GLOBALSIGN_DSS_BASE_URL', 'GLOBALSIGN_DSS_TOKEN'],
                'capabilities' => ['pdf_digital_signature', 'timestamp', 'ltv'],
            ],
        ];
    }

    public function status(string $provider): array
    {
        $item = $this->provider($provider);
        $enabled = $this->state->enabled($provider, false);
        $status = !$item['configured'] ? 'not_configured' : ($enabled ? 'ready' : 'disabled');

        return $item + [
            'slug' => $provider,
            'enabled' => $enabled,
            'status' => $status,
            'label' => match ($status) {
                'ready' => 'Activé',
                'disabled' => 'Configuré · désactivé',
                default => 'Configuration requise',
            },
        ];
    }

    public function allStatuses(): array
    {
        return collect(array_keys($this->providers()))->map(fn ($slug) => $this->status($slug))->all();
    }

    public function assertAvailable(string $provider): array
    {
        $status = $this->status($provider);
        if (!$status['configured']) {
            throw new InvalidArgumentException($status['name'] . ' n’est pas configuré.');
        }
        if (!$status['enabled']) {
            throw new InvalidArgumentException($status['name'] . ' est désactivé.');
        }

        return $status;
    }

    public function createRequest(string $provider, array $document, array $signer, string $workflow): DocumentSignatureRequest
    {
        $status = $this->assertAvailable($provider);
        foreach (['type', 'reference'] as $field) {
            if (blank($document[$field] ?? null)) {
                throw new InvalidArgumentException('Document signature: champ ' . $field . ' requis.');
            }
        }
        if (blank($signer['type'] ?? null)) {
            throw new InvalidArgumentException('Document signature: signer.type requis.');
        }

        return DocumentSignatureRequest::query()->create([
            'provider' => $provider,
            'workflow' => $workflow,
            'document_type' => $document['type'],
            'document_reference' => $document['reference'],
            'signer_type' => $signer['type'],
            'signer_id' => $signer['id'] ?? null,
            'signer_role' => $signer['role'] ?? null,
            'signer_name' => $signer['name'] ?? null,
            'signer_email' => $signer['email'] ?? null,
            'status' => 'pending',
            'metadata' => [
                'provider_name' => $status['name'],
                'provider_capabilities' => $status['capabilities'],
                'document' => array_diff_key($document, ['content' => true, 'bytes' => true]),
            ],
            'requested_at' => now(),
        ]);
    }

    private function provider(string $provider): array
    {
        $item = $this->providers()[$provider] ?? null;
        if (!$item) {
            throw new InvalidArgumentException('Fournisseur de signature inconnu : ' . $provider);
        }

        return $item;
    }
}
