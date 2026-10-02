<?php

namespace Tests\Unit\Components;

use App\Http\Middleware\DatabaseFallback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Tests\TestCase;

class DatabaseFallbackContractTest extends TestCase
{
    public function test_database_failure_returns_503_instead_of_simulated_data(): void
    {
        DB::shouldReceive('connection')
            ->once()
            ->andThrow(new RuntimeException('database unavailable'));

        $request = Request::create('/modules', 'GET', [], [], [], [
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_ACCEPT_LANGUAGE' => 'fr',
        ]);

        $nextCalled = false;
        $response = app(DatabaseFallback::class)->handle(
            $request,
            function () use (&$nextCalled) {
                $nextCalled = true;

                return response()->json(['ok' => true]);
            }
        );

        $this->assertFalse($nextCalled);
        $this->assertSame(503, $response->getStatusCode());
        $this->assertSame('30', $response->headers->get('Retry-After'));
        $this->assertSame(
            'Service temporairement indisponible. Veuillez réessayer plus tard.',
            $response->getData(true)['message']
        );
        $this->assertNull($request->input('simulated_data'));
        $this->assertNull($request->input('db_fallback'));
    }
}
