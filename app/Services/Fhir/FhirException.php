<?php

namespace App\Services\Fhir;

use RuntimeException;

/** Échec d'un échange avec le serveur FHIR ; porte l'OperationOutcome renvoyé, s'il existe. */
final class FhirException extends RuntimeException
{
    public function __construct(string $message, int $status = 0, public readonly ?array $outcome = null)
    {
        parent::__construct($message, $status);
    }

    /** Diagnostics de l'OperationOutcome (sévérité, code, texte), sans données du patient. */
    public function issues(): array
    {
        return collect($this->outcome['issue'] ?? [])->map(fn ($i) => trim(($i['severity'] ?? '') . ' ' . ($i['code'] ?? '') . ' ' . ($i['diagnostics'] ?? '')))->all();
    }
}
