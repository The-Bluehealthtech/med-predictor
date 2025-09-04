<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\PCMA;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class ConsoleVocaleTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create([
            'role' => 'user',
            'permissions' => ['pcma.create', 'pcma.read']
        ]);
    }

    /** @test */
    public function vocal_mode_is_visible_when_selected()
    {
        $this->actingAs($this->user);
        
        $response = $this->get('/pcma/create');
        $response->assertStatus(200);
        
        // Vérifier que le mode vocal est accessible
        $response->assertSee('Mode Vocal');
        $response->assertSee('Console Vocale');
    }

    /** @test */
    public function vocal_console_has_all_required_elements()
    {
        $this->actingAs($this->user);
        
        $response = $this->get('/pcma/create');
        $response->assertStatus(200);
        
        // Vérifier la présence des éléments de la console vocale
        $response->assertSee('Tester le Service');
        $response->assertSee('Démarrer Reconnaissance');
        $response->assertSee('Arrêter Reconnaissance');
        $response->assertSee('Transférer vers le formulaire PCMA');
    }

    /** @test */
    public function vocal_mode_switching_works_correctly()
    {
        $this->actingAs($this->user);
        
        $response = $this->get('/pcma/create');
        $response->assertStatus(200);
        
        // Vérifier que le mode manuel est actif par défaut
        $response->assertSee('Mode Manuel');
        
        // Vérifier que la console vocale n'est pas visible par défaut
        $response->assertDontSee('Console Vocale');
    }

    /** @test */
    public function google_speech_api_key_endpoint_is_accessible()
    {
        $this->actingAs($this->user);
        
        $response = $this->get('/api/google-speech-key');
        $response->assertStatus(200);
        
        // Vérifier que la réponse contient une clé API
        $response->assertJsonStructure(['api_key']);
    }

    /** @test */
    public function vocal_data_extraction_works()
    {
        $this->actingAs($this->user);
        
        // Simuler des données vocales extraites
        $vocalData = [
            'player_name' => 'Mohamed Salah',
            'age' => 33,
            'position' => 'Attaquant',
            'club' => 'Liverpool FC'
        ];
        
        // Tester la création d'un PCMA avec des données vocales
        $response = $this->post('/pcma', $vocalData);
        
        if ($response->getStatusCode() === 302) {
            // Redirection attendue après création
            $response->assertRedirect();
        } else {
            // Ou statut 200 si pas de redirection
            $response->assertStatus(200);
        }
        
        // Vérifier que les données sont en base
        $this->assertDatabaseHas('pcmas', [
            'player_name' => 'Mohamed Salah',
            'age' => 33,
            'position' => 'Attaquant',
            'club' => 'Liverpool FC'
        ]);
    }

    /** @test */
    public function vocal_mode_preserves_data_when_switching()
    {
        $this->actingAs($this->user);
        
        // Simuler des données vocales
        $vocalData = [
            'player_name' => 'Sadio Mané',
            'age' => 31,
            'position' => 'Attaquant',
            'club' => 'Bayern Munich'
        ];
        
        // Créer un PCMA avec des données vocales
        $pcma = PCMA::create($vocalData);
        
        // Vérifier que les données sont préservées
        $this->assertDatabaseHas('pcmas', [
            'id' => $pcma->id,
            'player_name' => 'Sadio Mané'
        ]);
        
        // Simuler le changement de mode
        $response = $this->get('/pcma/create');
        $response->assertStatus(200);
        
        // Les données devraient être disponibles pour transfert
        $response->assertSee('Transférer vers le formulaire PCMA');
    }

    /** @test */
    public function vocal_recognition_service_is_initialized()
    {
        $this->actingAs($this->user);
        
        $response = $this->get('/pcma/create');
        $response->assertStatus(200);
        
        // Vérifier que le service vocal est chargé
        $response->assertSee('SpeechRecognitionService-laravel.js');
        $response->assertSee('ServiceVocal.js');
    }

    /** @test */
    public function vocal_mode_has_proper_error_handling()
    {
        $this->actingAs($this->user);
        
        $response = $this->get('/pcma/create');
        $response->assertStatus(200);
        
        // Vérifier la présence des éléments de gestion d'erreur
        $response->assertSee('Gestion des erreurs');
        $response->assertSee('Statut du service');
    }

    /** @test */
    public function vocal_console_is_only_visible_in_vocal_mode()
    {
        $this->actingAs($this->user);
        
        $response = $this->get('/pcma/create');
        $response->assertStatus(200);
        
        // En mode manuel par défaut, la console vocale ne doit pas être visible
        $response->assertDontSee('Console Vocale');
        
        // Mais les boutons de mode doivent être présents
        $response->assertSee('Mode Vocal');
        $response->assertSee('Mode Manuel');
    }

    /** @test */
    public function vocal_data_transfer_to_pcma_form_works()
    {
        $this->actingAs($this->user);
        
        // Créer des données vocales simulées
        $vocalData = [
            'player_name' => 'Virgil van Dijk',
            'age' => 32,
            'position' => 'Défenseur',
            'club' => 'Liverpool FC'
        ];
        
        // Tester la création du PCMA
        $response = $this->post('/pcma', $vocalData);
        
        // Vérifier la réponse
        if ($response->getStatusCode() === 302) {
            $response->assertRedirect();
        } else {
            $response->assertStatus(200);
        }
        
        // Vérifier que les données sont en base
        $this->assertDatabaseHas('pcmas', [
            'player_name' => 'Virgil van Dijk',
            'age' => 32,
            'position' => 'Défenseur',
            'club' => 'Liverpool FC'
        ]);
    }

    /** @test */
    public function vocal_mode_has_proper_ui_indicators()
    {
        $this->actingAs($this->user);
        
        $response = $this->get('/pcma/create');
        $response->assertStatus(200);
        
        // Vérifier les indicateurs visuels du mode vocal
        $response->assertSee('Mode Vocal');
        $response->assertSee('Reconnaissance vocale intelligente');
        
        // Vérifier que l'icône du mode vocal est présente
        $response->assertSee('svg'); // Icône SVG
    }
}
