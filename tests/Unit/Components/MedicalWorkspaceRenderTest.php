<?php

namespace Tests\Unit\Components;

use App\Models\HealthRecord;
use App\Models\Player;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class MedicalWorkspaceRenderTest extends TestCase
{
    private string $layoutDirectory;

    public function createApplication()
    {
        $app = require dirname(__DIR__, 3).'/bootstrap/app.php';
        $app->booting(function () use ($app) {
            $app['config']->set('cache.default', 'array');
            $app['config']->set('database.default', 'sqlite');
            $app['config']->set('database.connections.sqlite.database', ':memory:');
        });
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        return $app;
    }

    protected function setUp(): void
    {
        parent::setUp();

        // Exercise the real dossier template without requiring compiled frontend assets.
        $this->layoutDirectory = sys_get_temp_dir().'/fit-workspace-test-'.bin2hex(random_bytes(8));
        mkdir($this->layoutDirectory.'/layouts', 0700, true);
        file_put_contents($this->layoutDirectory.'/layouts/app.blade.php', "@yield('content')");
        app('view')->getFinder()->prependLocation($this->layoutDirectory);

        foreach (['health-records.show', 'health-records.edit', 'health-records.assistant',
            'health-records.modules.show', 'pcma.create', 'medical-aut.choose', 'medical-imaging.index'] as $name) {
            if (!Route::has($name)) {
                Route::get('/workspace-fixture/'.str_replace('.', '/', $name).'/{healthRecord?}', fn () => '')->name($name);
            }
        }
        app('router')->getRoutes()->refreshNameLookups();
    }

    protected function tearDown(): void
    {
        unlink($this->layoutDirectory.'/layouts/app.blade.php');
        rmdir($this->layoutDirectory.'/layouts');
        rmdir($this->layoutDirectory);
        parent::tearDown();
    }

    private function renderWorkspace(?array $vigilance): string
    {
        $player = new Player(['name' => 'Fixture Player']);
        $player->id = 42;
        $player->setRelation('club', null);
        $record = new HealthRecord([
            'player_id' => 42, 'record_date' => '2026-10-01', 'visit_date' => '2026-10-01',
            'allergies' => ['Fixture allergy'], 'medications' => ['Fixture medication'],
        ]);
        $record->id = 873;
        $record->setRelation('player', $player);

        return view('health-records.workspace', [
            'healthRecord' => $record, 'dopingRecords' => collect([$record]),
            'pcmaRecords' => collect(), 'autRequests' => collect(), 'sectionHistory' => [],
            'sectionDocuments' => collect(), 'intakeDocuments' => collect(),
            'posturalAssessments' => collect(), 'vigilance' => $vigilance,
        ])->render();
    }

    public function test_dossier_renders_when_vigilance_is_unavailable(): void
    {
        $html = $this->renderWorkspace(null);

        $this->assertStringNotContainsString('Contrôles explicables', $html);
        $this->assertStringContainsString('id="clinical-assistant-panel"', $html);
        $this->assertStringContainsString('Fixture allergy', $html);
        $this->assertStringContainsString('Modules spécialisés du dossier', $html);
        $this->assertStringContainsString('Documents médicaux', $html);
    }

    public function test_dossier_renders_with_all_three_vigilance_axes(): void
    {
        $axis = ['status' => 'incomplete', 'flags' => [], 'confirmed' => [],
            'notice' => 'Fixture notice', 'references' => []];
        $html = $this->renderWorkspace(['method' => 'fixture',
            'identity' => $axis, 'injury' => $axis, 'cardiac' => $axis]);

        $this->assertStringContainsString('Contrôles explicables', $html);
        $this->assertStringContainsString('Identité / âge', $html);
        $this->assertStringContainsString('Cardiovasculaire', $html);
        $this->assertStringContainsString('id="clinical-assistant-panel"', $html);
        $this->assertStringContainsString('Documents médicaux', $html);
    }
}
