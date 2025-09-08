<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Competition;
use App\Models\Team;

class CleanupEplFirstTeams extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:epl-first-teams';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Keep only the 20 most recent unique first_team teams (one per club) in the EPL';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $epl = Competition::where('name', 'Premier League')->first();
        if (!$epl) {
            $this->error('Premier League competition not found!');
            return 1;
        }

        $epl->teams()->detach();

        $firstTeams = Team::where('type', 'first_team')
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('club_id')
            ->take(20);

        $epl->teams()->sync($firstTeams->pluck('id')->toArray());

        $this->info('EPL now has ' . $epl->teams()->count() . ' teams (should be 20).');
        foreach ($epl->teams as $team) {
            $this->line($team->name . ' (Club ID: ' . $team->club_id . ')');
        }
        return 0;
    }
} 