<?php

namespace Tests\Feature\Api\V1;

use App\Models\Association;
use App\Models\Club;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ClubControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected Association $association;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create association
        $this->association = Association::factory()->create([
            'name' => 'Test Association',
            'country' => 'Test Country',
            'fifa_id' => 'TEST_ASSOC_001'
        ]);

        // Create user with club management permissions
        $this->user = User::factory()->create([
            'role' => 'club_manager',
            'association_id' => $this->association->id
        ]);
    }

    public function test_index_returns_clubs_for_authenticated_user()
    {
        $club = Club::factory()->create([
            'association_id' => $this->association->id,
            'name' => 'Test Club'
        ]);

        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/v1/clubs');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'short_name',
                            'association_id',
                            'status',
                            'association'
                        ]
                    ],
                    'pagination'
                ]
            ]);
    }

    public function test_index_requires_authentication()
    {
        $response = $this->getJson('/api/v1/clubs');

        $response->assertStatus(401);
    }

    public function test_show_returns_club_details()
    {
        $club = Club::factory()->create([
            'association_id' => $this->association->id,
            'name' => 'Test Club',
            'city' => 'Test City',
            'country' => 'Test Country',
            'founded_year' => 1990
        ]);

        Sanctum::actingAs($this->user);

        $response = $this->getJson("/api/v1/clubs/{$club->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Club retrieved successfully',
                'data' => [
                    'id' => $club->id,
                    'name' => 'Test Club',
                    'city' => 'Test City',
                    'country' => 'Test Country',
                    'founded_year' => 1990,
                    'association' => [
                        'id' => $this->association->id,
                        'name' => 'Test Association'
                    ]
                ]
            ]);
    }

    public function test_show_returns_404_for_nonexistent_club()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/v1/clubs/99999');

        $response->assertStatus(404);
    }

    public function test_store_creates_new_club()
    {
        Sanctum::actingAs($this->user);

        $clubData = [
            'name' => 'New Test Club',
            'short_name' => 'NTC',
            'city' => 'New Test City',
            'country' => 'New Test Country',
            'founded_year' => 2000,
            'association_id' => $this->association->id,
            'status' => 'active',
            'fifa_connect_id' => 'NEW_CLUB_001'
        ];

        $response = $this->postJson('/api/v1/clubs', $clubData);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'name' => 'New Test Club',
                    'city' => 'New Test City',
                    'country' => 'New Test Country',
                    'founded_year' => 2000,
                    'association' => [
                        'id' => $this->association->id,
                        'name' => $this->association->name
                    ]
                ]
            ]);

        $this->assertDatabaseHas('clubs', [
            'name' => 'New Test Club',
            'association_id' => $this->association->id
        ]);
    }

    public function test_store_validates_required_fields()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/v1/clubs', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'short_name', 'association_id', 'status']);
    }

    public function test_store_validates_founded_year_range()
    {
        Sanctum::actingAs($this->user);

        $clubData = [
            'name' => 'Test Club',
            'short_name' => 'TC',
            'association_id' => $this->association->id,
            'status' => 'active',
            'founded_year' => 1799 // Should be invalid (before 1800)
        ];

        $response = $this->postJson('/api/v1/clubs', $clubData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['founded_year']);
    }

    public function test_update_modifies_existing_club()
    {
        Sanctum::actingAs($this->user);

        $club = Club::factory()->create([
            'association_id' => $this->association->id,
            'name' => 'Original Name',
            'short_name' => 'ON',
            'city' => 'Original City',
            'country' => 'Original Country',
            'founded_year' => 1990,
            'status' => 'active',
            'fifa_connect_id' => 'ORIGINAL_CLUB_001'
        ]);

        $updateData = [
            'name' => 'Updated Club Name',
            'short_name' => 'UCN',
            'city' => 'Updated City',
            'country' => 'Updated Country',
            'founded_year' => 1995,
            'association_id' => $this->association->id,
            'status' => 'active',
            'fifa_connect_id' => 'UPDATED_CLUB_001'
        ];

        $response = $this->putJson("/api/v1/clubs/{$club->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $club->id,
                    'name' => 'Updated Club Name',
                    'city' => 'Updated City',
                    'country' => 'Updated Country',
                    'founded_year' => 1995
                ]
            ]);

        $this->assertDatabaseHas('clubs', [
            'id' => $club->id,
            'name' => 'Updated Club Name',
            'city' => 'Updated City'
        ]);
    }

    public function test_update_returns_404_for_nonexistent_club()
    {
        Sanctum::actingAs($this->user);

        $updateData = [
            'name' => 'Updated Name',
            'city' => 'Updated City',
            'country' => 'Updated Country',
            'founded_year' => 1995,
            'fifa_connect_id' => 'UPDATED_CLUB_001'
        ];

        $response = $this->putJson('/api/v1/clubs/99999', $updateData);

        $response->assertStatus(404);
    }

    public function test_destroy_deletes_club()
    {
        // Create system admin user for delete permission
        $adminUser = User::factory()->create([
            'role' => 'system_admin'
        ]);

        $club = Club::factory()->create([
            'association_id' => $this->association->id
        ]);

        Sanctum::actingAs($adminUser);

        $response = $this->deleteJson("/api/v1/clubs/{$club->id}");

        $response->assertStatus(200); // API returns 200 with message, not 204

        $this->assertDatabaseMissing('clubs', ['id' => $club->id]);
    }

    public function test_destroy_returns_404_for_nonexistent_club()
    {
        Sanctum::actingAs($this->user);

        $response = $this->deleteJson('/api/v1/clubs/99999');

        $response->assertStatus(404);
    }

    public function test_club_manager_can_only_access_own_association_clubs()
    {
        // Create another association and club
        $otherAssociation = Association::factory()->create([
            'name' => 'Other Association',
            'country' => 'Other Country',
            'fifa_id' => 'OTHER_ASSOC_001'
        ]);

        $otherClub = Club::factory()->create([
            'association_id' => $otherAssociation->id,
            'name' => 'Other Club'
        ]);

        Sanctum::actingAs($this->user);

        // Should not be able to see other association's club (403 Forbidden, not 404)
        $response = $this->getJson("/api/v1/clubs/{$otherClub->id}");

        $response->assertStatus(403);
    }

    public function test_system_admin_can_access_all_clubs()
    {
        // Create system admin user
        $adminUser = User::factory()->create([
            'role' => 'system_admin'
        ]);

        // Create club in different association
        $otherAssociation = Association::factory()->create([
            'name' => 'Other Association',
            'country' => 'Other Country',
            'fifa_id' => 'OTHER_ASSOC_001'
        ]);

        $otherClub = Club::factory()->create([
            'association_id' => $otherAssociation->id,
            'name' => 'Other Club'
        ]);

        Sanctum::actingAs($adminUser);

        // Should be able to see other association's club
        $response = $this->getJson("/api/v1/clubs/{$otherClub->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $otherClub->id,
                    'name' => 'Other Club'
                ]
            ]);
    }

    public function test_pagination_works_correctly()
    {
        Sanctum::actingAs($this->user);

        // Create more clubs than default per page
        Club::factory()->count(25)->create([
            'association_id' => $this->association->id
        ]);

        $response = $this->getJson('/api/v1/clubs?page=1&per_page=10');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'data',
                    'pagination' => [
                        'current_page',
                        'last_page',
                        'per_page',
                        'total',
                        'from',
                        'to',
                        'has_more_pages'
                    ]
                ]
            ]);

        $this->assertCount(10, $response->json('data.data'));
        $this->assertEquals(1, $response->json('data.pagination.current_page'));
        $this->assertEquals(10, $response->json('data.pagination.per_page'));
        $this->assertEquals(25, $response->json('data.pagination.total'));
    }

    public function test_search_filter_works()
    {
        Sanctum::actingAs($this->user);

        // Create clubs with specific names
        Club::factory()->create([
            'association_id' => $this->association->id,
            'name' => 'Manchester United'
        ]);

        Club::factory()->create([
            'association_id' => $this->association->id,
            'name' => 'Liverpool FC'
        ]);

        Club::factory()->create([
            'association_id' => $this->association->id,
            'name' => 'Arsenal FC'
        ]);

        $response = $this->getJson('/api/v1/clubs?search=Manchester');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data.data'));
        $this->assertEquals('Manchester United', $response->json('data.data.0.name'));
    }

    public function test_city_filter_works()
    {
        Sanctum::actingAs($this->user);

        // Create clubs in specific cities
        Club::factory()->create([
            'association_id' => $this->association->id,
            'name' => 'Chelsea FC',
            'city' => 'London'
        ]);

        Club::factory()->create([
            'association_id' => $this->association->id,
            'name' => 'Arsenal FC',
            'city' => 'London'
        ]);

        Club::factory()->create([
            'association_id' => $this->association->id,
            'name' => 'Manchester United',
            'city' => 'Manchester'
        ]);

        $response = $this->getJson('/api/v1/clubs?city=London');

        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data.data'));
        $this->assertEquals('London', $response->json('data.data.0.city'));
        $this->assertEquals('London', $response->json('data.data.1.city'));
    }
} 