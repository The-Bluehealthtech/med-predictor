<?php

namespace Tests\Unit\Components;

use PHPUnit\Framework\TestCase;

class PlayerPortalSimpleSecurityContractTest extends TestCase
{
    public function test_current_portal_route_requires_authentication(): void
    {
        $routes = file_get_contents(dirname(__DIR__, 3) . '/routes/web.php');

        $this->assertStringContainsString(
            "[\\App\\Http\\Controllers\\PlayerPortalSimpleController::class, 'show']",
            $routes
        );

        $this->assertStringContainsString(
            ")->middleware(['auth'])->name('test.portail.joueur.simple');",
            $routes
        );
    }

    public function test_current_portal_has_no_default_player(): void
    {
        $routes = file_get_contents(dirname(__DIR__, 3) . '/routes/web.php');

        $this->assertStringNotContainsString(
            "\$request->get('player_id', 4)",
            $routes
        );
    }

    public function test_player_login_does_not_expose_player_id_in_url(): void
    {
        $login = file_get_contents(
            dirname(__DIR__, 3) . '/app/Http/Controllers/Auth/LoginController.php'
        );

        $this->assertStringNotContainsString(
            "/test-portail-joueur-simple?player_id=",
            $login
        );

        $this->assertStringContainsString(
            "route('test.portail.joueur.simple')",
            $login
        );
    }

    public function test_controller_enforces_player_ownership(): void
    {
        $controller = file_get_contents(
            dirname(__DIR__, 3) . '/app/Http/Controllers/PlayerPortalSimpleController.php'
        );

        $this->assertStringContainsString(
            '$user->isPlayer()',
            $controller
        );

        $this->assertStringContainsString(
            '$requestedPlayerId !== (int) $user->player_id',
            $controller
        );

        $this->assertStringContainsString(
            'findOrFail((int) $user->player_id)',
            $controller
        );
    }

    public function test_controller_enforces_staff_scope(): void
    {
        $controller = file_get_contents(
            dirname(__DIR__, 3) . '/app/Http/Controllers/PlayerPortalSimpleController.php'
        );

        $this->assertStringContainsString(
            '$user->isSystemAdmin()',
            $controller
        );

        $this->assertStringContainsString(
            '$user->isClubUser()',
            $controller
        );

        $this->assertStringContainsString(
            "\$user->role === 'association_admin'",
            $controller
        );
    }

    public function test_access_controller_does_not_log_access_secrets(): void
    {
        $controller = file_get_contents(
            dirname(__DIR__, 3) . '/app/Http/Controllers/PlayerAccessController.php'
        );

        $this->assertStringNotContainsString(
            "'request_data' =>",
            $controller
        );

        $this->assertStringNotContainsString(
            "['accessCode' => \$accessCode]",
            $controller
        );

        $this->assertStringNotContainsString(
            "'received' => \$accessCode",
            $controller
        );

        $this->assertStringNotContainsString(
            "'expected' => \$this->generateAccessCode",
            $controller
        );
    }
}
