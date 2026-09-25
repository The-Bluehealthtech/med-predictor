<?php

namespace Tests\Unit\Components;

use PHPUnit\Framework\TestCase;

class DigitalTwinContractTest extends TestCase
{
    private function projectPath(string $relative): string
    {
        return dirname(__DIR__, 3) . '/' . $relative;
    }

    public function test_digital_twin_uses_real_player_performance_baseline(): void
    {
        $routes = file_get_contents($this->projectPath('routes/web.php'));
        $controller = file_get_contents(
            $this->projectPath('app/Http/Controllers/DigitalTwinController.php')
        );

        $this->assertStringContainsString(
            "DigitalTwinController::class, 'index'",
            $routes
        );
        $this->assertStringContainsString(
            'PlayerPerformance::query()',
            $controller
        );
        $this->assertStringContainsString(
            "where('player_id', " . '$selectedPlayer->id' . ")",
            $controller
        );
        $this->assertStringContainsString(
            "'adjustment_pct' => 'required|numeric|min:-20|max:20'",
            $controller
        );
    }

    public function test_digital_twin_preserves_missing_values_and_clamps_scenario(): void
    {
        $controller = file_get_contents(
            $this->projectPath('app/Http/Controllers/DigitalTwinController.php')
        );

        $this->assertStringContainsString(
            '$value === null',
            $controller
        );
        $this->assertStringContainsString(
            '$this->clamp(',
            $controller
        );
        $this->assertStringContainsString(
            '0,',
            $controller
        );
        $this->assertStringContainsString(
            '100',
            $controller
        );
    }

    public function test_active_view_labels_scenario_as_simulation_not_observed_data(): void
    {
        $view = file_get_contents(
            $this->projectPath('resources/views/analytics/digital-twin-canonical.blade.php')
        );

        $this->assertStringContainsString(
            'scénarios simulés',
            $view
        );
        $this->assertStringContainsString(
            'pas le score FIT canonique',
            $view
        );
        $this->assertStringContainsString(
            "Aucune performance réelle n'est disponible",
            $view
        );
        $this->assertStringNotContainsString(
            'setTimeout(',
            $view
        );
        $this->assertStringNotContainsString(
            'Simulation terminée avec succès',
            $view
        );
    }
}
