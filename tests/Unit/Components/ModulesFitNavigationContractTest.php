<?php

namespace Tests\Unit\Components;

use PHPUnit\Framework\TestCase;

class ModulesFitNavigationContractTest extends TestCase
{
    private function projectPath(string $relative): string
    {
        return dirname(__DIR__, 3) . '/' . $relative;
    }

    public function test_modules_index_requires_authentication(): void
    {
        $routes = file_get_contents($this->projectPath('routes/web.php'));

        $start = strpos($routes, "Route::get('/modules', function () {");
        $this->assertNotFalse($start);

        $slice = substr($routes, $start, 16000);

        $this->assertStringContainsString(
            "->middleware(['auth'])->name('modules.index');",
            $slice
        );
    }

    public function test_fit_metrics_entry_is_declared_in_data_entry_modules(): void
    {
        $routes = file_get_contents($this->projectPath('routes/web.php'));

        // Outil de saisie, rangé dans « Données & saisie » plutôt que dans Analytics.
        $this->assertMatchesRegularExpression(
            "/'name' => 'Saisie des métriques FIT',.*?'route' => 'performances\.fit-metrics',.*?'category' => 'data_entry'/s",
            $routes
        );
        $view = file_get_contents($this->projectPath('resources/views/modules/index.blade.php'));
        $this->assertStringContainsString("'data_entry' => [", $view);
    }

    public function test_fit_card_uses_canonical_record_permission(): void
    {
        $view = file_get_contents(
            $this->projectPath('resources/views/modules/index.blade.php')
        );

        $this->assertStringContainsString(
            "'performances.fit-metrics' => auth()->check()",
            $view
        );
        $this->assertStringContainsString(
            "'record-performance-metrics'",
            $view
        );
        $this->assertStringContainsString(
            "'performances.fit-metrics': '/performances/fit-metrics'",
            $view
        );
    }

    public function test_fit_destination_route_uses_same_permission(): void
    {
        $routes = file_get_contents($this->projectPath('routes/web.php'));

        $this->assertStringContainsString(
            "'permission.unified:record-performance-metrics'",
            $routes
        );
    }
}
