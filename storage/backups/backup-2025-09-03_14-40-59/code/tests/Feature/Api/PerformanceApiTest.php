<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\Player;
use App\Models\Performance;
use App\Models\User;
use App\Models\Club;
use App\Models\Federation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;

class PerformanceApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;
    private Player $player;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create([
            'role' => 'admin'
        ]);
        
        $this->player = Player::factory()->create();
    }

    /** @test */
    public function it_can_list_performances()
    {
        // Arrange
        Performance::factory()->count(5)->create([
            'player_id' => $this->player->id
        ]);

        // Act
        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/performances');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'player_id',
                        'match_date',
                        'distance_covered',
                        'sprint_count',
                        'max_speed',
                        'created_at',
                        'updated_at'
                    ]
                ],
                'meta' => [
                    'current_page',
                    'per_page',
                    'total'
                ]
            ]);

        $this->assertEquals(5, count($response->json('data')));
    }

    /** @test */
    public function it_can_show_single_performance()
    {
        // Arrange
        $performance = Performance::factory()->create([
            'player_id' => $this->player->id,
            'distance_covered' => 10500,
            'sprint_count' => 25
        ]);

        // Act
        $response = $this->actingAs($this->user)
            ->getJson("/api/v1/performances/{$performance->id}");

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $performance->id,
                    'distance_covered' => 10500,
                    'sprint_count' => 25
                ]
            ]);
    }

    /** @test */
    public function it_can_create_performance()
    {
        // Arrange
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
            'rating' => 8.5
        ];

        // Act
        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/performances', $performanceData);

        // Assert
        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'player_id',
                    'match_date',
                    'distance_covered',
                    'sprint_count',
                    'max_speed'
                ],
                'message'
            ]);

        $this->assertDatabaseHas('performances', [
            'player_id' => $this->player->id,
            'distance_covered' => 10500,
            'sprint_count' => 25
        ]);
    }

    /** @test */
    public function it_can_update_performance()
    {
        // Arrange
        $performance = Performance::factory()->create([
            'player_id' => $this->player->id,
            'distance_covered' => 10000
        ]);

        $updateData = [
            'distance_covered' => 11000,
            'sprint_count' => 30
        ];

        // Act
        $response = $this->actingAs($this->user)
            ->putJson("/api/v1/performances/{$performance->id}", $updateData);

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'distance_covered' => 11000,
                    'sprint_count' => 30
                ]
            ]);

        $this->assertDatabaseHas('performances', [
            'id' => $performance->id,
            'distance_covered' => 11000
        ]);
    }

    /** @test */
    public function it_can_delete_performance()
    {
        // Arrange
        $performance = Performance::factory()->create([
            'player_id' => $this->player->id
        ]);

        // Act
        $response = $this->actingAs($this->user)
            ->deleteJson("/api/v1/performances/{$performance->id}");

        // Assert
        $response->assertStatus(200)
            ->assertJson(['message' => 'Performance deleted successfully']);

        $this->assertDatabaseMissing('performances', [
            'id' => $performance->id
        ]);
    }

    /** @test */
    public function it_can_get_performance_analytics()
    {
        // Arrange
        Performance::factory()->count(10)->create([
            'player_id' => $this->player->id,
            'distance_covered' => 10000,
            'rating' => 8.0
        ]);

        // Act
        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/performances/analytics');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'total_performances',
                    'average_distance',
                    'average_rating',
                    'total_matches',
                    'performance_trends',
                    'top_performers'
                ]
            ]);
    }

    /** @test */
    public function it_can_bulk_import_performances()
    {
        // Arrange
        $csvData = [
            ['player_id', 'match_date', 'distance_covered', 'sprint_count', 'max_speed'],
            [$this->player->id, '2024-01-15', '10500', '25', '32.5'],
            [$this->player->id, '2024-01-22', '9800', '22', '31.8']
        ];

        $file = $this->createCsvFile($csvData);

        // Act
        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/performances/bulk-import', [
                'file' => $file
            ]);

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Performances imported successfully',
                'imported_count' => 2
            ]);

        $this->assertDatabaseCount('performances', 2);
    }

    /** @test */
    public function it_can_export_performances()
    {
        // Arrange
        Performance::factory()->count(5)->create([
            'player_id' => $this->player->id
        ]);

        // Act
        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/performances/export?format=csv');

        // Assert
        $response->assertStatus(200)
            ->assertHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->assertHeader('Content-Disposition', 'attachment; filename="performances.csv"');
    }

    /** @test */
    public function it_can_get_dashboard_data()
    {
        // Arrange
        $federation = Federation::factory()->create();
        $club = Club::factory()->create(['federation_id' => $federation->id]);
        $player = Player::factory()->create(['club_id' => $club->id]);

        Performance::factory()->count(5)->create([
            'player_id' => $player->id
        ]);

        // Act
        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/performances/dashboard?level=federation&id=' . $federation->id);

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'summary_stats',
                    'performance_charts',
                    'recent_performances',
                    'top_players'
                ]
            ]);
    }

    /** @test */
    public function it_validates_performance_data_on_create()
    {
        // Arrange
        $invalidData = [
            'player_id' => 99999, // Non-existent player
            'match_date' => 'invalid-date',
            'distance_covered' => -1000 // Negative value
        ];

        // Act
        $response = $this->actingAs($this->user)
            ->postJson('/api/v1/performances', $invalidData);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['player_id', 'match_date', 'distance_covered']);
    }

    /** @test */
    public function it_filters_performances_by_date_range()
    {
        // Arrange
        Performance::factory()->create([
            'player_id' => $this->player->id,
            'match_date' => '2024-01-15'
        ]);

        Performance::factory()->create([
            'player_id' => $this->player->id,
            'match_date' => '2024-02-15'
        ]);

        // Act
        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/performances?start_date=2024-01-01&end_date=2024-01-31');

        // Assert
        $response->assertStatus(200);
        $this->assertEquals(1, count($response->json('data')));
    }

    /** @test */
    public function it_filters_performances_by_player()
    {
        // Arrange
        $player2 = Player::factory()->create();
        
        Performance::factory()->create(['player_id' => $this->player->id]);
        Performance::factory()->create(['player_id' => $player2->id]);

        // Act
        $response = $this->actingAs($this->user)
            ->getJson("/api/v1/performances?player_id={$this->player->id}");

        // Assert
        $response->assertStatus(200);
        $this->assertEquals(1, count($response->json('data')));
    }

    /** @test */
    public function it_requires_authentication()
    {
        // Act
        $response = $this->getJson('/api/v1/performances');

        // Assert
        $response->assertStatus(401);
    }

    /** @test */
    public function it_requires_authorization_for_admin_operations()
    {
        // Arrange
        $regularUser = User::factory()->create(['role' => 'user']);
        $performance = Performance::factory()->create();

        // Act
        $response = $this->actingAs($regularUser)
            ->deleteJson("/api/v1/performances/{$performance->id}");

        // Assert
        $response->assertStatus(403);
    }

    /** @test */
    public function it_handles_pagination_correctly()
    {
        // Arrange
        Performance::factory()->count(25)->create([
            'player_id' => $this->player->id
        ]);

        // Act
        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/performances?page=2&per_page=10');

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'meta' => [
                    'current_page' => 2,
                    'per_page' => 10,
                    'total' => 25
                ]
            ]);
    }

    /** @test */
    public function it_can_search_performances()
    {
        // Arrange
        Performance::factory()->create([
            'player_id' => $this->player->id,
            'notes' => 'Excellent performance in derby match'
        ]);

        Performance::factory()->create([
            'player_id' => $this->player->id,
            'notes' => 'Average performance'
        ]);

        // Act
        $response = $this->actingAs($this->user)
            ->getJson('/api/v1/performances?search=derby');

        // Assert
        $response->assertStatus(200);
        $this->assertEquals(1, count($response->json('data')));
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