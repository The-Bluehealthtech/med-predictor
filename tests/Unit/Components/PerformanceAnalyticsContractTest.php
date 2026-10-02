<?php

namespace Tests\Unit\Components;

use PHPUnit\Framework\TestCase;

/**
 * Analyse des performances : construite sur les feuilles de match (pas sur
 * les relevés player_performances à un point par joueur), avec des alertes
 * calculées et expliquées, des seuils explicites et aucune série figée.
 */
class PerformanceAnalyticsContractTest extends TestCase
{
    private function projectPath(string $relative): string
    {
        return dirname(__DIR__, 3) . '/' . $relative;
    }

    public function test_route_uses_canonical_analytics_controller(): void
    {
        $routes = file_get_contents($this->projectPath('routes/web.php'));

        $this->assertStringContainsString("PerformanceAnalyticsController::class, 'index'", $routes);
    }

    public function test_analytics_are_built_from_match_sheets_not_single_point_records(): void
    {
        $service = file_get_contents($this->projectPath('app/Services/Analytics/PlayerFormAnalytics.php'));
        $controller = file_get_contents($this->projectPath('app/Http/Controllers/PerformanceAnalyticsController.php'));

        foreach (['match_participations', 'player_match_detailed_stats', 'match_events'] as $table) {
            $this->assertStringContainsString("'{$table}", $service);
        }
        foreach ([$service, $controller] as $source) {
            $this->assertStringNotContainsString('PlayerPerformance::', $source);
            $this->assertStringNotContainsString('PerformanceAlert::', $source);
        }
        // Aucune donnée médicale dans l'analyse sportive.
        foreach (['health_records', 'pcmas', 'player_real_time_health', 'injuries'] as $table) {
            $this->assertStringNotContainsString("'{$table}'", $service);
        }
    }

    public function test_alerts_carry_a_reason_and_rankings_a_minimum_sample(): void
    {
        $service = file_get_contents($this->projectPath('app/Services/Analytics/PlayerFormAnalytics.php'));

        foreach (['form_drop', 'form_rise', 'high_load', 'minutes_drop', 'suspension_risk'] as $type) {
            $this->assertStringContainsString("'type' => '{$type}'", $service);
        }
        $this->assertSame(substr_count($service, "'type' => '"), substr_count($service, "'reason' => sprintf("), 'chaque alerte explique sa raison');
        $this->assertStringContainsString("\$p['matches'] >= \$minMatches", $service);
        $this->assertStringContainsString("\$p['minutes'] >= 270", $service);
    }

    public function test_active_view_has_no_hardcoded_chart_series(): void
    {
        $view = file_get_contents($this->projectPath('resources/views/modules/performances/analytics-canonical.blade.php'));

        $this->assertStringContainsString("@json(\$data['team_trend'])", $view);
        $this->assertStringContainsString('$alerts as $alert', $view);
        $this->assertDoesNotMatchRegularExpression('/data:\s*\[\s*\d/', $view, 'aucune série de valeurs écrite en dur');
    }
}
