<?php

namespace App\Services\Documents;

interface DocumentSignatureProvider
{
    public function slug(): string;
    public function isConfigured(): bool;
    public function isEnabled(): bool;
    public function status(): array;
    public function requestSignature(array $payload): array;
    public function testConnection(): array;
}
