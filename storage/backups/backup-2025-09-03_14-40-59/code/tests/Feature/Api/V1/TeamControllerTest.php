<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;
use App\Models\User;
use App\Models\Team;
use App\Models\Club;
use App\Models\Association;
use App\Models\Player;
use App\Models\Competition;
use App\Models\Standing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;

class TeamControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $admin;
    protected User $associationManager;
    protected User $clubManager;
    protected User $referee;
    protected Association $association;
    protected Club $club;
    protected Player $player;
    protected Competition $competition;

    protected function setUp(): void
    {
        parent::setUp();

        // Create association
        $this->association = Association::factory()->create();

        // Create club
        $this->club = Club::factory()->create([
            'association_id' => $this->association->id
        ]);

        // Create users with different roles
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'association_id' => $this->association->id
        ]);

        $this->associationManager = User::factory()->create([
            'role' => 'association_manager',
            'association_id' => $this->association->id
        ]);

        $this->clubManager = User::factory()->create([
            'role' => 'club_manager',
            'club_id' => $this->club->id,
            'association_id' => $this->association->id
        ]);

        $this->referee = User::factory()->create([
            'role' => 'referee',
            'association_id' => $this->association->id
        ]);

        // Create player
        $this->player = Player::factory()->create([
            'club_id' => $this->club->id,
            'association_id' => $this->association->id
        ]);

        // Create competition
        $this->competition = Competition::factory()->create([
            'association_id' => $this->association->id
        ]);
    }

    /** @test */
    public function it_can_list_teams_with_pagination()
    {
        Sanctum::actingAs($this->admin);

        $teams = Team::factory()->count(5)->create([
            'club_id' => $this->club->id,
            'association_id' => $this->association->id
        ]);

        $response = $this->getJson('/api/v1/teams');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'club_id',
                        'association_id',
                        'status',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total'
                ]
            ]);

        $this->assertEquals(5, $response->json('meta.total'));
    }

    /** @test */
    public function it_can_filter_teams_by_club()
    {
        Sanctum::actingAs($this->admin);

        $otherClub = Club::factory()->create(['association_id' => $this->association->id]);
        
        Team::factory()->create([
            'club_id' => $this->club->id,
            'association_id' => $this->association->id
        ]);
        
        Team::factory()->create([
            'club_id' => $otherClub->id,
            'association_id' => $this->association->id
        ]);

        $response = $this->getJson("/api/v1/teams?club_id={$this->club->id}");

        $response->assertStatus(200);
        $this->assertEquals(1, $response->json('meta.total'));
        $this->assertEquals($this->club->id, $response->json('data.0.club_id'));
    }

    /** @test */
    public function it_can_filter_teams_by_association()
    {
        Sanctum::actingAs($this->admin);

        $otherAssociation = Association::factory()->create();
        $otherClub = Club::factory()->create(['association_id' => $otherAssociation->id]);
        
        Team::factory()->create([
            'club_id' => $this->club->id,
            'association_id' => $this->association->id
        ]);
        
        Team::factory()->create([
            'club_id' => $otherClub->id,
            'association_id' => $otherAssociation->id
        ]);

        $response = $this->getJson("/api/v1/teams?association_id={$this->association->id}");

        $response->assertStatus(200);
        $this->assertEquals(1, $response->json('meta.total'));
        $this->assertEquals($this->association->id, $response->json('data.0.association_id'));
    }

    /** @test */
    public function it_can_filter_teams_by_competition()
    {
        Sanctum::actingAs($this->admin);

        $team = Team::factory()->create([
            'club_id' => $this->club->id,
            'association_id' => $this->association->id
        ]);
        
        $team->competitions()->attach($this->competition->id);

        $otherTeam = Team::factory()->create([
            'club_id' => $this->club->id,
            'association_id' => $this->association->id
        ]);

        $response = $this->getJson("/api/v1/teams?competition_id={$this->competition->id}");

        $response->assertStatus(200);
        $this->assertEquals(1, $response->json('meta.total'));
        $this->assertEquals($team->id, $response->json('data.0.id'));
    }

    /** @test */
    public function it_can_filter_teams_by_status()
    {
        Sanctum::actingAs($this->admin);

        Team::factory()->create([
            'club_id' => $this->club->id,
            'association_id' => $this->association->id,
            'status' => 'active'
        ]);
        
        Team::factory()->create([
            'club_id' => $this->club->id,
            'association_id' => $this->association->id,
            'status' => 'inactive'
        ]);

        $response = $this->getJson('/api/v1/teams?status=active');

        $response->assertStatus(200);
        $this->assertEquals(1, $response->json('meta.total'));
        $this->assertEquals('active', $response->json('data.0.status'));
    }

    /** @test */
    public function it_can_search_teams_by_name()
    {
        Sanctum::actingAs($this->admin);

        Team::factory()->create([
            'club_id' => $this->club->id,
            'association_id' => $this->association->id,
            'name' => 'Manchester United'
        ]);
        
        Team::factory()->create([
            'club_id' => $this->club->id,
            'association_id' => $this->association->id,
            'name' => 'Liverpool FC'
        ]);

        $response = $this->getJson('/api/v1/teams?search=Manchester');

        $response->assertStatus(200);
        $this->assertEquals(1, $response->json('meta.total'));
        $this->assertEquals('Manchester United', $response->json('data.0.name'));
    }

    /** @test */
    public function it_can_create_a_team()
    {
        Sanctum::actingAs($this->admin);

        $teamData = [
            'name' => 'Test Team',
            'club_id' => $this->club->id,
            'association_id' => $this->association->id,
            'status' => 'active',
            'founded_year' => 2020,
            'home_ground' => 'Test Stadium',
            'capacity' => 50000,
            'colors' => 'Red and White',
            'logo_url' => 'https://example.com/logo.png',
            'website' => 'https://example.com',
            'description' => 'A test team',
            'competition_ids' => [$this->competition->id],
            'player_ids' => [$this->player->id]
        ];

        $response = $this->postJson('/api/v1/teams', $teamData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'name',
                    'club_id',
                    'association_id',
                    'status',
                    'founded_year',
                    'home_ground',
                    'capacity',
                    'colors',
                    'logo_url',
                    'website',
                    'description',
                    'created_at',
                    'updated_at'
                ]
            ]);

        $this->assertDatabaseHas('teams', [
            'name' => 'Test Team',
            'club_id' => $this->club->id,
            'association_id' => $this->association->id,
            'status' => 'active'
        ]);

        $team = Team::where('name', 'Test Team')->first();
        $this->assertTrue($team->competitions()->where('competition_id', $this->competition->id)->exists());
        $this->assertTrue($team->players()->where('player_id', $this->player->id)->exists());
    }

    /** @test */
    public function it_validates_required_fields_when_creating_team()
    {
        Sanctum::actingAs($this->admin);

        $response = $this->postJson('/api/v1/teams', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'club_id', 'association_id', 'status']);
    }

    /** @test */
    public function it_validates_club_exists_when_creating_team()
    {
        Sanctum::actingAs($this->admin);

        $response = $this->postJson('/api/v1/teams', [
            'name' => 'Test Team',
            'club_id' => 99999,
            'association_id' => $this->association->id,
            'status' => 'active'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['club_id']);
    }

    /** @test */
    public function it_validates_association_exists_when_creating_team()
    {
        Sanctum::actingAs($this->admin);

        $response = $this->postJson('/api/v1/teams', [
            'name' => 'Test Team',
            'club_id' => $this->club->id,
            'association_id' => 99999,
            'status' => 'active'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['association_id']);
    }

    /** @test */
    public function it_validates_founded_year_range()
    {
        Sanctum::actingAs($this->admin);

        $response = $this->postJson('/api/v1/teams', [
            'name' => 'Test Team',
            'club_id' => $this->club->id,
            'association_id' => $this->association->id,
            'status' => 'active',
            'founded_year' => 1700
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['founded_year']);
    }

    /** @test */
    public function it_validates_url_fields()
    {
        Sanctum::actingAs($this->admin);

        $response = $this->postJson('/api/v1/teams', [
            'name' => 'Test Team',
            'club_id' => $this->club->id,
            'association_id' => $this->association->id,
            'status' => 'active',
            'logo_url' => 'invalid-url',
            'website' => 'invalid-url'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['logo_url', 'website']);
    }

    /** @test */
    public function it_can_show_a_team()
    {
        Sanctum::actingAs($this->admin);

        $team = Team::factory()->create([
            'club_id' => $this->club->id,
            'association_id' => $this->association->id
        ]);

        $response = $this->getJson("/api/v1/teams/{$team->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'club_id',
                    'association_id',
                    'status',
                    'created_at',
                    'updated_at'
                ]
            ]);

        $this->assertEquals($team->id, $response->json('data.id'));
    }

    /** @test */
    public function it_can_update_a_team()
    {
        Sanctum::actingAs($this->admin);

        $team = Team::factory()->create([
            'club_id' => $this->club->id,
            'association_id' => $this->association->id
        ]);

        $updateData = [
            'name' => 'Updated Team Name',
            'status' => 'inactive',
            'home_ground' => 'Updated Stadium',
            'capacity' => 60000,
            'competition_ids' => [$this->competition->id]
        ];

        $response = $this->putJson("/api/v1/teams/{$team->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Team updated successfully'
            ]);

        $this->assertDatabaseHas('teams', [
            'id' => $team->id,
            'name' => 'Updated Team Name',
            'status' => 'inactive',
            'home_ground' => 'Updated Stadium',
            'capacity' => 60000
        ]);

        $this->assertTrue($team->fresh()->competitions()->where('competition_id', $this->competition->id)->exists());
    }

    /** @test */
    public function it_can_delete_a_team()
    {
        Sanctum::actingAs($this->admin);

        $team = Team::factory()->create([
            'club_id' => $this->club->id,
            'association_id' => $this->association->id
        ]);

        $response = $this->deleteJson("/api/v1/teams/{$team->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Team deleted successfully'
            ]);

        $this->assertDatabaseMissing('teams', ['id' => $team->id]);
    }

    /** @test */
    public function it_cannot_delete_team_with_active_matches()
    {
        Sanctum::actingAs($this->admin);

        $team = Team::factory()->create([
            'club_id' => $this->club->id,
            'association_id' => $this->association->id
        ]);

        // Create a match with this team (simulating active match)
        \App\Models\MatchModel::factory()->create([
            'home_team_id' => $team->id,
            'match_status' => 'scheduled'
        ]);

        $response = $this->deleteJson("/api/v1/teams/{$team->id}");

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['team']);
    }

    /** @test */
    public function it_can_add_player_to_team()
    {
        Sanctum::actingAs($this->admin);

        $team = Team::factory()->create([
            'club_id' => $this->club->id,
            'association_id' => $this->association->id
        ]);

        $response = $this->postJson("/api/v1/teams/{$team->id}/players", [
            'player_id' => $this->player->id,
            'role' => 'substitute'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Player added to team successfully'
            ]);

        $this->assertTrue($team->players()->where('player_id', $this->player->id)->exists());
    }

    /** @test */
    public function it_cannot_add_player_already_in_team()
    {
        Sanctum::actingAs($this->admin);

        $team = Team::factory()->create([
            'club_id' => $this->club->id,
            'association_id' => $this->association->id
        ]);

        $team->players()->attach($this->player->id, [
            'joined_date' => now()->toDateString(),
            'role' => 'substitute'
        ]);

        $response = $this->postJson("/api/v1/teams/{$team->id}/players", [
            'player_id' => $this->player->id
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['player_id']);
    }

    /** @test */
    public function it_cannot_add_player_to_another_team_in_same_competition()
    {
        Sanctum::actingAs($this->admin);

        $team1 = Team::factory()->create([
            'club_id' => $this->club->id,
            'association_id' => $this->association->id
        ]);
        
        $team2 = Team::factory()->create([
            'club_id' => $this->club->id,
            'association_id' => $this->association->id
        ]);

        $team1->competitions()->attach($this->competition->id);
        $team2->competitions()->attach($this->competition->id);
        $team1->players()->attach($this->player->id, [
            'joined_date' => now()->toDateString(),
            'role' => 'substitute'
        ]);

        $response = $this->postJson("/api/v1/teams/{$team2->id}/players", [
            'player_id' => $this->player->id
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['player_id']);
    }

    /** @test */
    public function it_can_remove_player_from_team()
    {
        Sanctum::actingAs($this->admin);

        $team = Team::factory()->create([
            'club_id' => $this->club->id,
            'association_id' => $this->association->id
        ]);

        $team->players()->attach($this->player->id, [
            'joined_date' => now()->toDateString(),
            'role' => 'substitute'
        ]);

        $response = $this->deleteJson("/api/v1/teams/{$team->id}/players", [
            'player_id' => $this->player->id
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Player removed from team successfully'
            ]);

        $this->assertFalse($team->players()->where('player_id', $this->player->id)->exists());
    }

    /** @test */
    public function it_cannot_remove_player_not_in_team()
    {
        Sanctum::actingAs($this->admin);

        $team = Team::factory()->create([
            'club_id' => $this->club->id,
            'association_id' => $this->association->id
        ]);

        $response = $this->deleteJson("/api/v1/teams/{$team->id}/players", [
            'player_id' => $this->player->id
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['player_id']);
    }

    /** @test */
    public function it_can_get_team_roster()
    {
        Sanctum::actingAs($this->admin);

        $team = Team::factory()->create([
            'club_id' => $this->club->id,
            'association_id' => $this->association->id
        ]);

        $team->players()->attach($this->player->id, [
            'joined_date' => now()->toDateString(),
            'role' => 'substitute'
        ]);

        $response = $this->getJson("/api/v1/teams/{$team->id}/roster");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'team' => [
                        'id',
                        'name'
                    ],
                    'roster' => [
                        '*' => [
                            'id',
                            'name',
                            'position',
                            'joined_at',
                            'role'
                        ]
                    ]
                ]
            ]);

        $this->assertEquals(1, count($response->json('data.roster')));
        $this->assertEquals($this->player->id, $response->json('data.roster.0.id'));
    }

    /** @test */
    public function it_can_get_team_statistics()
    {
        Sanctum::actingAs($this->admin);

        $team = Team::factory()->create([
            'club_id' => $this->club->id,
            'association_id' => $this->association->id
        ]);

        $team->players()->attach($this->player->id, [
            'joined_date' => now()->toDateString(),
            'role' => 'substitute'
        ]);
        $this->competition->status = 'active';
        $this->competition->save();
        $team->competitions()->attach($this->competition->id);

        $response = $this->getJson("/api/v1/teams/{$team->id}/statistics");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'team' => [
                        'id',
                        'name'
                    ],
                    'statistics' => [
                        'total_matches',
                        'wins',
                        'losses',
                        'draws',
                        'total_players',
                        'active_competitions'
                    ]
                ]
            ]);

        $stats = $response->json('data.statistics');
        $this->assertEquals(1, $stats['total_players']);
        $this->assertEquals(1, $stats['active_competitions']);
    }

    /** @test */
    public function it_can_get_team_standings()
    {
        Sanctum::actingAs($this->admin);

        $team = Team::factory()->create([
            'club_id' => $this->club->id,
            'association_id' => $this->association->id
        ]);

        Standing::factory()->create([
            'team_id' => $team->id,
            'competition_id' => $this->competition->id,
            'position' => 1,
            'points' => 30
        ]);

        $response = $this->getJson("/api/v1/teams/{$team->id}/standings");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'team' => [
                        'id',
                        'name'
                    ],
                    'standings' => [
                        '*' => [
                            'competition',
                            'position',
                            'points',
                            'matches_played',
                            'wins',
                            'draws',
                            'losses',
                            'goals_for',
                            'goals_against',
                            'goal_difference'
                        ]
                    ]
                ]
            ]);

        $this->assertEquals(1, count($response->json('data.standings')));
        $this->assertEquals(1, $response->json('data.standings.0.position'));
        $this->assertEquals(30, $response->json('data.standings.0.points'));
    }

    /** @test */
    public function it_enforces_authorization_for_viewing_teams()
    {
        $team = Team::factory()->create([
            'club_id' => $this->club->id,
            'association_id' => $this->association->id
        ]);

        // Unauthenticated user
        $response = $this->getJson("/api/v1/teams/{$team->id}");
        $response->assertStatus(401);

        // User without proper role
        $user = User::factory()->create(['role' => 'player']);
        Sanctum::actingAs($user);
        
        $response = $this->getJson("/api/v1/teams/{$team->id}");
        $response->assertStatus(403);
    }

    /** @test */
    public function it_enforces_authorization_for_creating_teams()
    {
        $teamData = [
            'name' => 'Test Team',
            'club_id' => $this->club->id,
            'association_id' => $this->association->id,
            'status' => 'active'
        ];

        // Unauthenticated user
        $response = $this->postJson('/api/v1/teams', $teamData);
        $response->assertStatus(401);

        // User without proper role
        $user = User::factory()->create(['role' => 'player']);
        Sanctum::actingAs($user);
        
        $response = $this->postJson('/api/v1/teams', $teamData);
        $response->assertStatus(403);
    }

    /** @test */
    public function it_enforces_authorization_for_updating_teams()
    {
        $team = Team::factory()->create([
            'club_id' => $this->club->id,
            'association_id' => $this->association->id
        ]);

        $updateData = ['name' => 'Updated Name'];

        // Unauthenticated user
        $response = $this->putJson("/api/v1/teams/{$team->id}", $updateData);
        $response->assertStatus(401);

        // User without proper role
        $user = User::factory()->create(['role' => 'player']);
        Sanctum::actingAs($user);
        
        $response = $this->putJson("/api/v1/teams/{$team->id}", $updateData);
        $response->assertStatus(403);
    }

    /** @test */
    public function it_enforces_authorization_for_deleting_teams()
    {
        $team = Team::factory()->create([
            'club_id' => $this->club->id,
            'association_id' => $this->association->id
        ]);

        // Unauthenticated user
        $response = $this->deleteJson("/api/v1/teams/{$team->id}");
        $response->assertStatus(401);

        // User without proper role
        $user = User::factory()->create(['role' => 'player']);
        Sanctum::actingAs($user);
        
        $response = $this->deleteJson("/api/v1/teams/{$team->id}");
        $response->assertStatus(403);
    }

    /** @test */
    public function it_enforces_association_scoped_authorization()
    {
        $otherAssociation = Association::factory()->create();
        $otherClub = Club::factory()->create(['association_id' => $otherAssociation->id]);
        
        $team = Team::factory()->create([
            'club_id' => $otherClub->id,
            'association_id' => $otherAssociation->id
        ]);

        // Association manager can only access teams in their association
        Sanctum::actingAs($this->associationManager);
        
        $response = $this->getJson("/api/v1/teams/{$team->id}");
        $response->assertStatus(403);
    }

    /** @test */
    public function it_enforces_club_scoped_authorization()
    {
        $otherClub = Club::factory()->create(['association_id' => $this->association->id]);
        
        $team = Team::factory()->create([
            'club_id' => $otherClub->id,
            'association_id' => $this->association->id
        ]);

        // Club manager can only access teams in their club
        Sanctum::actingAs($this->clubManager);
        
        $response = $this->getJson("/api/v1/teams/{$team->id}");
        $response->assertStatus(403);
    }

    /** @test */
    public function it_sorts_teams_by_specified_field()
    {
        Sanctum::actingAs($this->admin);

        Team::factory()->create([
            'club_id' => $this->club->id,
            'association_id' => $this->association->id,
            'name' => 'Zebra Team'
        ]);
        
        Team::factory()->create([
            'club_id' => $this->club->id,
            'association_id' => $this->association->id,
            'name' => 'Alpha Team'
        ]);

        $response = $this->getJson('/api/v1/teams?sort_by=name&sort_direction=asc');

        $response->assertStatus(200);
        $this->assertEquals('Alpha Team', $response->json('data.0.name'));
        $this->assertEquals('Zebra Team', $response->json('data.1.name'));
    }

    /** @test */
    public function it_paginates_teams_correctly()
    {
        Sanctum::actingAs($this->admin);

        Team::factory()->count(25)->create([
            'club_id' => $this->club->id,
            'association_id' => $this->association->id
        ]);

        $response = $this->getJson('/api/v1/teams?per_page=10');

        $response->assertStatus(200);
        $this->assertEquals(10, $response->json('meta.per_page'));
        $this->assertEquals(25, $response->json('meta.total'));
        $this->assertEquals(3, $response->json('meta.last_page'));
        $this->assertEquals(10, count($response->json('data')));
    }

    /** @test */
    public function it_validates_player_exists_when_adding_to_team()
    {
        Sanctum::actingAs($this->admin);

        $team = Team::factory()->create([
            'club_id' => $this->club->id,
            'association_id' => $this->association->id
        ]);

        $response = $this->postJson("/api/v1/teams/{$team->id}/players", [
            'player_id' => 99999
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['player_id']);
    }

    /** @test */
    public function it_validates_player_exists_when_removing_from_team()
    {
        Sanctum::actingAs($this->admin);

        $team = Team::factory()->create([
            'club_id' => $this->club->id,
            'association_id' => $this->association->id
        ]);

        $response = $this->deleteJson("/api/v1/teams/{$team->id}/players", [
            'player_id' => 99999
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['player_id']);
    }

    /** @test */
    public function it_validates_role_when_adding_player()
    {
        Sanctum::actingAs($this->admin);

        $team = Team::factory()->create([
            'club_id' => $this->club->id,
            'association_id' => $this->association->id
        ]);

        $response = $this->postJson("/api/v1/teams/{$team->id}/players", [
            'player_id' => $this->player->id,
            'role' => 'invalid_role'
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['role']);
    }

    /** @test */
    public function it_returns_404_for_nonexistent_team()
    {
        Sanctum::actingAs($this->admin);

        $response = $this->getJson('/api/v1/teams/99999');
        $response->assertStatus(404);

        $response = $this->putJson('/api/v1/teams/99999', ['name' => 'Updated']);
        $response->assertStatus(404);

        $response = $this->deleteJson('/api/v1/teams/99999');
        $response->assertStatus(404);
    }

    /** @test */
    public function it_handles_empty_team_list()
    {
        Sanctum::actingAs($this->admin);

        $response = $this->getJson('/api/v1/teams');

        $response->assertStatus(200);
        $this->assertEquals(0, $response->json('meta.total'));
        $this->assertEquals(0, count($response->json('data')));
    }

    /** @test */
    public function it_handles_empty_team_roster()
    {
        Sanctum::actingAs($this->admin);

        $team = Team::factory()->create([
            'club_id' => $this->club->id,
            'association_id' => $this->association->id
        ]);

        $response = $this->getJson("/api/v1/teams/{$team->id}/roster");

        $response->assertStatus(200);
        $this->assertEquals(0, count($response->json('data.roster')));
    }

    /** @test */
    public function it_handles_empty_team_standings()
    {
        Sanctum::actingAs($this->admin);

        $team = Team::factory()->create([
            'club_id' => $this->club->id,
            'association_id' => $this->association->id
        ]);

        $response = $this->getJson("/api/v1/teams/{$team->id}/standings");

        $response->assertStatus(200);
        $this->assertEquals(0, count($response->json('data.standings')));
    }
} 