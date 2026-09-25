<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Player;
use App\Models\FifaConnectId;

class FixFifaIds extends Command
{
    protected $signature = 'fix:fifa-ids';
    protected $description = 'Fix missing FIFA Connect ID records';

    public function handle()
    {
        $players = Player::whereNotNull('fifa_connect_id')
            ->where('fifa_connect_id', '!=', '')
            ->get();

        $created = 0;
        foreach ($players as $player) {
            if (!FifaConnectId::where('fifa_id', $player->fifa_connect_id)->exists()) {
                FifaConnectId::create([
                    'fifa_id' => $player->fifa_connect_id,
                    'entity_type' => 'player',
                    'status' => 'active'
                ]);
                $created++;
                $this->info("Created FIFA ID: {$player->fifa_connect_id}");
            }
        }

        $this->info("Created {$created} FIFA Connect ID records");
    }
} 