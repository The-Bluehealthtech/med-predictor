<?php

namespace App\Services\Documents;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

final class AdobeSignProvider
{
    public function isConfigured(): bool
    {
        return filled(config('services.adobe_sign.base_url')) && filled(config('services.adobe_sign.access_token'));
    }

    public function testConnection(): array
    {
        if (!$this->isConfigured()) return ['ok'=>false,'message'=>'Configuration Adobe Sign incomplète.'];
        try {
            $r = $this->client()->get($this->url('/baseUris'));
            return $r->successful() ? ['ok'=>true,'message'=>'Adobe Sign répond.'] : ['ok'=>false,'message'=>'Adobe Sign HTTP '.$r->status().'.'];
        } catch (\Throwable $e) {
            report($e);
            return ['ok'=>false,'message'=>'Adobe Sign injoignable.'];
        }
    }

    public function send(string $pdfBytes, string $fileName, string $agreementName, string $signerEmail): array
    {
        if (!$this->isConfigured()) return ['status'=>'not_configured'];
        try {
            $upload = $this->client()->attach('File', $pdfBytes, $fileName)->post($this->url('/transientDocuments'));
            if (!$upload->successful() || !$upload->json('transientDocumentId')) return ['status'=>'error','step'=>'upload','http_status'=>$upload->status()];
            $agreement = $this->client()->post($this->url('/agreements'), [
                'fileInfos'=>[['transientDocumentId'=>$upload->json('transientDocumentId')]],
                'name'=>$agreementName,
                'participantSetsInfo'=>[['memberInfos'=>[['email'=>$signerEmail]],'order'=>1,'role'=>'SIGNER']],
                'signatureType'=>'ESIGN','state'=>'IN_PROCESS',
            ]);
            if (!$agreement->successful() || !$agreement->json('id')) return ['status'=>'error','step'=>'agreement','http_status'=>$agreement->status()];
            return ['status'=>'sent','external_reference'=>(string) $agreement->json('id')];
        } catch (\Throwable $e) {
            report($e);
            return ['status'=>'error','step'=>'transport'];
        }
    }

    public function status(string $agreementId): array
    {
        if (!$this->isConfigured()) return ['status'=>'error','reason'=>'not_configured'];
        try {
            $r = $this->client()->get($this->url('/agreements/'.rawurlencode($agreementId)));
            if (!$r->successful()) return ['status'=>'error','http_status'=>$r->status()];
            return ['status'=>(string) $r->json('status')];
        } catch (\Throwable $e) {
            report($e);
            return ['status'=>'error','reason'=>'transport'];
        }
    }

    public function downloadSigned(string $agreementId): ?string
    {
        if (!$this->isConfigured()) return null;
        try {
            $r = $this->client()->get($this->url('/agreements/'.rawurlencode($agreementId).'/combinedDocument'));
            return $r->successful() ? $r->body() : null;
        } catch (\Throwable $e) {
            report($e);
            return null;
        }
    }

    private function client(): PendingRequest
    {
        return Http::timeout((int) config('services.adobe_sign.timeout',20))
            ->acceptJson()->withToken((string) config('services.adobe_sign.access_token'));
    }

    private function url(string $path): string
    {
        return rtrim((string) config('services.adobe_sign.base_url'), '/').$path;
    }
}
