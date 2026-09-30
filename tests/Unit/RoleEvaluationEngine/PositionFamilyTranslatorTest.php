<?php

namespace Tests\Unit\RoleEvaluationEngine;

use App\Services\RoleEvaluationEngine\PositionFamilyTranslator;
use PHPUnit\Framework\TestCase;

/**
 * Régression pour la découverte du 30/09 : PerformanceScoreCalculator
 * compare la chaîne littérale anglaise "goalkeeper" en interne (non
 * modifié) ; utiliser "gardien" (français) tel quel cassait silencieusement
 * son traitement spécifique du gardien (voir RoleFitEvaluator, docblock).
 */
class PositionFamilyTranslatorTest extends TestCase
{
    public static function familyPairs(): array
    {
        return [
            ['défenseur central', 'central'],
            ['latéral', 'lateral'],
            ['milieu défensif', 'defensive_midfield'],
            ['milieu relayeur', 'central_midfield'],
            ['milieu offensif', 'attacking_midfield'],
            ['ailier', 'winger'],
            ['avant-centre', 'striker'],
            ['gardien', 'goalkeeper'],
        ];
    }

    /** @dataProvider familyPairs */
    public function test_translates_french_to_english(string $french, string $english): void
    {
        $this->assertSame($english, PositionFamilyTranslator::toEnglish($french));
    }

    /** @dataProvider familyPairs */
    public function test_translates_english_to_french(string $french, string $english): void
    {
        $this->assertSame($french, PositionFamilyTranslator::toFrench($english));
    }

    public function test_unknown_french_family_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        PositionFamilyTranslator::toEnglish('famille inconnue');
    }

    public function test_unknown_english_family_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        PositionFamilyTranslator::toFrench('unknown');
    }

    public function test_goalkeeper_is_never_silently_treated_as_a_field_family(): void
    {
        // Régression directe de la découverte : "gardien" ne doit jamais
        // traverser tel quel jusqu'au calculateur, où seule la chaîne
        // littérale "goalkeeper" déclenche son traitement spécifique.
        $this->assertSame('goalkeeper', PositionFamilyTranslator::toEnglish('gardien'));
        $this->assertNotSame('gardien', PositionFamilyTranslator::toEnglish('gardien'));
    }
}
