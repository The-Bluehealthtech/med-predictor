<?php

namespace Tests\Feature;

use App\Http\Controllers\MedicalSecretaryController;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/** Le secrétariat médical a le même haut de page que les autres modules. */
class SecretaryLayoutTest extends TestCase
{
    use DatabaseTransactions;

    public function test_secretary_dashboard_uses_the_common_page_top(): void
    {
        if (!Route::has('secretary.dashboard')) {
            Route::middleware(['web', 'auth'])->get('/secretary/dashboard', [MedicalSecretaryController::class, 'dashboard'])->name('secretary.dashboard');
        }
        foreach (['secretary.appointments.store' => ['post', '/_t/sec/appt'], 'secretary.appointments.intake' => ['get', '/_t/sec/{appointment}'],
            'secretary.orders.sync' => ['post', '/_t/sec/sync'], 'secretary.orders.retry' => ['post', '/_t/sec/retry/{order}'], 'medical-files.document' => ['get', '/_t/doc/{document}'],
            'modules.index' => ['get', '/_t/modules'], 'dashboard' => ['get', '/_t/dashboard'], 'logout' => ['post', '/_t/logout'], 'language.update' => ['post', '/_t/lang']] as $name => [$method, $uri]) {
            if (!Route::has($name)) {
                Route::$method($uri, fn () => 'ok')->name($name);
            }
        }
        app('router')->getRoutes()->refreshNameLookups();
        $clubId = (int) DB::table('clubs')->insertGetId(['name' => 'Club Layout', 'created_at' => now(), 'updated_at' => now()]);
        $secretary = User::factory()->create(['role' => 'secretary', 'club_id' => $clubId, 'status' => 'active', 'tenant_id' => 1, 'name' => 'Sophie Accueil']);
        $this->actingAs($secretary);
        $html = $this->get(route('secretary.dashboard'))->assertOk()->getContent();

        $this->assertStringContainsString('fixed top-4 right-8', $html, 'barre du haut commune');
        $this->assertStringContainsString('Sophie Accueil', $html);
        $this->assertStringContainsString('fit-logout', $html, 'bouton de déconnexion commun');
        $this->assertStringContainsString('id="patient-flow"', $html, 'ancre du menu « Parcours patients »');
        $this->assertStringNotContainsString('shadow-lg">' . "\n" . '        <div class="max-w-7xl mx-auto px-4">', $html, 'ancienne barre du secrétariat retirée');
        $this->assertStringNotContainsString('href="' . route('modules.index') . '"', $html, 'pas de boutons de retour sur le tableau de bord lui-même');
    }
}
