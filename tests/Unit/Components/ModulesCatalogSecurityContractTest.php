<?php

namespace Tests\Unit\Components;

use PHPUnit\Framework\TestCase;

class ModulesCatalogSecurityContractTest extends TestCase
{
    private function routes(): string
    {
        return file_get_contents(
            dirname(__DIR__, 3) . '/routes/web.php'
        );
    }

    /** Cartes de /modules : catalogue extrait dans config/fit_modules.php. */
    private function catalog(): string
    {
        return file_get_contents(dirname(__DIR__, 3) . '/config/fit_modules.php');
    }

    public function test_referee_card_uses_real_portal_route(): void
    {
        $routes = $this->catalog();

        $this->assertStringContainsString(
            "'route' => 'referee-portal.index'",
            $routes
        );
        $this->assertStringNotContainsString(
            "'route' => 'referee-dashboard-test'",
            $routes
        );
    }

    public function test_catalog_public_gaps_are_protected(): void
    {
        $routes = $this->routes();

        foreach ([
            'modules.players.index',
            'modules.confederations.index',
            'fifa.portal.integrated',
            'analytics.dashboard',
            'analytics.digital-twin',
            'dtn.index',
            'rpm.index',
            'portal.devices',
            'modules.finance.dashboard',
            'referee-portal.index',
        ] as $routeName) {
            $this->assertMatchesRegularExpression(
                "/middleware\(\['auth'\]\)->name\('"
                    . preg_quote($routeName, '/')
                    . "'\)/",
                $routes,
                $routeName
            );
        }
    }
}
