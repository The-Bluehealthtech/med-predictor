<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Competition;
use App\Models\GameMatch;
use App\Models\MatchSheet;
use App\Models\User;
use App\Models\MatchEvent;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GenerateEPLResults extends Command
{
    protected $signature = 'epl:generate-results';
    protected $description = 'Generate realistic results, fill match sheets, and update rankings for all EPL matches';

    public function handle()
    {
        $this->info('Starting EPL results generation...');
        $competition = Competition::where('short_name', 'EPL')->first();
        if (!$competition) {
            $this->error('EPL competition not found.');
            return 1;
        }

        $matches = GameMatch::where('competition_id', $competition->id)->get();
        if ($matches->isEmpty()) {
            $this->error('No matches found for EPL competition.');
            return 1;
        }

        $this->info("Found {$matches->count()} matches to process.");
        
        $faker = \Faker\Factory::create();
        $now = Carbon::now();
        $matchCount = 0;
        $totalMatches = $matches->count();
        $totalSheets = 0;
        $totalEvents = 0;

        $referees = User::where('role', 'referee')->get();
        if ($referees->isEmpty()) {
            $this->error('No referees found.');
            return 1;
        }

        $refereeCount = $referees->count();

        DB::beginTransaction();
        try {
            foreach ($matches as $i => $match) {
                // Generate realistic scores
                $homeAdv = $faker->biasedNumberBetween(0, 1, function($x) { return $x; });
                $drawChance = $faker->numberBetween(1, 100) <= 25; // 25% draw
                if ($drawChance) {
                    $goals = $faker->numberBetween(0, 2);
                    $homeScore = $goals;
                    $awayScore = $goals;
                } else {
                    $homeScore = $faker->numberBetween(0, 4) + $homeAdv;
                    $awayScore = $faker->numberBetween(0, 3);
                }
                $homeScore = min($homeScore, 6);
                $awayScore = min($awayScore, 6);

                $match->home_score = $homeScore;
                $match->away_score = $awayScore;
                $match->match_status = 'completed';
                $match->save();

                // Assign referees
                $mainRef = $referees[$i % $refereeCount];
                $assistant1 = $referees[($i+1) % $refereeCount];
                $assistant2 = $referees[($i+2) % $refereeCount];
                $fourth = $referees[($i+3) % $refereeCount];

                // Fill or create match sheet
                $sheet = MatchSheet::firstOrNew(['match_id' => $match->id]);
                $sheet->fill([
                    'stadium_venue' => $faker->randomElement(['Old Trafford','Anfield','Stamford Bridge','Emirates','Etihad','Tottenham Hotspur Stadium','Goodison Park','Villa Park','Molineux','Selhurst Park','St. James Park','King Power','London Stadium','Bramall Lane','Turf Moor','Craven Cottage','Brentford Community','Amex','Vitality','Kenilworth Road']),
                    'weather_conditions' => $faker->randomElement(['Clear','Rainy','Cloudy','Windy','Foggy']),
                    'pitch_conditions' => $faker->randomElement(['Excellent','Good','Average','Wet','Slippery']),
                    'kickoff_time' => $match->scheduled_at ?? $now->copy()->addDays($i),
                    'home_team_score' => $homeScore,
                    'away_team_score' => $awayScore,
                    'main_referee_id' => $mainRef->id,
                    'assistant_referee_1_id' => $assistant1->id,
                    'assistant_referee_2_id' => $assistant2->id,
                    'fourth_official_id' => $fourth->id,
                    'referee_report' => $faker->paragraph(3),
                    'match_status' => 'completed',
                    'match_statistics' => [
                        'shots' => [$faker->numberBetween(5,20), $faker->numberBetween(5,20)],
                        'shots_on_target' => [$faker->numberBetween(2,10), $faker->numberBetween(2,10)],
                        'possession' => [$faker->numberBetween(40,60), 100],
                        'fouls' => [$faker->numberBetween(5,20), $faker->numberBetween(5,20)],
                        'yellow_cards' => [$faker->numberBetween(0,4), $faker->numberBetween(0,4)],
                        'red_cards' => [$faker->numberBetween(0,1), $faker->numberBetween(0,1)],
                        'offsides' => [$faker->numberBetween(0,3), $faker->numberBetween(0,3)],
                        'corners' => [$faker->numberBetween(0,10), $faker->numberBetween(0,10)],
                    ],
                    'notes' => $faker->sentence(),
                    'referee_signed_at' => $now->copy()->addMinutes($i),
                    'status' => 'validated',
                    'validated_at' => $now->copy()->addMinutes($i),
                ]);
                $sheet->save();
                $sheet->refresh(); // Ensure ID is set
                if (!$sheet->id) {
                    $this->warn('No MatchSheet ID for match ' . $match->id . ', skipping events.');
                    continue;
                }
                $totalSheets++;

                // Generate match events (goals, cards, substitutions)
                MatchEvent::where('match_sheet_id', $sheet->id)->delete();
                $homePlayers = $match->homeTeam ? $match->homeTeam->players()->pluck('player_id')->toArray() : [];
                $awayPlayers = $match->awayTeam ? $match->awayTeam->players()->pluck('player_id')->toArray() : [];
                for ($g = 0; $g < $homeScore; $g++) {
                    MatchEvent::create([
                        'match_sheet_id' => $sheet->id,
                        'team_id' => $match->home_team_id,
                        'type' => 'goal',
                        'minute' => $faker->numberBetween(1, 90),
                        'player_id' => $faker->randomElement($homePlayers),
                        'description' => 'Goal for home team',
                    ]);
                    $totalEvents++;
                }
                for ($g = 0; $g < $awayScore; $g++) {
                    MatchEvent::create([
                        'match_sheet_id' => $sheet->id,
                        'team_id' => $match->away_team_id,
                        'type' => 'goal',
                        'minute' => $faker->numberBetween(1, 90),
                        'player_id' => $faker->randomElement($awayPlayers),
                        'description' => 'Goal for away team',
                    ]);
                    $totalEvents++;
                }
                // Cards
                for ($c = 0; $c < $faker->numberBetween(0, 4); $c++) {
                    $teamId = $faker->randomElement([$match->home_team_id, $match->away_team_id]);
                    $playerList = $teamId == $match->home_team_id ? $homePlayers : $awayPlayers;
                    MatchEvent::create([
                        'match_sheet_id' => $sheet->id,
                        'team_id' => $teamId,
                        'type' => 'yellow_card',
                        'minute' => $faker->numberBetween(1, 90),
                        'player_id' => $faker->randomElement($playerList),
                        'description' => 'Yellow card',
                    ]);
                    $totalEvents++;
                }
                for ($c = 0; $c < $faker->numberBetween(0, 1); $c++) {
                    $teamId = $faker->randomElement([$match->home_team_id, $match->away_team_id]);
                    $playerList = $teamId == $match->home_team_id ? $homePlayers : $awayPlayers;
                    MatchEvent::create([
                        'match_sheet_id' => $sheet->id,
                        'team_id' => $teamId,
                        'type' => 'red_card',
                        'minute' => $faker->numberBetween(1, 90),
                        'player_id' => $faker->randomElement($playerList),
                        'description' => 'Red card',
                    ]);
                    $totalEvents++;
                }
                $matchCount++;
            }
            
            // Recalculate and update standings
            $this->info('Recalculating competition standings...');
            $this->recalculateStandings($competition);
            
            DB::commit();
            
            $this->info('EPL results generation completed successfully!');
            $this->info("Generated results for {$totalMatches} matches");
            $this->info("Created/updated {$totalSheets} match sheets");
            $this->info("Generated {$totalEvents} match events");
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
        
        return 0;
    }
    
    private function recalculateStandings($competition)
    {
        // Get all teams in the competition
        $teams = $competition->teams()->with('club')->get();
        
        // Calculate standings for each team
        $standings = collect();
        
        foreach ($teams as $team) {
            $matches = GameMatch::where('competition_id', $competition->id)
                ->where(function ($query) use ($team) {
                    $query->where('home_team_id', $team->id)
                          ->orWhere('away_team_id', $team->id);
                })
                ->whereNotNull('home_score')
                ->whereNotNull('away_score')
                ->get();
            
            $points = 0;
            $played = $matches->count();
            $won = 0;
            $drawn = 0;
            $lost = 0;
            $goalsFor = 0;
            $goalsAgainst = 0;
            $form = [];
            
            foreach ($matches as $match) {
                if ($match->home_team_id === $team->id) {
                    $goalsFor += $match->home_score ?? 0;
                    $goalsAgainst += $match->away_score ?? 0;
                    
                    if (($match->home_score ?? 0) > ($match->away_score ?? 0)) {
                        $won++;
                        $points += 3;
                        $form[] = 'W';
                    } elseif (($match->home_score ?? 0) === ($match->away_score ?? 0)) {
                        $drawn++;
                        $points += 1;
                        $form[] = 'D';
                    } else {
                        $lost++;
                        $form[] = 'L';
                    }
                } else {
                    $goalsFor += $match->away_score ?? 0;
                    $goalsAgainst += $match->home_score ?? 0;
                    
                    if (($match->away_score ?? 0) > ($match->home_score ?? 0)) {
                        $won++;
                        $points += 3;
                        $form[] = 'W';
                    } elseif (($match->away_score ?? 0) === ($match->home_score ?? 0)) {
                        $drawn++;
                        $points += 1;
                        $form[] = 'D';
                    } else {
                        $lost++;
                        $form[] = 'L';
                    }
                }
            }
            
            $standings->push([
                'team_id' => $team->id,
                'team_name' => $team->name,
                'club_name' => $team->club ? $team->club->name : $team->name,
                'points' => $points,
                'played' => $played,
                'won' => $won,
                'drawn' => $drawn,
                'lost' => $lost,
                'goals_for' => $goalsFor,
                'goals_against' => $goalsAgainst,
                'goal_difference' => $goalsFor - $goalsAgainst,
                'form' => $form,
            ]);
        }
        
        // Sort standings by points (desc), goal difference (desc), goals scored (desc)
        $standings = $standings->sortByDesc('points')
            ->sortByDesc('goal_difference')
            ->sortByDesc('goals_for')
            ->values();
        
        // Convert to array format for storage
        $standingsArray = [];
        foreach ($standings as $index => $standing) {
            $standingsArray[$standing['team_id']] = [
                'position' => $index + 1,
                'team_name' => $standing['team_name'],
                'club_name' => $standing['club_name'],
                'points' => $standing['points'],
                'played' => $standing['played'],
                'won' => $standing['won'],
                'drawn' => $standing['drawn'],
                'lost' => $standing['lost'],
                'goals_for' => $standing['goals_for'],
                'goals_against' => $standing['goals_against'],
                'goal_difference' => $standing['goal_difference'],
                'form' => $standing['form'],
            ];
        }
        
        // Get the latest round number
        $latestRound = GameMatch::where('competition_id', $competition->id)
            ->whereNotNull('home_score')
            ->whereNotNull('away_score')
            ->max('matchday') ?? 0;
        
        // Store in competition_rankings table
        DB::table('competition_rankings')->updateOrInsert(
            [
                'competition_id' => $competition->id,
                'round' => $latestRound
            ],
            [
                'standings' => json_encode($standingsArray),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
        
        $this->info("Standings updated for round {$latestRound} with " . count($standingsArray) . " teams");
    }
} 