<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class PerformanceAnalyticsSmokeTest extends TestCase
{
    use DatabaseTransactions;

    public function test_performance_analytics_pages(): void
    {
        Route::middleware('web')->group(base_path('routes/web.php'));
        Route::middleware('web')->group(base_path('routes/auth.php'));
        app('router')->getRoutes()->refreshNameLookups();
        app('router')->getRoutes()->refreshActionLookups();
        $user = User::factory()->create(['role' => 'system_admin']);
        $paths = ['/performance', '/performances', '/performances/analytics',
            '/performances/trends', '/analytics/dashboard', '/analytics/digital-twin',
            '/dataset-analytics', '/fifa/analytics', '/performance-recommendations'];
        $results = [];
        foreach ($paths as $path) {
            try {
                $response = $this->actingAs($user)->get($path);
                $base = $response->baseResponse;
                $error = property_exists($base, 'exception') ? $base->exception : null;
                $results[$path] = [$response->status(), $error?->getMessage(), $response->headers->get('Location')];
            } catch (\Throwable $error) {
                $results[$path] = ['EXCEPTION', $error->getMessage()];
            }
        }
        $this->assertCount(count($paths), $results);
        foreach ($paths as $path) {
            $this->assertSame(
                $path === '/performances/trends' ? 302 : 200,
                $results[$path][0],
                $path . ': ' . ($results[$path][1] ?? 'unknown error')
            );
        }
        $this->assertStringEndsWith(
            '/performances/analytics',
            $results['/performances/trends'][2]
        );
    }
}
