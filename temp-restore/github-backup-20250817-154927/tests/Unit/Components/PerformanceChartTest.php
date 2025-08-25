<?php

namespace Tests\Unit\Components;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\View;

class PerformanceChartTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_renders_performance_chart_component()
    {
        // Arrange
        $chartData = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr'],
            'datasets' => [
                [
                    'label' => 'Distance Covered',
                    'data' => [8500, 9200, 8800, 9500],
                    'borderColor' => '#3B82F6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)'
                ]
            ]
        ];

        // Act
        $response = $this->get('/test-performance-chart', [
            'chartData' => $chartData,
            'chartType' => 'line',
            'title' => 'Performance Trends'
        ]);

        // Assert
        $response->assertStatus(200);
        $response->assertSee('PerformanceChart');
        $response->assertSee('Performance Trends');
    }

    /** @test */
    public function it_handles_chart_type_changes()
    {
        // Arrange
        $chartTypes = ['line', 'bar', 'radar', 'doughnut'];

        foreach ($chartTypes as $type) {
            // Act
            $response = $this->get('/test-performance-chart', [
                'chartType' => $type
            ]);

            // Assert
            $response->assertStatus(200);
            $response->assertSee($type);
        }
    }

    /** @test */
    public function it_updates_chart_data_dynamically()
    {
        // Arrange
        $initialData = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            'datasets' => [
                [
                    'label' => 'Performance Score',
                    'data' => [65, 59, 80, 81, 56, 55]
                ]
            ]
        ];

        $updatedData = [
            'labels' => ['Jan', 'Feb', 'Mar'],
            'datasets' => [
                [
                    'label' => 'Distance',
                    'data' => [8000, 8500, 9000]
                ]
            ]
        ];

        // Act & Assert
        $response1 = $this->get('/test-performance-chart?' . http_build_query(['chartData' => json_encode($initialData)]));
        $response1->assertStatus(200);

        $response2 = $this->get('/test-performance-chart?' . http_build_query(['chartData' => json_encode($updatedData)]));
        $response2->assertStatus(200);
        
        // Debug: Let's see what's actually in the response
        $content = $response2->getContent();
        if (strpos($content, '9000') === false) {
            // If 9000 is not found, let's see what data values are actually there
            preg_match_all('/<span class="data-value">([^<]+)<\/span>/', $content, $matches);
            $foundValues = $matches[1] ?? [];
            $this->fail("Expected '9000' not found. Found data values: " . implode(', ', $foundValues) . "\nFull content: " . substr($content, 0, 1000));
        }
        
        $response2->assertSee('9000'); // New data point
    }

    /** @test */
    public function it_handles_empty_chart_data()
    {
        // Arrange
        $emptyData = [
            'labels' => [],
            'datasets' => []
        ];

        // Act
        $response = $this->get('/test-performance-chart?' . http_build_query([
            'chartData' => json_encode($emptyData)
        ]));

        // Assert
        $response->assertStatus(200);
        $response->assertSee('No data available');
    }

    /** @test */
    public function it_applies_custom_styling_options()
    {
        // Arrange
        $chartData = [
            'labels' => ['Jan', 'Feb'],
            'datasets' => [
                [
                    'label' => 'Performance',
                    'data' => [80, 85],
                    'borderColor' => '#10B981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)'
                ]
            ]
        ];

        $options = [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'position' => 'top'
                ]
            ]
        ];

        // Act
        $response = $this->get('/test-performance-chart?' . http_build_query([
            'chartData' => json_encode($chartData),
            'options' => json_encode($options)
        ]));

        // Assert
        $response->assertStatus(200);
        $response->assertSee('responsive');
        $response->assertSee('maintainAspectRatio');
    }

    /** @test */
    public function it_handles_time_range_selection()
    {
        // Arrange
        $timeRanges = ['7d', '30d', '90d', '1y'];

        foreach ($timeRanges as $range) {
            // Act
            $response = $this->get('/test-performance-chart?' . http_build_query([
                'timeRange' => $range
            ]));

            // Assert
            $response->assertStatus(200);
            $response->assertSee($range);
        }
    }

    /** @test */
    public function it_displays_chart_legend_correctly()
    {
        // Arrange
        $chartData = [
            'labels' => ['Jan', 'Feb'],
            'datasets' => [
                [
                    'label' => 'Distance Covered',
                    'data' => [8000, 8500]
                ],
                [
                    'label' => 'Sprint Count',
                    'data' => [20, 25]
                ]
            ]
        ];

        // Act
        $response = $this->get('/test-performance-chart?' . http_build_query([
            'chartData' => json_encode($chartData),
            'showLegend' => true
        ]));

        // Assert
        $response->assertStatus(200);
        $response->assertSee('Distance Covered');
        $response->assertSee('Sprint Count');
    }

    /** @test */
    public function it_handles_chart_click_events()
    {
        // Arrange
        $chartData = [
            'labels' => ['Jan', 'Feb'],
            'datasets' => [
                [
                    'label' => 'Performance',
                    'data' => [80, 85]
                ]
            ]
        ];

        // Act
        $response = $this->post('/test-performance-chart-click', [
            'chartData' => json_encode($chartData),
            'clickedPoint' => json_encode(['index' => 0, 'value' => 80])
        ]);

        // Assert
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    /** @test */
    public function it_validates_chart_data_format()
    {
        // Arrange
        $invalidData = [
            'labels' => 'Invalid format', // Should be array
            'datasets' => 'Invalid format' // Should be array
        ];

        // Act
        $response = $this->get('/test-performance-chart?' . http_build_query([
            'chartData' => json_encode($invalidData)
        ]));

        // Assert
        $response->assertStatus(422); // Validation error
    }

    /** @test */
    public function it_handles_chart_loading_states()
    {
        // Arrange
        $loadingState = true;

        // Act
        $response = $this->get('/test-performance-chart', [
            'loading' => $loadingState
        ]);

        // Assert
        $response->assertStatus(200);
        $response->assertSee('Loading');
    }

    /** @test */
    public function it_applies_theme_colors()
    {
        // Arrange
        $themes = ['light', 'dark'];

        foreach ($themes as $theme) {
            // Act
            $response = $this->get('/test-performance-chart?' . http_build_query([
                'theme' => $theme
            ]));

            // Assert
            $response->assertStatus(200);
            $response->assertSee($theme);
        }
    }
} 