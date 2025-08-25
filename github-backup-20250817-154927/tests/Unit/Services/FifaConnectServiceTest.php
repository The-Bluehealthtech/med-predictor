<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\FifaConnectService;
use App\Models\Player;
use App\Models\Club;
use App\Models\Federation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Mockery;

class FifaConnectServiceTest extends TestCase
{
    use RefreshDatabase;

    private FifaConnectService $fifaService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fifaService = new FifaConnectService();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_can_sync_player_data_from_fifa_connect()
    {
        // Arrange
        $player = Player::factory()->create([
            'fifa_id' => '12345',
            'first_name' => 'John',
            'last_name' => 'Doe'
        ]);

        $fifaResponse = [
            'player' => [
                'id' => '12345',
                'firstName' => 'John',
                'lastName' => 'Doe',
                'dateOfBirth' => '1995-01-15',
                'nationality' => 'France',
                'position' => 'Forward',
                'club' => [
                    'id' => '67890',
                    'name' => 'Paris Saint-Germain'
                ],
                'federation' => [
                    'id' => '11111',
                    'name' => 'French Football Federation'
                ]
            ]
        ];

        Http::fake([
            'fifa-connect-api.com/players/*' => Http::response($fifaResponse, 200)
        ]);

        // Act
        $result = $this->fifaService->syncPlayerData($player->fifa_id);

        // Assert
        $this->assertTrue($result['success']);
        $this->assertEquals('Player data synchronized successfully', $result['message']);
        
        $updatedPlayer = Player::find($player->id);
        $this->assertEquals('1995-01-15', $updatedPlayer->date_of_birth);
        $this->assertEquals('France', $updatedPlayer->nationality);
        $this->assertEquals('Forward', $updatedPlayer->position);
    }

    /** @test */
    public function it_handles_fifa_api_errors_gracefully()
    {
        // Arrange
        $player = Player::factory()->create(['fifa_id' => '12345']);

        Http::fake([
            'fifa-connect-api.com/players/*' => Http::response(['error' => 'Player not found'], 404)
        ]);

        // Act
        $result = $this->fifaService->syncPlayerData($player->fifa_id);

        // Assert
        $this->assertFalse($result['success']);
        $this->assertEquals('Failed to sync player data: Player not found', $result['message']);
        $this->assertEquals(404, $result['status_code']);
    }

    /** @test */
    public function it_caches_player_data_for_performance()
    {
        // Arrange
        $player = Player::factory()->create(['fifa_id' => '12345']);
        $fifaResponse = ['player' => ['id' => '12345', 'firstName' => 'John']];

        Http::fake([
            'fifa-connect-api.com/players/*' => Http::response($fifaResponse, 200)
        ]);

        // Act
        $this->fifaService->syncPlayerData($player->fifa_id);
        $this->fifaService->syncPlayerData($player->fifa_id); // Second call should use cache

        // Assert
        $this->assertTrue(Cache::has("fifa_player_{$player->fifa_id}"));
        Http::assertSentCount(1); // Only one HTTP request should be made
    }

    /** @test */
    public function it_validates_fifa_compliance_requirements()
    {
        // Arrange
        $player = Player::factory()->create([
            'fifa_id' => '12345',
            'date_of_birth' => '2000-01-01',
            'nationality' => 'France'
        ]);

        $complianceResponse = [
            'compliance' => [
                'eligible' => true,
                'requirements' => [
                    'age_verified' => true,
                    'nationality_confirmed' => true,
                    'medical_clearance' => true
                ]
            ]
        ];

        Http::fake([
            'fifa-connect-api.com/compliance/*' => Http::response($complianceResponse, 200)
        ]);

        // Act
        $result = $this->fifaService->checkCompliance($player->fifa_id);

        // Assert
        $this->assertTrue($result['success']);
        $this->assertTrue($result['data']['eligible']);
        $this->assertTrue($result['data']['requirements']['age_verified']);
    }

    /** @test */
    public function it_syncs_club_data_from_fifa_connect()
    {
        // Arrange
        $club = Club::factory()->create(['fifa_id' => '67890']);

        $fifaResponse = [
            'club' => [
                'id' => '67890',
                'name' => 'Paris Saint-Germain',
                'country' => 'France',
                'league' => 'Ligue 1',
                'stadium' => 'Parc des Princes'
            ]
        ];

        Http::fake([
            'fifa-connect-api.com/clubs/*' => Http::response($fifaResponse, 200)
        ]);

        // Act
        $result = $this->fifaService->syncClubData($club->fifa_id);

        // Assert
        $this->assertTrue($result['success']);
        
        $updatedClub = Club::find($club->id);
        $this->assertEquals('Paris Saint-Germain', $updatedClub->name);
        $this->assertEquals('France', $updatedClub->country);
    }

    /** @test */
    public function it_syncs_federation_data_from_fifa_connect()
    {
        // Arrange
        $federation = Federation::factory()->create(['fifa_id' => '11111']);

        $fifaResponse = [
            'federation' => [
                'id' => '11111',
                'name' => 'French Football Federation',
                'country' => 'France',
                'region' => 'UEFA',
                'member_since' => '1904'
            ]
        ];

        Http::fake([
            'fifa-connect-api.com/federations/*' => Http::response($fifaResponse, 200)
        ]);

        // Act
        $result = $this->fifaService->syncFederationData($federation->fifa_id);

        // Assert
        $this->assertTrue($result['success']);
        
        $updatedFederation = Federation::find($federation->id);
        $this->assertEquals('French Football Federation', $updatedFederation->name);
        $this->assertEquals('UEFA', $updatedFederation->region);
    }

    /** @test */
    public function it_handles_network_timeouts()
    {
        // Arrange
        $player = Player::factory()->create(['fifa_id' => '12345']);

        Http::fake([
            'fifa-connect-api.com/players/*' => Http::timeout()
        ]);

        // Act
        $result = $this->fifaService->syncPlayerData($player->fifa_id);

        // Assert
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('timeout', strtolower($result['message']));
    }

    /** @test */
    public function it_logs_errors_for_debugging()
    {
        // Arrange
        $player = Player::factory()->create(['fifa_id' => '12345']);

        Http::fake([
            'fifa-connect-api.com/players/*' => Http::response(['error' => 'Server error'], 500)
        ]);

        Log::shouldReceive('error')->once();

        // Act
        $this->fifaService->syncPlayerData($player->fifa_id);

        // Assert
        // Log::error should have been called
    }

    /** @test */
    public function it_retries_failed_requests()
    {
        // Arrange
        $player = Player::factory()->create(['fifa_id' => '12345']);

        Http::fake([
            'fifa-connect-api.com/players/*' => Http::sequence()
                ->push(['error' => 'Temporary error'], 503)
                ->push(['error' => 'Temporary error'], 503)
                ->push(['player' => ['id' => '12345', 'firstName' => 'John']], 200)
        ]);

        // Act
        $result = $this->fifaService->syncPlayerData($player->fifa_id);

        // Assert
        $this->assertTrue($result['success']);
        Http::assertSentCount(3); // Should have retried 3 times
    }

    /** @test */
    public function it_clears_cache_when_data_is_updated()
    {
        // Arrange
        $player = Player::factory()->create(['fifa_id' => '12345']);
        $fifaResponse = ['player' => ['id' => '12345', 'firstName' => 'John']];

        Http::fake([
            'fifa-connect-api.com/players/*' => Http::response($fifaResponse, 200)
        ]);

        // Act
        $this->fifaService->syncPlayerData($player->fifa_id);
        $this->fifaService->clearPlayerCache($player->fifa_id);

        // Assert
        $this->assertFalse(Cache::has("fifa_player_{$player->fifa_id}"));
    }
} 