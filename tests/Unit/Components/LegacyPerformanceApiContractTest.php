<?php

namespace Tests\Unit\Components;

use PHPUnit\Framework\TestCase;

class LegacyPerformanceApiContractTest extends TestCase
{
    private function projectPath(string $relative): string
    {
        return dirname(__DIR__, 3) . '/' . $relative;
    }

    public function test_legacy_portal_api_is_authenticated_and_contains_no_random_data(): void
    {
        $routes = file_get_contents($this->projectPath('routes/api.php'));

        $start = strpos(
            $routes,
            "Route::middleware('auth:sanctum')->prefix('portal')"
        );
        $this->assertNotFalse($start);

        $end = strpos($routes, '// FIT V3', $start);
        $this->assertNotFalse($end);

        $block = substr($routes, $start, $end - $start);

        $this->assertStringContainsString("'status' => 'deprecated'", $block);
        $this->assertStringContainsString('410', $block);
        $this->assertStringNotContainsString('rand(', $block);
        $this->assertStringNotContainsString('Math.random', $block);
    }

    public function test_legacy_player_performance_endpoint_is_not_backed_by_synthetic_controller(): void
    {
        $routes = file_get_contents($this->projectPath('routes/web.php'));

        $start = strpos($routes, "Route::get('/api/player-performance/{id}'");
        $this->assertNotFalse($start);

        $segment = substr($routes, $start, 700);

        $this->assertStringContainsString("middleware(['auth'])", $segment);
        $this->assertStringContainsString("'status' => 'deprecated'", $segment);
        $this->assertStringContainsString('410', $segment);
        $this->assertStringNotContainsString('RealFIFAController', $segment);
    }
}
