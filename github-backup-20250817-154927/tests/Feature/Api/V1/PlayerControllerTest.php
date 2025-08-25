<?php

namespace Tests\Feature\Api\V1;

use App\Models\Player;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PlayerControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($this->user);
    }

    public function test_can_list_players()
    {
        Player::factory()->count(3)->create();
        $response = $this->getJson('/api/v1/players');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'data',
                    'pagination',
                ],
            ]);
    }

    public function test_can_create_player()
    {
        $club = \App\Models\Club::factory()->create();
        $playerData = Player::factory()->make()->toArray();
        $playerData['email'] = 'unique' . uniqid() . '@example.com';
        $playerData['club_id'] = $club->id;
        $playerData['status'] = 'active';
        $playerData['date_of_birth'] = '2000-01-01';
        $playerData['position'] = 'Forward';
        $playerData['nationality'] = 'English';
        $playerData['fifa_connect_id'] = 'FIFA' . uniqid();

        $response = $this->postJson('/api/v1/players', $playerData);
        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'first_name',
                    'last_name',
                ],
            ]);
    }

    public function test_can_show_player()
    {
        $player = Player::factory()->create();
        $response = $this->getJson('/api/v1/players/' . $player->id);
        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'first_name',
                    'last_name',
                ],
            ]);
    }

    public function test_can_update_player()
    {
        $player = Player::factory()->create();
        $response = $this->putJson('/api/v1/players/' . $player->id, [
            'first_name' => 'Updated',
        ]);
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Player updated successfully',
            ]);
    }

    public function test_can_delete_player()
    {
        $player = Player::factory()->create();
        $response = $this->deleteJson('/api/v1/players/' . $player->id);
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Player deleted successfully',
            ]);
    }

    public function test_can_get_player_profile()
    {
        $player = Player::factory()->create();
        $response = $this->getJson('/api/v1/players/' . $player->id . '/profile');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'first_name',
                    'last_name',
                ],
            ]);
    }

    public function test_can_get_player_statistics()
    {
        $player = Player::factory()->create();
        $response = $this->getJson('/api/v1/players/' . $player->id . '/statistics');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'player',
                    'statistics',
                ],
            ]);
    }
} 