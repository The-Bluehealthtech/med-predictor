<?php

namespace App\Services;

use App\Models\Competition;
use App\Models\MatchModel;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class LeagueSchedulingService
{
    /**
     * Generate full schedule (home and away) with semi-random algorithm
     */
    public function generateFullSchedule(Competition $competition, string $startDate, int $matchIntervalDays = 7): array
    {
        try {
            DB::beginTransaction();

            $teams = $competition->teams;
            
            if ($teams->count() < 2) {
                throw new \Exception('At least 2 teams are required to generate a schedule');
            }

            // Step 1: Semi-random shuffle of teams
            $shuffledTeams = $teams->shuffle();
            Log::info("Teams shuffled for competition {$competition->id}: " . $shuffledTeams->pluck('name')->implode(', '));

            // Step 2: Generate home and away schedule
            $schedule = $this->generateHomeAndAwaySchedule($shuffledTeams);
            
            // Step 3: Create matches with proper scheduling
            $matches = $this->createScheduledMatches($competition, $schedule, $startDate, $matchIntervalDays);

            // Step 4: Insert matches into database
            MatchModel::insert($matches);

            DB::commit();

            $totalMatches = count($matches);
            $totalRounds = $totalMatches / ($teams->count() / 2);

            Log::info("Successfully generated full schedule for competition {$competition->id}: {$totalMatches} matches, {$totalRounds} rounds");

            return [
                'success' => true,
                'total_matches' => $totalMatches,
                'total_rounds' => $totalRounds,
                'teams_count' => $teams->count(),
                'start_date' => $startDate,
                'end_date' => Carbon::parse($startDate)->addDays(($totalRounds - 1) * $matchIntervalDays)->format('Y-m-d'),
                'schedule_type' => 'home_and_away',
                'generated_at' => now()->toISOString(),
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to generate full schedule for competition {$competition->id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate home and away schedule using improved round-robin
     */
    private function generateHomeAndAwaySchedule(Collection $teams): array
    {
        $teamIds = $teams->pluck('id')->toArray();
        $n = count($teamIds);
        
        // If odd number of teams, add a "bye" team
        if ($n % 2 !== 0) {
            $teamIds[] = null;
            $n++;
        }

        $schedule = [];
        $half = $n / 2;

        // Generate first half (home and away)
        for ($round = 0; $round < $n - 1; $round++) {
            $roundMatches = [];
            for ($i = 0; $i < $half; $i++) {
                $team1 = $teamIds[$i];
                $team2 = $teamIds[$n - 1 - $i];
                if ($team1 === null || $team2 === null) {
                    continue;
                }
                if ($round % 2 === 0) {
                    $roundMatches[] = ['home' => $team1, 'away' => $team2];
                } else {
                    $roundMatches[] = ['home' => $team2, 'away' => $team1];
                }
            }
            $schedule[] = $roundMatches;
            // Correct rotation: keep first team fixed, rotate the rest
            $fixed = $teamIds[0];
            $rotating = array_slice($teamIds, 1);
            array_unshift($rotating, array_pop($rotating));
            $teamIds = array_merge([$fixed], $rotating);
        }

        // Generate second half (reverse fixtures)
        $secondHalf = [];
        foreach ($schedule as $round) {
            $reverseRound = [];
            foreach ($round as $match) {
                $reverseRound[] = [
                    'home' => $match['away'],
                    'away' => $match['home']
                ];
            }
            $secondHalf[] = $reverseRound;
        }

        // Combine both halves
        return array_merge($schedule, $secondHalf);
    }

    /**
     * Create scheduled matches with proper dates and matchdays
     */
    private function createScheduledMatches(Competition $competition, array $schedule, string $startDate, int $matchIntervalDays): array
    {
        $matches = [];
        $currentDate = Carbon::parse($startDate);
        $matchday = 1;

        foreach ($schedule as $round => $roundMatches) {
            foreach ($roundMatches as $match) {
                // Skip matches with BYE team
                if ($match['home'] === null || $match['away'] === null) {
                    continue;
                }

                $matches[] = [
                    'competition_id' => $competition->id,
                    'home_team_id' => $match['home'],
                    'away_team_id' => $match['away'],
                    'matchday' => $matchday,
                    'kickoff_time' => $currentDate->copy(),
                    'match_status' => 'scheduled',
                    'venue' => $this->getDefaultVenue($competition),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            $currentDate->addDays($matchIntervalDays);
            $matchday++;
        }

        return $matches;
    }

    /**
     * Get default venue for the competition
     */
    private function getDefaultVenue(Competition $competition): string
    {
        // You can implement venue selection logic here
        // For now, return a default venue
        return 'Stade Principal';
    }

    /**
     * Generate round-robin schedule for a competition (legacy method)
     */
    public function generateRoundRobinSchedule(Competition $competition, string $startDate, int $matchIntervalDays): array
    {
        $teams = $competition->teams;
        
        if ($teams->count() < 2) {
            throw new \Exception('At least 2 teams are required to generate a schedule');
        }

        // If odd number of teams, add a "bye" team
        if ($teams->count() % 2 !== 0) {
            $byeTeam = new Team();
            $byeTeam->name = 'BYE';
            $byeTeam->id = null;
            $teams = $teams->push($byeTeam);
        }

        $teamIds = $teams->pluck('id')->toArray();
        $schedule = $this->generateRoundRobin($teamIds);
        
        $matches = [];
        $currentDate = Carbon::parse($startDate);
        $matchday = 1;

        foreach ($schedule as $round => $roundMatches) {
            foreach ($roundMatches as $match) {
                // Skip matches with BYE team
                if ($match['home'] === null || $match['away'] === null) {
                    continue;
                }

                $homeTeam = $teams->firstWhere('id', $match['home']);
                $awayTeam = $teams->firstWhere('id', $match['away']);

                if ($homeTeam && $awayTeam) {
                    $matches[] = [
                        'competition_id' => $competition->id,
                        'home_team_id' => $homeTeam->id,
                        'away_team_id' => $awayTeam->id,
                        'matchday' => $matchday,
                        'kickoff_time' => $currentDate->copy(),
                        'status' => 'scheduled',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
            
            $currentDate->addDays($matchIntervalDays);
            $matchday++;
        }

        // Insert matches into database
        MatchModel::insert($matches);

        return [
            'total_matches' => count($matches),
            'total_rounds' => count($schedule),
            'start_date' => $startDate,
            'end_date' => $currentDate->subDays($matchIntervalDays)->format('Y-m-d'),
        ];
    }

    /**
     * Generate round-robin schedule using circle method (legacy)
     */
    private function generateRoundRobin(array $teams): array
    {
        $n = count($teams);
        $rounds = [];
        
        // If odd number, add a "bye" team
        if ($n % 2 !== 0) {
            $teams[] = null;
            $n++;
        }

        $half = $n / 2;
        
        for ($round = 0; $round < $n - 1; $round++) {
            $roundMatches = [];
            
            for ($i = 0; $i < $half; $i++) {
                $team1 = $teams[$i];
                $team2 = $teams[$n - 1 - $i];
                
                // Alternate home/away for each round
                if ($round % 2 === 0) {
                    $roundMatches[] = ['home' => $team1, 'away' => $team2];
                } else {
                    $roundMatches[] = ['home' => $team2, 'away' => $team1];
                }
            }
            
            $rounds[] = $roundMatches;
            
            // Rotate teams (keep first team fixed, rotate the rest)
            $lastTeam = array_pop($teams);
            array_splice($teams, 1, 0, $lastTeam);
        }
        
        return $rounds;
    }

    /**
     * Get upcoming matches with caching
     */
    public function getUpcomingMatches(Competition $competition, int $limit = 10): Collection
    {
        $cacheKey = "upcoming_matches_{$competition->id}_{$limit}";
        
        return Cache::remember($cacheKey, 300, function () use ($competition, $limit) {
            return MatchModel::where('competition_id', $competition->id)
                ->where('match_status', 'scheduled')
                ->where('kickoff_time', '>=', now())
                ->with([
                    'homeTeam:id,name,club_id',
                    'awayTeam:id,name,club_id',
                    'competition:id,name'
                ])
                ->orderBy('kickoff_time')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get matches by matchday with caching
     */
    public function getMatchesByMatchday(Competition $competition, int $matchday): Collection
    {
        $cacheKey = "matches_matchday_{$competition->id}_{$matchday}";
        
        return Cache::remember($cacheKey, 300, function () use ($competition, $matchday) {
            return MatchModel::where('competition_id', $competition->id)
                ->where('matchday', $matchday)
                ->with([
                    'homeTeam:id,name,club_id',
                    'awayTeam:id,name,club_id',
                    'officials.user:id,name'
                ])
                ->orderBy('kickoff_time')
                ->get();
        });
    }

    /**
     * Clear all caches for a competition
     */
    public function clearCompetitionCache(Competition $competition): void
    {
        // Clear upcoming matches cache
        Cache::forget("upcoming_matches_{$competition->id}_10");
        Cache::forget("upcoming_matches_{$competition->id}_20");
        
        // Clear matchday caches (clear first 50 matchdays)
        for ($i = 1; $i <= 50; $i++) {
            Cache::forget("matches_matchday_{$competition->id}_{$i}");
        }
        
        // Clear schedule cache
        Cache::forget("competition_schedule_{$competition->id}");
    }

    /**
     * Reschedule matches for a competition
     */
    public function rescheduleMatches(Competition $competition, string $newStartDate, int $matchIntervalDays): array
    {
        // Delete existing scheduled matches
        $competition->matches()
            ->where('match_status', 'scheduled')
            ->delete();

        // Generate new schedule
        return $this->generateFullSchedule($competition, $newStartDate, $matchIntervalDays);
    }

    /**
     * Validate manual match creation
     */
    public function validateManualMatch(Competition $competition, array $matchData): array
    {
        $errors = [];
        
        // Check team conflicts
        $teamConflicts = $this->checkTeamConflicts($competition, $matchData);
        if (!empty($teamConflicts)) {
            $errors = array_merge($errors, $teamConflicts);
        }

        // Check venue conflicts
        $venueConflicts = $this->checkVenueConflicts($competition, $matchData);
        if (!empty($venueConflicts)) {
            $errors = array_merge($errors, $venueConflicts);
        }

        // Check duplicate matches
        $duplicateMatches = $this->checkDuplicateMatches($competition, $matchData);
        if (!empty($duplicateMatches)) {
            $errors = array_merge($errors, $duplicateMatches);
        }

        return $errors;
    }

    /**
     * Check for team scheduling conflicts
     */
    private function checkTeamConflicts(Competition $competition, array $matchData): array
    {
        $errors = [];
        $matchDate = Carbon::parse($matchData['kickoff_time'])->startOfDay();
        
        // Check if home team has another match on the same day
        $homeTeamConflict = MatchModel::where('competition_id', $competition->id)
            ->where('home_team_id', $matchData['home_team_id'])
            ->whereDate('kickoff_time', $matchDate)
            ->exists();

        if ($homeTeamConflict) {
            $homeTeam = Team::find($matchData['home_team_id']);
            $errors[] = "L'équipe '{$homeTeam->name}' a déjà un match programmé ce jour-là.";
        }

        // Check if away team has another match on the same day
        $awayTeamConflict = MatchModel::where('competition_id', $competition->id)
            ->where('away_team_id', $matchData['away_team_id'])
            ->whereDate('kickoff_time', $matchDate)
            ->exists();

        if ($awayTeamConflict) {
            $awayTeam = Team::find($matchData['away_team_id']);
            $errors[] = "L'équipe '{$awayTeam->name}' a déjà un match programmé ce jour-là.";
        }

        return $errors;
    }

    /**
     * Check for venue scheduling conflicts
     */
    private function checkVenueConflicts(Competition $competition, array $matchData): array
    {
        $errors = [];
        $matchDateTime = Carbon::parse($matchData['kickoff_time']);
        $venue = $matchData['venue'] ?? 'Stade Principal';

        // Check if venue is already booked for the same time slot
        $venueConflict = MatchModel::where('competition_id', $competition->id)
            ->where('venue', $venue)
            ->where('kickoff_time', $matchDateTime)
            ->exists();

        if ($venueConflict) {
            $errors[] = "Le terrain '{$venue}' est déjà réservé pour ce créneau horaire.";
        }

        return $errors;
    }

    /**
     * Check for duplicate matches
     */
    private function checkDuplicateMatches(Competition $competition, array $matchData): array
    {
        $errors = [];

        // Check if this exact match already exists
        $duplicateMatch = MatchModel::where('competition_id', $competition->id)
            ->where('home_team_id', $matchData['home_team_id'])
            ->where('away_team_id', $matchData['away_team_id'])
            ->exists();

        if ($duplicateMatch) {
            $homeTeam = Team::find($matchData['home_team_id']);
            $awayTeam = Team::find($matchData['away_team_id']);
            $errors[] = "Le match '{$homeTeam->name} vs {$awayTeam->name}' a déjà été programmé.";
        }

        return $errors;
    }
} 