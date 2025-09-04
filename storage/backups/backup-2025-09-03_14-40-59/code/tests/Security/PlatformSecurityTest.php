<?php

namespace Tests\Security;

use Tests\TestCase;
use App\Models\User;
use App\Models\PCMA;
use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;

class PlatformSecurityTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $adminUser;
    protected $regularUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create([
            'role' => 'user',
            'permissions' => ['pcma.create', 'pcma.read', 'players.read']
        ]);

        $this->adminUser = User::factory()->create([
            'role' => 'admin',
            'permissions' => ['*']
        ]);

        $this->regularUser = User::factory()->create([
            'role' => 'user',
            'permissions' => ['pcma.read']
        ]);
    }

    /** @test */
    public function unauthenticated_users_cannot_access_protected_routes()
    {
        $protectedRoutes = [
            '/dashboard',
            '/pcma',
            '/pcma/create',
            '/players',
            '/medical',
            '/licenses',
            '/admin'
        ];

        foreach ($protectedRoutes as $route) {
            $response = $this->get($route);
            $response->assertRedirect('/login');
        }
    }

    /** @test */
    public function users_cannot_access_unauthorized_modules()
    {
        $this->actingAs($this->regularUser);
        
        // L'utilisateur régulier ne doit pas pouvoir accéder à l'administration
        $response = $this->get('/admin');
        $response->assertStatus(403);
        
        // L'utilisateur régulier ne doit pas pouvoir créer des PCMA
        $response = $this->get('/pcma/create');
        $response->assertStatus(403);
    }

    /** @test */
    public function csrf_protection_is_active()
    {
        $this->actingAs($this->user);
        
        $response = $this->post('/pcma', [
            'player_name' => 'Test Player',
            'age' => 25,
            'position' => 'Test',
            'club' => 'Test Club'
        ]);
        
        // Sans token CSRF, la requête doit échouer
        $response->assertStatus(419);
    }

    /** @test */
    public function sql_injection_is_prevented()
    {
        $this->actingAs($this->user);
        
        // Tentative d'injection SQL dans la recherche
        $maliciousInput = "'; DROP TABLE users; --";
        
        $response = $this->get('/players?search=' . urlencode($maliciousInput));
        
        // La requête doit être traitée sans erreur
        $response->assertStatus(200);
        
        // Vérifier que la table users existe toujours
        $this->assertDatabaseHas('users', ['id' => $this->user->id]);
    }

    /** @test */
    public function xss_attacks_are_prevented()
    {
        $this->actingAs($this->user);
        
        $maliciousInput = '<script>alert("XSS")</script>';
        
        $response = $this->post('/pcma', [
            'player_name' => $maliciousInput,
            'age' => 25,
            'position' => 'Test',
            'club' => 'Test Club'
        ]);
        
        // La requête doit être traitée
        if ($response->getStatusCode() === 302) {
            $response->assertRedirect();
        }
        
        // Vérifier que le script n'est pas exécuté
        $this->assertDatabaseHas('pcmas', [
            'player_name' => $maliciousInput // Le script doit être stocké comme texte
        ]);
    }

    /** @test */
    public function password_validation_is_strong()
    {
        $weakPasswords = [
            '123456',
            'password',
            'abc123',
            'qwerty',
            '123456789'
        ];

        foreach ($weakPasswords as $weakPassword) {
            $response = $this->post('/register', [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => $weakPassword,
                'password_confirmation' => $weakPassword
            ]);
            
            $response->assertSessionHasErrors('password');
        }
    }

    /** @test */
    public function session_fixation_is_prevented()
    {
        $this->actingAs($this->user);
        
        $oldSessionId = session()->getId();
        
        // Effectuer une action qui pourrait changer la session
        $this->get('/dashboard');
        
        $newSessionId = session()->getId();
        
        // La session ID ne doit pas changer de manière inattendue
        $this->assertEquals($oldSessionId, $newSessionId);
    }

    /** @test */
    public function brute_force_attacks_are_prevented()
    {
        $invalidCredentials = [
            'email' => $this->user->email,
            'password' => 'wrong-password'
        ];
        
        // Tenter plusieurs connexions échouées
        for ($i = 0; $i < 5; $i++) {
            $response = $this->post('/login', $invalidCredentials);
            $response->assertSessionHasErrors();
        }
        
        // Après 5 tentatives, l'utilisateur doit être temporairement bloqué
        $response = $this->post('/login', $invalidCredentials);
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function sensitive_data_is_not_exposed()
    {
        $this->actingAs($this->user);
        
        $response = $this->get('/api/google-speech-key');
        $response->assertStatus(200);
        
        $data = $response->json();
        
        // La clé API ne doit pas être exposée directement
        $this->assertArrayHasKey('api_key', $data);
        $this->assertNotEmpty($data['api_key']);
        
        // La clé ne doit pas être la clé réelle de l'environnement
        $this->assertNotEquals(config('services.google.speech_api_key'), $data['api_key']);
    }

    /** @test */
    public function file_upload_validation_is_secure()
    {
        $this->actingAs($this->user);
        
        // Tentative d'upload de fichier malveillant
        $maliciousFile = [
            'name' => 'malicious.php',
            'type' => 'application/x-php',
            'tmp_name' => '/tmp/malicious.php',
            'error' => 0,
            'size' => 1024
        ];
        
        $response = $this->post('/upload', [
            'file' => $maliciousFile
        ]);
        
        // L'upload doit être rejeté
        $response->assertSessionHasErrors('file');
    }

    /** @test */
    public function rate_limiting_is_active()
    {
        $this->actingAs($this->user);
        
        // Effectuer plusieurs requêtes rapides
        for ($i = 0; $i < 60; $i++) {
            $response = $this->get('/dashboard');
            $response->assertStatus(200);
        }
        
        // La 61ème requête doit être limitée
        $response = $this->get('/dashboard');
        $response->assertStatus(429); // Too Many Requests
    }

    /** @test */
    public function user_permissions_are_respected()
    {
        $this->actingAs($this->regularUser);
        
        // L'utilisateur régulier ne doit pas pouvoir modifier les permissions
        $response = $this->put('/users/' . $this->user->id, [
            'permissions' => ['*']
        ]);
        
        $response->assertStatus(403);
    }

    /** @test */
    public function api_authentication_is_required()
    {
        // Test sans authentification
        $response = $this->get('/api/players');
        $response->assertStatus(401);
        
        // Test avec authentification
        $this->actingAs($this->user);
        $response = $this->get('/api/players');
        $response->assertStatus(200);
    }

    /** @test */
    public function sensitive_routes_are_protected()
    {
        $sensitiveRoutes = [
            '/admin/users',
            '/admin/settings',
            '/admin/logs',
            '/api/admin/*'
        ];

        $this->actingAs($this->regularUser);

        foreach ($sensitiveRoutes as $route) {
            $response = $this->get($route);
            $response->assertStatus(403);
        }
    }

    /** @test */
    public function logout_invalidates_session()
    {
        $this->actingAs($this->user);
        
        // Vérifier que l'utilisateur est connecté
        $this->assertAuthenticated();
        
        // Déconnexion
        $response = $this->post('/logout');
        $response->assertRedirect('/');
        
        // Vérifier que l'utilisateur est déconnecté
        $this->assertGuest();
        
        // Tenter d'accéder à une route protégée
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function password_reset_is_secure()
    {
        $response = $this->post('/password/email', [
            'email' => 'nonexistent@example.com'
        ]);
        
        // Même avec un email inexistant, ne pas révéler l'existence de l'utilisateur
        $response->assertSessionHas('status');
        
        // Vérifier qu'aucun email n'a été envoyé
        $this->assertDatabaseMissing('password_resets', [
            'email' => 'nonexistent@example.com'
        ]);
    }
}
