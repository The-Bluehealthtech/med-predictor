<?php

namespace Tests\Unit\RoleEvaluationImport;

use App\Services\RoleEvaluationImport\ImportMapping;
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 * Tests unitaires purs du chargement/validation du fichier de correspondance
 * et de la détection des jetons "Données non disponibles".
 */
class ImportMappingTest extends TestCase
{
    private function baseMappingArray(): array
    {
        return [
            'type' => 'participations',
            'source_label' => 'Test',
            'columns' => [
                ['source' => 'poste', 'target' => 'detailed_position', 'kind' => 'position_code'],
            ],
        ];
    }

    public function test_valid_mapping_loads_successfully(): void
    {
        $mapping = ImportMapping::fromArray($this->baseMappingArray());
        $this->assertSame('participations', $mapping->type);
        $this->assertSame('match_participations', $mapping->table);
    }

    public function test_unknown_type_is_rejected(): void
    {
        $this->expectException(RuntimeException::class);
        ImportMapping::fromArray(array_merge($this->baseMappingArray(), ['type' => 'inconnu']));
    }

    public function test_missing_columns_is_rejected(): void
    {
        $this->expectException(RuntimeException::class);
        ImportMapping::fromArray(array_merge($this->baseMappingArray(), ['columns' => []]));
    }

    public function test_column_with_unknown_kind_is_rejected(): void
    {
        $data = $this->baseMappingArray();
        $data['columns'][0]['kind'] = 'kind-qui-nexiste-pas';

        $this->expectException(RuntimeException::class);
        ImportMapping::fromArray($data);
    }

    public function test_default_not_available_tokens_are_recognized_case_insensitively(): void
    {
        $mapping = ImportMapping::fromArray($this->baseMappingArray());

        $this->assertTrue($mapping->isNotAvailable('Données non disponibles'));
        $this->assertTrue($mapping->isNotAvailable('DONNÉES NON DISPONIBLES'));
        $this->assertTrue($mapping->isNotAvailable('N/A'));
        $this->assertTrue($mapping->isNotAvailable(''));
        $this->assertTrue($mapping->isNotAvailable(null));
        $this->assertFalse($mapping->isNotAvailable('0'));
        $this->assertFalse($mapping->isNotAvailable('CDM'));
    }

    public function test_custom_not_available_tokens_override_the_default_list(): void
    {
        $data = $this->baseMappingArray();
        $data['not_available_tokens'] = ['inconnu'];
        $mapping = ImportMapping::fromArray($data);

        $this->assertTrue($mapping->isNotAvailable('inconnu'));
        $this->assertTrue($mapping->isNotAvailable('Inconnu')); // insensible à la casse
        // "N/A" n'est plus dans la liste personnalisée : ce n'est donc PAS
        // traité comme "non disponible" ici (comportement attendu : la
        // configuration du mapping fait autorité).
        $this->assertFalse($mapping->isNotAvailable('N/A'));
    }
}
