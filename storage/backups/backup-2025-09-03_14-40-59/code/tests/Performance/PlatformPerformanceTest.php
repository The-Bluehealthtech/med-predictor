<?php

namespace Tests\Performance;

use Tests\TestCase;
use App\Models\User;
use App\Models\PCMA;
use App\Models\Player;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;

class PlatformPerformanceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $startTime;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create([
            'role' => 'user',
            'permissions' => ['pcma.create', 'pcma.read', 'players.read']
        ]);
    }

    protected function startTimer()
    {
        $this->startTime = microtime(true);
    }

    protected function endTimer()
    {
        $endTime = microtime(true);
        return ($endTime - $this->startTime) * 1000; // en millisecondes
    }

    /** @test */
    public function dashboard_loads_within_acceptable_time()
    {
        $this->startTimer();
        
        $this->actingAs($this->user);
        $response = $this->get('/dashboard');
        
        $executionTime = $this->endTimer();
        
        $response->assertStatus(200);
        
        // Le dashboard doit se charger en moins de 2 secondes
        $this->assertLessThan(2000, $executionTime, 
            "Le dashboard met trop de temps à charger : {$executionTime}ms");
    }

    /** @test */
    public function pcma_create_page_loads_quickly()
    {
        $this->startTimer();
        
        $this->actingAs($this->user);
        $response = $this->get('/pcma/create');
        
        $executionTime = $this->endTimer();
        
        $response->assertStatus(200);
        
        // La page de création PCMA doit se charger en moins de 1.5 secondes
        $this->assertLessThan(1500, $executionTime, 
            "La page PCMA met trop de temps à charger : {$executionTime}ms");
    }

    /** @test */
    public function players_list_loads_with_large_dataset()
    {
        // Créer 100 joueurs de test
        Player::factory()->count(100)->create();
        
        $this->startTimer();
        
        $this->actingAs($this->user);
        $response = $this->get('/players');
        
        $executionTime = $this->endTimer();
        
        $response->assertStatus(200);
        
        // La liste des joueurs doit se charger en moins de 3 secondes même avec 100 joueurs
        $this->assertLessThan(3000, $executionTime, 
            "La liste des joueurs met trop de temps à charger : {$executionTime}ms");
    }

    /** @test */
    public function player_search_is_fast_with_large_dataset()
    {
        // Créer 1000 joueurs de test
        Player::factory()->count(1000)->create();
        
        $this->startTimer();
        
        $this->actingAs($this->user);
        $response = $this->get('/players?search=test');
        
        $executionTime = $this->endTimer();
        
        $response->assertStatus(200);
        
        // La recherche doit être rapide même avec 1000 joueurs
        $this->assertLessThan(2000, $executionTime, 
            "La recherche de joueurs met trop de temps : {$executionTime}ms");
    }

    /** @test */
    public function pcma_creation_is_fast()
    {
        $this->actingAs($this->user);
        
        $pcmaData = [
            'player_name' => 'Test Player',
            'age' => 25,
            'position' => 'Test Position',
            'club' => 'Test Club',
            'medical_notes' => 'Test notes',
            'fitness_level' => 'Good'
        ];
        
        $this->startTimer();
        
        $response = $this->post('/pcma', $pcmaData);
        
        $executionTime = $this->endTimer();
        
        $response->assertRedirect();
        
        // La création d'un PCMA doit être rapide
        $this->assertLessThan(1000, $executionTime, 
            "La création du PCMA met trop de temps : {$executionTime}ms");
    }

    /** @test */
    public function database_queries_are_optimized()
    {
        // Créer des données de test
        Player::factory()->count(50)->create();
        PCMA::factory()->count(100)->create();
        
        $this->actingAs($this->user);
        
        // Compter le nombre de requêtes SQL
        DB::enableQueryLog();
        
        $this->get('/dashboard');
        
        $queryCount = count(DB::getQueryLog());
        
        // Le dashboard ne doit pas exécuter plus de 10 requêtes SQL
        $this->assertLessThan(10, $queryCount, 
            "Trop de requêtes SQL exécutées : {$queryCount}");
    }

    /** @test */
    public function api_endpoints_respond_quickly()
    {
        $this->actingAs($this->user);
        
        // Test de l'endpoint Google Speech API
        $this->startTimer();
        
        $response = $this->get('/api/google-speech-key');
        
        $executionTime = $this->endTimer();
        
        $response->assertStatus(200);
        
        // L'API doit répondre en moins de 500ms
        $this->assertLessThan(500, $executionTime, 
            "L'API met trop de temps à répondre : {$executionTime}ms");
    }

    /** @test */
    public function concurrent_users_are_handled_efficiently()
    {
        // Simuler 10 utilisateurs simultanés
        $users = User::factory()->count(10)->create();
        
        $startTime = microtime(true);
        
        foreach ($users as $user) {
            $this->actingAs($user);
            $response = $this->get('/dashboard');
            $response->assertStatus(200);
        }
        
        $totalTime = (microtime(true) - $startTime) * 1000;
        
        // 10 utilisateurs doivent pouvoir accéder au dashboard en moins de 5 secondes
        $this->assertLessThan(5000, $totalTime, 
            "Trop de temps pour gérer 10 utilisateurs simultanés : {$totalTime}ms");
    }

    /** @test */
    public function memory_usage_is_reasonable()
    {
        $initialMemory = memory_get_usage();
        
        $this->actingAs($this->user);
        
        // Charger plusieurs pages
        $this->get('/dashboard');
        $this->get('/pcma/create');
        $this->get('/players');
        
        $finalMemory = memory_get_usage();
        $memoryIncrease = $finalMemory - $initialMemory;
        
        // L'augmentation de mémoire ne doit pas dépasser 50MB
        $this->assertLessThan(50 * 1024 * 1024, $memoryIncrease, 
            "Utilisation mémoire excessive : " . round($memoryIncrease / 1024 / 1024, 2) . "MB");
    }

    /** @test */
    public function page_rendering_is_fast()
    {
        $this->actingAs($this->user);
        
        $this->startTimer();
        
        $response = $this->get('/pcma/create');
        
        $executionTime = $this->endTimer();
        
        $response->assertStatus(200);
        
        // Le rendu de la page doit être rapide
        $this->assertLessThan(1000, $executionTime, 
            "Le rendu de la page met trop de temps : {$executionTime}ms");
    }

    /** @test */
    public function form_submission_is_responsive()
    {
        $this->actingAs($this->user);
        
        $pcmaData = [
            'player_name' => 'Performance Test Player',
            'age' => 30,
            'position' => 'Midfielder',
            'club' => 'Test Club',
            'medical_notes' => 'Performance test notes',
            'fitness_level' => 'Excellent'
        ];
        
        $this->startTimer();
        
        $response = $this->post('/pcma', $pcmaData);
        
        $executionTime = $this->endTimer();
        
        $response->assertRedirect();
        
        // La soumission du formulaire doit être rapide
        $this->assertLessThan(1500, $executionTime, 
            "La soumission du formulaire met trop de temps : {$executionTime}ms");
    }

    /** @test */
    public function session_management_is_efficient()
    {
        $this->actingAs($this->user);
        
        $this->startTimer();
        
        // Simuler plusieurs actions utilisateur
        $this->get('/dashboard');
        $this->get('/pcma/create');
        $this->get('/players');
        $this->get('/medical');
        
        $executionTime = $this->endTimer();
        
        // La gestion des sessions doit être efficace
        $this->assertLessThan(3000, $executionTime, 
            "La gestion des sessions met trop de temps : {$executionTime}ms");
    }
}
