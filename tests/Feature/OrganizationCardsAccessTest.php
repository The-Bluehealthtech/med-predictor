<?php

namespace Tests\Feature;

use App\Http\Controllers\OrganizationCardController;
use App\Models\Association;
use App\Models\Club;
use App\Models\User;
use Tests\TestCase;

class OrganizationCardsAccessTest extends TestCase
{
    private function user(string $role, ?int $associationId = null, ?int $clubId = null): User
    {
        $user = new User();
        $user->forceFill([
            'id' => 900011,
            'name' => 'Cards Access Test',
            'email' => 'cards-access@example.invalid',
            'role' => $role,
            'association_id' => $associationId,
            'club_id' => $clubId,
            'status' => 'active',
        ]);
        $user->exists = true;

        return $user;
    }

    public function test_guest_cannot_write_organization_cards(): void
    {
        $this->post(route('organization-cards.store', 'confederations'), ['name' => 'Test'])
            ->assertRedirect(route('login'));
    }

    public function test_player_cannot_create_or_edit_organization_cards(): void
    {
        $this->withoutMiddleware();
        $this->actingAs($this->user('player'));

        $this->get(route('organization-cards.create', 'confederations'))->assertForbidden();
        $this->post(route('organization-cards.store', 'clubs'), ['name' => 'Test'])->assertForbidden();
    }

    public function test_only_owners_can_edit_organization_cards(): void
    {
        $association = new Association();
        $association->id = 15;
        $club = new Club();
        $club->id = 30;
        $club->association_id = 15;

        $this->assertTrue(OrganizationCardController::canEdit($this->user('system_admin'), 'confederations'));
        $this->assertTrue(OrganizationCardController::canEdit($this->user('association_admin', 15), 'associations', $association));
        $this->assertTrue(OrganizationCardController::canEdit($this->user('association_admin', 15), 'clubs', $club));
        $this->assertTrue(OrganizationCardController::canEdit($this->user('club_admin', null, 30), 'clubs', $club));
        $this->assertFalse(OrganizationCardController::canEdit($this->user('club_admin', null, 31), 'clubs', $club));
        $this->assertFalse(OrganizationCardController::canEdit($this->user('association_admin', 16), 'clubs', $club));
        $this->assertFalse(OrganizationCardController::canEdit($this->user('club_admin', null, 30), 'confederations'));
    }
}
