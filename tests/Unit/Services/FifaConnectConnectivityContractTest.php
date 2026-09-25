<?php

namespace Tests\Unit\Services;

use App\Services\FifaConnectService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FifaConnectConnectivityContractTest extends TestCase
{
    public function test_missing_api_key_is_reported_as_unconfigured_without_http_call(): void
    {
        config([
            'services.fifa_connect.api_key' => null,
            'services.fifa_connect.mock_mode' => false,
            'services.fifa_connect.base_url' => 'https://fifa.example.test/v1',
        ]);

        Http::fake();

        $result = (new FifaConnectService())->checkConnectivity();

        $this->assertFalse($result['connected']);
        $this->assertSame('unconfigured', $result['status']);
        $this->assertFalse($result['mock_mode']);
        Http::assertNothingSent();
    }

    public function test_explicit_mock_mode_is_never_reported_as_live_connection(): void
    {
        config([
            'services.fifa_connect.api_key' => null,
            'services.fifa_connect.mock_mode' => true,
        ]);

        Http::fake();

        $result = (new FifaConnectService())->checkConnectivity();

        $this->assertFalse($result['connected']);
        $this->assertSame('mock', $result['status']);
        $this->assertTrue($result['mock_mode']);
        $this->assertTrue($result['simulated']);
        Http::assertNothingSent();
    }

    public function test_live_health_success_is_reported_online_and_uses_bearer_auth(): void
    {
        config([
            'services.fifa_connect.api_key' => 'test-secret-token',
            'services.fifa_connect.mock_mode' => false,
            'services.fifa_connect.base_url' => 'https://fifa.example.test/v1',
        ]);

        Http::fake([
            'https://fifa.example.test/v1/health' => Http::response(
                ['status' => 'ok'],
                200
            ),
        ]);

        $result = (new FifaConnectService())->checkConnectivity();

        $this->assertTrue($result['connected']);
        $this->assertSame('online', $result['status']);
        $this->assertFalse($result['mock_mode']);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://fifa.example.test/v1/health'
                && $request->hasHeader(
                    'Authorization',
                    'Bearer test-secret-token'
                );
        });
    }

    public function test_live_health_http_error_is_not_reported_connected(): void
    {
        config([
            'services.fifa_connect.api_key' => 'test-secret-token',
            'services.fifa_connect.mock_mode' => false,
            'services.fifa_connect.base_url' => 'https://fifa.example.test/v1',
        ]);

        Http::fake([
            'https://fifa.example.test/v1/health' => Http::response([], 503),
        ]);

        $result = (new FifaConnectService())->checkConnectivity();

        $this->assertFalse($result['connected']);
        $this->assertSame('error', $result['status']);
        $this->assertSame(503, $result['http_status']);
    }

    public function test_fetch_players_uses_configured_fifa_api(): void
    {
        config([
            'services.fifa_connect.api_key' => 'test-secret-token',
            'services.fifa_connect.mock_mode' => false,
            'services.fifa_connect.base_url' => 'https://fifa.example.test/v1',
        ]);

        Http::fake([
            'https://fifa.example.test/v1/players*' => Http::response([
                'data' => [
                    ['fifa_id' => 'FIFA-1001', 'name' => 'API Player'],
                ],
            ], 200),
        ]);

        $result = (new FifaConnectService())->fetchPlayers(
            ['country' => 'NC'],
            2,
            25
        );

        $this->assertSame('FIFA-1001', $result['data'][0]['fifa_id']);

        Http::assertSent(function ($request) {
            return str_starts_with(
                $request->url(),
                'https://fifa.example.test/v1/players?'
            )
                && str_contains($request->url(), 'country=NC')
                && str_contains($request->url(), 'page=2')
                && str_contains($request->url(), 'limit=25')
                && $request->hasHeader(
                    'Authorization',
                    'Bearer test-secret-token'
                );
        });
    }
}
