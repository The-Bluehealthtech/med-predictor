<?php

namespace Tests\Unit\Services;

use App\Models\Association;
use App\Models\FifaConnect\DataHolder;
use App\Models\Player;
use App\Services\FifaConnect\DataHolderService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use InvalidArgumentException;
use Tests\TestCase;

class DataHolderServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_malformed_authoritative_identifier_is_rejected_before_write(): void
    {
        $association = new Association();
        $association->id = 1;

        $this->expectException(InvalidArgumentException::class);
        app(DataHolderService::class)->recordStoredFifaId(
            $association, 'FIFA2026TUN001', false
        );
    }

    public function test_verified_holder_is_not_reopened_by_local_recording(): void
    {
        $association = Association::factory()->create([
            'name' => 'Test Association',
            'country' => 'FR',
            'fifa_id' => null,
        ]);
        $service = app(DataHolderService::class);
        $holder = $service->recordStoredFifaId($association, 'ABC123A', false);
        $service->markClaimAccepted($holder);
        $service->markVerified($holder);
        $verifiedAt = $holder->last_verified_at;

        $again = $service->recordStoredFifaId($association, 'ABC123A', false);

        $this->assertSame($holder->id, $again->id);
        $this->assertSame('verified', $again->claim_status);
        $this->assertEquals($verifiedAt, $again->last_verified_at);
    }

    public function test_merge_rejects_a_malformed_primary_identifier(): void
    {
        $holder = new DataHolder(['person_fifa_id' => 'ABC123A']);
        $this->expectException(InvalidArgumentException::class);
        app(DataHolderService::class)->markMerged($holder, 'FIFA2026TUN001');
    }

    public function test_bulk_claim_excludes_invalid_and_completed_identifiers(): void
    {
        $association = Association::factory()->create([
            'name' => 'Claim Test Association', 'country' => 'FR', 'fifa_id' => null,
        ]);
        foreach (['ABC123A', 'DEF456B', 'FIFA2026TUN001'] as $id) {
            Player::query()->create([
                'association_id' => $association->id,
                'fifa_connect_id' => $id,
                'first_name' => 'Test',
                'last_name' => 'Player',
            ]);
        }
        $service = app(DataHolderService::class);
        $holder = $service->recordStoredFifaId($association, 'DEF456B', false);
        $service->markClaimAccepted($holder);
        $service->markVerified($holder);

        $this->assertSame(
            ['ABC123A'],
            $service->productionIdsForBulkClaim($association)->all()
        );
    }
}
