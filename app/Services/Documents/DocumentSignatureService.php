<?php

namespace App\Services\Documents;

use App\Models\DocumentSignatureRequest;
use App\Services\ApiConnectorState;
use InvalidArgumentException;

final class DocumentSignatureService
{
    public function __construct(
        private readonly ApiConnectorState $state,
        private readonly AdobeSignProvider $adobeSign,
    ) {
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

        $request = DocumentSignatureRequest::query()->create([
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

        if ($provider === 'adobe_sign') {
            $this->dispatchAdobe($request, $document, $signer);
        }

        return $request->fresh();
    }

    public function sync(DocumentSignatureRequest $request): DocumentSignatureRequest
    {
        if ($request->provider !== 'adobe_sign' || blank($request->external_reference)) {
            return $request;
        }

        $result = $this->adobeSign->status((string) $request->external_reference);
        if (($result['status'] ?? null) === 'error') {
            return $request;
        }

        $adobeStatus = strtoupper((string) ($result['status'] ?? ''));
        $mapped = match ($adobeStatus) {
            'SIGNED', 'APPROVED' => 'signed',
            'OUT_FOR_SIGNATURE', 'OUT_FOR_APPROVAL', 'IN_PROCESS' => 'sent',
            'CANCELLED', 'EXPIRED', 'ABORTED' => 'cancelled',
            default => $request->status,
        };
        $metadata = $request->metadata ?? [];
        $metadata['provider_status'] = $adobeStatus;
        $metadata['last_synced_at'] = now()->toIso8601String();

        if ($mapped === 'signed' && empty($metadata['signed_path'])) {
            $bytes = $this->adobeSign->downloadSigned((string) $request->external_reference);
            if (is_string($bytes) && $bytes !== '') {
                $path = 'document-signatures/'.$request->id.'/signed.pdf';
                \Illuminate\Support\Facades\Storage::disk('local')->put($path, $bytes);
                $metadata['signed_path'] = $path;
                $metadata['signed_sha256'] = hash('sha256', $bytes);
            }
        }

        $request->update([
            'status' => $mapped,
            'signed_at' => $mapped === 'signed' ? ($request->signed_at ?: now()) : $request->signed_at,
            'metadata' => $metadata,
        ]);

        return $request->fresh();
    }

    private function dispatchAdobe(DocumentSignatureRequest $request, array $document, array $signer): void
    {
        if (blank($document['bytes'] ?? null) || blank($signer['email'] ?? null)) {
            $request->update(['status' => 'error']);
            return;
        }

        $result = $this->adobeSign->send(
            (string) $document['bytes'],
            (string) ($document['filename'] ?? ($document['reference'].'.pdf')),
            (string) ($document['name'] ?? $document['reference']),
            (string) $signer['email']
        );
        $metadata = $request->metadata ?? [];
        $metadata['dispatch'] = array_diff_key($result, ['raw' => true]);
        $request->update([
            'status' => $result['status'] ?? 'error',
            'external_reference' => $result['external_reference'] ?? null,
            'metadata' => $metadata,
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
