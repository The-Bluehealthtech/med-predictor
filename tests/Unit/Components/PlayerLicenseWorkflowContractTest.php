<?php

namespace Tests\Unit\Components;

use PHPUnit\Framework\TestCase;

class PlayerLicenseWorkflowContractTest extends TestCase
{
    private function projectPath(string $relative): string
    {
        return dirname(__DIR__, 3) . '/' . $relative;
    }

    public function test_player_license_model_uses_real_database_columns(): void
    {
        $model = file_get_contents(
            $this->projectPath('app/Models/PlayerLicense.php')
        );

        foreach ([
            "'contract_start_date'",
            "'contract_end_date'",
            "'expiry_date'",
            "'issue_date'",
            "'issued_date'",
            "'requested_by'",
        ] as $column) {
            $this->assertStringContainsString($column, $model);
        }

        $fillableStart = strpos($model, 'protected $fillable');
        $castsStart = strpos($model, 'protected $casts');
        $fillable = substr($model, $fillableStart, $castsStart - $fillableStart);

        $this->assertStringNotContainsString("'start_date'", $fillable);
        $this->assertStringNotContainsString("'end_date'", $fillable);
        $this->assertStringNotContainsString("'issued_at'", $fillable);
    }

    public function test_modules_license_workflow_is_player_id_based(): void
    {
        $routes = file_get_contents($this->projectPath('routes/web.php'));
        $controller = file_get_contents(
            $this->projectPath('app/Http/Controllers/PlayerLicenseWorkflowController.php')
        );

        $this->assertStringContainsString(
            "PlayerLicenseWorkflowController::class, 'index'",
            $routes
        );
        $this->assertStringContainsString(
            "PlayerLicenseWorkflowController::class, 'create'",
            $routes
        );
        $this->assertStringContainsString(
            "PlayerLicenseWorkflowController::class, 'store'",
            $routes
        );

        $this->assertStringContainsString(
            "'player_id' => " . '$player->id',
            $controller
        );
        $this->assertStringContainsString(
            "'status' => 'pending'",
            $controller
        );
        $this->assertStringContainsString(
            "'approval_status' => 'pending'",
            $controller
        );
    }

    public function test_license_module_button_uses_license_workflow_not_player_registration(): void
    {
        $view = file_get_contents(
            $this->projectPath('resources/views/modules/licenses/index.blade.php')
        );

        $this->assertStringContainsString(
            "route('player-licenses.request.create', " . '$player' . ")",
            $view
        );
        $this->assertStringNotContainsString(
            "route('player-registration.create', ['player_id' => " . '$player->id' . "])",
            $view
        );
    }

    public function test_license_validation_uses_canonical_statuses_and_scope(): void
    {
        $controller = file_get_contents(
            $this->projectPath('app/Http/Controllers/LicenseController.php')
        );

        $this->assertStringContainsString(
            "return view('licenses.validation-canonical'",
            $controller
        );
        $this->assertStringContainsString(
            "'status' => 'active'",
            $controller
        );
        $this->assertStringContainsString(
            "'status' => 'revoked'",
            $controller
        );
        $this->assertStringContainsString(
            '$license->club->association_id',
            $controller
        );
        $this->assertStringNotContainsString(
            '$license->association_id !== $user->association_id',
            $controller
        );
    }

    public function test_legacy_license_entry_points_redirect_to_canonical_workflow(): void
    {
        $controller = file_get_contents(
            $this->projectPath('app/Http/Controllers/LicenseController.php')
        );

        $indexStart = strpos($controller, 'public function index');
        $editStart = strpos($controller, 'public function edit', $indexStart);
        $legacy = substr($controller, $indexStart, $editStart - $indexStart);

        $this->assertStringContainsString(
            "redirect()->route('modules.licenses.index')",
            $legacy
        );
        $this->assertStringContainsString(
            "'player-licenses.request.create'",
            $legacy
        );
        $this->assertStringNotContainsString(
            'License::create([',
            $legacy
        );
    }

    public function test_canonical_validation_view_has_no_vue_simulation_layer(): void
    {
        $view = file_get_contents(
            $this->projectPath('resources/views/licenses/validation-canonical.blade.php')
        );

        $this->assertStringNotContainsString('Vue.', $view);
        $this->assertStringNotContainsString('axios', $view);
        $this->assertStringContainsString(
            "/licenses/' + id + '/approve",
            $view
        );
        $this->assertStringContainsString(
            "/licenses/' + id + '/reject",
            $view
        );
    }
}
