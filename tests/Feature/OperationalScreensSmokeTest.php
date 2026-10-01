<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class OperationalScreensSmokeTest extends TestCase
{
    use DatabaseTransactions;

    public function test_dtn_rpm_and_referee_screens(): void
    {
        Route::middleware('web')->group(base_path('routes/web.php'));
        Route::middleware('web')->group(base_path('routes/auth.php'));
        app('router')->getRoutes()->refreshNameLookups();
        app('router')->getRoutes()->refreshActionLookups();

        // Ancienne route de contournement d'authentification '/test-referee-assignments'
        // supprimee (elle desactivait volontairement le middleware auth) - voir audit securite.
        $this->actingAs(User::factory()->create(['role' => 'system_admin']));
        foreach (['/dtn', '/rpm', '/admin/referee-assignments', '/modules/referees'] as $path) {
            $response = $this->get($path);
            $exception = $response->baseResponse->exception ?? null;
            $this->assertSame(200, $response->status(), $path . ': ' . $exception?->getMessage());
        }

        $this->get('/modules/referees')
            ->assertOk()
            ->assertDontSee('EST vs CSS')
            ->assertDontSee('Modifications sauvegardées');

        $record = (object) ['measurement_time' => '2026-09-25 10:30:00',
            'first_name' => 'Test', 'last_name' => 'Fixture', 'club_name' => null,
            'heart_rate' => null, 'oxygen_saturation' => null,
            'temperature' => null, 'hydration_level' => null,
            'recovery_score' => null, 'readiness_score' => null,
            'data_source' => 'manual_entry'];
        $html = view('rpm.index-canonical', [
            'measurements' => collect([$record]),
            'stats' => ['measurements' => 1, 'players' => 1,
                'active_alerts' => 0, 'latest_measurement' => $record->measurement_time],
        ])->render();
        $this->assertStringContainsString('25/09/2026 10:30', $html);

        $performance = new \App\Models\PlayerPerformance();
        $performance->performance_date = '2026-09-25';
        $this->assertInstanceOf(\Carbon\CarbonInterface::class, $performance->performance_date);
        // Outil DTN : liste des sélections nationales (dates au format français).
        $selection = new \App\Models\NationalSelection([
            'team_label' => 'Équipe nationale A', 'event_type' => 'friendly', 'event_name' => 'Fenêtre internationale',
            'start_date' => '2026-09-25', 'end_date' => '2026-09-30', 'status' => 'convoked',
        ]);
        $selection->id = 1;
        $selection->setRelation('player', new \App\Models\Player(['first_name' => 'Test', 'last_name' => 'Joueur']));
        $selection->setRelation('club', null);
        $selection->setRelation('departure', null);
        $selection->setRelation('returnReport', null);
        $dtn = view('dtn.index', [
            'todo' => collect([$selection]), 'ongoing' => collect(), 'history' => collect(),
            'canConvoke' => false, 'side' => 'club',
        ])->render();
        $this->assertStringContainsString('25/09/2026', $dtn);
    }
}
