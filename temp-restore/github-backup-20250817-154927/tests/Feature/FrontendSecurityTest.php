<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Club;
use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FrontendSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    /** @test */
    public function csrf_token_is_present_in_layout()
    {
        $user = User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($user)
            ->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('name="csrf-token"');
        $response->assertSee('content="' . csrf_token() . '"');
    }

    /** @test */
    public function user_metadata_is_present_in_layout()
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'club_id' => Club::factory()->create()->id
        ]);
        
        $response = $this->actingAs($user)
            ->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('name="user-role"');
        $response->assertSee('name="user-id"');
        $response->assertSee('name="user-club-id"');
        $response->assertSee('content="admin"');
        $response->assertSee('content="' . $user->id . '"');
    }

    /** @test */
    public function security_headers_are_present()
    {
        $user = User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($user)
            ->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('X-Content-Type-Options');
        $response->assertSee('X-Frame-Options');
        $response->assertSee('X-XSS-Protection');
        $response->assertSee('Referrer-Policy');
    }

    /** @test */
    public function security_script_is_loaded()
    {
        $user = User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($user)
            ->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('security-init.js');
    }

    /** @test */
    public function unauthorized_users_cannot_access_protected_pages()
    {
        $user = User::factory()->create(['role' => 'player']); // Rôle non autorisé
        
        // Essayer d'accéder à une page protégée
        $response = $this->actingAs($user)
            ->get('/licenses/queue');

        // Devrait être redirigé ou recevoir une erreur 403
        $response->assertStatus(403);
    }

    /** @test */
    public function authorized_users_can_access_protected_pages()
    {
        $user = User::factory()->create(['role' => 'license_agent']);
        
        $response = $this->actingAs($user)
            ->get('/licenses/queue');

        $response->assertStatus(200);
    }

    /** @test */
    public function form_validation_works_on_frontend()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $player = Player::factory()->create(['club_id' => $user->club_id]);
        
        // Test avec des données invalides
        $response = $this->actingAs($user)
            ->postJson('/api/licenses/request', [
                'player_id' => '', // Champ requis vide
                'license_type' => 'invalid_type', // Type invalide
                'document' => UploadedFile::fake()->create('document.exe', 100) // Type de fichier invalide
            ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['player_id', 'license_type', 'document']);
    }

    /** @test */
    public function file_upload_validation_works_on_frontend()
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
    }

    /** @test */
    public function xss_protection_works()
    {
        $user = User::factory()->create(['role' => 'admin']);
        
        // Test avec du contenu malveillant
        $maliciousContent = '<script>alert("xss")</script>';
        
        $response = $this->actingAs($user)
            ->postJson('/api/licenses/request', [
                'player_id' => 1,
                'license_type' => 'amateur',
                'notes' => $maliciousContent
            ]);

        // Le contenu malveillant devrait être échappé ou rejeté
        $response->assertStatus(422); // Ou 201 si accepté mais échappé
    }

    /** @test */
    public function rate_limiting_works_on_frontend()
    {
        $user = User::factory()->create(['role' => 'admin']);
        $player = Player::factory()->create(['club_id' => $user->club_id]);
        
        // Faire plusieurs requêtes rapides
        for ($i = 0; $i < 15; $i++) {
            $response = $this->actingAs($user)
                ->postJson('/api/licenses/request', [
                    'player_id' => $player->id,
                    'license_type' => 'amateur',
                    'document' => UploadedFile::fake()->create('document.pdf', 100)
                ]);
            
            if ($i >= 10) {
                // Après 10 requêtes, devrait être limité
                $response->assertStatus(429);
            }
        }
    }

    /** @test */
    public function session_security_works()
    {
        $user = User::factory()->create(['role' => 'admin']);
        
        // Se connecter
        $this->actingAs($user);
        
        // Vérifier que la session est sécurisée
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        
        // Simuler une session expirée
        auth()->logout();
        
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function sensitive_data_is_not_exposed()
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'password' => 'secret123'
        ]);
        
        $response = $this->actingAs($user)
            ->get('/dashboard');

        $response->assertStatus(200);
        $response->assertDontSee('secret123'); // Le mot de passe ne doit pas être exposé
    }

    /** @test */
    public function form_csrf_protection_works()
    {
        $user = User::factory()->create(['role' => 'admin']);
        
        // Test sans token CSRF
        $response = $this->actingAs($user)
            ->postJson('/api/licenses/request', [
                'player_id' => 1,
                'license_type' => 'amateur'
            ]);

        // Devrait échouer sans token CSRF
        $response->assertStatus(419); // CSRF token mismatch
    }

    /** @test */
    public function secure_form_attributes_are_present()
    {
        $user = User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($user)
            ->get('/licenses/request');

        $response->assertStatus(200);
        // Check for basic form security attributes that should be in the HTML
        $response->assertSee('method="POST"');
        $response->assertSee('enctype="multipart/form-data"');
    }

    /** @test */
    public function role_based_ui_elements_are_hidden()
    {
        $user = User::factory()->create(['role' => 'player']); // Rôle limité
        
        $response = $this->actingAs($user)
            ->get('/dashboard');

        $response->assertStatus(200);
        
        // Check that admin-specific content is not visible to players
        $response->assertDontSee('Admin Panel');
        $response->assertDontSee('User Management');
    }

    /** @test */
    public function secure_notifications_are_configured()
    {
        $user = User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($user)
            ->get('/dashboard');

        $response->assertStatus(200);
        // Check for basic notification container that should be in the layout
        $response->assertSee('notifications');
    }

    /** @test */
    public function error_handling_works_properly()
    {
        $user = User::factory()->create(['role' => 'admin']);
        
        // Test avec une route inexistante
        $response = $this->actingAs($user)
            ->get('/api/nonexistent-endpoint');

        $response->assertStatus(404);
    }

    /** @test */
    public function secure_headers_are_sent()
    {
        $user = User::factory()->create(['role' => 'admin']);
        
        $response = $this->actingAs($user)
            ->get('/dashboard');

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
    }
} 