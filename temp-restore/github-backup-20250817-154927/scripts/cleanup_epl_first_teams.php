<?php

use App\Models\Competition;
use App\Models\Team;

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Detach all teams from EPL
$epl = Competition::where('name', 'Premier League')->first();
$epl->teams()->detach();

// Get 20 most recent unique first_team teams (one per club)
$firstTeams = Team::where('type', 'first_team')->orderBy('created_at', 'desc')->get()->unique('club_id')->take(20);
$epl->teams()->sync($firstTeams->pluck('id')->toArray());

echo "EPL now has ".$epl->teams()->count()." teams (should be 20).\n";
foreach ($epl->teams as $team) {
    echo $team->name." (Club ID: ".$team->club_id.")\n";
} 