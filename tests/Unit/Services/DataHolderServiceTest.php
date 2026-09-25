<?php

namespace Tests\Unit\Services;

use App\Models\Association;
use App\Services\FifaConnect\DataHolderService;
use InvalidArgumentException;
use Tests\TestCase;

class DataHolderServiceTest extends TestCase
{
    public function test_malformed_authoritative_identifier_is_rejected_before_write(): void
    {
        $association = new Association();
        $association->id = 1;

        $this->expectException(InvalidArgumentException::class);
        app(DataHolderService::class)->recordStoredFifaId(
            $association, 'FIFA2026TUN001', false
        );
    }
}
