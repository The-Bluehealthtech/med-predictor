<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Player;
use App\Models\Club;
use App\Models\Federation;
use App\Models\Transfer;
use App\Services\FifaTransferService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class TransferSystemTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected Player $player;
    protected Club $clubOrigin;
    protected Club $clubDestination;
    protected Federation $federation;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Créer un utilisateur authentifié
        $this->user = User::factory()->create();
        
        // Créer une fédération
        $this->federation = Federation::factory()->create([
            'fifa_code' => 'FRA',
            'country' => 'France',
        ]);
        
        // Créer des clubs
        $this->clubOrigin = Club::factory()->create([
            'country' => 'France',
            'federation_id' => $this->federation->id,
            'can_conduct_transfers' => true,
        ]);
        
        $this->clubDestination = Club::factory()->create([
            'country' => 'Angleterre',
            'can_conduct_transfers' => true,
        ]);
        
        // Créer un joueur
        $this->player = Player::factory()->create([
            'is_transfer_eligible' => true,
            'current_club_id' => $this->clubOrigin->id,
        ]);
    }

    /** @test */
    public function user_can_view_transfers_list()
    {
        $response = $this->actingAs($this->user)
            ->get('/transfers');

        $response->assertStatus(200);
        $response->assertViewIs('transfers.index');
    }

    /** @test */
    public function user_can_create_transfer()
    {
        $transferData = [
            'player_id' => $this->player->id,
            'club_origin_id' => $this->clubOrigin->id,
            'club_destination_id' => $this->clubDestination->id,
            'transfer_type' => 'permanent',
            'transfer_date' => now()->format('Y-m-d'),
            'contract_start_date' => now()->addDay()->format('Y-m-d'),
            'transfer_fee' => 1000000,
            'currency' => 'EUR',
            'is_minor_transfer' => false,
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/transfers', $transferData);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('transfers', [
            'player_id' => $this->player->id,
            'club_origin_id' => $this->clubOrigin->id,
            'club_destination_id' => $this->clubDestination->id,
            'transfer_type' => 'permanent',
            'transfer_status' => 'draft',
        ]);
    }

    /** @test */
    public function transfer_creation_validates_required_fields()
    {
        $response = $this->actingAs($this->user)
            ->postJson('/api/transfers', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'player_id',
            'club_origin_id',
            'club_destination_id',
            'transfer_type',
            'transfer_date',
            'contract_start_date',
        ]);
    }

    /** @test */
    public function transfer_cannot_be_created_with_same_origin_and_destination()
    {
        $transferData = [
            'player_id' => $this->player->id,
            'club_origin_id' => $this->clubOrigin->id,
            'club_destination_id' => $this->clubOrigin->id, // Même club
            'transfer_type' => 'permanent',
            'transfer_date' => now()->format('Y-m-d'),
            'contract_start_date' => now()->addDay()->format('Y-m-d'),
            'currency' => 'EUR',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/transfers', $transferData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['club_destination_id']);
    }

    /** @test */
    public function international_transfer_is_detected_correctly()
    {
        $transferData = [
            'player_id' => $this->player->id,
            'club_origin_id' => $this->clubOrigin->id,
            'club_destination_id' => $this->clubDestination->id,
            'transfer_type' => 'permanent',
            'transfer_date' => now()->format('Y-m-d'),
            'contract_start_date' => now()->addDay()->format('Y-m-d'),
            'currency' => 'EUR',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/transfers', $transferData);

        $response->assertStatus(200);

        $transfer = Transfer::where('player_id', $this->player->id)->first();
        $this->assertTrue($transfer->is_international);
        $this->assertEquals('not_requested', $transfer->itc_status);
    }

    /** @test */
    public function domestic_transfer_does_not_require_itc()
    {
        $domesticClub = Club::factory()->create([
            'country' => 'France',
            'federation_id' => $this->federation->id,
        ]);

        $transferData = [
            'player_id' => $this->player->id,
            'club_origin_id' => $this->clubOrigin->id,
            'club_destination_id' => $domesticClub->id,
            'transfer_type' => 'permanent',
            'transfer_date' => now()->format('Y-m-d'),
            'contract_start_date' => now()->addDay()->format('Y-m-d'),
            'currency' => 'EUR',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/transfers', $transferData);

        $response->assertStatus(200);

        $transfer = Transfer::where('player_id', $this->player->id)->first();
        $this->assertFalse($transfer->is_international);
        $this->assertEquals('not_required', $transfer->itc_status);
    }

    /** @test */
    public function transfer_cannot_be_submitted_without_required_documents()
    {
        $transfer = Transfer::factory()->create([
            'player_id' => $this->player->id,
            'club_origin_id' => $this->clubOrigin->id,
            'club_destination_id' => $this->clubDestination->id,
            'transfer_status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)
            ->postJson("/api/transfers/{$transfer->id}/submit-fifa");

        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
    }

    /** @test */
    public function transfer_can_be_updated()
    {
        $transfer = Transfer::factory()->create([
            'player_id' => $this->player->id,
            'club_origin_id' => $this->clubOrigin->id,
            'club_destination_id' => $this->clubDestination->id,
            'transfer_status' => 'draft',
        ]);

        $updateData = [
            'transfer_fee' => 2000000,
            'currency' => 'EUR',
            'notes' => 'Transfert mis à jour',
        ];

        $response = $this->actingAs($this->user)
            ->putJson("/api/transfers/{$transfer->id}", $updateData);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $transfer->refresh();
        $this->assertEquals(2000000, $transfer->transfer_fee);
        $this->assertEquals('Transfert mis à jour', $transfer->notes);
    }

    /** @test */
    public function transfer_can_be_deleted_when_in_draft_status()
    {
        $transfer = Transfer::factory()->create([
            'player_id' => $this->player->id,
            'club_origin_id' => $this->clubOrigin->id,
            'club_destination_id' => $this->clubDestination->id,
            'transfer_status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/transfers/{$transfer->id}");

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseMissing('transfers', ['id' => $transfer->id]);
    }

    /** @test */
    public function transfer_cannot_be_deleted_when_not_in_draft_status()
    {
        $transfer = Transfer::factory()->create([
            'player_id' => $this->player->id,
            'club_origin_id' => $this->clubOrigin->id,
            'club_destination_id' => $this->clubDestination->id,
            'transfer_status' => 'approved',
        ]);

        $response = $this->actingAs($this->user)
            ->deleteJson("/api/transfers/{$transfer->id}");

        $response->assertStatus(400);
        $response->assertJson(['success' => false]);

        $this->assertDatabaseHas('transfers', ['id' => $transfer->id]);
    }

    /** @test */
    public function transfer_statistics_are_returned_correctly()
    {
        // Créer quelques transferts de test
        Transfer::factory()->count(3)->create(['transfer_status' => 'approved']);
        Transfer::factory()->count(2)->create(['transfer_status' => 'pending']);
        Transfer::factory()->count(1)->create(['transfer_status' => 'rejected']);

        $response = $this->actingAs($this->user)
            ->getJson('/api/transfers/statistics');

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $data = $response->json('data');
        $this->assertEquals(6, $data['total']);
        $this->assertEquals(3, $data['approved']);
        $this->assertEquals(2, $data['pending']);
        $this->assertEquals(1, $data['rejected']);
    }

    /** @test */
    public function daily_passport_endpoints_are_accessible()
    {
        $response = $this->actingAs($this->user)
            ->get('/daily-passport');

        $response->assertStatus(200);
        $response->assertViewIs('daily-passport.index');
    }

    /** @test */
    public function club_passport_returns_eligible_players()
    {
        $response = $this->actingAs($this->user)
            ->getJson("/api/clubs/{$this->clubOrigin->id}/players/daily-passport");

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    /** @test */
    public function federation_passport_returns_transfer_summary()
    {
        $response = $this->actingAs($this->user)
            ->getJson("/api/federations/{$this->federation->id}/daily-passport");

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    /** @test */
    public function player_transfers_history_is_accessible()
    {
        $response = $this->actingAs($this->user)
            ->getJson("/api/players/{$this->player->id}/transfers");

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    /** @test */
    public function fifa_webhook_can_process_events()
    {
        $webhookData = [
            'transfer_id' => 'FIFA123',
            'event_type' => 'transfer_approved',
            'timestamp' => now()->toISOString(),
        ];

        $response = $this->postJson('/webhooks/fifa', $webhookData);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    /** @test */
    public function transfer_filters_work_correctly()
    {
        // Créer des transferts avec différents statuts
        Transfer::factory()->create(['transfer_status' => 'approved']);
        Transfer::factory()->create(['transfer_status' => 'pending']);
        Transfer::factory()->create(['transfer_type' => 'loan']);

        // Test filtre par statut
        $response = $this->actingAs($this->user)
            ->getJson('/api/transfers?status=approved');

        $response->assertStatus(200);
        $data = $response->json('data.data');
        $this->assertCount(1, $data);

        // Test filtre par type
        $response = $this->actingAs($this->user)
            ->getJson('/api/transfers?type=loan');

        $response->assertStatus(200);
        $data = $response->json('data.data');
        $this->assertCount(1, $data);
    }

    /** @test */
    public function transfer_validation_respects_business_rules()
    {
        // Test : joueur non éligible
        $ineligiblePlayer = Player::factory()->create([
            'is_transfer_eligible' => false,
        ]);

        $transferData = [
            'player_id' => $ineligiblePlayer->id,
            'club_origin_id' => $this->clubOrigin->id,
            'club_destination_id' => $this->clubDestination->id,
            'transfer_type' => 'permanent',
            'transfer_date' => now()->format('Y-m-d'),
            'contract_start_date' => now()->addDay()->format('Y-m-d'),
            'currency' => 'EUR',
        ];

        $response = $this->actingAs($this->user)
            ->postJson('/api/transfers', $transferData);

        $response->assertStatus(400);
        $response->assertJson(['success' => false]);

        // Test : club ne peut pas effectuer de transferts
        $inactiveClub = Club::factory()->create([
            'can_conduct_transfers' => false,
        ]);

        $transferData['club_origin_id'] = $inactiveClub->id;
        $transferData['player_id'] = $this->player->id;

        $response = $this->actingAs($this->user)
            ->postJson('/api/transfers', $transferData);

        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
    }
} 