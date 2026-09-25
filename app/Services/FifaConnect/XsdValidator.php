<?php

namespace App\Services\FifaConnect;

use DOMDocument;
use RuntimeException;

class XsdValidator
{
    private const ROOT_SCHEMAS = [
        'PersonData' => 'scenarios.xsd',
        'PersonLocal' => 'scenarios.xsd',
        'OrganisationLocal' => 'scenarios.xsd',
        'FacilityLocal' => 'scenarios.xsd',
        'EventMessage' => 'scenarios.xsd',
        'CompetitionInternationalData' => 'scenarios.xsd',
        'CompetitionInternational' => 'scenarios.xsd',
        'MatchInternationalData' => 'scenarios.xsd',
        'MatchInternational' => 'scenarios.xsd',
        'Case' => 'discipline.xsd',
    ];

    public function validate(string $xml, ?string $schema = null): array
    {
        $previous = libxml_use_internal_errors(true);
        libxml_clear_errors();

        try {
            $document = new DOMDocument();
            $document->preserveWhiteSpace = false;

            if (!$document->loadXML($xml, LIBXML_NONET)) {
                return $this->failure('XML document is not well formed.');
            }

            $root = $document->documentElement?->localName;

            if (!$root) {
                return $this->failure('XML root element is missing.');
            }

            $schema ??= self::ROOT_SCHEMAS[$root] ?? null;

            if (!$schema) {
                return $this->failure(
                    "No FIFA Connect XSD mapping exists for root {$root}."
                );
            }

            $schemaPath = $this->schemaPath($schema);

            if (!is_file($schemaPath)) {
                return $this->failure(
                    "FIFA Connect XSD is not installed: {$schema}."
                );
            }

            $valid = $document->schemaValidate($schemaPath);

            if ($valid) {
                return [
                    'valid' => true,
                    'schema' => $schema,
                    'root' => $root,
                    'errors' => [],
                ];
            }

            return [
                'valid' => false,
                'schema' => $schema,
                'root' => $root,
                'errors' => $this->libxmlErrors(),
            ];
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
    }

    public function assertValid(string $xml, ?string $schema = null): void
    {
        $result = $this->validate($xml, $schema);

        if (!$result['valid']) {
            throw new RuntimeException(
                'FIFA Connect XML validation failed: '
                . implode(' | ', $result['errors'])
            );
        }
    }

    public function schemaPath(string $schema): string
    {
        $base = rtrim(
            (string) config(
                'services.fifa_connect.xsd_validation_path',
                config('services.fifa_connect.xsd_path')
            ),
            DIRECTORY_SEPARATOR
        );

        return $base . DIRECTORY_SEPARATOR . basename($schema);
    }

    private function failure(string $message): array
    {
        return [
            'valid' => false,
            'schema' => null,
            'root' => null,
            'errors' => [$message],
        ];
    }

    private function libxmlErrors(): array
    {
        return array_values(array_filter(array_map(
            static fn ($error) => trim($error->message),
            libxml_get_errors()
        )));
    }
}
