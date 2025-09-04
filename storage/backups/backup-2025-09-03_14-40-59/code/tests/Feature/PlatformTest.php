<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\PCMA;
use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class PlatformTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Créer un utilisateur normal
        $this->user = User::factory()->create([
            'role' => 'user',
            'permissions' => ['pcma.create', 'pcma.read', 'players.read']
        ]);

        // Créer un utilisateur administrateur
        $this->adminUser = User::factory()->create([
            'role' => 'admin',
            'permissions' => ['*']
        ]);
    }

    /** @test */
    public function user_can_access_login_page()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Connexion');
    }

    /** @test */
    public function user_can_login_with_valid_credentials()
    {
        $response = $this->post('/login', [
            'email' => $this->user->email,
            'password' => 'password'
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    /** @test */
    public function user_cannot_login_with_invalid_credentials()
    {
        $response = $this->post('/login', [
            'email' => $this->user->email,
            'password' => 'wrong-password'
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    /** @test */
    public function authenticated_user_can_access_dashboard()
    {
        $this->actingAs($this->user);
        
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard');
    }

    /** @test */
    public function unauthenticated_user_cannot_access_dashboard()
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function user_can_access_pcma_create_page()
    {
        $this->actingAs($this->user);
        
        $response = $this->get('/pcma/create');
        $response->assertStatus(200);
        $response->assertSee('Mode Manuel');
        $response->assertSee('Mode Vocal');
    }

    /** @test */
    public function user_can_create_pcma_manually()
    {
        $this->actingAs($this->user);
        
        $pcmaData = [
            'player_name' => 'Mohamed Salah',
            'age' => 33,
            'position' => 'Attaquant',
            'club' => 'Liverpool FC',
            'medical_notes' => 'Aucun problème médical',
            'fitness_level' => 'Excellent'
        ];

        $response = $this->post('/pcma', $pcmaData);
        $response->assertRedirect();
        
        $this->assertDatabaseHas('pcmas', [
            'player_name' => 'Mohamed Salah',
            'age' => 33
        ]);
    }

    /** @test */
    public function user_can_access_players_list()
    {
        $this->actingAs($this->user);
        
        $response = $this->get('/players');
        $response->assertStatus(200);
        $response->assertSee('Joueurs');
    }

    /** @test */
    public function user_can_search_players()
    {
        $this->actingAs($this->user);
        
        // Créer quelques joueurs de test
        Player::factory()->create(['name' => 'Mohamed Salah']);
        Player::factory()->create(['name' => 'Sadio Mané']);
        
        $response = $this->get('/players?search=Salah');
        $response->assertStatus(200);
        $response->assertSee('Mohamed Salah');
        $response->assertDontSee('Sadio Mané');
    }

    /** @test */
    public function user_can_view_player_profile()
    {
        $this->actingAs($this->user);
        
        $player = Player::factory()->create();
        
        $response = $this->get("/players/{$player->id}");
        $response->assertStatus(200);
        $response->assertSee($player->name);
    }

    /** @test */
    public function user_can_access_medical_records()
    {
        $this->actingAs($this->user);
        
        $response = $this->get('/medical');
        $response->assertStatus(200);
        $response->assertSee('Dossiers médicaux');
    }

    /** @test */
    public function user_can_access_licenses()
    {
        $this->actingAs($this->user);
        
        $response = $this->get('/licenses');
        $response->assertStatus(200);
        $response->assertSee('Licences');
    }

    /** @test */
    public function admin_can_access_administration()
    {
        $this->actingAs($this->adminUser);
        
        $response = $this->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Administration');
    }

    /** @test */
    public function regular_user_cannot_access_administration()
    {
        $this->actingAs($this->user);
        
        $response = $this->get('/admin');
        $response->assertStatus(403);
    }

    /** @test */
    public function user_can_logout()
    {
        $this->actingAs($this->user);
        
        $response = $this->post('/logout');
        $response->assertRedirect('/');
        $this->assertGuest();
    }

    /** @test */
    public function platform_is_responsive()
    {
        $this->actingAs($this->user);
        
        // Tester différentes tailles d'écran
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        
        // Vérifier la présence des classes CSS responsive
        $response->assertSee('container');
        $response->assertSee('responsive');
    }

    /** @test */
    public function api_endpoints_are_protected()
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
    public function csrf_protection_is_active()
    {
        $this->actingAs($this->user);
        
        $response = $this->post('/pcma', [
            'player_name' => 'Test Player',
            'age' => 25,
            'position' => 'Test',
            'club' => 'Test Club'
        ]);
        
        // Si CSRF est actif, la requête devrait échouer sans token
        $response->assertStatus(419); // Token CSRF manquant
    }

    /** @test */
    public function platform_performance_is_acceptable()
    {
        $this->actingAs($this->user);
        
        $startTime = microtime(true);
        
        $response = $this->get('/dashboard');
        
        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000; // en millisecondes
        
        $response->assertStatus(200);
        
        // Le temps de réponse doit être inférieur à 3 secondes
        $this->assertLessThan(3000, $executionTime, 
            "Le temps de réponse ({$executionTime}ms) dépasse 3 secondes");
    }
}
