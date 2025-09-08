<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Club;

class DebugClubsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('=== DEBUGGING CLUBS AND PLAYERS ===');
        
        $clubs = Club::with('players')->get();
        
        $this->command->info('Total clubs: ' . $clubs->count());
        $this->command->info('Total players: ' . $clubs->sum(function($club) { return $club->players->count(); }));
        
        $this->command->info('');
        $this->command->info('Clubs with players:');
        
        $clubsWithPlayers = $clubs->filter(function($club) {
            return $club->players->count() > 0;
        });
        
        foreach ($clubsWithPlayers as $club) {
            $this->command->info("ID: {$club->id} - {$club->name} - {$club->players->count()} players");
        }
        
        $this->command->info('');
        $this->command->info('Top 10 clubs by player count:');
        
        $topClubs = $clubs->sortByDesc(function($club) {
            return $club->players->count();
        })->take(10);
        
        foreach ($topClubs as $club) {
            $this->command->info("{$club->name}: {$club->players->count()} players");
        }
        
        $this->command->info('');
        $this->command->info('=== END DEBUG ===');
    }
} 