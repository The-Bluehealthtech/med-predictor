<?php

namespace Tests\Unit\Components;

use PHPUnit\Framework\TestCase;

class DtnContractTest extends TestCase
{
    private function projectPath(string $relative): string
    {
        return dirname(__DIR__, 3) . '/' . $relative;
    }

    public function test_dtn_uses_canonical_controller_and_real_models(): void
    {
        $routes = file_get_contents($this->projectPath('routes/web.php'));
        $controller = file_get_contents(
            $this->projectPath('app/Http/Controllers/DtnController.php')
        );

        $this->assertStringContainsString(
            "DtnController::class, 'index'",
            $routes
        );

        foreach ([
            'Player::query()->count()',
            'Club::query()->count()',
            'Team::query()->count()',
            'Competition::query()->count()',
            'PlayerPerformance::query()->count()',
        ] as $source) {
            $this->assertStringContainsString($source, $controller);
        }
    }

    public function test_dtn_access_is_limited_to_system_and_association_users(): void
    {
        $controller = file_get_contents(
            $this->projectPath('app/Http/Controllers/DtnController.php')
        );

        $this->assertStringContainsString(
            '$user->isSystemAdmin() || $user->isAssociationUser()',
            $controller
        );
    }

    public function test_active_dtn_view_has_real_navigation_not_fake_modals(): void
    {
        $view = file_get_contents(
            $this->projectPath('resources/views/dtn/index-canonical.blade.php')
        );

        $this->assertStringContainsString(
            "route('modules.players.index')",
            $view
        );
        $this->assertStringContainsString(
            "route('performances.analytics')",
            $view
        );
        $this->assertStringNotContainsString('manageFormation()', $view);
        $this->assertStringNotContainsString('modal.innerHTML', $view);
        $this->assertStringNotContainsString('Direction active', $view);
    }
}
