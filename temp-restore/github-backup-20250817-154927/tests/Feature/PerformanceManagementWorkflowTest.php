<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Player;
use App\Models\Performance;
use App\Models\Club;
use App\Models\Federation;
use App\Models\PerformanceRecommendation;
use App\Services\FifaConnectService;
use App\Services\Hl7FhirService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class PerformanceManagementWorkflowTest extends TestCase
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
    public function complete_performance_data_entry_workflow()
    {
        // Step 1: Coach logs in and navigates to performance entry
        $this->actingAs($this->coachUser)
            ->get('/performances/create')
            ->assertStatus(200)
            ->assertSee('Create Performance Record');

        // Step 2: Coach enters performance data
        $performanceData = [
            'player_id' => $this->player->id,
            'match_date' => '2024-01-15',
            'distance_covered' => 10500,
            'sprint_count' => 25,
            'max_speed' => 32.5,
            'avg_speed' => 8.2,
            'passes_completed' => 45,
            'passes_attempted' => 50,
            'tackles_won' => 3,
            'tackles_attempted' => 4,
            'shots_on_target' => 2,
            'shots_total' => 4,
            'goals_scored' => 1,
            'assists' => 1,
            'yellow_cards' => 0,
            'red_cards' => 0,
            'minutes_played' => 90,
            'rating' => 8.5,
            'notes' => 'Excellent performance in derby match'
        ];

        $response = $this->actingAs($this->coachUser)
            ->post('/performances', $performanceData);

        $response->assertRedirect('/performances')
            ->assertSessionHas('success');

        // Step 3: Verify performance was created
        $this->assertDatabaseHas('performances', [
            'player_id' => $this->player->id,
            'distance_covered' => 10500,
            'rating' => 8.5
        ]);

        // Step 4: Coach views the performance list
        $this->actingAs($this->coachUser)
            ->get('/performances')
            ->assertStatus(200)
            ->assertSee('10500')
            ->assertSee('8.5');
    }

    /** @test */
    public function fifa_connect_integration_workflow()
    {
        // Mock FIFA Connect API responses
        Http::fake([
            'fifa-connect-api.com/players/*' => Http::response([
                'player' => [
                    'id' => $this->player->fifa_id,
                    'firstName' => $this->player->first_name,
                    'lastName' => $this->player->last_name,
                    'dateOfBirth' => '1995-01-15',
                    'nationality' => 'France',
                    'position' => 'Forward'
                ]
            ], 200),
            'fifa-connect-api.com/compliance/*' => Http::response([
                'compliance' => [
                    'eligible' => true,
                    'requirements' => [
                        'age_verified' => true,
                        'nationality_confirmed' => true,
                        'medical_clearance' => true
                    ]
                ]
            ], 200)
        ]);

        // Step 1: Admin initiates FIFA data sync
        $this->actingAs($this->adminUser)
            ->post('/fifa/sync-player/' . $this->player->fifa_id)
            ->assertStatus(200)
            ->assertJson(['success' => true]);

        // Step 2: Admin checks compliance
        $this->actingAs($this->adminUser)
            ->get('/fifa/compliance/' . $this->player->fifa_id)
            ->assertStatus(200)
            ->assertJson(['success' => true]);

        // Step 3: Verify data was updated
        $this->player->refresh();
        $this->assertEquals('France', $this->player->nationality);
        $this->assertEquals('Forward', $this->player->position);
    }

    /** @test */
    public function hl7_fhir_medical_data_workflow()
    {
        // Mock FHIR server responses
        Http::fake([
            'fhir-server.com/Patient' => Http::response([
                'resourceType' => 'Patient',
                'id' => 'patient-12345',
                'name' => [
                    [
                        'family' => $this->player->last_name,
                        'given' => [$this->player->first_name]
                    ]
                ]
            ], 201),
            'fhir-server.com/Observation' => Http::response([
                'resourceType' => 'Observation',
                'id' => 'observation-67890'
            ], 201)
        ]);

        // Step 1: Create performance record
        $performance = Performance::factory()->create([
            'player_id' => $this->player->id,
            'distance_covered' => 10500,
            'sprint_count' => 25
        ]);

        // Step 2: Admin syncs medical data to FHIR
        $this->actingAs($this->adminUser)
            ->post('/hl7/sync-performance/' . $performance->id)
            ->assertStatus(200)
            ->assertJson(['success' => true]);

        // Step 3: Verify FHIR patient resource was created
        $this->player->refresh();
        $this->assertNotNull($this->player->fhir_patient_id);
    }

    /** @test */
    public function performance_analytics_and_reporting_workflow()
    {
        // Create multiple performance records
        Performance::factory()->count(10)->create([
            'player_id' => $this->player->id,
            'distance_covered' => 10000,
            'rating' => 8.0
        ]);

        // Step 1: Coach views performance dashboard
        $this->actingAs($this->coachUser)
            ->get('/performances/dashboard?level=player&id=' . $this->player->id)
            ->assertStatus(200)
            ->assertSee('Performance Dashboard')
            ->assertSee('10000');

        // Step 2: Coach generates performance report
        $this->actingAs($this->coachUser)
            ->get('/performances/export?format=pdf&player_id=' . $this->player->id)
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'application/pdf');

        // Step 3: Coach views analytics
        $this->actingAs($this->coachUser)
            ->get('/performances/analytics?player_id=' . $this->player->id)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'total_performances',
                    'average_distance',
                    'average_rating'
                ]
            ]);
    }

    /** @test */
    public function ai_recommendation_workflow()
    {
        // Create performance data for AI analysis
        Performance::factory()->count(5)->create([
            'player_id' => $this->player->id,
            'distance_covered' => 9500, // Below optimal
            'sprint_count' => 20 // Below optimal
        ]);

        // Step 1: System generates AI recommendations
        $recommendation = PerformanceRecommendation::factory()->create([
            'player_id' => $this->player->id,
            'type' => 'performance_improvement',
            'title' => 'Increase Sprint Count',
            'description' => 'Player should aim for 25+ sprints per match',
            'priority' => 'high',
            'status' => 'pending'
        ]);

        // Step 2: Coach views recommendations
        $this->actingAs($this->coachUser)
            ->get('/performance-recommendations')
            ->assertStatus(200)
            ->assertSee('Increase Sprint Count')
            ->assertSee('high');

        // Step 3: Coach implements recommendation
        $this->actingAs($this->coachUser)
            ->patch('/performance-recommendations/' . $recommendation->id, [
                'status' => 'implemented',
                'implementation_notes' => 'Added sprint training to weekly program'
            ])
            ->assertStatus(200);

        // Step 4: Verify recommendation was updated
        $this->assertDatabaseHas('performance_recommendations', [
            'id' => $recommendation->id,
            'status' => 'implemented'
        ]);
    }

    /** @test */
    public function multi_level_dashboard_workflow()
    {
        // Create data for different levels
        $players = Player::factory()->count(5)->create(['club_id' => $this->club->id]);
        
        foreach ($players as $player) {
            Performance::factory()->count(3)->create(['player_id' => $player->id]);
        }

        // Step 1: Federation level dashboard
        $this->actingAs($this->adminUser)
            ->get('/performances/dashboard?level=federation&id=' . $this->federation->id)
            ->assertStatus(200)
            ->assertSee('Federation Dashboard')
            ->assertSee('15'); // Total performances

        // Step 2: Club level dashboard
        $this->actingAs($this->adminUser)
            ->get('/performances/dashboard?level=club&id=' . $this->club->id)
            ->assertStatus(200)
            ->assertSee('Club Dashboard')
            ->assertSee('15'); // Total performances

        // Step 3: Player level dashboard
        $this->actingAs($this->coachUser)
            ->get('/performances/dashboard?level=player&id=' . $this->player->id)
            ->assertStatus(200)
            ->assertSee('Player Dashboard')
            ->assertSee('3'); // Player performances
    }

    /** @test */
    public function bulk_data_import_workflow()
    {
        // Step 1: Admin prepares CSV file
        $csvData = [
            ['player_id', 'match_date', 'distance_covered', 'sprint_count', 'max_speed'],
            [$this->player->id, '2024-01-15', '10500', '25', '32.5'],
            [$this->player->id, '2024-01-22', '9800', '22', '31.8'],
            [$this->player->id, '2024-01-29', '10200', '28', '33.1']
        ];

        $file = $this->createCsvFile($csvData);

        // Step 2: Admin uploads and imports data
        $this->actingAs($this->adminUser)
            ->post('/performances/bulk-import', ['file' => $file])
            ->assertStatus(200)
            ->assertJson(['imported_count' => 3]);

        // Step 3: Verify all records were imported
        $this->assertDatabaseCount('performances', 3);
        $this->assertDatabaseHas('performances', [
            'player_id' => $this->player->id,
            'distance_covered' => 10500
        ]);
    }

    /** @test */
    public function performance_comparison_workflow()
    {
        // Create performance data for multiple players
        $player2 = Player::factory()->create(['club_id' => $this->club->id]);
        
        Performance::factory()->count(3)->create([
            'player_id' => $this->player->id,
            'distance_covered' => 10000,
            'rating' => 8.0
        ]);

        Performance::factory()->count(3)->create([
            'player_id' => $player2->id,
            'distance_covered' => 9500,
            'rating' => 7.5
        ]);

        // Step 1: Coach compares players
        $this->actingAs($this->coachUser)
            ->get('/performances/compare?players[]=' . $this->player->id . '&players[]=' . $player2->id)
            ->assertStatus(200)
            ->assertSee('Performance Comparison')
            ->assertSee('10000')
            ->assertSee('9500');
    }

    /** @test */
    public function performance_trend_analysis_workflow()
    {
        // Create performance data over time
        $dates = ['2024-01-01', '2024-01-08', '2024-01-15', '2024-01-22', '2024-01-29'];
        $distances = [9500, 9800, 10200, 10500, 10800]; // Improving trend

        foreach ($dates as $index => $date) {
            Performance::factory()->create([
                'player_id' => $this->player->id,
                'match_date' => $date,
                'distance_covered' => $distances[$index]
            ]);
        }

        // Step 1: Coach views trend analysis
        $this->actingAs($this->coachUser)
            ->get('/performances/trends?player_id=' . $this->player->id . '&metric=distance_covered')
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'trend',
                    'improvement_rate',
                    'projection'
                ]
            ]);

        // Step 2: Coach views trend chart
        $this->actingAs($this->coachUser)
            ->get('/performances/chart?player_id=' . $this->player->id . '&type=line&metric=distance_covered')
            ->assertStatus(200)
            ->assertSee('9500')
            ->assertSee('10800');
    }

    /** @test */
    public function performance_alert_workflow()
    {
        // Create performance below threshold
        Performance::factory()->create([
            'player_id' => $this->player->id,
            'distance_covered' => 8000, // Below 9000 threshold
            'rating' => 6.0 // Below 7.0 threshold
        ]);

        // Step 1: System generates alerts
        $this->actingAs($this->adminUser)
            ->post('/performances/generate-alerts')
            ->assertStatus(200);

        // Step 2: Coach views alerts
        $this->actingAs($this->coachUser)
            ->get('/alerts/performance')
            ->assertStatus(200)
            ->assertSee('Low Performance Alert')
            ->assertSee('8000');
    }

    private function createCsvFile(array $data): \Illuminate\Http\UploadedFile
    {
        $filename = 'performances.csv';
        $filepath = storage_path('app/temp/' . $filename);
        
        if (!file_exists(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }

        $handle = fopen($filepath, 'w');
        foreach ($data as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);

        return new \Illuminate\Http\UploadedFile(
            $filepath,
            $filename,
            'text/csv',
            null,
            true
        );
    }
} 