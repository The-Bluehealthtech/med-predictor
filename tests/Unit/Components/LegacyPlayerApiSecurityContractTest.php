<?php

namespace Tests\Unit\Components;

use PHPUnit\Framework\TestCase;

class LegacyPlayerApiSecurityContractTest extends TestCase
{
    private function projectPath(string $relative): string
    {
        return dirname(__DIR__, 3) . '/' . $relative;
    }

    public function test_browser_player_api_group_requires_authentication(): void
    {
        $routes = file_get_contents($this->projectPath('routes/web.php'));

        $this->assertStringContainsString(
            "Route::prefix('api')->middleware(['auth'])->group(function () {",
            $routes
        );
    }

    public function test_api_player_group_requires_sanctum(): void
    {
        $routes = file_get_contents($this->projectPath('routes/api.php'));

        $this->assertStringContainsString(
            "Route::middleware(['auth:sanctum'])->prefix('players')->group(function () {",
            $routes
        );
    }

    public function test_legacy_player_api_does_not_fabricate_player_values(): void
    {
        $routes = file_get_contents($this->projectPath('routes/api.php'));
        $start = strpos($routes, "Route::middleware(['auth:sanctum'])->prefix('players')");
        $end = strpos($routes, "// Player information route", $start);

        $this->assertNotFalse($start);
        $this->assertNotFalse($end);

        $segment = substr($routes, $start, $end - $start);

        $this->assertStringNotContainsString('rand(', $segment);
        $this->assertStringNotContainsString('random_int(', $segment);
        $this->assertStringNotContainsString('Mock FIT Health System data', $segment);
    }
}
