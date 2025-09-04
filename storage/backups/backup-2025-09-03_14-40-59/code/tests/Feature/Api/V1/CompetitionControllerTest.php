<?php

namespace Tests\Feature\Api\V1;

use App\Models\Association;
use App\Models\Competition;
use App\Models\Season;
use App\Models\Standing;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CompetitionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $association;
    protected $competition;
    protected $season;
    protected $homeTeam;
    protected $awayTeam;

    protected function setUp(): void
    {
        parent::setUp();

        // Create association
        $this->association = Association::factory()->create();

        // Create user with appropriate role
        $this->user = User::factory()->create([
            'role' => 'association_admin',
            'association_id' => $this->association->id
        ]);

        // Create competition
        $this->competition = Competition::factory()->create([
            'association_id' => $this->association->id,
            'name' => 'Test League',
            'type' => 'league',
            'format' => 'round_robin',
            'status' => 'active'
        ]);

        // Create season
        $this->season = Season::factory()->create([
            'name' => '2024-2025',
            'status' => 'active',
            'competition_id' => $this->competition->id
        ]);

        // Associate season with competition
        $this->competition->update(['season_id' => $this->season->id]);

        // Create teams
        $this->homeTeam = Team::factory()->create([
            'name' => 'Home Team',
            'association_id' => $this->association->id
        ]);

        $this->awayTeam = Team::factory()->create([
            'name' => 'Away Team',
            'association_id' => $this->association->id
        ]);

        // Attach teams to competition
        $this->competition->teams()->attach([$this->homeTeam->id, $this->awayTeam->id]);
    }

    public function test_index_returns_competitions_for_authenticated_user()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/v1/competitions');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Competitions retrieved successfully'
            ])
            ->assertJsonStructure([
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'type',
                            'format',
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
        $response = $this->getJson('/api/v1/competitions');

        $response->assertStatus(401);
    }

    public function test_show_returns_competition_details()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson("/api/v1/competitions/{$this->competition->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Competition retrieved successfully',
                'data' => [
                    'id' => $this->competition->id,
                    'name' => 'Test League'
                ]
            ]);
    }

    public function test_show_returns_404_for_nonexistent_competition()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/v1/competitions/99999');

        $response->assertStatus(404);
    }

    public function test_store_creates_new_competition()
    {
        Sanctum::actingAs($this->user);

        $competitionData = [
            'name' => 'New Competition',
            'description' => 'A new test competition',
            'type' => 'cup',
            'format' => 'knockout',
            'status' => 'active',
            'association_id' => $this->association->id,
            'start_date' => now()->addDays(10)->toDateString(),
            'end_date' => now()->addDays(30)->toDateString(),
            'max_teams' => 16,
            'age_group' => 'senior',
            'gender' => 'male'
        ];

        $response = $this->postJson('/api/v1/competitions', $competitionData);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Competition created successfully'
            ])
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'type',
                    'format',
                    'status'
                ]
            ]);

        $this->assertDatabaseHas('competitions', [
            'name' => 'New Competition',
            'type' => 'cup',
            'association_id' => $this->association->id
        ]);
    }

    public function test_store_validates_required_fields()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/v1/competitions', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'type', 'format', 'status', 'association_id', 'start_date', 'end_date']);
    }

    public function test_store_validates_competition_type()
    {
        Sanctum::actingAs($this->user);

        $competitionData = [
            'name' => 'Invalid Competition',
            'type' => 'invalid_type',
            'format' => 'round_robin',
            'status' => 'active',
            'association_id' => $this->association->id,
            'start_date' => '2024-09-01',
            'end_date' => '2024-12-31'
        ];

        $response = $this->postJson('/api/v1/competitions', $competitionData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['type']);
    }

    public function test_update_modifies_existing_competition()
    {
        Sanctum::actingAs($this->user);

        $updateData = [
            'name' => 'Updated Competition',
            'description' => 'Updated description',
            'status' => 'inactive'
        ];

        $response = $this->putJson("/api/v1/competitions/{$this->competition->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Competition updated successfully',
                'data' => [
                    'name' => 'Updated Competition',
                    'status' => 'inactive'
                ]
            ]);

        $this->assertDatabaseHas('competitions', [
            'id' => $this->competition->id,
            'name' => 'Updated Competition',
            'status' => 'inactive'
        ]);
    }

    public function test_update_returns_404_for_nonexistent_competition()
    {
        Sanctum::actingAs($this->user);

        $response = $this->putJson('/api/v1/competitions/99999', ['name' => 'Test']);

        $response->assertStatus(404);
    }

    public function test_destroy_deletes_competition()
    {
        Sanctum::actingAs($this->user);

        $response = $this->deleteJson("/api/v1/competitions/{$this->competition->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Competition deleted successfully'
            ]);

        $this->assertDatabaseMissing('competitions', [
            'id' => $this->competition->id
        ]);
    }

    public function test_destroy_returns_404_for_nonexistent_competition()
    {
        Sanctum::actingAs($this->user);

        $response = $this->deleteJson('/api/v1/competitions/99999');

        $response->assertStatus(404);
    }

    public function test_association_admin_can_only_access_own_association_competitions()
    {
        // Create another association and competition
        $otherAssociation = Association::factory()->create();
        $otherCompetition = Competition::factory()->create([
            'association_id' => $otherAssociation->id
        ]);

        Sanctum::actingAs($this->user);

        // Should be able to access own competition
        $response = $this->getJson("/api/v1/competitions/{$this->competition->id}");
        $response->assertStatus(200);

        // Should not be able to access other association's competition
        $response = $this->getJson("/api/v1/competitions/{$otherCompetition->id}");
        $response->assertStatus(403);
    }

    public function test_system_admin_can_access_all_competitions()
    {
        // Create system admin user
        $systemAdmin = User::factory()->create(['role' => 'system_admin']);

        // Create another association and competition
        $otherAssociation = Association::factory()->create();
        $otherCompetition = Competition::factory()->create([
            'association_id' => $otherAssociation->id
        ]);

        Sanctum::actingAs($systemAdmin);

        // Should be able to access both competitions
        $response = $this->getJson("/api/v1/competitions/{$this->competition->id}");
        $response->assertStatus(200);

        $response = $this->getJson("/api/v1/competitions/{$otherCompetition->id}");
        $response->assertStatus(200);
    }

    public function test_pagination_works_correctly()
    {
        Sanctum::actingAs($this->user);

        // Create additional competitions
        Competition::factory()->count(25)->create([
            'association_id' => $this->association->id
        ]);

        $response = $this->getJson('/api/v1/competitions?per_page=10');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'pagination' => [
                        'current_page',
                        'last_page',
                        'per_page',
                        'total',
                        'from',
                        'to'
                    ]
                ]
            ]);

        $pagination = $response->json('data.pagination');
        $this->assertEquals(10, $pagination['per_page']);
        $this->assertGreaterThan(1, $pagination['last_page']);
    }

    public function test_search_filter_works()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/v1/competitions?search=Test League');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data.data'));
    }

    public function test_status_filter_works()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/v1/competitions?status=active');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data.data'));
    }

    public function test_get_competition_standings()
    {
        Sanctum::actingAs($this->user);

        // Create standings
        Standing::factory()->create([
            'competition_id' => $this->competition->id,
            'season_id' => $this->season->id,
            'team_id' => $this->homeTeam->id,
            'points' => 10,
            'played' => 5,
            'won' => 3,
            'drawn' => 1,
            'lost' => 1,
            'goals_for' => 8,
            'goals_against' => 4
        ]);

        Standing::factory()->create([
            'competition_id' => $this->competition->id,
            'season_id' => $this->season->id,
            'team_id' => $this->awayTeam->id,
            'points' => 8,
            'played' => 5,
            'won' => 2,
            'drawn' => 2,
            'lost' => 1,
            'goals_for' => 6,
            'goals_against' => 5
        ]);

        $response = $this->getJson("/api/v1/competitions/{$this->competition->id}/standings?season_id={$this->season->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Competition standings retrieved successfully'
            ])
            ->assertJsonStructure([
                'data' => [
                    'competition',
                    'season_id',
                    'standings' => [
                        '*' => [
                            'position',
                            'team',
                            'team_id',
                            'played',
                            'won',
                            'drawn',
                            'lost',
                            'goals_for',
                            'goals_against',
                            'goal_difference',
                            'points'
                        ]
                    ]
                ]
            ]);

        $standings = $response->json('data.standings');
        $this->assertCount(2, $standings);
        $this->assertEquals(1, $standings[0]['position']); // Home team should be first with 10 points
        $this->assertEquals(2, $standings[1]['position']); // Away team should be second with 8 points
    }

    public function test_get_competition_seasons()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson("/api/v1/competitions/{$this->competition->id}/seasons");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Competition seasons retrieved successfully'
            ])
            ->assertJsonStructure([
                'data' => [
                    'competition',
                    'seasons'
                ]
            ]);

        $seasons = $response->json('data.seasons');
        $this->assertCount(1, $seasons);
        $this->assertEquals('2024-2025', $seasons[0]['name']);
    }

    public function test_get_competition_teams()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson("/api/v1/competitions/{$this->competition->id}/teams");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Competition teams retrieved successfully'
            ])
            ->assertJsonStructure([
                'data' => [
                    'competition',
                    'teams'
                ]
            ]);

        $teams = $response->json('data.teams');
        $this->assertCount(2, $teams);
    }

    public function test_get_competition_statistics()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson("/api/v1/competitions/{$this->competition->id}/statistics?season_id={$this->season->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Competition statistics retrieved successfully'
            ])
            ->assertJsonStructure([
                'data' => [
                    'competition',
                    'season_id',
                    'statistics' => [
                        'total_matches',
                        'completed_matches',
                        'scheduled_matches',
                        'total_teams',
                        'total_goals',
                        'average_goals_per_match',
                        'top_scorers',
                        'most_assists',
                        'clean_sheets'
                    ]
                ]
            ]);
    }

    public function test_add_team_to_competition()
    {
        Sanctum::actingAs($this->user);

        $newTeam = Team::factory()->create([
            'association_id' => $this->association->id
        ]);

        $response = $this->postJson("/api/v1/competitions/{$this->competition->id}/teams", [
            'team_id' => $newTeam->id,
            'season_id' => $this->season->id
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Team added to competition successfully'
            ]);

        $this->assertDatabaseHas('competition_team', [
            'competition_id' => $this->competition->id,
            'team_id' => $newTeam->id
        ]);
    }

    public function test_remove_team_from_competition()
    {
        Sanctum::actingAs($this->user);

        $response = $this->deleteJson("/api/v1/competitions/{$this->competition->id}/teams", [
            'team_id' => $this->homeTeam->id
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Team removed from competition successfully'
            ]);

        $this->assertDatabaseMissing('competition_team', [
            'competition_id' => $this->competition->id,
            'team_id' => $this->homeTeam->id
        ]);
    }

    public function test_cannot_add_duplicate_team_to_competition()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson("/api/v1/competitions/{$this->competition->id}/teams", [
            'team_id' => $this->homeTeam->id,
            'season_id' => $this->season->id
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Team is already in this competition'
            ]);
    }

    public function test_store_validates_end_date_after_start_date()
    {
        Sanctum::actingAs($this->user);

        $competitionData = [
            'name' => 'Invalid Competition',
            'type' => 'league',
            'format' => 'round_robin',
            'status' => 'active',
            'association_id' => $this->association->id,
            'start_date' => '2024-12-31',
            'end_date' => '2024-09-01' // End date before start date
        ];

        $response = $this->postJson('/api/v1/competitions', $competitionData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['end_date']);
    }

    public function test_store_validates_max_teams_greater_than_min_teams()
    {
        Sanctum::actingAs($this->user);

        $competitionData = [
            'name' => 'Invalid Competition',
            'type' => 'league',
            'format' => 'round_robin',
            'status' => 'active',
            'association_id' => $this->association->id,
            'start_date' => '2024-09-01',
            'end_date' => '2024-12-31',
            'max_teams' => 8,
            'min_teams' => 16 // Min teams greater than max teams
        ];

        $response = $this->postJson('/api/v1/competitions', $competitionData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['min_teams']);
    }

    public function test_api_returns_consistent_response_structure()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/v1/competitions');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'type',
                            'format',
                            'status',
                            'association',
                            'start_date',
                            'end_date',
                            'created_at',
                            'updated_at'
                        ]
                    ],
                    'pagination'
                ]
            ]);
    }
}
