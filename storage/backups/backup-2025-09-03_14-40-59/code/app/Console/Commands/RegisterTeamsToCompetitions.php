<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Competition;
use App\Models\Team;
use App\Models\GameMatch;
use App\Models\MatchSheet;
use App\Models\CompetitionRanking;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RegisterTeamsToCompetitions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'competitions:register-teams {competition_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register teams to competitions and generate fixtures';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $competitionId = $this->argument('competition_id');
        
        if ($competitionId) {
            $competitions = Competition::where('id', $competitionId)->get();
        } else {
            $competitions = Competition::where('status', 'active')->get();
        }
        
        foreach ($competitions as $competition) {
            $this->info("Processing competition: {$competition->name}");
            
            // Get first teams from clubs
            $teams = Team::where('type', 'first_team')
                ->with('club')
                ->get();
            
            $this->info("Found {$teams->count()} teams to register");
            
            // Register teams to competition
            foreach ($teams as $team) {
                // Check if team is already registered
                $existingRegistration = DB::table('competition_club')
                    ->where('competition_id', $competition->id)
                    ->where('club_id', $team->club_id)
                    ->first();
                
                if (!$existingRegistration) {
                    DB::table('competition_club')->insert([
                        'competition_id' => $competition->id,
                        'club_id' => $team->club_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $this->line("Registered {$team->club->name} to {$competition->name}");
                } else {
                    $this->line("Team {$team->club->name} already registered to {$competition->name}");
                }
            }
            
            // Generate fixtures if they don't exist
            $this->generateFixtures($competition, $teams);
            
            // Initialize standings
            $this->initializeStandings($competition, $teams);
        }
        
        $this->info('Team registration and fixture generation completed!');
    }
    
    private function generateFixtures($competition, $teams)
    {
        $this->info("Generating fixtures for {$competition->name}");
        
        // Check if fixtures already exist
        $existingMatches = GameMatch::where('competition_id', $competition->id)->count();
        if ($existingMatches > 0) {
            $this->warn("Fixtures already exist for {$competition->name}, skipping...");
            return;
        }
        
        $teamIds = $teams->pluck('id')->toArray();
        $teamCount = count($teamIds);
        
        if ($teamCount < 2) {
            $this->warn("Not enough teams for fixtures in {$competition->name}");
            return;
        }
        
        // Generate round-robin fixtures
        $matchday = 1;
        $matchDate = Carbon::parse($competition->start_date);
        
        // For round-robin, each team plays every other team twice (home and away)
        for ($round = 0; $round < $teamCount - 1; $round++) {
            for ($i = 0; $i < $teamCount / 2; $i++) {
                $homeIndex = $i;
                $awayIndex = $teamCount - 1 - $i;
                
                if ($homeIndex >= $awayIndex) continue;
                
                $homeTeamId = $teamIds[$homeIndex];
                $awayTeamId = $teamIds[$awayIndex];
                
                // Create match
                $match = GameMatch::create([
                    'competition_id' => $competition->id,
                    'home_team_id' => $homeTeamId,
                    'away_team_id' => $awayTeamId,
                    'matchday' => $matchday,
                    'match_date' => $matchDate,
                    'venue' => 'Home Stadium',
                    'status' => 'scheduled',
                    'home_score' => null,
                    'away_score' => null,
                ]);
                
                // Create match sheet
                MatchSheet::create([
                    'match_id' => $match->id,
                    'competition_id' => $competition->id,
                    'home_team_id' => $homeTeamId,
                    'away_team_id' => $awayTeamId,
                    'match_date' => $matchDate,
                    'venue' => 'Home Stadium',
                    'status' => 'pending',
                    'referee_name' => 'TBD',
                    'assistant_referee_1' => 'TBD',
                    'assistant_referee_2' => 'TBD',
                    'fourth_official' => 'TBD',
                ]);
            }
            
            // Rotate teams for next round (except first team)
            $firstTeam = array_shift($teamIds);
            array_push($teamIds, $firstTeam);
            
            $matchday++;
            $matchDate->addDays(7); // Weekly matches
        }
        
        // Generate return fixtures (away teams become home teams)
        for ($round = 0; $round < $teamCount - 1; $round++) {
            for ($i = 0; $i < $teamCount / 2; $i++) {
                $homeIndex = $i;
                $awayIndex = $teamCount - 1 - $i;
                
                if ($homeIndex >= $awayIndex) continue;
                
                $homeTeamId = $teamIds[$homeIndex];
                $awayTeamId = $teamIds[$awayIndex];
                
                // Create match
                $match = GameMatch::create([
                    'competition_id' => $competition->id,
                    'home_team_id' => $homeTeamId,
                    'away_team_id' => $awayTeamId,
                    'matchday' => $matchday,
                    'match_date' => $matchDate,
                    'venue' => 'Home Stadium',
                    'status' => 'scheduled',
                    'home_score' => null,
                    'away_score' => null,
                ]);
                
                // Create match sheet
                MatchSheet::create([
                    'match_id' => $match->id,
                    'competition_id' => $competition->id,
                    'home_team_id' => $homeTeamId,
                    'away_team_id' => $awayTeamId,
                    'match_date' => $matchDate,
                    'venue' => 'Home Stadium',
                    'status' => 'pending',
                    'referee_name' => 'TBD',
                    'assistant_referee_1' => 'TBD',
                    'assistant_referee_2' => 'TBD',
                    'fourth_official' => 'TBD',
                ]);
            }
            
            // Rotate teams for next round (except first team)
            $firstTeam = array_shift($teamIds);
            array_push($teamIds, $firstTeam);
            
            $matchday++;
            $matchDate->addDays(7); // Weekly matches
        }
        
        $this->info("Generated fixtures for {$competition->name} with {$matchday} matchdays");
    }
    
    private function initializeStandings($competition, $teams)
    {
        $this->info("Initializing standings for {$competition->name}");
        
        // Check if standings already exist
        $existingStandings = CompetitionRanking::where('competition_id', $competition->id)->count();
        if ($existingStandings > 0) {
            $this->warn("Standings already exist for {$competition->name}, skipping...");
            return;
        }
        
        $standings = [];
        
        foreach ($teams as $team) {
            $standings[$team->id] = [
                'team_name' => $team->club->name,
                'club_name' => $team->club->name,
                'points' => 0,
                'played' => 0,
                'won' => 0,
                'drawn' => 0,
                'lost' => 0,
                'goals_for' => 0,
                'goals_against' => 0,
                'goal_difference' => 0,
                'form' => [],
            ];
        }
        
        // Create initial standings record
        CompetitionRanking::create([
            'competition_id' => $competition->id,
            'round' => 0,
            'standings' => json_encode($standings),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        $this->info("Initialized standings for {$competition->name}");
    }
}
