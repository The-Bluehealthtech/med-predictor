<?php

namespace Tests\Feature;

use App\Services\ApiConnectorState;
use App\Services\Documents\DocumentSignatureService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\{Http, Storage};
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

    public function test_adobe_sign_dispatch_and_signed_document_sync_are_persisted(): void
    {
        config(['services.adobe_sign.base_url' => 'https://api.adobesign.com/api/rest/v6', 'services.adobe_sign.access_token' => 'secret']);
        app(ApiConnectorState::class)->setEnabled('adobe_sign', true);
        Storage::fake('local');
        Http::fake([
            'api.adobesign.com/api/rest/v6/transientDocuments' => Http::response(['transientDocumentId' => 'transient-1']),
            'api.adobesign.com/api/rest/v6/agreements' => Http::response(['id' => 'agreement-1']),
            'api.adobesign.com/api/rest/v6/agreements/agreement-1' => Http::response(['status' => 'SIGNED']),
            'api.adobesign.com/api/rest/v6/agreements/agreement-1/combinedDocument' => Http::response('%PDF-signed', 200, ['Content-Type' => 'application/pdf']),
        ]);

        $service = app(DocumentSignatureService::class);
        $request = $service->createRequest('adobe_sign',
            ['type'=>'pcma_pdf','reference'=>'PCMA-873-v2','sha256'=>str_repeat('b',64),'bytes'=>'%PDF-original','filename'=>'PCMA-873.pdf','name'=>'PCMA 873'],
            ['type'=>'user','id'=>12,'role'=>'doctor','name'=>'Doctor','email'=>'doctor@example.test'],
            'pcma.final_document');

        $this->assertSame('sent', $request->status);
        $this->assertSame('agreement-1', $request->external_reference);
        $this->assertArrayNotHasKey('bytes', $request->metadata['document']);

        $signed = $service->sync($request);
        $this->assertSame('signed', $signed->status);
        $this->assertNotNull($signed->signed_at);
        $this->assertSame(hash('sha256', '%PDF-signed'), $signed->metadata['signed_sha256']);
        Storage::disk('local')->assertExists($signed->metadata['signed_path']);
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
