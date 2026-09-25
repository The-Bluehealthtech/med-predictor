<?php

namespace App\Services;

use App\Models\Team;
use App\Models\Player;
use App\Models\Lineup;
use App\Models\LineupPlayer;
use App\Models\GameMatch;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class LineupGenerationService
{
    /**
     * Generate an optimal lineup for a team in a specific match
     */
    public function generateLineupForMatch(Team $team, GameMatch $match, array $options = []): Lineup
    {
        $formation = $options['formation'] ?? $team->formation ?? '4-4-2';
        $tacticalStyle = $options['tactical_style'] ?? 'balanced';
        $includeSubstitutes = $options['include_substitutes'] ?? true;
        $maxSubstitutes = $options['max_substitutes'] ?? 7;

        // Get available players for the team
        $availablePlayers = $this->getAvailablePlayers($team, $match);

        // Generate starting XI
        $starters = $this->generateStartingXI($availablePlayers, $formation, $tacticalStyle);

        // Generate substitutes if requested
        $substitutes = collect();
        if ($includeSubstitutes) {
            $substitutes = $this->generateSubstitutes($availablePlayers, $starters, $maxSubstitutes);
        }

        // Create the lineup
        $lineup = $this->createLineup($team, $match, $formation, $starters, $substitutes, $options);

        return $lineup;
    }

    /**
     * Get available players for a team (not injured, suspended, etc.)
     */
    private function getAvailablePlayers(Team $team, GameMatch $match): Collection
    {
        return $team->players()
            ->where('fitness_status', 'fit')
            ->where('suspension_status', '!=', 'suspended')
            ->orderBy('overall_rating', 'desc')
            ->get();
    }

    /**
     * Generate starting XI based on formation and tactical style
     */
    private function generateStartingXI(Collection $players, string $formation, string $tacticalStyle): Collection
    {
        $formationPositions = $this->parseFormation($formation);
        $starters = collect();

        foreach ($formationPositions as $position => $count) {
            $positionPlayers = $this->selectPlayersForPosition($players, $position, $count, $tacticalStyle);
            $starters = $starters->merge($positionPlayers);
        }

        return $starters;
    }

    /**
     * Parse formation string into position requirements
     */
    private function parseFormation(string $formation): array
    {
        $parts = explode('-', $formation);
        
        if (count($parts) === 2) {
            // 4-4-2 style
            return [
                'GK' => 1,
                'CB' => $parts[0],
                'CM' => $parts[1],
                'ST' => 2
            ];
        } elseif (count($parts) === 3) {
            // 4-3-3 style
            return [
                'GK' => 1,
                'CB' => $parts[0],
                'CM' => $parts[1],
                'ST' => $parts[2]
            ];
        } elseif (count($parts) === 4) {
            // 4-2-3-1 style
            return [
                'GK' => 1,
                'CB' => $parts[0],
                'CDM' => $parts[1],
                'CAM' => $parts[2],
                'ST' => $parts[3]
            ];
        }

        // Default to 4-4-2
        return [
            'GK' => 1,
            'CB' => 4,
            'CM' => 4,
            'ST' => 2
        ];
    }

    /**
     * Select players for a specific position
     */
    private function selectPlayersForPosition(Collection $players, string $position, int $count, string $tacticalStyle): Collection
    {
        $positionMap = $this->getPositionMap();
        $targetPositions = $positionMap[$position] ?? [$position];

        $availablePlayers = $players->filter(function ($player) use ($targetPositions) {
            return in_array($player->position, $targetPositions);
        });

        // Apply tactical style preferences
        $availablePlayers = $this->applyTacticalStyle($availablePlayers, $position, $tacticalStyle);

        return $availablePlayers->take($count);
    }

    /**
     * Get position mapping for flexible player selection
     */
    private function getPositionMap(): array
    {
        return [
            'GK' => ['GK'],
            'CB' => ['CB', 'RB', 'LB'],
            'RB' => ['RB', 'CB'],
            'LB' => ['LB', 'CB'],
            'CDM' => ['CDM', 'CM'],
            'CM' => ['CM', 'CDM', 'CAM'],
            'CAM' => ['CAM', 'CM', 'ST'],
            'RW' => ['RW', 'ST', 'CAM'],
            'LW' => ['LW', 'ST', 'CAM'],
            'ST' => ['ST', 'CAM', 'RW', 'LW']
        ];
    }

    /**
     * Apply tactical style preferences to player selection
     */
    private function applyTacticalStyle(Collection $players, string $position, string $tacticalStyle): Collection
    {
        switch ($tacticalStyle) {
            case 'attacking':
                return $this->applyAttackingStyle($players, $position);
            case 'defensive':
                return $this->applyDefensiveStyle($players, $position);
            case 'possession':
                return $this->applyPossessionStyle($players, $position);
            case 'counter':
                return $this->applyCounterStyle($players, $position);
            default:
                return $players;
        }
    }

    /**
     * Apply attacking style preferences
     */
    private function applyAttackingStyle(Collection $players, string $position): Collection
    {
        if (in_array($position, ['ST', 'CAM', 'RW', 'LW'])) {
            return $players->sortByDesc('attacking_rating');
        } elseif (in_array($position, ['CM', 'CDM'])) {
            return $players->sortByDesc('passing_rating');
        }
        return $players;
    }

    /**
     * Apply defensive style preferences
     */
    private function applyDefensiveStyle(Collection $players, string $position): Collection
    {
        if (in_array($position, ['CB', 'RB', 'LB'])) {
            return $players->sortByDesc('defending_rating');
        } elseif (in_array($position, ['CDM', 'CM'])) {
            return $players->sortByDesc('defending_rating');
        }
        return $players;
    }

    /**
     * Apply possession style preferences
     */
    private function applyPossessionStyle(Collection $players, string $position): Collection
    {
        if (in_array($position, ['CM', 'CDM', 'CAM'])) {
            return $players->sortByDesc('passing_rating');
        }
        return $players;
    }

    /**
     * Apply counter-attacking style preferences
     */
    private function applyCounterStyle(Collection $players, string $position): Collection
    {
        if (in_array($position, ['ST', 'RW', 'LW'])) {
            return $players->sortByDesc('pace_rating');
        } elseif (in_array($position, ['CM', 'CDM'])) {
            return $players->sortByDesc('passing_rating');
        }
        return $players;
    }

    /**
     * Generate substitutes
     */
    private function generateSubstitutes(Collection $players, Collection $starters, int $maxSubstitutes): Collection
    {
        $usedPlayerIds = $starters->pluck('id');
        $availablePlayers = $players->whereNotIn('id', $usedPlayerIds);

        // Ensure we have a goalkeeper substitute
        $goalkeeperSub = $availablePlayers->where('position', 'GK')->first();
        $substitutes = collect();

        if ($goalkeeperSub) {
            $substitutes->push($goalkeeperSub);
            $availablePlayers = $availablePlayers->where('id', '!=', $goalkeeperSub->id);
        }

        // Add remaining substitutes (prioritize by overall rating)
        $remainingSubs = $availablePlayers->sortByDesc('overall_rating')->take($maxSubstitutes - $substitutes->count());
        $substitutes = $substitutes->merge($remainingSubs);

        return $substitutes;
    }

    /**
     * Create the lineup record
     */
    private function createLineup(Team $team, GameMatch $match, string $formation, Collection $starters, Collection $substitutes, array $options): Lineup
    {
        return DB::transaction(function () use ($team, $match, $formation, $starters, $substitutes, $options) {
            // Create the lineup
            $lineup = Lineup::create([
                'team_id' => $team->id,
                'club_id' => $team->club_id,
                'competition_id' => $match->competition_id,
                'match_id' => $match->id,
                'name' => $options['name'] ?? "Auto-generated lineup for {$match->homeTeam->name} vs {$match->awayTeam->name}",
                'formation' => $formation,
                'match_type' => 'league',
                'opponent' => $match->home_team_id === $team->id ? $match->awayTeam->name : $match->homeTeam->name,
                'venue' => $match->home_team_id === $team->id ? 'home' : 'away',
                'weather_conditions' => $match->weather_conditions,
                'pitch_condition' => $match->pitch_condition,
                'pressing_intensity' => $options['pressing_intensity'] ?? 'medium',
                'possession_style' => $options['possession_style'] ?? 'balanced',
                'defensive_line_height' => $options['defensive_line_height'] ?? 'medium',
                'marking_system' => $options['marking_system'] ?? 'zone',
                'captain_id' => $this->selectCaptain($starters),
                'vice_captain_id' => $this->selectViceCaptain($starters),
                'penalty_taker_id' => $this->selectPenaltyTaker($starters),
                'free_kick_taker_id' => $this->selectFreeKickTaker($starters),
                'corner_taker_id' => $this->selectCornerTaker($starters),
                'status' => 'planned',
                'created_by' => auth()->id() ?? 1
            ]);

            // Add starters
            $positionOrder = 1;
            foreach ($starters as $player) {
                $assignedPosition = $this->getAssignedPosition($player, $formation);
                LineupPlayer::create([
                    'lineup_id' => $lineup->id,
                    'player_id' => $player->id,
                    'is_substitute' => false,
                    'position_order' => $positionOrder++,
                    'assigned_position' => $assignedPosition,
                    'fitness_status' => 'fit'
                ]);
            }

            // Add substitutes
            foreach ($substitutes as $player) {
                LineupPlayer::create([
                    'lineup_id' => $lineup->id,
                    'player_id' => $player->id,
                    'is_substitute' => true,
                    'position_order' => $positionOrder++,
                    'assigned_position' => 'SUB',
                    'fitness_status' => 'fit'
                ]);
            }

            return $lineup->load(['players', 'team', 'competition', 'match']);
        });
    }

    /**
     * Select captain (highest rated player with leadership qualities)
     */
    private function selectCaptain(Collection $starters): ?int
    {
        return $starters->sortByDesc('overall_rating')->first()?->id;
    }

    /**
     * Select vice captain (second highest rated player)
     */
    private function selectViceCaptain(Collection $starters): ?int
    {
        return $starters->sortByDesc('overall_rating')->skip(1)->first()?->id;
    }

    /**
     * Select penalty taker (best shooting/finishing)
     */
    private function selectPenaltyTaker(Collection $starters): ?int
    {
        return $starters->sortByDesc('shooting_rating')->first()?->id;
    }

    /**
     * Select free kick taker (best free kick accuracy)
     */
    private function selectFreeKickTaker(Collection $starters): ?int
    {
        return $starters->sortByDesc('free_kick_accuracy')->first()?->id;
    }

    /**
     * Select corner taker (best crossing)
     */
    private function selectCornerTaker(Collection $starters): ?int
    {
        return $starters->sortByDesc('crossing_rating')->first()?->id;
    }

    /**
     * Get assigned position based on player and formation
     */
    private function getAssignedPosition(Player $player, string $formation): string
    {
        // For now, return the player's natural position
        // This could be enhanced to map to formation-specific positions
        return $player->position;
    }

    /**
     * Generate lineups for all upcoming matches
     */
    public function generateLineupsForUpcomingMatches(array $options = []): array
    {
        $upcomingMatches = GameMatch::where('match_status', 'scheduled')
            ->where('match_date', '>=', now())
            ->with(['homeTeam', 'awayTeam'])
            ->get();

        $generated = [];
        $errors = [];

        foreach ($upcomingMatches as $match) {
            try {
                // Generate for home team
                $homeLineup = $this->generateLineupForMatch($match->home_team, $match, $options);
                $generated[] = $homeLineup;

                // Generate for away team
                $awayLineup = $this->generateLineupForMatch($match->away_team, $match, $options);
                $generated[] = $awayLineup;
            } catch (\Exception $e) {
                $errors[] = "Failed to generate lineup for match {$match->id}: " . $e->getMessage();
            }
        }

        return [
            'generated' => $generated,
            'errors' => $errors,
            'total_matches' => $upcomingMatches->count(),
            'total_lineups' => count($generated)
        ];
    }

    /**
     * Generate lineup for a specific team and match
     */
    public function generateLineupForTeamMatch(int $teamId, int $matchId, array $options = []): ?Lineup
    {
        $team = Team::findOrFail($teamId);
        $match = GameMatch::findOrFail($matchId);

        // Check if lineup already exists
        $existingLineup = Lineup::where('team_id', $teamId)
            ->where('match_id', $matchId)
            ->first();

        if ($existingLineup) {
            return $existingLineup;
        }

        return $this->generateLineupForMatch($team, $match, $options);
    }
} 