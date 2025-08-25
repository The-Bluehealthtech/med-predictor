<?php

namespace Tests\Feature\Api\V1;

use App\Models\MatchSheet;
use App\Models\MatchModel;
use App\Models\User;
use App\Models\Team;
use App\Models\Competition;
use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MatchSheetControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $systemAdmin;
    protected User $faAdmin;
    protected User $referee;
    protected User $clubAdmin;
    protected User $teamOfficial;
    protected MatchModel $match;
    protected Team $homeTeam;
    protected Team $awayTeam;
    protected Competition $competition;

    protected function setUp(): void
    {
        parent::setUp();

        // Create users with different roles
        $this->systemAdmin = User::factory()->create(['role' => 'system_admin']);
        $this->faAdmin = User::factory()->create(['role' => 'association_admin']);
        $this->referee = User::factory()->create(['role' => 'referee']);
        $this->clubAdmin = User::factory()->create(['role' => 'club_admin']);
        $this->teamOfficial = User::factory()->create(['role' => 'team_official']);

        // Create competition and teams
        $this->competition = Competition::factory()->create();
        $this->homeTeam = Team::factory()->create();
        $this->awayTeam = Team::factory()->create();

        // Create match
        $this->match = MatchModel::factory()->create([
            'home_team_id' => $this->homeTeam->id,
            'away_team_id' => $this->awayTeam->id,
            'competition_id' => $this->competition->id,
        ]);

        // Create some players for rosters
        Player::factory()->count(30)->create();
    }

    // Index Tests
    public function test_system_admin_can_view_all_match_sheets(): void
    {
        MatchSheet::factory()->count(3)->create([
            'match_id' => $this->match->id,
            'referee_id' => $this->referee->id,
            'main_referee_id' => $this->referee->id,
            'assistant_referee_1_id' => $this->referee->id,
            'assistant_referee_2_id' => $this->referee->id,
            'fourth_official_id' => $this->referee->id,
            'var_referee_id' => $this->referee->id,
            'var_assistant_id' => $this->referee->id,
            'match_observer_id' => $this->referee->id,
            'validated_by' => $this->faAdmin->id,
            'rejected_by' => $this->faAdmin->id,
            'fa_validated_by' => $this->faAdmin->id,
            'assigned_referee_id' => $this->referee->id,
        ]);

        $response = $this->actingAs($this->systemAdmin)
            ->getJson('/api/v1/match-sheets');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id', 'match_id', 'match_number', 'stadium_venue',
                        'kickoff_time', 'status', 'stage'
                    ]
                ],
                'meta' => ['current_page', 'total', 'per_page']
            ]);

        $this->assertEquals(3, $response->json('meta.total'));
    }

    public function test_referee_can_view_assigned_match_sheets(): void
    {
        // Create match sheet assigned to this referee
        $assignedSheet = MatchSheet::factory()->create([
            'referee_id' => $this->referee->id,
            'main_referee_id' => $this->referee->id,
            'assistant_referee_1_id' => $this->referee->id,
            'assistant_referee_2_id' => $this->referee->id,
            'fourth_official_id' => $this->referee->id,
            'var_referee_id' => $this->referee->id,
            'var_assistant_id' => $this->referee->id,
            'match_observer_id' => $this->referee->id,
            'validated_by' => $this->faAdmin->id,
            'rejected_by' => $this->faAdmin->id,
            'fa_validated_by' => $this->faAdmin->id,
            'assigned_referee_id' => $this->referee->id,
            'match_id' => $this->match->id,
        ]);

        // Create another match sheet not assigned to this referee
        $otherReferee = User::factory()->create(['role' => 'referee']);
        $otherFaAdmin = User::factory()->create(['role' => 'fa_admin']);
        $otherMatch = MatchModel::factory()->create();
        MatchSheet::factory()->create([
            'referee_id' => $otherReferee->id,
            'main_referee_id' => $otherReferee->id,
            'assistant_referee_1_id' => $otherReferee->id,
            'assistant_referee_2_id' => $otherReferee->id,
            'fourth_official_id' => $otherReferee->id,
            'var_referee_id' => $otherReferee->id,
            'var_assistant_id' => $otherReferee->id,
            'match_observer_id' => $otherReferee->id,
            'validated_by' => $otherFaAdmin->id,
            'rejected_by' => $otherFaAdmin->id,
            'fa_validated_by' => $otherFaAdmin->id,
            'assigned_referee_id' => $otherReferee->id,
            'match_id' => $otherMatch->id,
        ]);

        $response = $this->actingAs($this->referee)
            ->getJson('/api/v1/match-sheets');

        $response->assertStatus(200);
        $this->assertEquals(1, $response->json('meta.total'));
        $this->assertEquals($assignedSheet->id, $response->json('data.0.id'));
    }

    public function test_team_official_can_view_team_match_sheets(): void
    {
        // Set team official's club to home team
        $this->teamOfficial->update(['club_id' => $this->homeTeam->club_id]);

        // Create match sheet for this team's match
        $teamSheet = MatchSheet::factory()->create([
            'match_id' => $this->match->id,
            'referee_id' => $this->referee->id,
            'main_referee_id' => $this->referee->id,
            'assistant_referee_1_id' => $this->referee->id,
            'assistant_referee_2_id' => $this->referee->id,
            'fourth_official_id' => $this->referee->id,
            'var_referee_id' => $this->referee->id,
            'var_assistant_id' => $this->referee->id,
            'match_observer_id' => $this->referee->id,
            'validated_by' => $this->faAdmin->id,
            'rejected_by' => $this->faAdmin->id,
            'fa_validated_by' => $this->faAdmin->id,
            'assigned_referee_id' => $this->referee->id,
        ]);

        // Create another match sheet for different teams
        $otherMatch = MatchModel::factory()->create();
        MatchSheet::factory()->create([
            'match_id' => $otherMatch->id,
            'referee_id' => $this->referee->id,
            'main_referee_id' => $this->referee->id,
            'assistant_referee_1_id' => $this->referee->id,
            'assistant_referee_2_id' => $this->referee->id,
            'fourth_official_id' => $this->referee->id,
            'var_referee_id' => $this->referee->id,
            'var_assistant_id' => $this->referee->id,
            'match_observer_id' => $this->referee->id,
            'validated_by' => $this->faAdmin->id,
            'rejected_by' => $this->faAdmin->id,
            'fa_validated_by' => $this->faAdmin->id,
            'assigned_referee_id' => $this->referee->id,
        ]);

        $response = $this->actingAs($this->teamOfficial)
            ->getJson('/api/v1/match-sheets');

        $response->assertStatus(200);
        $this->assertEquals(1, $response->json('meta.total'));
        $this->assertEquals($teamSheet->id, $response->json('data.0.id'));
    }

    // Store Tests
    public function test_system_admin_can_create_match_sheet(): void
    {
        // Debug: Check if match exists
        $this->assertDatabaseHas('matches', ['id' => $this->match->id]);
        
        $data = [
            'match_id' => $this->match->id,
            'match_number' => 'MS-2024-001',
            'stadium_venue' => 'Old Trafford',
            'weather_conditions' => 'Sunny',
            'pitch_conditions' => 'Good',
            'kickoff_time' => now()->addDays(1)->toISOString(),
            'home_team_roster' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11],
            'away_team_roster' => [12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22],
            'home_team_coach' => 'Erik ten Hag',
            'away_team_coach' => 'Pep Guardiola',
            'referee_id' => $this->referee->id,
            'status' => 'draft',
            'stage' => 'draft',
        ];

        $response = $this->actingAs($this->systemAdmin)
            ->postJson('/api/v1/match-sheets', $data);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id', 'match_id', 'match_number', 'stadium_venue',
                    'weather_conditions', 'pitch_conditions', 'kickoff_time',
                    'home_team_roster', 'away_team_roster', 'status', 'stage'
                ]
            ]);

        $this->assertDatabaseHas('match_sheets', [
            'match_id' => $this->match->id,
            'match_number' => 'MS-2024-001',
            'stadium_venue' => 'Old Trafford',
        ]);
    }

    public function test_referee_can_create_match_sheet(): void
    {
        $data = [
            'match_id' => $this->match->id,
            'match_number' => 'MS-2024-002',
            'stadium_venue' => 'Anfield',
            'kickoff_time' => now()->addDays(1)->toISOString(),
            'referee_id' => $this->referee->id,
        ];

        $response = $this->actingAs($this->referee)
            ->postJson('/api/v1/match-sheets', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('match_sheets', [
            'match_id' => $this->match->id,
            'match_number' => 'MS-2024-002',
            'referee_id' => $this->referee->id,
        ]);
    }

    public function test_team_official_cannot_create_match_sheet(): void
    {
        $data = [
            'match_id' => $this->match->id,
            'match_number' => 'MS-2024-003',
            'stadium_venue' => 'Emirates Stadium',
            'kickoff_time' => now()->addDays(1)->toISOString(),
        ];

        $response = $this->actingAs($this->teamOfficial)
            ->postJson('/api/v1/match-sheets', $data);

        $response->assertStatus(403);
    }

    public function test_validation_requires_match_id(): void
    {
        $data = [
            'match_number' => 'MS-2024-004',
            'stadium_venue' => 'Stamford Bridge',
            'kickoff_time' => now()->addDays(1)->toISOString(),
        ];

        $response = $this->actingAs($this->systemAdmin)
            ->postJson('/api/v1/match-sheets', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['match_id']);
    }

    public function test_validation_requires_unique_match_number(): void
    {
        MatchSheet::factory()->create(['match_number' => 'MS-2024-005', 'match_id' => $this->match->id]);

        $data = [
            'match_id' => $this->match->id,
            'match_number' => 'MS-2024-005',
            'stadium_venue' => 'Etihad Stadium',
            'kickoff_time' => now()->addDays(1)->toISOString(),
        ];

        $response = $this->actingAs($this->systemAdmin)
            ->postJson('/api/v1/match-sheets', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['match_number']);
    }

    // Show Tests
    public function test_system_admin_can_view_any_match_sheet(): void
    {
        $matchSheet = MatchSheet::factory()->create(['match_id' => $this->match->id]);

        $response = $this->actingAs($this->systemAdmin)
            ->getJson("/api/v1/match-sheets/{$matchSheet->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id', 'match_id', 'match_number', 'stadium_venue',
                    'kickoff_time', 'status', 'stage', 'match'
                ]
            ]);
    }

    public function test_referee_can_view_assigned_match_sheet(): void
    {
        $matchSheet = MatchSheet::factory()->create([
            'match_id' => $this->match->id,
            'referee_id' => $this->referee->id,
        ]);

        $response = $this->actingAs($this->referee)
            ->getJson("/api/v1/match-sheets/{$matchSheet->id}");

        $response->assertStatus(200);
    }

    public function test_referee_cannot_view_unassigned_match_sheet(): void
    {
        $matchSheet = MatchSheet::factory()->create([
            'match_id' => $this->match->id,
            'referee_id' => User::factory()->create(['role' => 'referee'])->id,
        ]);

        $response = $this->actingAs($this->referee)
            ->getJson("/api/v1/match-sheets/{$matchSheet->id}");

        $response->assertStatus(403);
    }

    // Update Tests
    public function test_system_admin_can_update_match_sheet(): void
    {
        $matchSheet = MatchSheet::factory()->create(['match_id' => $this->match->id]);

        $data = [
            'stadium_venue' => 'Updated Stadium',
            'weather_conditions' => 'Rainy',
            'notes' => 'Updated notes',
        ];

        $response = $this->actingAs($this->systemAdmin)
            ->putJson("/api/v1/match-sheets/{$matchSheet->id}", $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('match_sheets', [
            'id' => $matchSheet->id,
            'stadium_venue' => 'Updated Stadium',
            'weather_conditions' => 'Rainy',
        ]);
    }

    public function test_referee_can_update_assigned_match_sheet(): void
    {
        $matchSheet = MatchSheet::factory()->create([
            'match_id' => $this->match->id,
            'referee_id' => $this->referee->id,
        ]);

        $data = [
            'referee_report' => 'Updated referee report',
            'match_status' => 'in_progress',
        ];

        $response = $this->actingAs($this->referee)
            ->putJson("/api/v1/match-sheets/{$matchSheet->id}", $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('match_sheets', [
            'id' => $matchSheet->id,
            'referee_report' => 'Updated referee report',
            'match_status' => 'in_progress',
        ]);
    }

    public function test_team_official_cannot_update_match_sheet(): void
    {
        $matchSheet = MatchSheet::factory()->create(['match_id' => $this->match->id]);

        $data = ['notes' => 'Team notes'];

        $response = $this->actingAs($this->teamOfficial)
            ->putJson("/api/v1/match-sheets/{$matchSheet->id}", $data);

        $response->assertStatus(403);
    }

    // Delete Tests
    public function test_system_admin_can_delete_match_sheet(): void
    {
        $matchSheet = MatchSheet::factory()->create(['match_id' => $this->match->id]);

        $response = $this->actingAs($this->systemAdmin)
            ->deleteJson("/api/v1/match-sheets/{$matchSheet->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('match_sheets', ['id' => $matchSheet->id]);
    }

    public function test_referee_cannot_delete_match_sheet(): void
    {
        $matchSheet = MatchSheet::factory()->create([
            'match_id' => $this->match->id,
            'referee_id' => $this->referee->id,
        ]);

        $response = $this->actingAs($this->referee)
            ->deleteJson("/api/v1/match-sheets/{$matchSheet->id}");

        $response->assertStatus(403);
    }

    // Submit Tests
    public function test_referee_can_submit_match_sheet(): void
    {
        $matchSheet = MatchSheet::factory()->create([
            'match_id' => $this->match->id,
            'referee_id' => $this->referee->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->referee)
            ->postJson("/api/v1/match-sheets/{$matchSheet->id}/submit");

        $response->assertStatus(200);
        $this->assertDatabaseHas('match_sheets', [
            'id' => $matchSheet->id,
            'status' => 'submitted',
        ]);
    }

    public function test_system_admin_cannot_submit_match_sheet(): void
    {
        $matchSheet = MatchSheet::factory()->create([
            'match_id' => $this->match->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->systemAdmin)
            ->postJson("/api/v1/match-sheets/{$matchSheet->id}/submit");

        $response->assertStatus(403);
    }

    // Validate Tests
    public function test_fa_admin_can_validate_match_sheet(): void
    {
        $matchSheet = MatchSheet::factory()->create([
            'match_id' => $this->match->id,
            'status' => 'submitted',
        ]);

        $data = ['notes' => 'Validation approved'];

        $response = $this->actingAs($this->faAdmin)
            ->postJson("/api/v1/match-sheets/{$matchSheet->id}/validate", $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('match_sheets', [
            'id' => $matchSheet->id,
            'status' => 'validated',
            'validated_by' => $this->faAdmin->id,
        ]);
    }

    public function test_referee_cannot_validate_match_sheet(): void
    {
        $matchSheet = MatchSheet::factory()->create([
            'match_id' => $this->match->id,
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($this->referee)
            ->postJson("/api/v1/match-sheets/{$matchSheet->id}/validate");

        $response->assertStatus(403);
    }

    // Reject Tests
    public function test_fa_admin_can_reject_match_sheet(): void
    {
        $matchSheet = MatchSheet::factory()->create([
            'match_id' => $this->match->id,
            'status' => 'submitted',
        ]);

        $data = ['reason' => 'Incomplete information'];

        $response = $this->actingAs($this->faAdmin)
            ->postJson("/api/v1/match-sheets/{$matchSheet->id}/reject", $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('match_sheets', [
            'id' => $matchSheet->id,
            'status' => 'rejected',
            'rejected_by' => $this->faAdmin->id,
        ]);
    }

    // Assign Referee Tests
    public function test_fa_admin_can_assign_referee(): void
    {
        $matchSheet = MatchSheet::factory()->create([
            'match_id' => $this->match->id,
            'stage' => 'in_progress'
        ]);
        $newReferee = User::factory()->create(['role' => 'referee']);

        $data = ['referee_id' => $newReferee->id];

        $response = $this->actingAs($this->faAdmin)
            ->postJson("/api/v1/match-sheets/{$matchSheet->id}/assign-referee", $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('match_sheets', [
            'id' => $matchSheet->id,
            'assigned_referee_id' => $newReferee->id,
        ]);
    }

    // Sign Tests
    public function test_team_official_can_sign_match_sheet(): void
    {
        $this->teamOfficial->update(['club_id' => $this->homeTeam->club_id]);
        $matchSheet = MatchSheet::factory()->create(['match_id' => $this->match->id]);

        $data = [
            'team_type' => 'home',
            'signature' => 'team_signature_hash',
        ];

        $response = $this->actingAs($this->teamOfficial)
            ->postJson("/api/v1/match-sheets/{$matchSheet->id}/sign-team", $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('match_sheets', [
            'id' => $matchSheet->id,
            'home_team_signature' => 'team_signature_hash',
        ]);
    }

    public function test_referee_can_sign_match_sheet(): void
    {
        $matchSheet = MatchSheet::factory()->create([
            'match_id' => $this->match->id,
            'referee_id' => $this->referee->id,
        ]);

        $data = ['signature' => 'referee_signature_hash'];

        $response = $this->actingAs($this->referee)
            ->postJson("/api/v1/match-sheets/{$matchSheet->id}/sign-referee", $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('match_sheets', [
            'id' => $matchSheet->id,
            'referee_digital_signature' => 'referee_signature_hash',
        ]);
    }

    // Lock/Unlock Tests
    public function test_fa_admin_can_lock_lineups(): void
    {
        $matchSheet = MatchSheet::factory()->create(['match_id' => $this->match->id]);

        $response = $this->actingAs($this->faAdmin)
            ->postJson("/api/v1/match-sheets/{$matchSheet->id}/lock-lineups");

        $response->assertStatus(200);
        $this->assertDatabaseHas('match_sheets', [
            'id' => $matchSheet->id,
            'lineups_locked' => true,
        ]);
    }

    public function test_fa_admin_can_unlock_lineups(): void
    {
        $matchSheet = MatchSheet::factory()->create([
            'match_id' => $this->match->id,
            'lineups_locked' => true,
        ]);

        $response = $this->actingAs($this->faAdmin)
            ->postJson("/api/v1/match-sheets/{$matchSheet->id}/unlock-lineups");

        $response->assertStatus(200);
        $this->assertDatabaseHas('match_sheets', [
            'id' => $matchSheet->id,
            'lineups_locked' => false,
        ]);
    }

    // Stage Transition Tests
    public function test_referee_can_transition_stage(): void
    {
        $matchSheet = MatchSheet::factory()->create([
            'match_id' => $this->match->id,
            'referee_id' => $this->referee->id,
            'stage' => 'in_progress',
        ]);

        $data = ['new_stage' => 'before_game_signed'];

        $response = $this->actingAs($this->referee)
            ->postJson("/api/v1/match-sheets/{$matchSheet->id}/transition-stage", $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('match_sheets', [
            'id' => $matchSheet->id,
            'stage' => 'before_game_signed',
        ]);
    }

    // Statistics Tests
    public function test_can_get_match_sheet_statistics(): void
    {
        $matchSheet = MatchSheet::factory()->create([
            'match_id' => $this->match->id,
            'home_team_score' => 2,
            'away_team_score' => 1,
        ]);

        $response = $this->actingAs($this->systemAdmin)
            ->getJson("/api/v1/match-sheets/{$matchSheet->id}/statistics");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'total_goals', 'goal_difference', 'winner',
                    'match_statistics', 'var_decisions', 'penalty_shootout_data'
                ]
            ]);

        $this->assertEquals(3, $response->json('data.total_goals'));
        $this->assertEquals(1, $response->json('data.goal_difference'));
        $this->assertEquals('home', $response->json('data.winner'));
    }

    // Stage Progress Tests
    public function test_can_get_stage_progress(): void
    {
        $matchSheet = MatchSheet::factory()->create([
            'match_id' => $this->match->id,
            'stage' => 'in_progress',
        ]);

        $response = $this->actingAs($this->systemAdmin)
            ->getJson("/api/v1/match-sheets/{$matchSheet->id}/stage-progress");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'in_progress' => ['label', 'completed', 'current', 'timestamp'],
                    'before_game_signed' => ['label', 'completed', 'current', 'timestamp'],
                    'after_game_signed' => ['label', 'completed', 'current', 'timestamp'],
                    'fa_validated' => ['label', 'completed', 'current', 'timestamp'],
                ]
            ]);
    }

    // Export Tests
    public function test_can_export_match_sheet(): void
    {
        $matchSheet = MatchSheet::factory()->create([
            'match_id' => $this->match->id,
            'home_team_score' => 1,
            'away_team_score' => 0,
        ]);

        $response = $this->actingAs($this->systemAdmin)
            ->getJson("/api/v1/match-sheets/{$matchSheet->id}/export");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'match_sheet_id', 'match_number', 'competition', 'season',
                    'matchday', 'date', 'kickoff_time', 'stadium_venue',
                    'home_team', 'away_team', 'home_team_score', 'away_team_score',
                    'officials', 'match_statistics', 'referee_report',
                    'match_status', 'status', 'version'
                ]
            ]);
    }

    // Filter Tests
    public function test_can_filter_by_status(): void
    {
        MatchSheet::factory()->create(['status' => 'draft', 'match_id' => $this->match->id]);
        MatchSheet::factory()->create(['status' => 'submitted', 'match_id' => $this->match->id]);
        MatchSheet::factory()->create(['status' => 'validated', 'match_id' => $this->match->id]);

        $response = $this->actingAs($this->systemAdmin)
            ->getJson('/api/v1/match-sheets?status=submitted');

        $response->assertStatus(200);
        $this->assertEquals(1, $response->json('meta.total'));
        $this->assertEquals('submitted', $response->json('data.0.status'));
    }

    public function test_can_filter_by_stage(): void
    {
        MatchSheet::factory()->create(['stage' => 'draft', 'match_id' => $this->match->id]);
        MatchSheet::factory()->create(['stage' => 'in_progress', 'match_id' => $this->match->id]);
        MatchSheet::factory()->create(['stage' => 'completed', 'match_id' => $this->match->id]);

        $response = $this->actingAs($this->systemAdmin)
            ->getJson('/api/v1/match-sheets?stage=in_progress');

        $response->assertStatus(200);
        $this->assertEquals(1, $response->json('meta.total'));
        $this->assertEquals('in_progress', $response->json('data.0.stage'));
    }

    public function test_can_search_match_sheets(): void
    {
        MatchSheet::factory()->create([
            'match_number' => 'MS-2024-SEARCH',
            'stadium_venue' => 'Search Stadium',
            'match_id' => $this->match->id,
        ]);
        MatchSheet::factory()->create(['match_number' => 'MS-2024-OTHER', 'match_id' => $this->match->id]);

        $response = $this->actingAs($this->systemAdmin)
            ->getJson('/api/v1/match-sheets?search=SEARCH');

        $response->assertStatus(200);
        $this->assertEquals(1, $response->json('meta.total'));
        $this->assertEquals('MS-2024-SEARCH', $response->json('data.0.match_number'));
    }

    // Pagination Tests
    public function test_supports_pagination(): void
    {
        MatchSheet::factory()->count(25)->create(['match_id' => $this->match->id]);

        $response = $this->actingAs($this->systemAdmin)
            ->getJson('/api/v1/match-sheets?per_page=10');

        $response->assertStatus(200);
        $this->assertEquals(25, $response->json('meta.total'));
        $this->assertEquals(10, $response->json('meta.per_page'));
        $this->assertEquals(3, $response->json('meta.last_page'));
        $this->assertCount(10, $response->json('data'));
    }

    // Sorting Tests
    public function test_supports_sorting(): void
    {
        MatchSheet::factory()->create(['kickoff_time' => now()->addDays(1), 'match_id' => $this->match->id]);
        MatchSheet::factory()->create(['kickoff_time' => now()->addDays(3), 'match_id' => $this->match->id]);
        MatchSheet::factory()->create(['kickoff_time' => now()->addDays(2), 'match_id' => $this->match->id]);

        $response = $this->actingAs($this->systemAdmin)
            ->getJson('/api/v1/match-sheets?sort_by=kickoff_time&sort_direction=asc');

        $response->assertStatus(200);
        $kickoffTimes = collect($response->json('data'))->pluck('kickoff_time')->toArray();
        $this->assertEquals($kickoffTimes, collect($kickoffTimes)->sort()->toArray());
    }
} 