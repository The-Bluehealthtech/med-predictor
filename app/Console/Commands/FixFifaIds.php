<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Player;
use App\Models\FifaConnectId;

class FixFifaIds extends Command
{
    protected $signature = 'fix:fifa-ids';
    protected $description = 'Audit legacy FIFA ID candidates without promoting them';

    public function handle(\App\Services\FifaConnect\SchemaCatalog $catalog): int
    {
        $candidates = 0;
        $invalid = 0;
        $missing = 0;

        Player::query()->whereNotNull('fifa_connect_id')
            ->where('fifa_connect_id', '<>', '')
            ->select(['id', 'fifa_connect_id'])
            ->chunkById(500, function ($players) use ($catalog, &$candidates, &$invalid, &$missing): void {
                foreach ($players as $player) {
                    $candidates++;
                    $id = trim((string) $player->fifa_connect_id);
                    try {
                        $catalog->assertFifaIdentifier($id);
                    } catch (\RuntimeException) {
                        $invalid++;
                        continue;
                    }

                    if (!FifaConnectId::query()->where('fifa_id', $id)->exists()) {
                        $missing++;
                    }
                }
            });

        $this->line("Legacy candidates: {$candidates}; invalid format: {$invalid}; absent from legacy ID table: {$missing}.");
        $this->warn('No FIFA ID was created. Verify provenance and authority before promoting legacy values.');

        return self::SUCCESS;
    }
} 