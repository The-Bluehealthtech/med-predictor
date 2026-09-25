<?php

namespace Tests\Unit\Components;

use PHPUnit\Framework\TestCase;

class TransferManagementContractTest extends TestCase
{
    private function projectPath(string $relative): string
    {
        return dirname(__DIR__, 3) . '/' . $relative;
    }

    public function test_canonical_transfer_controller_scopes_data_by_user_context(): void
    {
        $controller = file_get_contents(
            $this->projectPath('app/Http/Controllers/TransferController.php')
        );

        $this->assertStringContainsString('scopeTransfersForUser', $controller);
        $this->assertStringContainsString('$user->isSystemAdmin()', $controller);
        $this->assertStringContainsString('$user->isClubUser()', $controller);
        $this->assertStringContainsString('$user->isAssociationUser()', $controller);
        $this->assertStringContainsString('$user->isPlayer()', $controller);
        $this->assertStringContainsString(
            "where('transfer_status', 'approved')->count()",
            $controller
        );
    }

    public function test_transfer_mutations_require_management_authorization(): void
    {
        $controller = file_get_contents(
            $this->projectPath('app/Http/Controllers/TransferController.php')
        );

        foreach (['store', 'edit', 'update', 'destroy', 'submitToFifa', 'checkItcStatus'] as $method) {
            $start = strpos($controller, 'public function ' . $method);
            $this->assertNotFalse($start, $method);

            $segment = substr($controller, $start, 700);
            $this->assertStringContainsString(
                'authorizeTransferManagement()',
                $segment,
                $method
            );
        }
    }

    public function test_transfer_transaction_starts_after_eligibility_checks(): void
    {
        $controller = file_get_contents(
            $this->projectPath('app/Http/Controllers/TransferController.php')
        );

        $store = substr(
            $controller,
            strpos($controller, 'public function store'),
            strpos($controller, 'public function show')
                - strpos($controller, 'public function store')
        );

        $this->assertGreaterThan(
            strpos($store, 'is_transfer_eligible'),
            strpos($store, 'DB::beginTransaction()')
        );
        $this->assertGreaterThan(
            strpos($store, 'can_conduct_transfers'),
            strpos($store, 'DB::beginTransaction()')
        );
    }

    public function test_admin_transfer_dashboard_uses_real_transfer_model_without_fake_records(): void
    {
        $controller = file_get_contents(
            $this->projectPath('app/Http/Controllers/TransferManagementController.php')
        );

        $this->assertStringContainsString('use App\Models\Transfer;', $controller);
        $this->assertStringContainsString('scopedQuery()', $controller);
        $this->assertStringNotContainsString('rand(', $controller);
        $this->assertStringNotContainsString('Mohamed Salah', $controller);
        $this->assertStringNotContainsString('Ahmed Ben Ali', $controller);
        $this->assertStringNotContainsString("'status' => 'connected'", $controller);
    }

    public function test_legacy_transfer_routes_are_authenticated_redirects(): void
    {
        $routes = file_get_contents($this->projectPath('routes/web.php'));

        $this->assertStringContainsString(
            "->middleware(['auth'])->name('legacy.admin-transfer-management')",
            $routes
        );

        foreach (['domestic', 'international', 'loan', 'free-transfer'] as $path) {
            $start = strpos($routes, "Route::get('/admin/transfer-management/{$path}'");
            $this->assertNotFalse($start, $path);
            $segment = substr($routes, $start, 500);
            $this->assertStringContainsString("middleware(['auth'])", $segment);
        }
    }

    public function test_transfer_dashboard_javascript_has_no_simulated_type_statistics(): void
    {
        $view = file_get_contents(
            $this->projectPath('resources/views/admin/transfer-management/index.blade.php')
        );

        $this->assertStringNotContainsString('Simulate different stats', $view);
        $this->assertStringNotContainsString('total: 45', $view);
        $this->assertStringNotContainsString('total: 67', $view);
        $this->assertStringContainsString('Synchronisation TMS reportée', $view);
    }
}
