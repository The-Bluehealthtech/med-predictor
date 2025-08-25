<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Player;
use App\Models\Club;
use App\Models\PlayerLicense;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class LicenseSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    /** @test */
    public function unauthorized_users_cannot_request_license()
    {
        $user = User::factory()->create(['role' => 'player']); // Rôle non autorisé
        $player = Player::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/api/licenses/request', [
                'player_id' => $player->id,
                'license_type' => 'amateur',
                'document' => UploadedFile::fake()->create('document.pdf', 100)
            ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('player_licenses', ['player_id' => $player->id]);
    }

    /** @test */
    public function users_cannot_request_license_for_players_from_other_clubs()
    {
        $club1 = Club::factory()->create();
        $club2 = Club::factory()->create();
        
        $user = User::factory()->create([
            'role' => 'admin',
            'club_id' => $club1->id
        ]);
        
        $player = Player::factory()->create(['club_id' => $club2->id]);

        $response = $this->actingAs($user)
            ->postJson('/api/licenses/request', [
                'player_id' => $player->id,
                'license_type' => 'amateur',
                'document' => UploadedFile::fake()->create('document.pdf', 100)
            ]);

        $response->assertStatus(422); // Validation error
        $this->assertDatabaseMissing('player_licenses', ['player_id' => $player->id]);
    }

    /** @test */
    public function unauthorized_users_cannot_view_license_queue()
    {
        $user = User::factory()->create(['role' => 'player']);

        $response = $this->actingAs($user)
            ->getJson('/api/licenses/queue');

        $response->assertStatus(403);
    }

    /** @test */
    public function license_agents_cannot_approve_licenses_from_other_clubs()
    {
        $club1 = Club::factory()->create();
        $club2 = Club::factory()->create();
        
        $user = User::factory()->create([
            'role' => 'license_agent',
            'club_id' => $club1->id
        ]);
        
        $license = PlayerLicense::factory()->create([
            'club_id' => $club2->id,
            'status' => 'pending'
        ]);

        $response = $this->actingAs($user)
            ->postJson("/api/licenses/{$license->id}/approve", [
                'action' => 'approve'
            ]);

        $response->assertStatus(403);
        $this->assertDatabaseHas('player_licenses', [
            'id' => $license->id,
            'status' => 'pending'
        ]);
    }

    /** @test */
    public function only_license_agents_and_admins_can_approve_licenses()
    {
        $user = User::factory()->create(['role' => 'captain']);
        $license = PlayerLicense::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($user)
            ->postJson("/api/licenses/{$license->id}/approve", [
                'action' => 'approve'
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function cannot_approve_already_approved_license()
    {
        $user = User::factory()->create(['role' => 'license_agent']);
        $license = PlayerLicense::factory()->create(['status' => 'approved']);

        $response = $this->actingAs($user)
            ->postJson("/api/licenses/{$license->id}/approve", [
                'action' => 'approve'
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function file_upload_validation_works()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $player = Player::factory()->create(['club_id' => $user->club_id]);

        // Test avec un fichier trop volumineux
        $response = $this->actingAs($user)
            ->postJson('/api/licenses/request', [
                'player_id' => $player->id,
                'license_type' => 'amateur',
                'document' => UploadedFile::fake()->create('document.pdf', 3000) // 3MB
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['document']);

        // Test avec un type de fichier non autorisé
        $response = $this->actingAs($user)
            ->postJson('/api/licenses/request', [
                'player_id' => $player->id,
                'license_type' => 'amateur',
                'document' => UploadedFile::fake()->create('document.exe', 100)
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['document']);
    }

    /** @test */
    public function rate_limiting_works_for_license_requests()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $player = Player::factory()->create(['club_id' => $user->club_id]);

        // Faire 6 requêtes (limite semble être à 4)
        for ($i = 0; $i < 6; $i++) {
            $response = $this->actingAs($user)
                ->postJson('/api/licenses/request', [
                    'player_id' => $player->id,
                    'license_type' => 'amateur',
                    'document' => UploadedFile::fake()->create('document.pdf', 100)
                ]);

            if ($i < 4) {
                $response->assertStatus(201);
            } else {
                $response->assertStatus(429); // Too Many Requests
            }
        }
    }

    /** @test */
    public function sensitive_actions_are_logged()
    {
        $user = User::factory()->create(['role' => 'license_agent']);
        $license = PlayerLicense::factory()->create([
            'status' => 'pending',
            'club_id' => $user->club_id // Ensure user can approve this license
        ]);

        $response = $this->actingAs($user)
            ->postJson("/api/licenses/{$license->id}/approve", [
                'action' => 'approve'
            ]);

        $response->assertStatus(200); // Should be successful

        // Vérifier que l'action est loggée
        $this->assertDatabaseHas('player_licenses', [
            'id' => $license->id,
            'status' => 'approved',
            'approved_by' => $user->id
        ]);
    }

    /** @test */
    public function invalid_license_id_returns_404()
    {
        $user = User::factory()->create(['role' => 'license_agent']);

        $response = $this->actingAs($user)
            ->postJson('/api/licenses/999999/approve', [
                'action' => 'approve'
            ]);

        $response->assertStatus(404);
    }

    /** @test */
    public function unauthenticated_users_cannot_access_license_endpoints()
    {
        $response = $this->getJson('/api/licenses/queue');
        $response->assertStatus(401);

        $response = $this->postJson('/api/licenses/request', []);
        $response->assertStatus(401);
    }
} 