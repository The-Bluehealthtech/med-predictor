<?php

namespace Tests\Unit\Services;

use App\Services\FifaTmsLicenseService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FifaTmsLicenseServiceContractTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_missing_key_is_reported_unconfigured_without_http_call(): void
    {
        config([
            'services.fifa_tms.api_key' => null,
            'services.fifa_tms.mock_mode' => false,
            'services.fifa_tms.base_url' => 'https://tms.example.test/v1',
        ]);

        Http::fake();

        $status = (new FifaTmsLicenseService())->testConnectivity();

        $this->assertFalse($status['connected']);
        $this->assertSame('unconfigured', $status['status']);
        $this->assertFalse($status['mock_mode']);
        Http::assertNothingSent();
    }

    public function test_mock_mode_is_explicit_and_never_reported_live(): void
    {
        config([
            'services.fifa_tms.api_key' => null,
            'services.fifa_tms.mock_mode' => true,
        ]);

        Http::fake();

        $status = (new FifaTmsLicenseService())->testConnectivity();

        $this->assertFalse($status['connected']);
        $this->assertSame('mock', $status['status']);
        $this->assertTrue($status['mock_mode']);
        $this->assertTrue($status['simulated']);
        Http::assertNothingSent();
    }

    public function test_live_license_fetch_uses_bearer_auth_when_configured(): void
    {
        config([
            'services.fifa_tms.api_key' => 'tms-test-token',
            'services.fifa_tms.mock_mode' => false,
            'services.fifa_tms.base_url' => 'https://tms.example.test/v1',
        ]);

        Http::fake([
            'https://tms.example.test/v1/players/FIFA-1/licenses' =>
                Http::response([
                    'licenses' => [[
                        'id' => 'L-1',
                        'type' => 'professional',
                        'status' => 'active',
                    ]],
                ], 200),
        ]);

        $licenses = (new FifaTmsLicenseService())
            ->getPlayerLicenses('FIFA-1');

        $this->assertCount(1, $licenses);
        $this->assertSame('FIFA TMS', $licenses[0]['source_donnee']);

        Http::assertSent(function ($request) {
            return $request->url()
                    === 'https://tms.example.test/v1/players/FIFA-1/licenses'
                && $request->hasHeader(
                    'Authorization',
                    'Bearer tms-test-token'
                );
        });
    }
}
