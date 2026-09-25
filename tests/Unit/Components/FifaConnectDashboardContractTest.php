<?php

namespace Tests\Unit\Components;

use PHPUnit\Framework\TestCase;

class FifaConnectDashboardContractTest extends TestCase
{
    private function projectPath(string $relative): string
    {
        return dirname(__DIR__, 3) . '/' . $relative;
    }

    public function test_dashboard_uses_canonical_controller_instead_of_simulated_route_data(): void
    {
        $routes = file_get_contents($this->projectPath('routes/web.php'));

        $this->assertStringContainsString(
            "FifaConnectDashboardController::class, 'index'",
            $routes
        );
        $this->assertStringContainsString(
            "FifaConnectDashboardController::class, 'status'",
            $routes
        );
    }

    public function test_dashboard_controller_uses_real_models_and_fifa_service(): void
    {
        $controller = file_get_contents(
            $this->projectPath(
                'app/Http/Controllers/FifaConnectDashboardController.php'
            )
        );

        $this->assertStringContainsString(
            'FifaConnectService $fifaConnectService',
            $controller
        );
        $this->assertStringContainsString(
            "whereNotNull('fifa_connect_id')",
            $controller
        );
        $this->assertStringContainsString(
            "where('fifa_sync_status', 'synced')",
            $controller
        );
        $this->assertStringNotContainsString(
            "'connected' => true",
            $controller
        );
    }

    public function test_connectivity_command_is_registered(): void
    {
        $kernel = file_get_contents(
            $this->projectPath('app/Console/Kernel.php')
        );

        $this->assertStringContainsString(
            'TestFifaConnectivity::class',
            $kernel
        );
    }
}
