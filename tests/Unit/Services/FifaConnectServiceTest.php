<?php

namespace Tests\Unit\Services;

use App\Services\FifaConnectService;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use LogicException;
use Tests\TestCase;

class FifaConnectServiceTest extends TestCase
{
    private FifaConnectService $service;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.fifa_connect.base_url' => 'https://fifa.example.test/v1',
            'services.fifa_connect.api_key' => 'test-token',
            'services.fifa_connect.timeout' => 5,
            'services.fifa_connect.mock_mode' => false,
        ]);

        Cache::flush();
        $this->service = new FifaConnectService();
    }

    public function test_player_sync_returns_authoritative_payload_and_caches_it(): void
    {
        Http::fake([
            'https://fifa.example.test/v1/players/ABC123A' =>
                Http::response([
                    'first_name' => 'A',
                    'last_name' => 'B',
                    'nationality' => 'FRA',
                ], 200),
        ]);

        $first = $this->service->syncPlayerData('ABC123A');
        $second = $this->service->syncPlayerData('ABC123A');

        $this->assertSame('A', $first['first_name']);
        $this->assertSame($first, $second);
        $this->assertTrue(Cache::has('fifa_player_ABC123A'));
        Http::assertSentCount(1);
    }

    public function test_player_sync_sends_bearer_token(): void
    {
        Http::fake([
            '*' => Http::response(['first_name' => 'A'], 200),
        ]);

        $this->service->syncPlayerData('ABC123A');

        Http::assertSent(fn ($request) =>
            $request->hasHeader('Authorization', 'Bearer test-token')
        );
    }

    public function test_player_sync_throws_on_http_error(): void
    {
        Http::fake([
            '*' => Http::response(['error' => 'not found'], 404),
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('FIFA API error: 404');

        $this->service->syncPlayerData('ABC123A');
    }

    public function test_compliance_returns_api_payload_on_success(): void
    {
        Http::fake([
            'https://fifa.example.test/v1/compliance/ABC123A' =>
                Http::response([
                    'compliant' => true,
                    'requirements' => ['identity' => true],
                ], 200),
        ]);

        $result = $this->service->validateCompliance('ABC123A');

        $this->assertTrue($result['compliant']);
        $this->assertTrue($result['requirements']['identity']);
    }

    public function test_compliance_fails_closed_on_http_error(): void
    {
        Http::fake([
            '*' => Http::response([], 503),
        ]);

        $result = $this->service->validateCompliance('ABC123A');

        $this->assertFalse($result['compliant']);
        $this->assertNotEmpty($result['errors']);
    }

    public function test_cache_can_be_cleared(): void
    {
        Cache::put('fifa_player_ABC123A', ['ok' => true], 60);

        $this->service->clearCache('ABC123A');

        $this->assertFalse(Cache::has('fifa_player_ABC123A'));
    }

    public function test_service_never_generates_fifa_identifiers(): void
    {
        $this->expectException(LogicException::class);

        $this->service->generatePlayerId();
    }
}
