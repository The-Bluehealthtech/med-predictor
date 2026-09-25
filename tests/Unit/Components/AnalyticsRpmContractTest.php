<?php

namespace Tests\Unit\Components;

use PHPUnit\Framework\TestCase;

class AnalyticsRpmContractTest extends TestCase
{
    private function projectPath(string $relative): string
    {
        return dirname(__DIR__, 3) . '/' . $relative;
    }

    public function test_analytics_dashboard_uses_real_controller_and_scoped_alerts(): void
    {
        $routes = file_get_contents($this->projectPath('routes/web.php'));
        $controller = file_get_contents(
            $this->projectPath('app/Http/Controllers/AnalyticsDashboardController.php')
        );

        $this->assertStringContainsString(
            "AnalyticsDashboardController::class, 'index'",
            $routes
        );
        $this->assertStringContainsString('scopeAlerts', $controller);
        $this->assertStringContainsString('$user->isSystemAdmin()', $controller);
        $this->assertStringContainsString('$user->isClubUser()', $controller);
        $this->assertStringContainsString('$user->isAssociationUser()', $controller);
        $this->assertStringContainsString('$user->isPlayer()', $controller);
    }

    public function test_active_analytics_view_contains_no_fabricated_player_alerts(): void
    {
        $view = file_get_contents(
            $this->projectPath('resources/views/analytics/dashboard-canonical.blade.php')
        );

        $this->assertStringNotContainsString('Player ID: 123', $view);
        $this->assertStringNotContainsString('Risk score: 85%', $view);
        $this->assertStringNotContainsString('Player ID: 456', $view);
        $this->assertStringNotContainsString('Performance dropped 15%', $view);
        $this->assertStringContainsString('$alerts as $alert', $view);
    }

    public function test_rpm_uses_real_health_measurements_and_user_scope(): void
    {
        $routes = file_get_contents($this->projectPath('routes/web.php'));
        $controller = file_get_contents(
            $this->projectPath('app/Http/Controllers/RpmController.php')
        );

        $this->assertStringContainsString(
            "RpmController::class, 'index'",
            $routes
        );
        $this->assertStringContainsString('player_real_time_health as health', $controller);
        $this->assertStringContainsString('players.club_id', $controller);
        $this->assertStringContainsString('players.association_id', $controller);
        $this->assertStringContainsString('health.player_id', $controller);
    }

    public function test_rpm_view_does_not_claim_live_data_when_none_exists(): void
    {
        $view = file_get_contents(
            $this->projectPath('resources/views/rpm/index-canonical.blade.php')
        );

        $this->assertStringContainsString(
            "Aucune mesure temps réel n'est disponible",
            $view
        );
        $this->assertStringContainsString(
            'Le module ne simule aucune donnée.',
            $view
        );
        $this->assertStringNotContainsString('href="#"', $view);
    }
}
