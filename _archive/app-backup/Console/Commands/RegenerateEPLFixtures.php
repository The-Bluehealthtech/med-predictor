<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Competition;
use App\Models\GameMatch;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RegenerateEPLFixtures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'epl:regenerate-fixtures';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear existing EPL fixtures and regenerate them properly';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Regenerating EPL fixtures...');

        // Get EPL competition
        $epl = Competition::where('short_name', 'EPL')->first();
        if (!$epl) {
            $this->error('EPL competition not found!');
            return 1;
        }

        // Clear existing fixtures
        $deletedMatches = GameMatch::where('competition_id', $epl->id)->delete();
        $this->info("Deleted {$deletedMatches} existing matches");

        // Get teams registered to EPL
        $teams = $epl->teams()->with('club')->get();
        $teamCount = $teams->count();

        if ($teamCount < 2) {
            $this->error('Not enough teams registered for fixture generation. Need at least 2 teams.');
            return 1;
        }

        $this->info("Generating fixtures for {$teamCount} teams...");

        $teamIds = $teams->pluck('id')->toArray();
        $matchday = 1;
        $matchDate = Carbon::parse($epl->start_date);

        // Generate round-robin fixtures (first half of season)
        for ($round = 0; $round < $teamCount - 1; $round++) {
            for ($i = 0; $i < $teamCount / 2; $i++) {
                $homeIndex = $i;
                $awayIndex = $teamCount - 1 - $i;
                
                if ($homeIndex >= $awayIndex) continue;
                
                $homeTeamId = $teamIds[$homeIndex];
                $awayTeamId = $teamIds[$awayIndex];
                
                $homeTeam = $teams->where('id', $homeTeamId)->first();
                $awayTeam = $teams->where('id', $awayTeamId)->first();
                
                // Create match
                $match = GameMatch::create([
                    'competition_id' => $epl->id,
                    'home_team_id' => $homeTeamId,
                    'away_team_id' => $awayTeamId,
                    'home_club_id' => $homeTeam->club_id,
                    'away_club_id' => $awayTeam->club_id,
                    'matchday' => $matchday,
                    'match_date' => $matchDate,
                    'kickoff_time' => $matchDate->copy()->setTime(15, 0),
                    'venue' => 'home',
                    'stadium' => $homeTeam->club->stadium,
                    'capacity' => rand(40000, 75000),
                    'weather_conditions' => 'Clear',
                    'pitch_condition' => 'Excellent',
                    'referee' => 'TBD',
                    'assistant_referee_1' => 'TBD',
                    'assistant_referee_2' => 'TBD',
                    'fourth_official' => 'TBD',
                    'var_referee' => 'TBD',
                    'match_status' => 'scheduled',
                    'home_score' => null,
                    'away_score' => null,
                    'home_possession' => null,
                    'away_possession' => null,
                    'home_shots' => null,
                    'away_shots' => null,
                    'home_shots_on_target' => null,
                    'away_shots_on_target' => null,
                    'home_corners' => null,
                    'away_corners' => null,
                    'home_fouls' => null,
                    'away_fouls' => null,
                    'home_offsides' => null,
                    'away_offsides' => null,
                ]);

                $this->line("Matchday {$matchday}: {$homeTeam->club->name} vs {$awayTeam->club->name}");
            }
            
            // Rotate teams for next round (except first team)
            $firstTeam = array_shift($teamIds);
            array_push($teamIds, $firstTeam);
            
            $matchday++;
            $matchDate->addDays(7); // Weekly matches
        }
        
        // Generate return fixtures (second half of season)
        for ($round = 0; $round < $teamCount - 1; $round++) {
            for ($i = 0; $i < $teamCount / 2; $i++) {
                $homeIndex = $i;
                $awayIndex = $teamCount - 1 - $i;
                
                if ($homeIndex >= $awayIndex) continue;
                
                $homeTeamId = $teamIds[$homeIndex];
                $awayTeamId = $teamIds[$awayIndex];
                
                $homeTeam = $teams->where('id', $homeTeamId)->first();
                $awayTeam = $teams->where('id', $awayTeamId)->first();
                
                // Create match
                $match = GameMatch::create([
                    'competition_id' => $epl->id,
                    'home_team_id' => $homeTeamId,
                    'away_team_id' => $awayTeamId,
                    'home_club_id' => $homeTeam->club_id,
                    'away_club_id' => $awayTeam->club_id,
                    'matchday' => $matchday,
                    'match_date' => $matchDate,
                    'kickoff_time' => $matchDate->copy()->setTime(15, 0),
                    'venue' => 'home',
                    'stadium' => $homeTeam->club->stadium,
                    'capacity' => rand(40000, 75000),
                    'weather_conditions' => 'Clear',
                    'pitch_condition' => 'Excellent',
                    'referee' => 'TBD',
                    'assistant_referee_1' => 'TBD',
                    'assistant_referee_2' => 'TBD',
                    'fourth_official' => 'TBD',
                    'var_referee' => 'TBD',
                    'match_status' => 'scheduled',
                    'home_score' => null,
                    'away_score' => null,
                    'home_possession' => null,
                    'away_possession' => null,
                    'home_shots' => null,
                    'away_shots' => null,
                    'home_shots_on_target' => null,
                    'away_shots_on_target' => null,
                    'home_corners' => null,
                    'away_corners' => null,
                    'home_fouls' => null,
                    'away_fouls' => null,
                    'home_offsides' => null,
                    'away_offsides' => null,
                ]);

                $this->line("Matchday {$matchday}: {$homeTeam->club->name} vs {$awayTeam->club->name}");
            }
            
            // Rotate teams for next round (except first team)
            $firstTeam = array_shift($teamIds);
            array_push($teamIds, $firstTeam);
            
            $matchday++;
            $matchDate->addDays(7); // Weekly matches
        }
        
        $totalMatches = GameMatch::where('competition_id', $epl->id)->count();
        
        $this->info("Successfully generated {$totalMatches} matches across " . ($matchday - 1) . " matchdays!");
        
        return 0;
    }
} 