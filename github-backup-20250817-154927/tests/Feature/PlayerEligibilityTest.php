<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Player;
use App\Models\Competition;
use App\Models\PlayerLicense;
use App\Models\HealthRecord;
use App\Models\Club;
use App\Models\User;
use App\Services\PlayerEligibilityService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PlayerEligibilityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_player_with_valid_license_and_health_is_eligible()
    {
        // Create test data
        $competition = Competition::factory()->create([
            'require_federation_license' => true,
            'season' => '2024-2025'
        ]);

        $player = Player::factory()->create();

        // Create valid license
        PlayerLicense::factory()->create([
            'player_id' => $player->id,
            'status' => 'approved',
            'season' => '2024-2025'
        ]);

        // Create healthy status with low risk
        HealthRecord::factory()->create([
            'player_id' => $player->id,
            'risk_score' => 0.2,
            'status' => 'active'
        ]);

        $eligibilityService = app(PlayerEligibilityService::class);
        [$eligible, $reason] = $eligibilityService->isEligible($player, $competition);

        $this->assertTrue($eligible);
        $this->assertNull($reason);
    }

    public function test_player_without_license_is_ineligible()
    {
        $competition = Competition::factory()->create([
            'require_federation_license' => true,
            'season' => '2024-2025'
        ]);

        $player = Player::factory()->create();

        // No license created

        $eligibilityService = app(PlayerEligibilityService::class);
        [$eligible, $reason] = $eligibilityService->isEligible($player, $competition);

        $this->assertFalse($eligible);
        $this->assertEquals('Licence fédération non valide ou non approuvée pour la saison', $reason);
    }

    public function test_player_with_high_medical_risk_is_ineligible()
    {
        $competition = Competition::factory()->create([
            'require_federation_license' => false
        ]);

        $player = Player::factory()->create();

        // Create high risk health record
        HealthRecord::factory()->create([
            'player_id' => $player->id,
            'risk_score' => 0.9,
            'status' => 'active'
        ]);

        $eligibilityService = app(PlayerEligibilityService::class);
        [$eligible, $reason] = $eligibilityService->isEligible($player, $competition);

        $this->assertFalse($eligible);
        $this->assertStringContainsString('Risque médical élevé', $reason);
    }

    public function test_player_with_serious_symptoms_is_ineligible()
    {
        $competition = Competition::factory()->create([
            'require_federation_license' => false
        ]);

        $player = Player::factory()->create();

        // Create health record with serious symptoms
        HealthRecord::factory()->create([
            'player_id' => $player->id,
            'risk_score' => 0.3,
            'symptoms' => ['douleur intense', 'fracture'],
            'status' => 'active'
        ]);

        $eligibilityService = app(PlayerEligibilityService::class);
        [$eligible, $reason] = $eligibilityService->isEligible($player, $competition);

        $this->assertFalse($eligible);
        $this->assertStringContainsString('Symptômes médicaux graves', $reason);
    }

    public function test_player_with_serious_diagnosis_is_ineligible()
    {
        $competition = Competition::factory()->create([
            'require_federation_license' => false
        ]);

        $player = Player::factory()->create();

        // Create health record with serious diagnosis
        HealthRecord::factory()->create([
            'player_id' => $player->id,
            'risk_score' => 0.3,
            'diagnosis' => 'Fracture de la jambe droite',
            'status' => 'active'
        ]);

        $eligibilityService = app(PlayerEligibilityService::class);
        [$eligible, $reason] = $eligibilityService->isEligible($player, $competition);

        $this->assertFalse($eligible);
        $this->assertStringContainsString('Diagnostic médical incompatible', $reason);
    }

    public function test_player_with_pending_license_is_ineligible()
    {
        $competition = Competition::factory()->create([
            'require_federation_license' => true,
            'season' => '2024-2025'
        ]);

        $player = Player::factory()->create();

        // Create pending license
        PlayerLicense::factory()->create([
            'player_id' => $player->id,
            'status' => 'pending',
            'season' => '2024-2025'
        ]);

        $eligibilityService = app(PlayerEligibilityService::class);
        [$eligible, $reason] = $eligibilityService->isEligible($player, $competition);

        $this->assertFalse($eligible);
        $this->assertEquals('Licence fédération non valide ou non approuvée pour la saison', $reason);
    }

    public function test_player_with_wrong_season_license_is_ineligible()
    {
        $competition = Competition::factory()->create([
            'require_federation_license' => true,
            'season' => '2024-2025'
        ]);

        $player = Player::factory()->create();

        // Create license for different season
        PlayerLicense::factory()->create([
            'player_id' => $player->id,
            'status' => 'approved',
            'season' => '2023-2024'
        ]);

        $eligibilityService = app(PlayerEligibilityService::class);
        [$eligible, $reason] = $eligibilityService->isEligible($player, $competition);

        $this->assertFalse($eligible);
        $this->assertEquals('Licence fédération non valide ou non approuvée pour la saison', $reason);
    }

    public function test_competition_without_license_requirement_ignores_license()
    {
        $competition = Competition::factory()->create([
            'require_federation_license' => false
        ]);

        $player = Player::factory()->create();

        // No license, but should be eligible
        $eligibilityService = app(PlayerEligibilityService::class);
        [$eligible, $reason] = $eligibilityService->isEligible($player, $competition);

        $this->assertTrue($eligible);
        $this->assertNull($reason);
    }

    public function test_api_returns_eligible_and_ineligible_players()
    {
        $user = User::factory()->create(['role' => 'club_admin']);
        $club = Club::factory()->create();
        $user->update(['club_id' => $club->id]);

        $competition = Competition::factory()->create([
            'require_federation_license' => true,
            'season' => '2024-2025'
        ]);

        // Create eligible player
        $eligiblePlayer = Player::factory()->create(['club_id' => $club->id]);
        PlayerLicense::factory()->create([
            'player_id' => $eligiblePlayer->id,
            'status' => 'approved',
            'season' => '2024-2025'
        ]);

        // Create ineligible player (no license)
        $ineligiblePlayer = Player::factory()->create(['club_id' => $club->id]);

        $response = $this->actingAs($user)
            ->getJson("/api/club/eligible-players/{$competition->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'eligible_players',
                    'ineligible_players',
                    'total_eligible',
                    'total_ineligible',
                    'competition'
                ]
            ]);

        $data = $response->json('data');
        $this->assertEquals(1, $data['total_eligible']);
        $this->assertEquals(1, $data['total_ineligible']);
        $this->assertCount(1, $data['eligible_players']);
        $this->assertCount(1, $data['ineligible_players']);
    }
} 