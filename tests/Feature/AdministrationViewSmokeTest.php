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

        $this->get('/competitions/create')->assertOk();
        $this->get('/teams')->assertOk();
        $this->get('/club-management/dashboard')->assertOk();
        $this->get('/player-registration')->assertOk();
        $this->get('/healthcare')->assertOk();

        auth()->logout();
        $this->get('/healthcare')->assertRedirect();
    }
}
