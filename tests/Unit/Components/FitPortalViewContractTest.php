<?php

namespace Tests\Unit\Components;

use PHPUnit\Framework\TestCase;

class FitPortalViewContractTest extends TestCase
{
    public function test_fit_axes_use_latest_attempt_while_global_fit_uses_complete_snapshot(): void
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
                "'value' => \$latestFitAttempt?->{$field}",
                $view
            );

            $this->assertStringContainsString(
                "'{$field}' => \$latestFitAttempt->{$field}",
                $view
            );
        }
    }
}
