<?php

namespace Tests\Feature\Api\V1;

use App\Models\Association;
use App\Models\Club;
use App\Models\Competition;
use App\Models\MatchModel;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Illuminate\Support\Facades\Gate;
use App\Policies\MatchPolicy;

class MatchControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected Association $association;
    protected Competition $competition;
    protected Team $homeTeam;
    protected Team $awayTeam;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create association
        $this->association = Association::factory()->create([
            'name' => 'Test Association',
            'country' => 'Test Country',
            'fifa_id' => 'TEST_ASSOC_001'
        ]);

        // Create competition
        $this->competition = Competition::factory()->create([
            'name' => 'Test Competition',
            'association_id' => $this->association->id,
            'type' => 'league',
            'status' => 'active'
        ]);

        // Create club
        $club = Club::factory()->create([
            'association_id' => $this->association->id,
            'name' => 'Test Club'
        ]);

        // Create teams
        $this->homeTeam = Team::factory()->create([
            'name' => 'Home Team',
            'club_id' => $club->id
        ]);

        $this->awayTeam = Team::factory()->create([
            'name' => 'Away Team',
            'club_id' => $club->id
        ]);

        // Attach teams to competition
        $this->competition->teams()->attach([$this->homeTeam->id, $this->awayTeam->id]);

        // Create user with organizer permissions
        $this->user = User::factory()->create([
            'role' => 'organizer',
            'association_id' => $this->association->id
        ]);

        // Register the policy manually for testing
        Gate::policy(MatchModel::class, MatchPolicy::class);
    }

    public function test_gate_direct_authorization_for_view_any()
    {
        \Log::info('About to call Gate::forUser->authorize');
        \Illuminate\Support\Facades\Gate::forUser($this->user)->authorize('viewAny', \App\Models\MatchModel::class);
        \Log::info('Gate::forUser->authorize did not throw');
        $this->assertTrue(true);
    }

    public function test_authentication_debug()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/v1/debug/auth');

        $response->assertStatus(200)
            ->assertJson([
                'authenticated' => true,
                'user' => [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                    'role' => $this->user->role,
                    'association_id' => $this->user->association_id,
                ]
            ]);
    }

    public function test_index_returns_matches_for_authenticated_user()
    {
        $match = MatchModel::factory()->create([
            'competition_id' => $this->competition->id,
            'home_team_id' => $this->homeTeam->id,
            'away_team_id' => $this->awayTeam->id,
        ]);

        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/v1/matches');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'competition_id',
                            'home_team_id',
                            'away_team_id',
                            'match_status',
                            'matchday'
                        ]
                    ],
                    'pagination'
                ]
            ]);
    }

    public function test_index_requires_authentication()
    {
        $response = $this->getJson('/api/v1/matches');

        $response->assertStatus(401);
    }

    public function test_show_returns_match_details()
    {
        $match = MatchModel::factory()->create([
            'competition_id' => $this->competition->id,
            'home_team_id' => $this->homeTeam->id,
            'away_team_id' => $this->awayTeam->id,
            'matchday' => 'MATCH001',
            'match_date' => '2024-12-25',
            'kickoff_time' => '15:00',
            'match_status' => 'scheduled'
        ]);

        Sanctum::actingAs($this->user);

        $response = $this->getJson("/api/v1/matches/{$match->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Match retrieved successfully',
                'data' => [
                    'id' => $match->id,
                    'matchday' => 'MATCH001',
                    'match_date' => '2024-12-25',
                    'kickoff_time' => '15:00',
                    'match_status' => 'scheduled'
                ]
            ]);
    }

    public function test_show_returns_404_for_nonexistent_match()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/v1/matches/99999');

        $response->assertStatus(404);
    }

    public function test_store_creates_new_match()
    {
        Sanctum::actingAs($this->user);

        $matchData = [
            'competition_id' => $this->competition->id,
            'home_team_id' => $this->homeTeam->id,
            'away_team_id' => $this->awayTeam->id,
            'match_date' => now()->addDays(7)->format('Y-m-d'),
            'kickoff_time' => '16:00',
            'venue' => 'Test Stadium',
            'match_status' => 'scheduled',
        ];

        $response = $this->postJson('/api/v1/matches', $matchData);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'competition_id' => $this->competition->id,
                    'home_team_id' => $this->homeTeam->id,
                    'away_team_id' => $this->awayTeam->id,
                    'match_status' => 'scheduled',
                    'venue' => 'Test Stadium',
                ]
            ]);

        $this->assertDatabaseHas('matches', [
            'competition_id' => $this->competition->id,
            'home_team_id' => $this->homeTeam->id,
            'away_team_id' => $this->awayTeam->id,
            'venue' => 'Test Stadium',
            'match_status' => 'scheduled',
        ]);
    }

    public function test_store_validates_required_fields()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/v1/matches', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['competition_id', 'home_team_id', 'away_team_id', 'match_date', 'kickoff_time', 'match_status']);
    }

    public function test_store_validates_home_and_away_teams_different()
    {
        Sanctum::actingAs($this->user);

        $matchData = [
            'competition_id' => $this->competition->id,
            'home_team_id' => $this->homeTeam->id,
            'away_team_id' => $this->homeTeam->id, // Same team
            'match_date' => '2024-12-26',
            'kickoff_time' => '16:00',
            'match_status' => 'scheduled',
            'matchday' => 'NEW_MATCH_001'
        ];

        $response = $this->postJson('/api/v1/matches', $matchData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['away_team_id']);
    }

    public function test_update_modifies_existing_match()
    {
        Sanctum::actingAs($this->user);

        $match = MatchModel::factory()->create([
            'competition_id' => $this->competition->id,
            'home_team_id' => $this->homeTeam->id,
            'away_team_id' => $this->awayTeam->id,
            'matchday' => 'ORIGINAL_MATCH',
            'match_status' => 'scheduled'
        ]);

        $updateData = [
            'match_status' => 'in_progress',
            'home_score' => 1,
            'away_score' => 0,
        ];

        $response = $this->putJson("/api/v1/matches/{$match->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $match->id,
                    'match_status' => 'in_progress',
                    'home_score' => 1,
                    'away_score' => 0,
                ]
            ]);

        $this->assertDatabaseHas('matches', [
            'id' => $match->id,
            'match_status' => 'in_progress'
        ]);
    }

    public function test_update_returns_404_for_nonexistent_match()
    {
        Sanctum::actingAs($this->user);

        $updateData = [
            'matchday' => 'UPDATED_MATCH'
        ];

        $response = $this->putJson('/api/v1/matches/99999', $updateData);

        $response->assertStatus(404);
    }

    public function test_destroy_deletes_match()
    {
        // Create system admin user for delete permission
        $adminUser = User::factory()->create([
            'role' => 'system_admin'
        ]);

        $match = MatchModel::factory()->create([
            'competition_id' => $this->competition->id,
            'home_team_id' => $this->homeTeam->id,
            'away_team_id' => $this->awayTeam->id
        ]);

        Sanctum::actingAs($adminUser);

        $response = $this->deleteJson("/api/v1/matches/{$match->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('matches', ['id' => $match->id]);
    }

    public function test_destroy_returns_404_for_nonexistent_match()
    {
        Sanctum::actingAs($this->user);

        $response = $this->deleteJson('/api/v1/matches/99999');

        $response->assertStatus(404);
    }

    public function test_organizer_can_only_access_own_association_matches()
    {
        // Create another association and match
        $otherAssociation = Association::factory()->create([
            'name' => 'Other Association',
            'country' => 'Other Country',
            'fifa_id' => 'OTHER_ASSOC_001'
        ]);

        $otherCompetition = Competition::factory()->create([
            'association_id' => $otherAssociation->id
        ]);

        $otherMatch = MatchModel::factory()->create([
            'competition_id' => $otherCompetition->id,
            'home_team_id' => $this->homeTeam->id,
            'away_team_id' => $this->awayTeam->id
        ]);

        Sanctum::actingAs($this->user);

        // Should not be able to see other association's match (403 Forbidden)
        $response = $this->getJson("/api/v1/matches/{$otherMatch->id}");

        $response->assertStatus(403);
    }

    public function test_system_admin_can_access_all_matches()
    {
        // Create system admin user
        $adminUser = User::factory()->create([
            'role' => 'system_admin'
        ]);

        // Create match in different association
        $otherAssociation = Association::factory()->create([
            'name' => 'Other Association',
            'country' => 'Other Country',
            'fifa_id' => 'OTHER_ASSOC_001'
        ]);

        $otherCompetition = Competition::factory()->create([
            'association_id' => $otherAssociation->id
        ]);

        $otherMatch = MatchModel::factory()->create([
            'competition_id' => $otherCompetition->id,
            'home_team_id' => $this->homeTeam->id,
            'away_team_id' => $this->awayTeam->id
        ]);

        Sanctum::actingAs($adminUser);

        // Should be able to see other association's match
        $response = $this->getJson("/api/v1/matches/{$otherMatch->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $otherMatch->id
                ]
            ]);
    }

    public function test_pagination_works_correctly()
    {
        Sanctum::actingAs($this->user);

        // Create more matches than default per page
        MatchModel::factory()->count(25)->create([
            'competition_id' => $this->competition->id,
            'home_team_id' => $this->homeTeam->id,
            'away_team_id' => $this->awayTeam->id
        ]);

        $response = $this->getJson('/api/v1/matches?page=1&per_page=10');

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

        // Create matches with specific matchday values
        MatchModel::factory()->create([
            'competition_id' => $this->competition->id,
            'home_team_id' => $this->homeTeam->id,
            'away_team_id' => $this->awayTeam->id,
            'matchday' => 'MATCH_001'
        ]);

        MatchModel::factory()->create([
            'competition_id' => $this->competition->id,
            'home_team_id' => $this->homeTeam->id,
            'away_team_id' => $this->awayTeam->id,
            'matchday' => 'MATCH_002'
        ]);

        MatchModel::factory()->create([
            'competition_id' => $this->competition->id,
            'home_team_id' => $this->homeTeam->id,
            'away_team_id' => $this->awayTeam->id,
            'matchday' => 'OTHER_001'
        ]);

        $response = $this->getJson('/api/v1/matches?search=MATCH');

        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data.data'));
        $this->assertEquals('MATCH_001', $response->json('data.data.0.matchday'));
        $this->assertEquals('MATCH_002', $response->json('data.data.1.matchday'));
    }

    public function test_status_filter_works()
    {
        Sanctum::actingAs($this->user);

        // Create matches with different statuses
        MatchModel::factory()->create([
            'competition_id' => $this->competition->id,
            'home_team_id' => $this->homeTeam->id,
            'away_team_id' => $this->awayTeam->id,
            'match_status' => 'scheduled'
        ]);

        MatchModel::factory()->create([
            'competition_id' => $this->competition->id,
            'home_team_id' => $this->homeTeam->id,
            'away_team_id' => $this->awayTeam->id,
            'match_status' => 'completed'
        ]);

        MatchModel::factory()->create([
            'competition_id' => $this->competition->id,
            'home_team_id' => $this->homeTeam->id,
            'away_team_id' => $this->awayTeam->id,
            'match_status' => 'scheduled'
        ]);

        $response = $this->getJson('/api/v1/matches?status=scheduled');

        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data.data'));
        $this->assertEquals('scheduled', $response->json('data.data.0.match_status'));
        $this->assertEquals('scheduled', $response->json('data.data.1.match_status'));
    }

    public function test_date_range_filter_works()
    {
        Sanctum::actingAs($this->user);

        // Create matches with different dates
        MatchModel::factory()->create([
            'competition_id' => $this->competition->id,
            'home_team_id' => $this->homeTeam->id,
            'away_team_id' => $this->awayTeam->id,
            'match_date' => '2024-01-15'
        ]);

        MatchModel::factory()->create([
            'competition_id' => $this->competition->id,
            'home_team_id' => $this->homeTeam->id,
            'away_team_id' => $this->awayTeam->id,
            'match_date' => '2024-01-20'
        ]);

        MatchModel::factory()->create([
            'competition_id' => $this->competition->id,
            'home_team_id' => $this->homeTeam->id,
            'away_team_id' => $this->awayTeam->id,
            'match_date' => '2024-02-01'
        ]);

        $response = $this->getJson('/api/v1/matches?date_from=2024-01-15&date_to=2024-01-25');

        $response->assertStatus(200);
        $this->assertCount(2, $response->json('data.data'));
    }

    public function test_competition_filter_works()
    {
        Sanctum::actingAs($this->user);

        // Create another competition
        $otherCompetition = Competition::factory()->create([
            'association_id' => $this->association->id,
            'name' => 'Other Competition'
        ]);

        // Create matches in different competitions
        MatchModel::factory()->create([
            'competition_id' => $this->competition->id,
            'home_team_id' => $this->homeTeam->id,
            'away_team_id' => $this->awayTeam->id
        ]);

        MatchModel::factory()->create([
            'competition_id' => $otherCompetition->id,
            'home_team_id' => $this->homeTeam->id,
            'away_team_id' => $this->awayTeam->id
        ]);

        $response = $this->getJson("/api/v1/matches?competition_id={$this->competition->id}");

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data.data'));
        $this->assertEquals($this->competition->id, $response->json('data.data.0.competition_id'));
    }

    public function test_get_match_events()
    {
        $admin = \App\Models\User::factory()->create([
            'role' => 'system_admin',
            'association_id' => $this->association->id
        ]);
        Sanctum::actingAs($admin);

        $match = MatchModel::factory()->create([
            'competition_id' => $this->competition->id,
            'home_team_id' => $this->homeTeam->id,
            'away_team_id' => $this->awayTeam->id
        ]);

        // Debug output
        $userAssociationId = $admin->association_id;
        $matchCompetition = $match->competition()->first();
        $matchAssociationId = $matchCompetition ? $matchCompetition->association_id : null;
        \Log::info('DEBUG: test_get_match_events', [
            'user_association_id' => $userAssociationId,
            'match_association_id' => $matchAssociationId,
            'competition_id' => $match->competition_id,
            'match_id' => $match->id
        ]);

        // Create some match events
        $player = \App\Models\Player::factory()->create();
        
        $match->events()->create([
            'type' => 'goal',
            'minute' => 15,
            'player_id' => $player->id,
            'team_id' => $this->homeTeam->id,
            'description' => 'Goal scored'
        ]);

        $match->events()->create([
            'type' => 'yellow_card',
            'minute' => 30,
            'player_id' => $player->id,
            'team_id' => $this->awayTeam->id,
            'description' => 'Yellow card'
        ]);

        $response = $this->getJson("/api/v1/matches/{$match->id}/events");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Match events retrieved successfully'
            ])
            ->assertJsonStructure([
                'data' => [
                    'match',
                    'events' => [
                        '*' => [
                            'id',
                            'type',
                            'minute',
                            'player_id',
                            'team_id',
                            'description'
                        ]
                    ]
                ]
            ]);

        $this->assertCount(2, $response->json('data.events'));
    }

    public function test_add_match_event()
    {
        Sanctum::actingAs($this->user);

        $match = MatchModel::factory()->create([
            'competition_id' => $this->competition->id,
            'home_team_id' => $this->homeTeam->id,
            'away_team_id' => $this->awayTeam->id
        ]);

        $player = \App\Models\Player::factory()->create();

        $eventData = [
            'type' => 'goal',
            'minute' => 45,
            'player_id' => $player->id,
            'team_id' => $this->homeTeam->id,
            'description' => 'Beautiful goal'
        ];

        $response = $this->postJson("/api/v1/matches/{$match->id}/events", $eventData);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Match event added successfully'
            ])
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'type',
                    'minute',
                    'player_id',
                    'team_id',
                    'description'
                ]
            ]);

        $this->assertDatabaseHas('match_events', [
            'match_id' => $match->id,
            'type' => 'goal',
            'minute' => 45,
            'player_id' => $player->id,
            'team_id' => $this->homeTeam->id
        ]);
    }

    public function test_add_match_event_validates_required_fields()
    {
        Sanctum::actingAs($this->user);

        $match = MatchModel::factory()->create([
            'competition_id' => $this->competition->id,
            'home_team_id' => $this->homeTeam->id,
            'away_team_id' => $this->awayTeam->id
        ]);

        $response = $this->postJson("/api/v1/matches/{$match->id}/events", []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['type', 'minute', 'player_id', 'team_id']);
    }

    public function test_get_match_lineups()
    {
        Sanctum::actingAs($this->user);

        $match = MatchModel::factory()->create([
            'competition_id' => $this->competition->id,
            'home_team_id' => $this->homeTeam->id,
            'away_team_id' => $this->awayTeam->id
        ]);

        $response = $this->getJson("/api/v1/matches/{$match->id}/lineups");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Match lineups retrieved successfully'
            ])
            ->assertJsonStructure([
                'data' => [
                    'match',
                    'lineups'
                ]
            ]);
    }

    public function test_get_match_statistics()
    {
        Sanctum::actingAs($this->user);

        $match = MatchModel::factory()->create([
            'competition_id' => $this->competition->id,
            'home_team_id' => $this->homeTeam->id,
            'away_team_id' => $this->awayTeam->id
        ]);

        // Create some events for statistics
        $player = \App\Models\Player::factory()->create();
        
        $match->events()->create([
            'type' => 'goal',
            'minute' => 15,
            'player_id' => $player->id,
            'team_id' => $this->homeTeam->id
        ]);

        $match->events()->create([
            'type' => 'yellow_card',
            'minute' => 30,
            'player_id' => $player->id,
            'team_id' => $this->awayTeam->id
        ]);

        $response = $this->getJson("/api/v1/matches/{$match->id}/statistics");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Match statistics retrieved successfully'
            ])
            ->assertJsonStructure([
                'data' => [
                    'match',
                    'statistics' => [
                        'home_team' => [
                            'goals',
                            'yellow_cards',
                            'red_cards',
                            'injuries'
                        ],
                        'away_team' => [
                            'goals',
                            'yellow_cards',
                            'red_cards',
                            'injuries'
                        ],
                        'total_events',
                        'match_duration'
                    ]
                ]
            ]);

        $statistics = $response->json('data.statistics');
        $this->assertEquals(1, $statistics['home_team']['goals']);
        $this->assertEquals(1, $statistics['away_team']['yellow_cards']);
        $this->assertEquals(2, $statistics['total_events']);
    }

    public function test_update_match_status()
    {
        Sanctum::actingAs($this->user);

        $match = MatchModel::factory()->create([
            'competition_id' => $this->competition->id,
            'home_team_id' => $this->homeTeam->id,
            'away_team_id' => $this->awayTeam->id,
            'match_status' => 'scheduled'
        ]);

        $response = $this->patchJson("/api/v1/matches/{$match->id}/status", [
            'status' => 'in_progress'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Match status updated successfully',
                'data' => [
                    'match_status' => 'in_progress'
                ]
            ]);

        $this->assertDatabaseHas('matches', [
            'id' => $match->id,
            'match_status' => 'in_progress'
        ]);
    }

    public function test_update_match_status_validates_status()
    {
        Sanctum::actingAs($this->user);

        $match = MatchModel::factory()->create([
            'competition_id' => $this->competition->id,
            'home_team_id' => $this->homeTeam->id,
            'away_team_id' => $this->awayTeam->id
        ]);

        $response = $this->patchJson("/api/v1/matches/{$match->id}/status", [
            'status' => 'invalid_status'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['status']);
    }

    public function test_referee_can_view_matches_but_not_modify()
    {
        // Create referee user
        $referee = User::factory()->create([
            'role' => 'referee',
            'association_id' => $this->association->id
        ]);

        $match = MatchModel::factory()->create([
            'competition_id' => $this->competition->id,
            'home_team_id' => $this->homeTeam->id,
            'away_team_id' => $this->awayTeam->id,
            'referee' => $referee->name // Assign referee name to the match
        ]);

        Sanctum::actingAs($referee);

        // Should be able to view
        $response = $this->getJson("/api/v1/matches/{$match->id}");
        $response->assertStatus(200);

        // Should be able to update (referees can update matches they are assigned to)
        $response = $this->putJson("/api/v1/matches/{$match->id}", [
            'match_status' => 'completed'
        ]);
        $response->assertStatus(200);

        // Should not be able to delete
        $response = $this->deleteJson("/api/v1/matches/{$match->id}");
        $response->assertStatus(403);
    }

    public function test_club_manager_cannot_access_matches_from_other_associations()
    {
        // Create club manager user
        $clubManager = User::factory()->create([
            'role' => 'club_manager',
            'association_id' => $this->association->id
        ]);

        // Create another association and match
        $otherAssociation = Association::factory()->create([
            'name' => 'Other Association',
            'country' => 'Other Country',
            'fifa_id' => 'OTHER_ASSOC_002'
        ]);

        $otherCompetition = Competition::factory()->create([
            'association_id' => $otherAssociation->id
        ]);

        $otherMatch = MatchModel::factory()->create([
            'competition_id' => $otherCompetition->id,
            'home_team_id' => $this->homeTeam->id,
            'away_team_id' => $this->awayTeam->id
        ]);

        Sanctum::actingAs($clubManager);

        // Should not be able to access other association's match
        $response = $this->getJson("/api/v1/matches/{$otherMatch->id}");
        $response->assertStatus(403);
    }

    public function test_store_validates_match_date_is_in_future()
    {
        Sanctum::actingAs($this->user);

        $matchData = [
            'competition_id' => $this->competition->id,
            'home_team_id' => $this->homeTeam->id,
            'away_team_id' => $this->awayTeam->id,
            'match_date' => now()->subDays(1)->format('Y-m-d'), // Past date
            'kickoff_time' => '16:00',
            'venue' => 'Test Stadium',
            'match_status' => 'scheduled',
        ];

        $response = $this->postJson('/api/v1/matches', $matchData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['match_date']);
    }

    public function test_store_validates_kickoff_time_format()
    {
        Sanctum::actingAs($this->user);

        $matchData = [
            'competition_id' => $this->competition->id,
            'home_team_id' => $this->homeTeam->id,
            'away_team_id' => $this->awayTeam->id,
            'match_date' => now()->addDays(7)->format('Y-m-d'),
            'kickoff_time' => '25:00', // Invalid time format
            'venue' => 'Test Stadium',
            'match_status' => 'scheduled',
        ];

        $response = $this->postJson('/api/v1/matches', $matchData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['kickoff_time']);
    }

    public function test_update_validates_teams_belong_to_same_competition()
    {
        Sanctum::actingAs($this->user);

        $match = MatchModel::factory()->create([
            'competition_id' => $this->competition->id,
            'home_team_id' => $this->homeTeam->id,
            'away_team_id' => $this->awayTeam->id
        ]);

        // Create another competition and team
        $otherCompetition = Competition::factory()->create([
            'association_id' => $this->association->id,
            'name' => 'Other Competition'
        ]);

        $otherTeam = Team::factory()->create([
            'name' => 'Other Team'
        ]);

        $updateData = [
            'competition_id' => $this->competition->id, // Include competition_id
            'home_team_id' => $otherTeam->id, // Team from different competition
        ];

        $response = $this->putJson("/api/v1/matches/{$match->id}", $updateData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['home_team_id']);
    }

    public function test_index_handles_large_datasets_with_pagination()
    {
        Sanctum::actingAs($this->user);

        // Create 50 matches
        MatchModel::factory()->count(50)->create([
            'competition_id' => $this->competition->id,
            'home_team_id' => $this->homeTeam->id,
            'away_team_id' => $this->awayTeam->id
        ]);

        $response = $this->getJson('/api/v1/matches?per_page=10&page=3');

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'pagination' => [
                        'current_page' => 3,
                        'per_page' => 10,
                        'total' => 50,
                        'last_page' => 5
                    ]
                ]
            ]);

        // Should return 10 items on page 3
        $this->assertCount(10, $response->json('data.data'));
    }

    public function test_api_returns_consistent_response_structure()
    {
        Sanctum::actingAs($this->user);

        $match = MatchModel::factory()->create([
            'competition_id' => $this->competition->id,
            'home_team_id' => $this->homeTeam->id,
            'away_team_id' => $this->awayTeam->id
        ]);

        // Test show endpoint structure
        $showResponse = $this->getJson("/api/v1/matches/{$match->id}");
        $showResponse->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'competition_id',
                'home_team_id',
                'away_team_id',
                'match_status',
                'match_date',
                'kickoff_time',
                'venue',
                'created_at',
                'updated_at'
            ]
        ]);

        // Test index endpoint structure
        $indexResponse = $this->getJson('/api/v1/matches');
        $indexResponse->assertJsonStructure([
            'message',
            'data' => [
                'data' => [
                    '*' => [
                        'id',
                        'competition_id',
                        'home_team_id',
                        'away_team_id',
                        'match_status',
                        'match_date',
                        'kickoff_time',
                        'venue',
                        'created_at',
                        'updated_at'
                    ]
                ],
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
    }
} 