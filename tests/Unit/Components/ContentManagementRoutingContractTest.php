<?php

namespace Tests\Unit\Components;

use PHPUnit\Framework\TestCase;

class ContentManagementRoutingContractTest extends TestCase
{
    public function test_canonical_content_route_is_not_overridden_by_legacy_alias(): void
    {
        $routes = file_get_contents(
            dirname(__DIR__, 3) . '/routes/web.php'
        );

        $this->assertStringContainsString(
            "Route::get('/', [App\\Http\\Controllers\\ContentManagementController::class, 'index'])->name('index');",
            $routes
        );

        $this->assertStringContainsString(
            "->name('legacy.admin-content-management');",
            $routes
        );

        $this->assertStringNotContainsString(
            "return view('admin.content-management.index');
})->middleware(['auth'])->name('admin.content-management.index');",
            $routes
        );
    }
}
