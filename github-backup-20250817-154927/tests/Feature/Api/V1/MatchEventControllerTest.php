<?php

namespace Tests\Feature\Api\V1;

use App\Models\MatchEvent;
use App\Models\MatchModel;
use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use App\Models\Association;
use App\Models\Club;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MatchEventControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $systemAdmin;
    protected User $associationAdmin;
    protected User $clubAdmin;
    protected User $referee;
    protected Association $association;
    protected Club $club;
    protected Team $team;
    protected Player $player;
    protected MatchModel $match;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create association
        $this->association = Association::factory()->create();
        
        // Create club
        $this->club = Club::factory()->create([
            'association_id' => $this->association->id
        ]);
        
        // Create team
        $this->team = Team::factory()->create([
            'club_id' => $this->club->id,
            'association_id' => $this->association->id
        ]);
        
        // Create player
        $this->player = Player::factory()->create([
            'club_id' => $this->club->id,
            'association_id' => $this->association->id
        ]);
        
        // Create competition
        $competition = \App\Models\Competition::factory()->create([
            'association_id' => $this->association->id
        ]);
        
        // Create match
        $this->match = MatchModel::factory()->create([
            'competition_id' => $competition->id,
            'home_team_id' => $this->team->id,
            'away_team_id' => Team::factory()->create([
                'club_id' => $this->club->id,
                'association_id' => $this->association->id
            ])->id,
            'home_club_id' => $this->club->id,
            'away_club_id' => $this->club->id
        ]);
        
        // Create users with different roles
        $this->systemAdmin = User::factory()->create([
            'role' => 'system_admin',
            'association_id' => null
        ]);
        
        $this->associationAdmin = User::factory()->create([
            'role' => 'association_admin',
            'association_id' => $this->association->id
        ]);
        
        $this->clubAdmin = User::factory()->create([
            'role' => 'club_admin',
            'association_id' => $this->association->id,
            'club_id' => $this->club->id
        ]);
        
        $this->referee = User::factory()->create([
            'role' => 'referee',
            'association_id' => $this->association->id
        ]);
    }

    public function test_system_admin_can_view_all_match_events()
    {
        Sanctum::actingAs($this->systemAdmin);
        
        MatchEvent::factory()->count(3)->create([
            'match_id' => $this->match->id,
            'team_id' => $this->team->id,
            'player_id' => $this->player->id,
            'recorded_by_user_id' => $this->systemAdmin->id
        ]);
        
        $response = $this->getJson('/api/v1/match-events');
        
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id', 'match_id', 'team_id', 'player_id', 'event_type',
                            'minute', 'period', 'is_confirmed', 'is_contested'
                        ]
                    ],
                    'meta' => ['current_page', 'last_page', 'per_page', 'total']
                ]);
    }

    public function test_association_admin_can_view_events_in_their_association()
    {
        Sanctum::actingAs($this->associationAdmin);
        
        MatchEvent::factory()->create([
            'match_id' => $this->match->id,
            'team_id' => $this->team->id,
            'player_id' => $this->player->id,
            'recorded_by_user_id' => $this->associationAdmin->id
        ]);
        
        $response = $this->getJson('/api/v1/match-events');
        
        $response->assertStatus(200);
    }

    public function test_club_admin_can_view_events_for_their_club()
    {
        Sanctum::actingAs($this->clubAdmin);
        
        MatchEvent::factory()->create([
            'match_id' => $this->match->id,
            'team_id' => $this->team->id,
            'player_id' => $this->player->id,
            'recorded_by_user_id' => $this->clubAdmin->id
        ]);
        
        $response = $this->getJson('/api/v1/match-events');
        
        $response->assertStatus(200);
    }

    public function test_system_admin_can_create_match_event()
    {
        Sanctum::actingAs($this->systemAdmin);
        
        $eventData = [
            'match_id' => $this->match->id,
            'team_id' => $this->team->id,
            'player_id' => $this->player->id,
            'event_type' => 'goal',
            'minute' => 25,
            'period' => 'first_half',
            'description' => 'Great goal from outside the box'
        ];
        
        $response = $this->postJson('/api/v1/match-events', $eventData);
        
        $response->assertStatus(201)
                ->assertJson([
                    'data' => [
                        'event_type' => 'goal',
                        'minute' => 25,
                        'period' => 'first_half'
                    ]
                ]);
    }

    public function test_association_admin_can_create_match_event()
    {
        Sanctum::actingAs($this->associationAdmin);
        
        $eventData = [
            'match_id' => $this->match->id,
            'team_id' => $this->team->id,
            'player_id' => $this->player->id,
            'event_type' => 'yellow_card',
            'minute' => 30,
            'period' => 'first_half'
        ];
        
        $response = $this->postJson('/api/v1/match-events', $eventData);
        
        $response->assertStatus(201);
    }

    public function test_club_admin_can_create_match_event()
    {
        Sanctum::actingAs($this->clubAdmin);
        
        $eventData = [
            'match_id' => $this->match->id,
            'team_id' => $this->team->id,
            'player_id' => $this->player->id,
            'event_type' => 'substitution_in',
            'minute' => 60,
            'period' => 'second_half'
        ];
        
        $response = $this->postJson('/api/v1/match-events', $eventData);
        
        $response->assertStatus(201);
    }

    public function test_validation_requires_match_and_team()
    {
        Sanctum::actingAs($this->systemAdmin);
        
        $eventData = [
            'event_type' => 'goal',
            'minute' => 25,
            'period' => 'first_half'
        ];
        
        $response = $this->postJson('/api/v1/match-events', $eventData);
        
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['match_id', 'team_id']);
    }

    public function test_validation_requires_valid_event_type()
    {
        Sanctum::actingAs($this->systemAdmin);
        
        $eventData = [
            'match_id' => $this->match->id,
            'team_id' => $this->team->id,
            'player_id' => $this->player->id,
            'event_type' => 'invalid_event',
            'minute' => 25,
            'period' => 'first_half'
        ];
        
        $response = $this->postJson('/api/v1/match-events', $eventData);
        
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['event_type']);
    }

    public function test_system_admin_can_view_specific_match_event()
    {
        Sanctum::actingAs($this->systemAdmin);
        
        $event = MatchEvent::factory()->create([
            'match_id' => $this->match->id,
            'team_id' => $this->team->id,
            'player_id' => $this->player->id,
            'recorded_by_user_id' => $this->systemAdmin->id
        ]);
        
        $response = $this->getJson("/api/v1/match-events/{$event->id}");
        
        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'id' => $event->id,
                        'match_id' => $this->match->id,
                        'team_id' => $this->team->id
                    ]
                ]);
    }

    public function test_association_admin_can_view_event_in_their_association()
    {
        Sanctum::actingAs($this->associationAdmin);
        
        $event = MatchEvent::factory()->create([
            'match_id' => $this->match->id,
            'team_id' => $this->team->id,
            'player_id' => $this->player->id,
            'recorded_by_user_id' => $this->associationAdmin->id
        ]);
        
        $response = $this->getJson("/api/v1/match-events/{$event->id}");
        
        $response->assertStatus(200);
    }

    public function test_club_admin_can_view_event_for_their_club()
    {
        Sanctum::actingAs($this->clubAdmin);
        
        $event = MatchEvent::factory()->create([
            'match_id' => $this->match->id,
            'team_id' => $this->team->id,
            'player_id' => $this->player->id,
            'recorded_by_user_id' => $this->clubAdmin->id
        ]);
        
        $response = $this->getJson("/api/v1/match-events/{$event->id}");
        
        $response->assertStatus(200);
    }

    public function test_unauthorized_user_cannot_view_event()
    {
        $event = MatchEvent::factory()->create([
            'match_id' => $this->match->id,
            'team_id' => $this->team->id,
            'player_id' => $this->player->id,
            'recorded_by_user_id' => $this->systemAdmin->id
        ]);
        
        $response = $this->getJson("/api/v1/match-events/{$event->id}");
        
        $response->assertStatus(401);
    }

    public function test_system_admin_can_update_match_event()
    {
        Sanctum::actingAs($this->systemAdmin);
        
        $event = MatchEvent::factory()->create([
            'match_id' => $this->match->id,
            'team_id' => $this->team->id,
            'player_id' => $this->player->id,
            'recorded_by_user_id' => $this->systemAdmin->id
        ]);
        
        $updateData = [
            'minute' => 35,
            'description' => 'Updated description'
        ];
        
        $response = $this->putJson("/api/v1/match-events/{$event->id}", $updateData);
        
        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'minute' => 35,
                        'description' => 'Updated description'
                    ]
                ]);
    }

    public function test_association_admin_can_update_event_in_their_association()
    {
        Sanctum::actingAs($this->associationAdmin);
        
        $event = MatchEvent::factory()->create([
            'match_id' => $this->match->id,
            'team_id' => $this->team->id,
            'player_id' => $this->player->id,
            'recorded_by_user_id' => $this->associationAdmin->id
        ]);
        
        $updateData = [
            'minute' => 40,
            'description' => 'Updated by association admin'
        ];
        
        $response = $this->putJson("/api/v1/match-events/{$event->id}", $updateData);
        
        $response->assertStatus(200);
    }

    public function test_club_admin_can_update_event_for_their_club()
    {
        Sanctum::actingAs($this->clubAdmin);
        
        $event = MatchEvent::factory()->create([
            'match_id' => $this->match->id,
            'team_id' => $this->team->id,
            'player_id' => $this->player->id,
            'recorded_by_user_id' => $this->clubAdmin->id
        ]);
        
        $updateData = [
            'minute' => 45,
            'description' => 'Updated by club admin'
        ];
        
        $response = $this->putJson("/api/v1/match-events/{$event->id}", $updateData);
        
        $response->assertStatus(200);
    }

    public function test_system_admin_can_delete_match_event()
    {
        Sanctum::actingAs($this->systemAdmin);
        
        $event = MatchEvent::factory()->create([
            'match_id' => $this->match->id,
            'team_id' => $this->team->id,
            'player_id' => $this->player->id,
            'recorded_by_user_id' => $this->systemAdmin->id
        ]);
        
        $response = $this->deleteJson("/api/v1/match-events/{$event->id}");
        
        $response->assertStatus(204);
    }

    public function test_association_admin_can_delete_event_in_their_association()
    {
        Sanctum::actingAs($this->associationAdmin);
        
        $event = MatchEvent::factory()->create([
            'match_id' => $this->match->id,
            'team_id' => $this->team->id,
            'player_id' => $this->player->id,
            'recorded_by_user_id' => $this->associationAdmin->id
        ]);
        
        $response = $this->deleteJson("/api/v1/match-events/{$event->id}");
        
        $response->assertStatus(204);
    }

    public function test_club_admin_cannot_delete_event()
    {
        Sanctum::actingAs($this->clubAdmin);
        
        $event = MatchEvent::factory()->create([
            'match_id' => $this->match->id,
            'team_id' => $this->team->id,
            'player_id' => $this->player->id,
            'recorded_by_user_id' => $this->systemAdmin->id
        ]);
        
        $response = $this->deleteJson("/api/v1/match-events/{$event->id}");
        
        $response->assertStatus(403);
    }

    public function test_association_admin_can_confirm_event()
    {
        Sanctum::actingAs($this->associationAdmin);
        
        $event = MatchEvent::factory()->create([
            'match_id' => $this->match->id,
            'team_id' => $this->team->id,
            'player_id' => $this->player->id,
            'recorded_by_user_id' => $this->associationAdmin->id
        ]);
        
        $response = $this->postJson("/api/v1/match-events/{$event->id}/confirm");
        
        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'is_confirmed' => true
                    ]
                ]);
    }

    public function test_association_admin_can_contest_event()
    {
        Sanctum::actingAs($this->associationAdmin);
        
        $event = MatchEvent::factory()->create([
            'match_id' => $this->match->id,
            'team_id' => $this->team->id,
            'player_id' => $this->player->id,
            'recorded_by_user_id' => $this->associationAdmin->id
        ]);
        
        $contestData = [
            'reason' => 'Incorrect decision by referee'
        ];
        
        $response = $this->postJson("/api/v1/match-events/{$event->id}/contest", $contestData);
        
        $response->assertStatus(200)
                ->assertJson([
                    'data' => [
                        'is_contested' => true,
                        'contest_reason' => 'Incorrect decision by referee'
                    ]
                ]);
    }

    public function test_club_admin_cannot_confirm_event()
    {
        Sanctum::actingAs($this->clubAdmin);
        
        $event = MatchEvent::factory()->create([
            'match_id' => $this->match->id,
            'team_id' => $this->team->id,
            'player_id' => $this->player->id,
            'recorded_by_user_id' => $this->systemAdmin->id
        ]);
        
        $response = $this->postJson("/api/v1/match-events/{$event->id}/confirm");
        
        $response->assertStatus(403);
    }

    public function test_cannot_contest_event_without_reason()
    {
        Sanctum::actingAs($this->associationAdmin);
        
        $event = MatchEvent::factory()->create([
            'match_id' => $this->match->id,
            'team_id' => $this->team->id,
            'player_id' => $this->player->id,
            'recorded_by_user_id' => $this->associationAdmin->id
        ]);
        
        $response = $this->postJson("/api/v1/match-events/{$event->id}/contest", []);
        
        $response->assertStatus(422)
                ->assertJsonValidationErrors(['reason']);
    }

    public function test_can_view_events_by_match()
    {
        Sanctum::actingAs($this->systemAdmin);
        
        MatchEvent::factory()->count(3)->create([
            'match_id' => $this->match->id,
            'team_id' => $this->team->id,
            'player_id' => $this->player->id,
            'recorded_by_user_id' => $this->systemAdmin->id
        ]);
        
        $response = $this->getJson("/api/v1/matches/{$this->match->id}/events");
        
        $response->assertStatus(200)
                ->assertJsonCount(3, 'data.events');
    }

    public function test_can_view_events_by_team()
    {
        Sanctum::actingAs($this->systemAdmin);
        
        MatchEvent::factory()->count(3)->create([
            'match_id' => $this->match->id,
            'team_id' => $this->team->id,
            'player_id' => $this->player->id,
            'recorded_by_user_id' => $this->systemAdmin->id
        ]);
        
        $response = $this->getJson("/api/v1/teams/{$this->team->id}/events");
        
        $response->assertStatus(200)
                ->assertJsonCount(3, 'data');
    }

    public function test_can_view_events_by_player()
    {
        Sanctum::actingAs($this->systemAdmin);
        
        MatchEvent::factory()->count(3)->create([
            'match_id' => $this->match->id,
            'team_id' => $this->team->id,
            'player_id' => $this->player->id,
            'recorded_by_user_id' => $this->systemAdmin->id
        ]);
        
        $response = $this->getJson("/api/v1/players/{$this->player->id}/events");
        
        $response->assertStatus(200)
                ->assertJsonCount(3, 'data');
    }

    public function test_can_view_event_statistics()
    {
        Sanctum::actingAs($this->systemAdmin);
        
        MatchEvent::factory()->count(5)->create([
            'match_id' => $this->match->id,
            'team_id' => $this->team->id,
            'player_id' => $this->player->id,
            'recorded_by_user_id' => $this->systemAdmin->id
        ]);
        
        $response = $this->getJson('/api/v1/match-events/statistics');
        
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'total_events',
                        'events_by_type',
                        'events_by_period'
                    ]
                ]);
    }

    public function test_can_view_match_timeline()
    {
        Sanctum::actingAs($this->systemAdmin);
        
        MatchEvent::factory()->count(3)->create([
            'match_id' => $this->match->id,
            'team_id' => $this->team->id,
            'player_id' => $this->player->id,
            'recorded_by_user_id' => $this->systemAdmin->id
        ]);
        
        $response = $this->getJson("/api/v1/matches/{$this->match->id}/events/timeline");
        
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            '*' => [
                                'id', 'minute', 'event_type', 'event_type_label', 'description'
                            ]
                        ]
                    ]
                ]);
    }
} 