<?php

namespace Tests\Feature\Api\V1;

use App\Models\User;
use App\Models\Club;
use App\Models\Association;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $systemAdmin;
    protected User $associationAdmin;
    protected User $clubAdmin;
    protected Club $club;
    protected Association $association;
    protected Team $team;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->association = Association::factory()->create();
        $this->club = Club::factory()->create(['association_id' => $this->association->id]);
        $this->team = Team::factory()->create(['club_id' => $this->club->id, 'association_id' => $this->association->id]);

        // Create users with different roles
        $this->systemAdmin = User::factory()->create([
            'role' => 'system_admin',
            'status' => 'active'
        ]);

        $this->associationAdmin = User::factory()->create([
            'role' => 'association_admin',
            'association_id' => $this->association->id,
            'status' => 'active'
        ]);

        $this->clubAdmin = User::factory()->create([
            'role' => 'club_admin',
            'club_id' => $this->club->id,
            'status' => 'active'
        ]);
    }

    /** @test */
    public function system_admin_can_view_all_users()
    {
        Sanctum::actingAs($this->systemAdmin);

        $response = $this->getJson('/api/v1/users');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'email',
                            'role',
                            'status',
                            'created_at',
                            'updated_at'
                        ]
                    ],
                    'meta' => [
                        'current_page',
                        'last_page',
                        'per_page',
                        'total',
                        'from',
                        'to'
                    ]
                ]);
    }

    /** @test */
    public function association_admin_can_view_users_in_their_association()
    {
        Sanctum::actingAs($this->associationAdmin);

        $response = $this->getJson('/api/v1/users');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        foreach ($data as $user) {
            if (isset($user['association'])) {
                $this->assertEquals($this->association->id, $user['association']['id']);
            }
        }
    }

    /** @test */
    public function club_admin_cannot_view_users()
    {
        Sanctum::actingAs($this->clubAdmin);

        $response = $this->getJson('/api/v1/users');

        $response->assertStatus(403);
    }

    /** @test */
    public function system_admin_can_create_user()
    {
        Sanctum::actingAs($this->systemAdmin);

        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'club_manager',
            'club_id' => $this->club->id,
            'status' => 'active'
        ];

        $response = $this->postJson('/api/v1/users', $userData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'message',
                    'data' => [
                        'id',
                        'name',
                        'email',
                        'role',
                        'status'
                    ]
                ]);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role' => 'club_manager',
            'club_id' => $this->club->id
        ]);
    }

    /** @test */
    public function association_admin_can_create_user_in_their_association()
    {
        Sanctum::actingAs($this->associationAdmin);

        $userData = [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'association_registrar',
            'association_id' => $this->association->id,
            'status' => 'active'
        ];

        $response = $this->postJson('/api/v1/users', $userData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'role' => 'association_registrar',
            'association_id' => $this->association->id
        ]);
    }

    /** @test */
    public function club_admin_cannot_create_user()
    {
        Sanctum::actingAs($this->clubAdmin);

        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'club_medical',
            'club_id' => $this->club->id
        ];

        $response = $this->postJson('/api/v1/users', $userData);

        $response->assertStatus(403);
    }

    /** @test */
    public function system_admin_can_view_specific_user()
    {
        Sanctum::actingAs($this->systemAdmin);

        $user = User::factory()->create();

        $response = $this->getJson("/api/v1/users/{$user->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'name',
                        'email',
                        'role',
                        'status',
                        'created_at',
                        'updated_at'
                    ]
                ]);
    }

    /** @test */
    public function association_admin_can_view_user_in_their_association()
    {
        Sanctum::actingAs($this->associationAdmin);

        $user = User::factory()->create([
            'association_id' => $this->association->id
        ]);

        $response = $this->getJson("/api/v1/users/{$user->id}");

        $response->assertStatus(200);
    }

    /** @test */
    public function association_admin_cannot_view_user_outside_their_association()
    {
        Sanctum::actingAs($this->associationAdmin);

        $otherAssociation = Association::factory()->create();
        $user = User::factory()->create([
            'association_id' => $otherAssociation->id
        ]);

        $response = $this->getJson("/api/v1/users/{$user->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function system_admin_can_update_user()
    {
        Sanctum::actingAs($this->systemAdmin);

        $user = User::factory()->create();

        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'status' => 'inactive'
        ];

        $response = $this->putJson("/api/v1/users/{$user->id}", $updateData);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'message',
                    'data' => [
                        'id',
                        'name',
                        'email',
                        'status'
                    ]
                ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'status' => 'inactive'
        ]);
    }

    /** @test */
    public function association_admin_can_update_user_in_their_association()
    {
        Sanctum::actingAs($this->associationAdmin);

        $user = User::factory()->create([
            'association_id' => $this->association->id
        ]);

        $updateData = [
            'name' => 'Updated Name',
            'status' => 'inactive'
        ];

        $response = $this->putJson("/api/v1/users/{$user->id}", $updateData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Updated Name',
            'status' => 'inactive'
        ]);
    }

    /** @test */
    public function system_admin_can_delete_user()
    {
        Sanctum::actingAs($this->systemAdmin);

        $user = User::factory()->create();

        $response = $this->deleteJson("/api/v1/users/{$user->id}");

        $response->assertStatus(200)
                ->assertJson(['message' => 'User deleted successfully']);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /** @test */
    public function system_admin_cannot_delete_self()
    {
        Sanctum::actingAs($this->systemAdmin);

        $response = $this->deleteJson("/api/v1/users/{$this->systemAdmin->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function association_admin_cannot_delete_user()
    {
        Sanctum::actingAs($this->associationAdmin);

        $user = User::factory()->create([
            'association_id' => $this->association->id
        ]);

        $response = $this->deleteJson("/api/v1/users/{$user->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function system_admin_can_update_user_role()
    {
        Sanctum::actingAs($this->systemAdmin);

        $user = User::factory()->create(['role' => 'club_manager']);

        $response = $this->patchJson("/api/v1/users/{$user->id}/role", [
            'role' => 'club_admin'
        ]);

        $response->assertStatus(200)
                ->assertJson(['message' => 'User role updated successfully']);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => 'club_admin'
        ]);
    }

    /** @test */
    public function association_admin_can_update_role_within_association()
    {
        Sanctum::actingAs($this->associationAdmin);

        $user = User::factory()->create([
            'association_id' => $this->association->id,
            'role' => 'association_registrar'
        ]);

        $response = $this->patchJson("/api/v1/users/{$user->id}/role", [
            'role' => 'association_medical'
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'role' => 'association_medical'
        ]);
    }

    /** @test */
    public function association_admin_cannot_promote_to_system_admin()
    {
        Sanctum::actingAs($this->associationAdmin);

        $user = User::factory()->create([
            'association_id' => $this->association->id
        ]);

        $response = $this->patchJson("/api/v1/users/{$user->id}/role", [
            'role' => 'system_admin'
        ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function system_admin_can_update_user_permissions()
    {
        Sanctum::actingAs($this->systemAdmin);

        $user = User::factory()->create();

        $response = $this->patchJson("/api/v1/users/{$user->id}/permissions", [
            'permissions' => ['player_registration_access', 'healthcare_access']
        ]);

        $response->assertStatus(200)
                ->assertJson(['message' => 'User permissions updated successfully']);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'permissions' => json_encode(['player_registration_access', 'healthcare_access'])
        ]);
    }

    /** @test */
    public function system_admin_can_update_user_status()
    {
        Sanctum::actingAs($this->systemAdmin);

        $user = User::factory()->create(['status' => 'active']);

        $response = $this->patchJson("/api/v1/users/{$user->id}/status", [
            'status' => 'suspended'
        ]);

        $response->assertStatus(200)
                ->assertJson(['message' => 'User status updated successfully']);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'status' => 'suspended'
        ]);
    }

    /** @test */
    public function can_get_user_statistics()
    {
        Sanctum::actingAs($this->systemAdmin);

        $user = User::factory()->create();

        $response = $this->getJson("/api/v1/users/{$user->id}/statistics");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'health_records_count',
                        'last_login_at',
                        'login_count',
                        'days_since_last_login',
                        'account_age_days',
                        'is_online'
                    ]
                ]);
    }

    /** @test */
    public function can_get_users_by_role()
    {
        Sanctum::actingAs($this->systemAdmin);

        $response = $this->getJson('/api/v1/users/by-role?role=club_admin');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'email',
                            'role'
                        ]
                    ]
                ]);
    }

    /** @test */
    public function can_get_users_by_status()
    {
        Sanctum::actingAs($this->systemAdmin);

        $response = $this->getJson('/api/v1/users/by-status?status=active');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'email',
                            'status'
                        ]
                    ]
                ]);
    }

    /** @test */
    public function can_get_users_by_club()
    {
        Sanctum::actingAs($this->systemAdmin);

        $response = $this->getJson("/api/v1/users/by-club?club_id={$this->club->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'email',
                            'club'
                        ]
                    ]
                ]);
    }

    /** @test */
    public function can_get_users_by_association()
    {
        Sanctum::actingAs($this->systemAdmin);

        $response = $this->getJson("/api/v1/users/by-association?association_id={$this->association->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'email',
                            'association'
                        ]
                    ]
                ]);
    }

    /** @test */
    public function can_get_online_users()
    {
        Sanctum::actingAs($this->systemAdmin);

        // Create a user who logged in recently
        User::factory()->create([
            'last_login_at' => now()->subMinutes(15)
        ]);

        $response = $this->getJson('/api/v1/users/online');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'email',
                            'last_login_at'
                        ]
                    ]
                ]);
    }

    /** @test */
    public function can_get_recently_active_users()
    {
        Sanctum::actingAs($this->systemAdmin);

        // Create a user who logged in recently
        User::factory()->create([
            'last_login_at' => now()->subDays(3)
        ]);

        $response = $this->getJson('/api/v1/users/recently-active');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'email',
                            'last_login_at'
                        ]
                    ]
                ]);
    }

    /** @test */
    public function validates_required_fields_when_creating_user()
    {
        Sanctum::actingAs($this->systemAdmin);

        $response = $this->postJson('/api/v1/users', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'email', 'password', 'role']);
    }

    /** @test */
    public function validates_email_uniqueness_when_creating_user()
    {
        Sanctum::actingAs($this->systemAdmin);

        $existingUser = User::factory()->create();

        $response = $this->postJson('/api/v1/users', [
            'name' => 'Test User',
            'email' => $existingUser->email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'club_manager'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function validates_password_confirmation_when_creating_user()
    {
        Sanctum::actingAs($this->systemAdmin);

        $response = $this->postJson('/api/v1/users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'differentpassword',
            'role' => 'club_manager'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['password']);
    }

    /** @test */
    public function validates_club_required_for_club_roles()
    {
        Sanctum::actingAs($this->systemAdmin);

        $response = $this->postJson('/api/v1/users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'club_manager'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['club_id']);
    }

    /** @test */
    public function validates_association_required_for_association_roles()
    {
        Sanctum::actingAs($this->systemAdmin);

        $response = $this->postJson('/api/v1/users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'association_admin'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['association_id']);
    }

    /** @test */
    public function can_filter_users_by_search()
    {
        Sanctum::actingAs($this->systemAdmin);

        $user = User::factory()->create(['name' => 'John Doe']);

        $response = $this->getJson('/api/v1/users?search=John');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $this->assertCount(1, $data);
        $this->assertEquals($user->id, $data[0]['id']);
    }

    /** @test */
    public function can_paginate_users()
    {
        Sanctum::actingAs($this->systemAdmin);

        // Create more users than default per page
        User::factory()->count(25)->create();

        $response = $this->getJson('/api/v1/users?per_page=10');

        $response->assertStatus(200);
        
        $meta = $response->json('meta');
        $this->assertEquals(10, $meta['per_page']);
        $this->assertGreaterThan(1, $meta['last_page']);
    }
} 