<?php

namespace App\Services\FifaConnect;

use DOMDocument;
use DOMXPath;
use RuntimeException;

class SchemaCatalog
{
    private array $documents = [];

    public function enumValues(string $type): array
    {
        $node = $this->simpleType($type);

        $xpath = new DOMXPath($node->ownerDocument);
        $xpath->registerNamespace('xs', 'http://www.w3.org/2001/XMLSchema');

        $values = [];

        foreach ($xpath->query('.//xs:enumeration', $node) as $enum) {
            $values[] = $enum->getAttribute('value');
        }

        return $values;
    }

    public function pattern(string $type): ?string
    {
        $node = $this->simpleType($type);

        $xpath = new DOMXPath($node->ownerDocument);
        $xpath->registerNamespace('xs', 'http://www.w3.org/2001/XMLSchema');

        $pattern = $xpath->query('.//xs:pattern', $node)->item(0);

        return $pattern?->getAttribute('value') ?: null;
    }

    public function length(string $type): ?int
    {
        $node = $this->simpleType($type);

        $xpath = new DOMXPath($node->ownerDocument);
        $xpath->registerNamespace('xs', 'http://www.w3.org/2001/XMLSchema');

        $length = $xpath->query('.//xs:length', $node)->item(0);

        return $length ? (int) $length->getAttribute('value') : null;
    }

    public function assertEnum(string $type, ?string $value, bool $nullable = false): void
    {
        if ($value === null && $nullable) {
            return;
        }

        $allowed = $this->enumValues($type);

        if (!in_array($value, $allowed, true)) {
            throw new RuntimeException(
                "{$type} value is invalid: " . var_export($value, true)
            );
        }
    }

    public function assertFifaIdentifier(string $value): void
    {
        $length = $this->length('FIFAIdentifier');
        $pattern = $this->pattern('FIFAIdentifier');

        if ($length !== null && strlen($value) !== $length) {
            throw new RuntimeException('FIFAIdentifier must be exactly 7 characters.');
        }

        if ($pattern !== null && !preg_match('~' . $pattern . '~', $value)) {
            throw new RuntimeException(
                'FIFAIdentifier does not satisfy FIFA Connect Data 3.3.'
            );
        }
    }

    private function simpleType(string $name): \DOMElement
    {
        foreach ($this->schemaFiles() as $schema) {
            $document = $this->document($schema);
            $xpath = new DOMXPath($document);
            $xpath->registerNamespace('xs', 'http://www.w3.org/2001/XMLSchema');

            $nodes = $xpath->query(
                '/xs:schema/xs:simpleType[@name="' . $name . '"]'
            );

            if ($nodes->length === 1) {
                return $nodes->item(0);
            }
        }

        throw new RuntimeException(
            "FIFA Connect simpleType {$name} was not found in installed XSDs."
        );
    }

    private function schemaFiles(): array
    {
        return [
            'generic.xsd',
            'registration.xsd',
            'competition.xsd',
            'discipline.xsd',
            'scenarios.xsd',
            'includes/iso639-2-language-code.xsd',
            'includes/iso3166-country-code.xsd',
            'includes/iso3166-13-country-code.xsd',
            'includes/iso4217-currency-code.xsd',
        ];
    }

    private function document(string $schema): DOMDocument
    {
        if (isset($this->documents[$schema])) {
            return $this->documents[$schema];
        }

        $path = rtrim(
            (string) config('services.fifa_connect.xsd_path'),
            DIRECTORY_SEPARATOR
        ) . DIRECTORY_SEPARATOR . $schema;

        if (!is_file($path)) {
            throw new RuntimeException(
                "FIFA Connect XSD is not installed: {$schema}."
            );
        }

        $document = new DOMDocument();

        if (!$document->load($path, LIBXML_NONET)) {
            throw new RuntimeException(
                "Unable to parse FIFA Connect XSD: {$schema}."
            );
        }

        return $this->documents[$schema] = $document;
    }
}
