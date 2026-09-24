<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class PlayerPortalSimpleAccessTest extends TestCase
{
    private function bypassRouteAuthentication(): void
    {
        /*
         * Le test invité couvre séparément le middleware auth.
         *
         * Ici on teste uniquement l'autorisation du contrôleur
         * (ownership / rôle) via une vraie requête HTTP routée,
         * sans dépendre de la persistance d'un utilisateur de test.
         */
        $this->withoutMiddleware();
    }

    private function authenticatedUser(array $attributes): User
    {
        $user = new User();

        $user->forceFill(array_merge([
            'id' => 900001,
            'name' => 'Portal Security Test',
            'email' => 'portal-security-test@example.invalid',
            'role' => 'player',
            'player_id' => null,
            'club_id' => null,
            'association_id' => null,
            'tenant_id' => null,
            'status' => 'active',
        ], $attributes));

        $user->exists = true;

        $this->actingAs($user);

        return $user;
    }

    public function test_guest_cannot_access_current_player_portal(): void
    {
        $response = $this->get(
            route('test.portail.joueur.simple')
        );

        $response->assertRedirect(route('login'));
    }

    public function test_player_without_linked_player_is_forbidden(): void
    {
        $this->bypassRouteAuthentication();

        $this->authenticatedUser([
            'role' => 'player',
            'player_id' => null,
        ]);

        $response = $this->get(
            route('test.portail.joueur.simple')
        );

        $response->assertForbidden();
    }

    public function test_player_cannot_request_another_player_id(): void
    {
        $this->bypassRouteAuthentication();

        $this->authenticatedUser([
            'role' => 'player',
            'player_id' => 900101,
        ]);

        $response = $this->get(
            route('test.portail.joueur.simple', [
                'player_id' => 900102,
            ])
        );

        $response->assertForbidden();
    }

    public function test_unauthorized_role_cannot_request_player_portal(): void
    {
        $this->bypassRouteAuthentication();

        $this->authenticatedUser([
            'role' => 'referee',
            'player_id' => null,
        ]);

        $response = $this->get(
            route('test.portail.joueur.simple', [
                'player_id' => 900102,
            ])
        );

        $response->assertForbidden();
    }
}
