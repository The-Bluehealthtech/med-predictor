<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AdministrationViewSmokeTest extends TestCase
{
    use DatabaseTransactions;

    public function test_administration_page_renders_without_unavailable_permission_link(): void
    {
        Route::middleware('web')->group(base_path('routes/web.php'));
        Route::middleware('web')->group(base_path('routes/auth.php'));
        app('router')->getRoutes()->refreshNameLookups();
        app('router')->getRoutes()->refreshActionLookups();

        $this->actingAs(User::factory()->create(['role' => 'system_admin']))
            ->get('/administration')
            ->assertOk()
            ->assertSee('Permissions par module indisponibles');

        $this->get('/association/dashboard')
            ->assertOk()
            ->assertSee('Détection de fraude indisponible');

        \Illuminate\Support\Facades\Http::fake();
        $this->get('/competitions/create')->assertOk();
        $this->get('/fifa/connectivity')->assertOk();
        $this->get('/fifa/statistics')->assertOk();
        $this->get('/fifa/players/search')->assertOk()->assertDontSee('Aucun joueur trouvé.');
        $this->get('/fifa/players/search?q=introuvable')->assertOk()->assertSee('Aucun joueur trouvé.');
        \App\Models\Player::factory()->create([
            'first_name' => 'RechercheUnique',
            'fifa_connect_id' => 'CANONICAL-TEST-123',
        ]);
        $this->get('/fifa/players/search?q=CANONICAL-TEST-123')
            ->assertOk()->assertSee('RechercheUnique')->assertSee('CANONICAL-TEST-123');
        $this->get('/teams')->assertOk();
        $this->get('/club-management/dashboard')->assertOk();
        $this->get('/player-portal')->assertRedirect(route('portail.joueur'));
        $this->get('/player-portal/fifa-ultimate')->assertRedirect(route('portail.joueur'));
        $this->get('/player-registration')->assertOk();
        $this->get('/healthcare')->assertOk();

        auth()->logout();
        $this->get('/healthcare')->assertRedirect();
        $this->get('/')->assertOk()->assertDontSee('120000000');
        $this->get('/dashboard-temp')->assertGone();
        $this->get('/dashboard-simulated')->assertGone();
        $this->get('/fifa-ultimate-working')->assertGone();
        $this->get('/fifa-ultimate-complete')->assertGone();
        $this->get('/fifa-test-public')->assertGone();
        $this->get('/fifa-working')->assertGone();
        $this->get('/fifa-complete')->assertGone();
    }
}
