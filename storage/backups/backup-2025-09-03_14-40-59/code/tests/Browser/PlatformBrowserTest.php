<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use App\Models\User;
use App\Models\PCMA;
use App\Models\Player;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PlatformBrowserTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $user;
    protected $adminUser;

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
    }

    /** @test */
    public function user_can_login_and_access_dashboard()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                    ->type('email', $this->user->email)
                    ->type('password', 'password')
                    ->press('Se connecter')
                    ->waitForLocation('/dashboard')
                    ->assertPathIs('/dashboard')
                    ->assertSee('Dashboard');
        });
    }

    /** @test */
    public function user_can_navigate_between_modules()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/dashboard')
                    ->assertSee('Dashboard')
                    
                    // Navigation vers le module PCMA
                    ->clickLink('PCMA')
                    ->waitForLocation('/pcma')
                    ->assertPathIs('/pcma')
                    
                    // Navigation vers le module joueurs
                    ->clickLink('Joueurs')
                    ->waitForLocation('/players')
                    ->assertPathIs('/players')
                    
                    // Navigation vers le module médical
                    ->clickLink('Médical')
                    ->waitForLocation('/medical')
                    ->assertPathIs('/medical')
                    
                    // Retour au dashboard
                    ->clickLink('Dashboard')
                    ->waitForLocation('/dashboard')
                    ->assertPathIs('/dashboard');
        });
    }

    /** @test */
    public function user_can_create_pcma_in_manual_mode()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/pcma/create')
                    ->assertSee('Mode Manuel')
                    ->assertSee('Mode Vocal')
                    
                    // Remplir le formulaire manuel
                    ->type('player_name', 'Mohamed Salah')
                    ->type('age', '33')
                    ->type('position', 'Attaquant')
                    ->type('club', 'Liverpool FC')
                    ->type('medical_notes', 'Aucun problème médical')
                    ->select('fitness_level', 'Excellent')
                    
                    // Soumettre le formulaire
                    ->press('Créer PCMA')
                    ->waitForLocation('/pcma')
                    ->assertPathIs('/pcma')
                    ->assertSee('Mohamed Salah');
        });
    }

    /** @test */
    public function user_can_switch_to_vocal_mode()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/pcma/create')
                    
                    // Vérifier que le mode manuel est actif par défaut
                    ->assertSee('Mode Manuel')
                    ->assertDontSee('Console Vocale')
                    
                    // Cliquer sur le mode vocal
                    ->click('#mode-vocal')
                    ->waitForText('Console Vocale')
                    ->assertSee('Console Vocale')
                    ->assertSee('Tester le Service')
                    ->assertSee('Démarrer Reconnaissance');
        });
    }

    /** @test */
    public function vocal_console_has_all_functional_elements()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/pcma/create')
                    ->click('#mode-vocal')
                    ->waitForText('Console Vocale')
                    
                    // Vérifier tous les éléments de la console vocale
                    ->assertSee('Tester le Service')
                    ->assertSee('Démarrer Reconnaissance')
                    ->assertSee('Arrêter Reconnaissance')
                    ->assertSee('Transférer vers le formulaire PCMA')
                    ->assertSee('Statut du service')
                    ->assertSee('Gestion des erreurs');
        });
    }

    /** @test */
    public function user_can_search_and_filter_players()
    {
        $this->browse(function (Browser $browser) {
            // Créer des joueurs de test
            Player::factory()->create(['name' => 'Mohamed Salah', 'club' => 'Liverpool FC']);
            Player::factory()->create(['name' => 'Sadio Mané', 'club' => 'Bayern Munich']);
            
            $browser->loginAs($this->user)
                    ->visit('/players')
                    ->assertSee('Joueurs')
                    
                    // Recherche par nom
                    ->type('search', 'Salah')
                    ->press('Rechercher')
                    ->waitForText('Mohamed Salah')
                    ->assertSee('Mohamed Salah')
                    ->assertDontSee('Sadio Mané')
                    
                    // Recherche par club
                    ->clear('search')
                    ->type('search', 'Liverpool')
                    ->press('Rechercher')
                    ->waitForText('Mohamed Salah')
                    ->assertSee('Mohamed Salah');
        });
    }

    /** @test */
    public function user_can_view_player_profile()
    {
        $this->browse(function (Browser $browser) {
            $player = Player::factory()->create([
                'name' => 'Virgil van Dijk',
                'age' => 32,
                'position' => 'Défenseur',
                'club' => 'Liverpool FC'
            ]);
            
            $browser->loginAs($this->user)
                    ->visit("/players/{$player->id}")
                    ->assertSee('Virgil van Dijk')
                    ->assertSee('32')
                    ->assertSee('Défenseur')
                    ->assertSee('Liverpool FC');
        });
    }

    /** @test */
    public function user_can_access_medical_records()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/medical')
                    ->assertSee('Dossiers médicaux')
                    ->assertSee('Rechercher')
                    ->assertSee('Filtrer');
        });
    }

    /** @test */
    public function user_can_access_licenses()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/licenses')
                    ->assertSee('Licences')
                    ->assertSee('Statut')
                    ->assertSee('Date d\'expiration');
        });
    }

    /** @test */
    public function admin_can_access_administration()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->adminUser)
                    ->visit('/admin')
                    ->assertSee('Administration')
                    ->assertSee('Utilisateurs')
                    ->assertSee('Paramètres système')
                    ->assertSee('Logs système');
        });
    }

    /** @test */
    public function regular_user_cannot_access_administration()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/admin')
                    ->assertSee('Accès interdit')
                    ->assertPathIs('/admin');
        });
    }

    /** @test */
    public function platform_is_responsive_on_different_screen_sizes()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/dashboard')
                    
                    // Test sur desktop
                    ->resize(1920, 1080)
                    ->assertSee('Dashboard')
                    
                    // Test sur tablette
                    ->resize(768, 1024)
                    ->assertSee('Dashboard')
                    
                    // Test sur mobile
                    ->resize(375, 667)
                    ->assertSee('Dashboard');
        });
    }

    /** @test */
    public function user_can_logout_successfully()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/dashboard')
                    ->assertSee('Dashboard')
                    
                    // Déconnexion
                    ->click('#user-menu')
                    ->clickLink('Déconnexion')
                    ->waitForLocation('/')
                    ->assertPathIs('/')
                    ->assertSee('Connexion');
        });
    }

    /** @test */
    public function platform_handles_errors_gracefully()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->user)
                    ->visit('/nonexistent-page')
                    ->assertSee('Page non trouvée')
                    ->assertSee('Erreur 404');
        });
    }

    /** @test */
    public function platform_performance_is_acceptable()
    {
        $this->browse(function (Browser $browser) {
            $startTime = microtime(true);
            
            $browser->loginAs($this->user)
                    ->visit('/dashboard')
                    ->waitForText('Dashboard');
            
            $endTime = microtime(true);
            $executionTime = ($endTime - $startTime) * 1000; // en millisecondes
            
            // Le temps de chargement doit être inférieur à 5 secondes
            $this->assertLessThan(5000, $executionTime, 
                "Le temps de chargement ({$executionTime}ms) dépasse 5 secondes");
        });
    }
}
