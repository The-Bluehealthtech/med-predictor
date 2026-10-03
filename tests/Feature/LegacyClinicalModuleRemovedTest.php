<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

/**
 * L'ancien module « clinical workflow » (portails clinicien/patient, /api/clinical/*) exposait les
 * patients FHIR sans contrôle du périmètre médical et des réponses simulées : il est retiré. Les
 * routes cliniques FHIR (données et imagerie d'établissement par joueur) restent en place.
 */
class LegacyClinicalModuleRemovedTest extends TestCase
{
    use DatabaseTransactions;

    public function test_legacy_clinical_routes_no_longer_exist(): void
    {
        Route::middleware('web')->group(base_path('routes/web.php'));
        app('router')->getRoutes()->refreshNameLookups();
        foreach (['clinical.patient-portal', 'clinical.clinician-portal', 'api.clinical.patients.create', 'api.clinical.patients.show',
            'api.clinical.patients.update', 'api.clinical.summarize', 'api.clinical.clinical-trials', 'api.clinical.care-gaps'] as $name) {
            $this->assertFalse(Route::has($name), $name);
        }
        $this->assertTrue(Route::has('clinical.external-data'), 'routes FHIR conservées');

        $user = User::factory()->create(['role' => 'club_medical', 'status' => 'active', 'tenant_id' => 1]);
        $this->actingAs($user)->get('/clinical/clinician-portal')->assertNotFound();
        // Les URL /api inconnues tombent sur la réponse générique de l'assistant vocal (Route::fallback) :
        // aucune route réelle ne doit plus y répondre.
        foreach ([['GET', '/api/clinical/patients/1'], ['PUT', '/api/clinical/patients/1'], ['POST', '/api/clinical/summarize'],
            ['GET', '/api/clinical/care-gaps/1'], ['GET', '/clinical/patient-portal']] as [$method, $uri]) {
            try {
                $route = app('router')->getRoutes()->match(Request::create($uri, $method));
                $this->assertTrue($route->isFallback, "$method $uri");
            } catch (NotFoundHttpException|MethodNotAllowedHttpException) {
                $this->addToAssertionCount(1);
            }
        }
    }
}
