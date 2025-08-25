<?php

namespace Tests\Feature;

use App\Models\Competition;
use App\Models\Team;
use App\Models\User;
use App\Models\GameMatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CalendarManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Créer un utilisateur admin pour l'authentification
        $this->user = User::factory()->create();
        $this->actingAs($this->user, 'sanctum');
    }

    public function test_automatic_schedule_generation()
    {
        $competition = Competition::factory()->hasTeams(20)->create();
        $startDate = now()->addDays(2)->toDateString();

        $response = $this->postJson("/api/calendar/competitions/{$competition->id}/generate-full-schedule", [
            'start_date' => $startDate,
            'match_interval_days' => 7,
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
        $this->assertDatabaseCount('game_matches', 380); // 20 équipes, aller-retour
    }

    public function test_manual_match_creation_and_conflict_validation()
    {
        $competition = Competition::factory()->hasTeams(4)->create();
        $teams = $competition->teams;
        $date = now()->addDays(3)->format('Y-m-d\TH:i');

        // Créer un premier match
        $response1 = $this->postJson("/api/calendar/competitions/{$competition->id}/matches/validate-and-create", [
            'home_team_id' => $teams[0]->id,
            'away_team_id' => $teams[1]->id,
            'matchday' => 1,
            'kickoff_time' => $date . ':00',
            'venue' => 'Stade Principal',
        ]);
        $response1->assertStatus(201)->assertJson(['success' => true]);

        // Essayer de créer un match avec la même équipe le même jour (conflit)
        $response2 = $this->postJson("/api/calendar/competitions/{$competition->id}/matches/validate-and-create", [
            'home_team_id' => $teams[0]->id,
            'away_team_id' => $teams[2]->id,
            'matchday' => 1,
            'kickoff_time' => $date . ':00',
            'venue' => 'Stade Principal',
        ]);
        $response2->assertStatus(422)
            ->assertJsonFragment(['error' => 'Conflit de calendrier']);

        // Essayer de créer un match avec le même terrain et créneau (conflit)
        $response3 = $this->postJson("/api/calendar/competitions/{$competition->id}/matches/validate-and-create", [
            'home_team_id' => $teams[1]->id,
            'away_team_id' => $teams[2]->id,
            'matchday' => 1,
            'kickoff_time' => $date . ':00',
            'venue' => 'Stade Principal',
        ]);
        $response3->assertStatus(422)
            ->assertJsonFragment(['error' => 'Conflit de calendrier']);
    }

    public function test_clear_schedule()
    {
        $competition = Competition::factory()->hasTeams(4)->create();
        $startDate = now()->addDays(2)->toDateString();
        $this->postJson("/api/calendar/competitions/{$competition->id}/generate-full-schedule", [
            'start_date' => $startDate,
            'match_interval_days' => 7,
        ]);
        $this->assertDatabaseCount('game_matches', 12); // 4 équipes, aller-retour

        $response = $this->deleteJson("/api/calendar/competitions/{$competition->id}/schedule");
        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseCount('game_matches', 0);
    }
}
