<?php

namespace Tests\Unit\Components;

use PHPUnit\Framework\TestCase;

class FitPortalViewContractTest extends TestCase
{
    public function test_fit_cards_and_radar_use_the_same_complete_snapshot(): void
    {
        $viewPath = dirname(__DIR__, 3)
            . '/resources/views/test-portail-joueur-simple.blade.php';

        $view = file_get_contents($viewPath);

        $this->assertNotFalse($view);

        $this->assertStringContainsString(
            "'value' => \$latestFitSnapshot?->fit_score",
            $view
        );

        foreach ([
            'physical_score',
            'technical_score',
            'tactical_score',
            'mental_score',
            'social_score',
        ] as $field) {
            $this->assertStringContainsString(
                "'value' => \$latestFitSnapshot?->{$field}",
                $view
            );

            $this->assertStringContainsString(
                "'{$field}' => \$latestFitSnapshot->{$field}",
                $view
            );
        }
    }
}
