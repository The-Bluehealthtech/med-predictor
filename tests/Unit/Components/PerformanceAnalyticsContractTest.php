<?php

namespace Tests\Unit\Components;

use PHPUnit\Framework\TestCase;

class PerformanceAnalyticsContractTest extends TestCase
{
    private function projectPath(string $relative): string
    {
        return dirname(__DIR__, 3) . '/' . $relative;
    }

    public function test_route_uses_canonical_analytics_controller(): void
    {
        $routes = file_get_contents($this->projectPath('routes/web.php'));

        $this->assertStringContainsString(
            "PerformanceAnalyticsController::class, 'index'",
            $routes
        );
    }

    public function test_controller_uses_real_player_performance_columns(): void
    {
        $controller = file_get_contents(
            $this->projectPath('app/Http/Controllers/PerformanceAnalyticsController.php')
        );

        foreach ([
            'overall_performance_score',
            'physical_score',
            'technical_score',
            'tactical_score',
            'mental_score',
            'social_score',
            'passing_accuracy',
            'shooting_accuracy',
            'goals',
            'assists',
            'minutes_played',
        ] as $column) {
            $this->assertStringContainsString($column, $controller);
        }

        foreach ([
            'distance_covered',
            'goals_scored',
            'pass_accuracy',
            'max_speed',
            'sprints_count',
            'passes_completed',
            'shots_on_target',
            'key_passes',
            'tackles_won',
            'interceptions',
            'duels_won',
        ] as $forbidden) {
            $this->assertStringNotContainsString($forbidden, $controller);
        }
    }

    public function test_active_view_has_no_hardcoded_chart_series(): void
    {
        $view = file_get_contents(
            $this->projectPath(
                'resources/views/modules/performances/analytics-canonical.blade.php'
            )
        );

        $this->assertStringContainsString('@json($trend[', $view);
        $this->assertStringContainsString('@json($topPerformers[', $view);
        $this->assertStringNotContainsString(
            'data: [7.2, 7.5, 7.8, 7.6]',
            $view
        );
        $this->assertStringNotContainsString(
            'data: [8.5, 9.2, 8.8, 9.1]',
            $view
        );
    }
}
