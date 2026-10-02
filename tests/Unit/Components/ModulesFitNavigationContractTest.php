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

        // Jusqu'au nom de la route, quelle que soit la longueur du catalogue des modules.
        $end = strpos($routes, "->name('modules.index');", $start);
        $this->assertNotFalse($end, 'route modules.index introuvable après sa déclaration');
        $declaration = substr($routes, $start, $end - $start);

        $this->assertSame(1, substr_count($declaration, 'Route::'), 'le nom appartient bien à la route /modules');
        $this->assertStringEndsWith("})->middleware(['auth'])", $declaration);
    }

    public function test_fit_metrics_entry_is_declared_in_performance_centre(): void
    {
        $routes = file_get_contents($this->projectPath('routes/web.php'));

        // Outil du staff sportif, rangé dans « Le centre de performance ».
        $this->assertMatchesRegularExpression(
            "/'name' => 'Saisie des métriques FIT',.*?'route' => 'performances\.fit-metrics',.*?'category' => 'performance'/s",
            $routes
        );
        $view = file_get_contents($this->projectPath('resources/views/modules/index.blade.php'));
        $this->assertStringContainsString("'performance' => [", $view);
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

    public function test_catalog_has_four_business_sections_and_no_known_duplicates(): void
    {
        $routes = file_get_contents($this->projectPath('routes/web.php'));
        $start = strpos($routes, "Route::get('/modules', function () {");
        $catalog = substr($routes, $start, strpos($routes, "->name('modules.index');", $start) - $start);

        preg_match_all("/'category' => '([a-z_]+)'/", $catalog, $categories);
        $this->assertSame(['administration', 'clinique', 'performance', 'selections'], array_values(array_unique(array_merge([], (function ($c) { sort($c); return $c; })(array_unique($categories[1]))))));

        // Doublons retirés du catalogue (les pages restent accessibles par leur adresse).
        foreach (['modules.referees.index', 'players.list', 'fifa.portal.integrated', 'fifa.analytics', 'clinical.patient-portal', 'clinical.clinician-portal'] as $route) {
            $this->assertStringNotContainsString("'route' => '{$route}'", $catalog);
        }
        preg_match_all("/'route' => '([^']+)'/", $catalog, $cards);
        $this->assertSame(count($cards[1]), count(array_unique($cards[1])), 'une seule carte par page');
    }
}
