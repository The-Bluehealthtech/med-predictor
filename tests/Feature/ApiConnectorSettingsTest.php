<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Modules\ModuleCatalog;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ApiConnectorSettingsTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Route::middleware(['web', 'auth'])
            ->get('/_t/api-connectors', [\App\Http\Controllers\ApiConnectorSettingsController::class, 'index'])
            ->name('modules.api-connectors.test');
    }

    public function test_api_configuration_card_is_visible_only_to_global_admins(): void
    {
        $system = User::factory()->create(['role' => 'system_admin', 'status' => 'active']);
        $club = User::factory()->create(['role' => 'club_admin', 'status' => 'active']);
        $catalog = app(ModuleCatalog::class);

        $this->assertContains('Configuration des API', $catalog->visibleFor($system)->pluck('name')->all());
        $this->assertNotContains('Configuration des API', $catalog->visibleFor($club)->pluck('name')->all());
    }

    public function test_api_configuration_page_never_displays_secret_values(): void
    {
        config([
            'services.aws_rekognition.key' => 'super-secret-key',
            'services.aws_rekognition.secret' => 'super-secret-secret',
            'services.aws_rekognition.region' => 'ap-southeast-2',
            'services.signotec.bridge_url' => 'https://bridge.test',
            'services.signotec.bridge_token' => 'signotec-secret',
        ]);
        $system = User::factory()->create(['role' => 'system_admin', 'status' => 'active']);

        $this->actingAs($system)->get('/_t/api-connectors')
            ->assertOk()
            ->assertSee('Configuration des API')
            ->assertSee('AWS Rekognition CompareFaces')
            ->assertSee('signotec Biometrics API')
            ->assertSee('AWS_ACCESS_KEY_ID')
            ->assertDontSee('super-secret-key')
            ->assertDontSee('super-secret-secret')
            ->assertDontSee('signotec-secret');
    }

    public function test_non_admin_cannot_open_api_configuration_page(): void
    {
        $club = User::factory()->create(['role' => 'club_admin', 'status' => 'active']);

        $this->actingAs($club)->get('/_t/api-connectors')->assertStatus(302);
    }
}
