<?php

namespace Tests\Feature;

use App\Services\ApiConnectorState;
use App\Services\Documents\DocumentSignatureService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DocumentSignatureServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_document_signature_provider_must_be_configured_and_enabled(): void
    {
        config(['services.adobe_sign.base_url' => null, 'services.adobe_sign.access_token' => null]);
        $service = app(DocumentSignatureService::class);
        $this->assertSame('not_configured', $service->status('adobe_sign')['status']);

        config(['services.adobe_sign.base_url' => 'https://api.adobesign.com/api/rest/v6', 'services.adobe_sign.access_token' => 'secret']);
        $this->assertSame('disabled', $service->status('adobe_sign')['status']);
        app(ApiConnectorState::class)->setEnabled('adobe_sign', true);
        $this->assertSame('ready', $service->status('adobe_sign')['status']);
    }

    public function test_pcma_doctor_signature_request_is_audited_without_document_bytes(): void
    {
        config(['services.globalsign_dss.base_url' => 'https://dss.test', 'services.globalsign_dss.token' => 'secret']);
        app(ApiConnectorState::class)->setEnabled('globalsign_dss', true);
        $request = app(DocumentSignatureService::class)->createRequest(
            'globalsign_dss',
            ['type' => 'pcma_pdf', 'reference' => 'PCMA-873-v1', 'sha256' => str_repeat('a', 64), 'bytes' => 'sensitive-pdf-bytes'],
            ['type' => 'user', 'id' => 12, 'role' => 'doctor', 'name' => 'Doctor'],
            'pcma.final_document'
        );

        $this->assertSame('pcma.final_document', $request->workflow);
        $this->assertSame('doctor', $request->signer_role);
        $this->assertSame('pending', $request->status);
        $this->assertArrayNotHasKey('bytes', $request->metadata['document']);
    }
}
