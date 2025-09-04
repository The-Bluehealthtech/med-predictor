<?php

namespace Tests\Feature\Api\V1;

use App\Models\Season;
use App\Models\User;
use App\Models\Competition;
use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SeasonControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $systemAdmin;
    private User $associationAdmin;
    private User $clubAdmin;
    private Season $season;

    protected function setUp(): void
    {
        parent::setUp();

        // Create users with different roles
        $this->systemAdmin = User::factory()->create(['role' => 'system_admin']);
        $this->associationAdmin = User::factory()->create(['role' => 'association_admin']);
        $this->clubAdmin = User::factory()->create(['role' => 'club_admin']);

        // Create a test season
        $this->season = Season::factory()->create([
            'status' => 'upcoming',
            'is_current' => false,
        ]);
    }

    /** @test */
    public function it_can_list_seasons()
    {
        $response = $this->actingAs($this->systemAdmin)
            ->getJson('/api/v1/seasons');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'short_name',
                        'start_date',
                        'end_date',
                        'status',
                        'is_current',
                        'created_at',
                        'updated_at',
                    ]
                ],
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ]
            ]);
    }

    /** @test */
    public function it_can_filter_seasons_by_status()
    {
        Season::factory()->create(['status' => 'active']);
        Season::factory()->create(['status' => 'completed']);

        $response = $this->actingAs($this->systemAdmin)
            ->getJson('/api/v1/seasons?status=active');

        $response->assertStatus(200);
        $this->assertEquals(1, count($response->json('data')));
    }

    /** @test */
    public function it_can_filter_seasons_by_current_flag()
    {
        Season::factory()->create(['is_current' => true]);
        Season::factory()->create(['is_current' => false]);

        $response = $this->actingAs($this->systemAdmin)
            ->getJson('/api/v1/seasons?is_current=true');

        $response->assertStatus(200);
        $this->assertEquals(1, count($response->json('data')));
    }

    /** @test */
    public function it_can_search_seasons()
    {
        Season::factory()->create(['name' => 'Test Season 2024']);
        Season::factory()->create(['name' => 'Another Season']);

        $response = $this->actingAs($this->systemAdmin)
            ->getJson('/api/v1/seasons?search=Test');

        $response->assertStatus(200);
        $this->assertEquals(1, count($response->json('data')));
    }

    /** @test */
    public function it_can_create_a_season()
    {
        $seasonData = [
            'name' => '2024-2025 Season',
            'short_name' => '24-25',
            'start_date' => now()->addMonths(2)->format('Y-m-d'),
            'end_date' => now()->addMonths(8)->format('Y-m-d'),
            'registration_start_date' => now()->addMonth()->format('Y-m-d'),
            'registration_end_date' => now()->addMonths(2)->subDay()->format('Y-m-d'),
            'status' => 'upcoming',
            'description' => 'Test season description',
            'settings' => [
                'allow_player_registration' => true,
                'allow_competition_creation' => true,
            ]
        ];

        $response = $this->actingAs($this->systemAdmin)
            ->postJson('/api/v1/seasons', $seasonData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'name',
                    'short_name',
                    'start_date',
                    'end_date',
                    'status',
                    'is_current',
                ]
            ]);

        $this->assertDatabaseHas('seasons', [
            'name' => '2024-2025 Season',
            'short_name' => '24-25',
        ]);
    }

    /** @test */
    public function it_validates_required_fields_when_creating_season()
    {
        $response = $this->actingAs($this->systemAdmin)
            ->postJson('/api/v1/seasons', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
                'short_name',
                'start_date',
                'end_date',
                'registration_start_date',
                'registration_end_date',
            ]);
    }

    /** @test */
    public function it_validates_unique_name_and_short_name()
    {
        Season::factory()->create([
            'name' => 'Existing Season',
            'short_name' => 'EXIST',
        ]);

        $response = $this->actingAs($this->systemAdmin)
            ->postJson('/api/v1/seasons', [
                'name' => 'Existing Season',
                'short_name' => 'EXIST',
                'start_date' => '2024-08-01',
                'end_date' => '2025-05-31',
                'registration_start_date' => '2024-06-01',
                'registration_end_date' => '2024-07-31',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'short_name']);
    }

    /** @test */
    public function it_validates_date_logic()
    {
        $response = $this->actingAs($this->systemAdmin)
            ->postJson('/api/v1/seasons', [
                'name' => 'Test Season',
                'short_name' => 'TEST',
                'start_date' => '2024-08-01',
                'end_date' => '2024-07-01', // Before start date
                'registration_start_date' => '2024-09-01', // After start date
                'registration_end_date' => '2024-06-01', // Before registration start
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'end_date',
                'registration_start_date',
                'registration_end_date',
            ]);
    }

    /** @test */
    public function it_can_show_a_season()
    {
        $response = $this->actingAs($this->systemAdmin)
            ->getJson("/api/v1/seasons/{$this->season->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'short_name',
                    'start_date',
                    'end_date',
                    'status',
                    'is_current',
                    'competitions_count',
                    'players_count',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }

    /** @test */
    public function it_can_update_a_season()
    {
        $updateData = [
            'name' => 'Updated Season Name',
            'description' => 'Updated description',
            'status' => 'active',
        ];

        $response = $this->actingAs($this->systemAdmin)
            ->putJson("/api/v1/seasons/{$this->season->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Season updated successfully',
                'data' => [
                    'name' => 'Updated Season Name',
                    'description' => 'Updated description',
                    'status' => 'active',
                ]
            ]);

        $this->assertDatabaseHas('seasons', [
            'id' => $this->season->id,
            'name' => 'Updated Season Name',
            'status' => 'active',
        ]);
    }

    /** @test */
    public function it_can_delete_a_season()
    {
        $response = $this->actingAs($this->systemAdmin)
            ->deleteJson("/api/v1/seasons/{$this->season->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Season deleted successfully']);

        $this->assertDatabaseMissing('seasons', ['id' => $this->season->id]);
    }

    /** @test */
    public function it_cannot_delete_season_with_competitions()
    {
        Competition::factory()->create(['season_id' => $this->season->id]);

        $response = $this->actingAs($this->systemAdmin)
            ->deleteJson("/api/v1/seasons/{$this->season->id}");

        $response->assertStatus(422)
            ->assertJson(['message' => 'Cannot delete season with existing competitions']);

        $this->assertDatabaseHas('seasons', ['id' => $this->season->id]);
    }

    /** @test */
    public function it_cannot_delete_season_with_players()
    {
        Player::factory()->create(['season_id' => $this->season->id]);

        $response = $this->actingAs($this->systemAdmin)
            ->deleteJson("/api/v1/seasons/{$this->season->id}");

        $response->assertStatus(422)
            ->assertJson(['message' => 'Cannot delete season with existing players']);

        $this->assertDatabaseHas('seasons', ['id' => $this->season->id]);
    }

    /** @test */
    public function it_can_set_season_as_current()
    {
        $currentSeason = Season::factory()->create(['is_current' => true]);

        $response = $this->actingAs($this->systemAdmin)
            ->postJson("/api/v1/seasons/{$this->season->id}/set-current");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Season set as current successfully']);

        $this->assertDatabaseHas('seasons', [
            'id' => $this->season->id,
            'is_current' => true,
        ]);

        $this->assertDatabaseHas('seasons', [
            'id' => $currentSeason->id,
            'is_current' => false,
        ]);
    }

    /** @test */
    public function it_can_update_season_status()
    {
        $response = $this->actingAs($this->systemAdmin)
            ->patchJson("/api/v1/seasons/{$this->season->id}/status", [
                'status' => 'active'
            ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Season status updated successfully']);

        $this->assertDatabaseHas('seasons', [
            'id' => $this->season->id,
            'status' => 'active',
        ]);
    }

    /** @test */
    public function it_validates_status_values()
    {
        $response = $this->actingAs($this->systemAdmin)
            ->patchJson("/api/v1/seasons/{$this->season->id}/status", [
                'status' => 'invalid_status'
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    /** @test */
    public function it_can_get_season_statistics()
    {
        Competition::factory()->count(3)->create(['season_id' => $this->season->id]);
        Player::factory()->count(5)->create(['season_id' => $this->season->id]);

        $response = $this->actingAs($this->systemAdmin)
            ->getJson("/api/v1/seasons/{$this->season->id}/statistics");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'competitions_count',
                    'active_competitions_count',
                    'players_count',
                    'matches_count',
                    'duration_days',
                    'is_registration_open',
                    'days_until_start',
                    'days_until_end',
                ]
            ]);

        $this->assertEquals(3, $response->json('data.competitions_count'));
        $this->assertEquals(5, $response->json('data.players_count'));
    }

    /** @test */
    public function it_can_get_current_season()
    {
        $currentSeason = Season::factory()->create(['is_current' => true]);

        $response = $this->actingAs($this->systemAdmin)
            ->getJson('/api/v1/seasons/current');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $currentSeason->id,
                    'is_current' => true,
                ]
            ]);
    }

    /** @test */
    public function it_returns_404_when_no_current_season()
    {
        Season::query()->update(['is_current' => false]);

        $response = $this->actingAs($this->systemAdmin)
            ->getJson('/api/v1/seasons/current');

        $response->assertStatus(404)
            ->assertJson(['message' => 'No current season found']);
    }

    /** @test */
    public function it_can_get_active_seasons()
    {
        Season::factory()->create(['status' => 'active']);
        Season::factory()->create(['status' => 'active']);
        Season::factory()->create(['status' => 'upcoming']);

        $response = $this->actingAs($this->systemAdmin)
            ->getJson('/api/v1/seasons/active');

        $response->assertStatus(200);
        $this->assertEquals(2, count($response->json('data')));
    }

    /** @test */
    public function it_can_get_upcoming_seasons()
    {
        $upcoming1 = Season::factory()->create(['status' => 'upcoming']);
        $upcoming2 = Season::factory()->create(['status' => 'upcoming']);
        $notUpcoming = Season::factory()->create(['status' => 'active']);

        $response = $this->actingAs($this->systemAdmin)
            ->getJson('/api/v1/seasons/upcoming');

        $response->assertStatus(200);
        $upcomingCount = Season::where('status', 'upcoming')->count();
        $this->assertEquals($upcomingCount, count($response->json('data')));
    }

    /** @test */
    public function it_can_get_completed_seasons()
    {
        Season::factory()->create(['status' => 'completed']);
        Season::factory()->create(['status' => 'completed']);
        Season::factory()->create(['status' => 'active']);

        $response = $this->actingAs($this->systemAdmin)
            ->getJson('/api/v1/seasons/completed');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ]
            ]);

        $this->assertEquals(2, count($response->json('data')));
    }

    // Authorization tests
    /** @test */
    public function system_admin_can_perform_all_operations()
    {
        $response = $this->actingAs($this->systemAdmin)
            ->getJson('/api/v1/seasons');

        $response->assertStatus(200);
    }

    /** @test */
    public function association_admin_can_perform_all_operations()
    {
        $response = $this->actingAs($this->associationAdmin)
            ->getJson('/api/v1/seasons');

        $response->assertStatus(200);
    }

    /** @test */
    public function club_admin_can_view_seasons()
    {
        $response = $this->actingAs($this->clubAdmin)
            ->getJson('/api/v1/seasons');

        $response->assertStatus(200);
    }

    /** @test */
    public function club_admin_cannot_create_seasons()
    {
        $response = $this->actingAs($this->clubAdmin)
            ->postJson('/api/v1/seasons', [
                'name' => 'Test Season',
                'short_name' => 'TEST',
                'start_date' => '2024-08-01',
                'end_date' => '2025-05-31',
                'registration_start_date' => '2024-06-01',
                'registration_end_date' => '2024-07-31',
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function club_admin_cannot_update_seasons()
    {
        $response = $this->actingAs($this->clubAdmin)
            ->putJson("/api/v1/seasons/{$this->season->id}", [
                'name' => 'Updated Name'
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function club_admin_cannot_delete_seasons()
    {
        $response = $this->actingAs($this->clubAdmin)
            ->deleteJson("/api/v1/seasons/{$this->season->id}");

        $response->assertStatus(403);
    }

    /** @test */
    public function club_admin_cannot_set_current_season()
    {
        $response = $this->actingAs($this->clubAdmin)
            ->postJson("/api/v1/seasons/{$this->season->id}/set-current");

        $response->assertStatus(403);
    }

    /** @test */
    public function club_admin_cannot_update_season_status()
    {
        $response = $this->actingAs($this->clubAdmin)
            ->patchJson("/api/v1/seasons/{$this->season->id}/status", [
                'status' => 'active'
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_seasons()
    {
        $response = $this->getJson('/api/v1/seasons');

        $response->assertStatus(401);
    }
} 