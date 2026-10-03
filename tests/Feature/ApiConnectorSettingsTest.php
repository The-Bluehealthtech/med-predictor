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
            ->name('modules.api-connectors.index');
        Route::middleware(['web', 'auth'])
            ->post('/_t/api-connectors/{connector}/activation', [\App\Http\Controllers\ApiConnectorSettingsController::class, 'activation'])
            ->name('modules.api-connectors.activation');
        Route::middleware(['web', 'auth'])
            ->post('/_t/api-connectors/{connector}/test', [\App\Http\Controllers\ApiConnectorSettingsController::class, 'test'])
            ->name('modules.api-connectors.test');
        app('router')->getRoutes()->refreshNameLookups();
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

    public function test_admin_can_activate_and_disable_a_configured_connector(): void
    {
        config([
            'services.aws_rekognition.key' => 'key',
            'services.aws_rekognition.secret' => 'secret',
            'services.aws_rekognition.region' => 'ap-southeast-2',
        ]);
        $system = User::factory()->create(['role' => 'system_admin', 'status' => 'active']);
        $state = app(\App\Services\ApiConnectorState::class);

        $this->assertFalse($state->enabled('aws_rekognition', false));
        $this->actingAs($system)->post('/_t/api-connectors/aws_rekognition/activation', ['enabled' => 1])
            ->assertRedirect()->assertSessionHas('success');
        $this->assertTrue($state->enabled('aws_rekognition', false));

        $this->actingAs($system)->get('/_t/api-connectors')
            ->assertOk()->assertSee('Désactiver')->assertSee('Activation appliquée au runtime FIT');

        $this->actingAs($system)->post('/_t/api-connectors/aws_rekognition/activation', ['enabled' => 0])
            ->assertRedirect()->assertSessionHas('success');
        $this->assertFalse($state->enabled('aws_rekognition', false));
    }

    public function test_connector_cannot_be_activated_without_required_configuration(): void
    {
        config(['services.signotec.bridge_url' => null, 'services.signotec.bridge_token' => null]);
        $system = User::factory()->create(['role' => 'system_admin', 'status' => 'active']);

        $this->actingAs($system)->post('/_t/api-connectors/signotec/activation', ['enabled' => 1])
            ->assertRedirect()->assertSessionHas('error');
        $this->assertFalse(app(\App\Services\ApiConnectorState::class)->enabled('signotec', false));
    }

    public function test_signotec_connection_can_be_tested_before_activation(): void
    {
        config(['services.signotec.bridge_url' => 'https://signotec-bridge.test', 'services.signotec.bridge_token' => 'secret']);
        \Illuminate\Support\Facades\Http::fake([
            'signotec-bridge.test/health' => \Illuminate\Support\Facades\Http::response(['status' => 'ok'], 200),
        ]);
        $system = User::factory()->create(['role' => 'system_admin', 'status' => 'active']);

        $this->actingAs($system)->post('/_t/api-connectors/signotec/test')
            ->assertRedirect()->assertSessionHas('success');
        $this->assertFalse(app(\App\Services\ApiConnectorState::class)->enabled('signotec', false));
    }
}
