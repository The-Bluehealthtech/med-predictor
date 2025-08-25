<?php

namespace Tests\Feature\Views;

use Tests\TestCase;
use App\Models\User;
use App\Models\Player;
use App\Models\Performance;
use App\Models\Club;
use App\Models\Federation;
use App\Models\PerformanceRecommendation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class PerformanceViewsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $adminUser;
    private User $coachUser;
    private Player $player;
    private Club $club;
    private Federation $federation;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->federation = Federation::factory()->create();
        $this->club = Club::factory()->create(['federation_id' => $this->federation->id]);
        $this->player = Player::factory()->create(['club_id' => $this->club->id]);
        
        $this->adminUser = User::factory()->create(['role' => 'admin']);
        $this->coachUser = User::factory()->create(['role' => 'coach']);
    }

    /** @test */
    public function it_renders_performance_index_view()
    {
        // Arrange
        Performance::factory()->count(5)->create([
            'player_id' => $this->player->id
        ]);

        // Act
        $response = $this->actingAs($this->coachUser)
            ->get('/performances');

        // Assert
        $response->assertStatus(200)
            ->assertViewIs('performances.index')
            ->assertViewHas('performances')
            ->assertSee('Performance Management')
            ->assertSee('Create New Performance')
            ->assertSee('Filter')
            ->assertSee('Search');
    }

    /** @test */
    public function it_renders_performance_create_view()
    {
        // Act
        $response = $this->actingAs($this->coachUser)
            ->get('/performances/create');

        // Assert
        $response->assertStatus(200)
            ->assertViewIs('performances.create')
            ->assertSee('Create Performance Record')
            ->assertSee('Player')
            ->assertSee('Match Date')
            ->assertSee('Distance Covered')
            ->assertSee('Sprint Count')
            ->assertSee('Max Speed')
            ->assertSee('Submit');
    }

    /** @test */
    public function it_renders_performance_dashboard_view()
    {
        // Arrange
        Performance::factory()->count(10)->create([
            'player_id' => $this->player->id
        ]);

        // Act
        $response = $this->actingAs($this->coachUser)
            ->get('/performances/dashboard?level=player&id=' . $this->player->id);

        // Assert
        $response->assertStatus(200)
            ->assertViewIs('performances.dashboard')
            ->assertViewHas('dashboardData')
            ->assertSee('Performance Dashboard')
            ->assertSee('Summary Statistics')
            ->assertSee('Performance Charts')
            ->assertSee('Recent Performances');
    }

    /** @test */
    public function it_renders_player_passport_index_view()
    {
        // Act
        $response = $this->actingAs($this->adminUser)
            ->get('/player-passports');

        // Assert
        $response->assertStatus(200)
            ->assertViewIs('player-passports.index')
            ->assertSee('Player Passports')
            ->assertSee('Status')
            ->assertSee('Actions');
    }

    /** @test */
    public function it_renders_performance_recommendations_index_view()
    {
        // Arrange
        PerformanceRecommendation::factory()->count(3)->create([
            'player_id' => $this->player->id
        ]);

        // Act
        $response = $this->actingAs($this->coachUser)
            ->get('/performance-recommendations');

        // Assert
        $response->assertStatus(200)
            ->assertViewIs('performance-recommendations.index')
            ->assertViewHas('recommendations')
            ->assertSee('Performance Recommendations')
            ->assertSee('Priority')
            ->assertSee('Status')
            ->assertSee('Actions');
    }

    /** @test */
    public function it_displays_performance_statistics_cards()
    {
        // Arrange
        Performance::factory()->count(10)->create([
            'player_id' => $this->player->id,
            'distance_covered' => 10000,
            'rating' => 8.0
        ]);

        // Act
        $response = $this->actingAs($this->coachUser)
            ->get('/performances');

        // Assert
        $response->assertStatus(200)
            ->assertSee('Total Performances')
            ->assertSee('Average Distance')
            ->assertSee('Average Rating')
            ->assertSee('10') // Total performances
            ->assertSee('10000'); // Average distance
    }

    /** @test */
    public function it_displays_performance_filters()
    {
        // Act
        $response = $this->actingAs($this->coachUser)
            ->get('/performances');

        // Assert
        $response->assertStatus(200)
            ->assertSee('Filter by Player')
            ->assertSee('Filter by Date Range')
            ->assertSee('Filter by Rating')
            ->assertSee('Apply Filters')
            ->assertSee('Clear Filters');
    }

    /** @test */
    public function it_displays_performance_search()
    {
        // Act
        $response = $this->actingAs($this->coachUser)
            ->get('/performances');

        // Assert
        $response->assertStatus(200)
            ->assertSee('Search Performances')
            ->assertSee('placeholder="Search by player name, notes..."')
            ->assertSee('Search');
    }

    /** @test */
    public function it_displays_performance_list_with_pagination()
    {
        // Arrange
        Performance::factory()->count(25)->create([
            'player_id' => $this->player->id
        ]);

        // Act
        $response = $this->actingAs($this->coachUser)
            ->get('/performances');

        // Assert
        $response->assertStatus(200)
            ->assertSee('Showing')
            ->assertSee('of')
            ->assertSee('results')
            ->assertSee('Previous')
            ->assertSee('Next');
    }

    /** @test */
    public function it_displays_performance_actions()
    {
        // Arrange
        $performance = Performance::factory()->create([
            'player_id' => $this->player->id
        ]);

        // Act
        $response = $this->actingAs($this->coachUser)
            ->get('/performances');

        // Assert
        $response->assertStatus(200)
            ->assertSee('View')
            ->assertSee('Edit')
            ->assertSee('Delete');
    }

    /** @test */
    public function it_displays_performance_details()
    {
        // Arrange
        $performance = Performance::factory()->create([
            'player_id' => $this->player->id,
            'distance_covered' => 10500,
            'sprint_count' => 25,
            'max_speed' => 32.5,
            'rating' => 8.5
        ]);

        // Act
        $response = $this->actingAs($this->coachUser)
            ->get('/performances/' . $performance->id);

        // Assert
        $response->assertStatus(200)
            ->assertSee('10500')
            ->assertSee('25')
            ->assertSee('32.5')
            ->assertSee('8.5')
            ->assertSee('Performance Details');
    }

    /** @test */
    public function it_displays_performance_edit_form()
    {
        // Arrange
        $performance = Performance::factory()->create([
            'player_id' => $this->player->id
        ]);

        // Act
        $response = $this->actingAs($this->coachUser)
            ->get('/performances/' . $performance->id . '/edit');

        // Assert
        $response->assertStatus(200)
            ->assertViewIs('performances.edit')
            ->assertSee('Edit Performance Record')
            ->assertSee('Update')
            ->assertSee($performance->distance_covered);
    }

    /** @test */
    public function it_displays_federation_level_dashboard()
    {
        // Arrange
        $players = Player::factory()->count(5)->create(['club_id' => $this->club->id]);
        foreach ($players as $player) {
            Performance::factory()->count(3)->create(['player_id' => $player->id]);
        }

        // Act
        $response = $this->actingAs($this->adminUser)
            ->get('/performances/dashboard?level=federation&id=' . $this->federation->id);

        // Assert
        $response->assertStatus(200)
            ->assertSee('Federation Dashboard')
            ->assertSee('15') // Total performances
            ->assertSee('Top Players')
            ->assertSee('Performance Trends');
    }

    /** @test */
    public function it_displays_club_level_dashboard()
    {
        // Arrange
        $players = Player::factory()->count(3)->create(['club_id' => $this->club->id]);
        foreach ($players as $player) {
            Performance::factory()->count(2)->create(['player_id' => $player->id]);
        }

        // Act
        $response = $this->actingAs($this->adminUser)
            ->get('/performances/dashboard?level=club&id=' . $this->club->id);

        // Assert
        $response->assertStatus(200)
            ->assertSee('Club Dashboard')
            ->assertSee('6') // Total performances
            ->assertSee('Club Statistics')
            ->assertSee('Player Rankings');
    }

    /** @test */
    public function it_displays_player_level_dashboard()
    {
        // Arrange
        Performance::factory()->count(5)->create([
            'player_id' => $this->player->id
        ]);

        // Act
        $response = $this->actingAs($this->coachUser)
            ->get('/performances/dashboard?level=player&id=' . $this->player->id);

        // Assert
        $response->assertStatus(200)
            ->assertSee('Player Dashboard')
            ->assertSee('5') // Player performances
            ->assertSee('Performance History')
            ->assertSee('Trends');
    }

    /** @test */
    public function it_displays_performance_charts()
    {
        // Arrange
        Performance::factory()->count(10)->create([
            'player_id' => $this->player->id,
            'distance_covered' => 10000
        ]);

        // Act
        $response = $this->actingAs($this->coachUser)
            ->get('/performances/dashboard?level=player&id=' . $this->player->id);

        // Assert
        $response->assertStatus(200)
            ->assertSee('performance-chart')
            ->assertSee('Chart.js')
            ->assertSee('canvas');
    }

    /** @test */
    public function it_displays_performance_metrics()
    {
        // Arrange
        Performance::factory()->create([
            'player_id' => $this->player->id,
            'distance_covered' => 10500,
            'rating' => 8.5
        ]);

        // Act
        $response = $this->actingAs($this->coachUser)
            ->get('/performances/dashboard?level=player&id=' . $this->player->id);

        // Assert
        $response->assertStatus(200)
            ->assertSee('performance-metrics')
            ->assertSee('10500')
            ->assertSee('8.5')
            ->assertSee('Progress');
    }

    /** @test */
    public function it_displays_ai_recommendation_cards()
    {
        // Arrange
        PerformanceRecommendation::factory()->create([
            'player_id' => $this->player->id,
            'title' => 'Increase Sprint Count',
            'priority' => 'high'
        ]);

        // Act
        $response = $this->actingAs($this->coachUser)
            ->get('/performance-recommendations');

        // Assert
        $response->assertStatus(200)
            ->assertSee('ai-recommendation-card')
            ->assertSee('Increase Sprint Count')
            ->assertSee('high')
            ->assertSee('Implement');
    }

    /** @test */
    public function it_displays_performance_export_options()
    {
        // Act
        $response = $this->actingAs($this->adminUser)
            ->get('/performances');

        // Assert
        $response->assertStatus(200)
            ->assertSee('Export')
            ->assertSee('CSV')
            ->assertSee('PDF')
            ->assertSee('Excel');
    }

    /** @test */
    public function it_displays_performance_bulk_import()
    {
        // Act
        $response = $this->actingAs($this->adminUser)
            ->get('/performances');

        // Assert
        $response->assertStatus(200)
            ->assertSee('Bulk Import')
            ->assertSee('Upload CSV')
            ->assertSee('Import');
    }

    /** @test */
    public function it_displays_performance_comparison()
    {
        // Arrange
        $player2 = Player::factory()->create(['club_id' => $this->club->id]);
        
        Performance::factory()->create(['player_id' => $this->player->id, 'distance_covered' => 10000]);
        Performance::factory()->create(['player_id' => $player2->id, 'distance_covered' => 9500]);

        // Act
        $response = $this->actingAs($this->coachUser)
            ->get('/performances/compare?players[]=' . $this->player->id . '&players[]=' . $player2->id);

        // Assert
        $response->assertStatus(200)
            ->assertSee('Performance Comparison')
            ->assertSee('10000')
            ->assertSee('9500')
            ->assertSee('Compare');
    }

    /** @test */
    public function it_displays_performance_alerts()
    {
        // Arrange
        Performance::factory()->create([
            'player_id' => $this->player->id,
            'distance_covered' => 8000, // Below threshold
            'rating' => 6.0 // Below threshold
        ]);

        // Act
        $response = $this->actingAs($this->coachUser)
            ->get('/alerts/performance');

        // Assert
        $response->assertStatus(200)
            ->assertSee('Performance Alerts')
            ->assertSee('Low Performance Alert')
            ->assertSee('8000')
            ->assertSee('6.0');
    }

    /** @test */
    public function it_displays_performance_trends()
    {
        // Arrange
        $dates = ['2024-01-01', '2024-01-08', '2024-01-15'];
        $distances = [9500, 9800, 10200];

        foreach ($dates as $index => $date) {
            Performance::factory()->create([
                'player_id' => $this->player->id,
                'match_date' => $date,
                'distance_covered' => $distances[$index]
            ]);
        }

        // Act
        $response = $this->actingAs($this->coachUser)
            ->get('/performances/trends?player_id=' . $this->player->id);

        // Assert
        $response->assertStatus(200)
            ->assertSee('Performance Trends')
            ->assertSee('9500')
            ->assertSee('10200')
            ->assertSee('Improving');
    }
} 