<?php

namespace Tests\Feature;

use App\Http\Middleware\DatabaseFallback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class PrimaryDatabaseAvailabilityTest extends TestCase
{
    public function test_database_outage_blocks_the_writer_without_simulated_data(): void
    {
        DB::shouldReceive('connection')->once()->andReturnSelf();
        DB::shouldReceive('getPdo')->once()->andThrow(new \RuntimeException('private connection detail'));
        $request = Request::create('/players', 'POST');
        $request->headers->set('Accept', 'application/json');
        $called = false;
        $response = (new DatabaseFallback())->handle($request, function () use (&$called) {
            $called = true;
        });
        self::assertFalse($called);
        self::assertSame(503, $response->getStatusCode());
        self::assertFalse($request->has('simulated_data'));
        self::assertStringNotContainsString('private connection detail', $response->getContent());
    }

    public function test_available_database_runs_the_action_once(): void
    {
        DB::shouldReceive('connection')->once()->andReturnSelf();
        DB::shouldReceive('getPdo')->once()->andReturn(new \stdClass());
        $calls = 0;
        $response = (new DatabaseFallback())->handle(Request::create('/players', 'POST'), function () use (&$calls) {
            $calls++;
            return response('saved', 201);
        });
        self::assertSame(1, $calls);
        self::assertSame(201, $response->getStatusCode());
    }

    public function test_business_exception_does_not_retry_an_action(): void
    {
        DB::shouldReceive('connection')->once()->andReturnSelf();
        DB::shouldReceive('getPdo')->once()->andReturn(new \stdClass());
        $calls = 0;
        try {
            (new DatabaseFallback())->handle(Request::create('/players', 'POST'), function () use (&$calls) {
                $calls++;
                throw new \RuntimeException('business failure');
            });
            self::fail('Exception métier attendue');
        } catch (\RuntimeException $exception) {
            self::assertSame('business failure', $exception->getMessage());
            self::assertSame(1, $calls);
        }
    }
}
