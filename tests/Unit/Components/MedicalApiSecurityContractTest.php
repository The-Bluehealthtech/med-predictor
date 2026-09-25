<?php

namespace Tests\Unit\Components;

use PHPUnit\Framework\TestCase;

class MedicalApiSecurityContractTest extends TestCase
{
    private function projectPath(string $relative): string
    {
        return dirname(__DIR__, 3) . '/' . $relative;
    }

    public function test_medical_cli_api_requires_sanctum(): void
    {
        $routes = file_get_contents($this->projectPath('routes/api.php'));

        $this->assertStringContainsString(
            "Route::middleware('auth:sanctum')->prefix('modules/medical')->group(function () {",
            $routes
        );
        $this->assertStringContainsString(
            "Route::middleware('auth:sanctum')->prefix('modules/healthcare')->group(function () {",
            $routes
        );
    }

    public function test_pcma_api_duplicates_require_sanctum(): void
    {
        $routes = file_get_contents($this->projectPath('routes/api.php'));

        $this->assertStringContainsString(
            "->middleware('auth:sanctum')->name('api.pcma.pdf')",
            $routes
        );
        $this->assertStringContainsString(
            "->middleware('auth:sanctum')->name('api.pcma.store')",
            $routes
        );
    }

    public function test_medical_api_attributes_records_to_authenticated_user(): void
    {
        $routes = file_get_contents($this->projectPath('routes/api.php'));

        $this->assertStringNotContainsString("'user_id' => 1", $routes);
        $this->assertStringContainsString('$request->user()->id', $routes);
    }

    public function test_pcma_browser_form_uses_authenticated_web_routes(): void
    {
        $view = file_get_contents(
            $this->projectPath('resources/views/pcma/create.blade.php')
        );

        $this->assertStringContainsString('route("pcma.pdf.post")', $view);
        $this->assertStringContainsString('route("pcma.store")', $view);
        $this->assertStringNotContainsString("fetch('/api/pcma/store'", $view);
        $this->assertStringNotContainsString("fetch('/api/pcma/pdf'", $view);
    }

    public function test_signed_pcmas_endpoint_requires_authentication(): void
    {
        $routes = file_get_contents($this->projectPath('routes/web.php'));

        $start = strpos($routes, "Route::get('/api/signed-pcmas'");
        $this->assertNotFalse($start);

        $segment = substr($routes, $start, 2500);

        $this->assertStringContainsString(
            "->middleware(['auth'])->name('api.signed-pcmas')",
            $segment
        );
    }
}
