<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Player;
use App\Models\MatchModel;
use App\Models\Team;
use App\Models\Competition;
use App\Models\Season;
use App\Models\PlayerMatchDetailedStats;
use Carbon\Carbon;

class PlayerPerformanceStatsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding player performance statistics...');
        
        // Créer des saisons et compétitions si elles n'existent pas
        $this->createSeasonsAndCompetitions();
        
        // Créer des équipes si elles n'existent pas
        $this->createTeams();
        
        // Créer des matchs
        $this->createMatches();
        
        // Créer des statistiques détaillées pour chaque joueur
        $this->createPlayerMatchStats();
        
        $this->command->info('Player performance statistics seeded successfully!');
    }

    private function createSeasonsAndCompetitions()
    {
        // Créer la saison 2024-25 si elle n'existe pas
        $season = Season::firstOrCreate(
            ['short_name' => '2024-25'],
            [
                'name' => '2024-25 Season',
                'short_name' => '2024-25',
                'start_date' => '2024-08-01',
                'end_date' => '2025-05-31',
                'registration_start_date' => '2024-07-01',
                'registration_end_date' => '2024-08-31',
                'status' => 'active',
                'is_current' => true,
                'description' => 'Saison 2024-2025',
                'created_by' => 1,
                'updated_by' => 1
            ]
        );

        // Créer des compétitions si elles n'existent pas
        $competitions = [
            'Premier League' => 'league',
            'FA Cup' => 'cup',
            'Champions League' => 'champions_league',
            'Ligue 1' => 'league',
            'La Liga' => 'league',
            'Bundesliga' => 'league',
            'Serie A' => 'league'
        ];

        foreach ($competitions as $name => $type) {
            Competition::firstOrCreate(
                ['name' => $name],
                [
                    'type' => $type,
                    'season' => $season->short_name,
                    'status' => 'active',
                    'description' => $name . ' ' . $season->short_name
                ]
            );
        }
    }

    private function createTeams()
    {
        $teams = [
            'Manchester United', 'Manchester City', 'Chelsea', 'Arsenal', 'Liverpool',
            'Tottenham', 'Barcelona', 'Real Madrid', 'Atletico Madrid', 'Bayern Munich',
            'Borussia Dortmund', 'PSG', 'Juventus', 'AC Milan', 'Inter Milan'
        ];

        foreach ($teams as $teamName) {
            Team::firstOrCreate(
                ['name' => $teamName],
                [
                    'type' => 'first_team',
                    'formation' => '4-3-3',
                    'tactical_style' => 'possession',
                    'playing_philosophy' => 'Attacking football with high pressing',
                    'coach_name' => 'Coach ' . $teamName,
                    'assistant_coach' => 'Assistant ' . $teamName,
                    'status' => 'active',
                    'season' => '2024-25',
                    'competition_level' => 'elite',
                    'home_ground' => $teamName . ' Stadium',
                    'capacity' => rand(40000, 80000),
                    'colors' => 'Red, White, Black',
                    'club_id' => 1, // Utiliser un club existant ou créer un club par défaut
                    'founded_year' => rand(1880, 1950)
                ]
            );
        }
    }

    private function createMatches()
    {
        $teams = Team::all();
        $competitions = Competition::all();
        $season = Season::where('name', '2024-25')->first();

        if ($teams->count() < 2) {
            $this->command->warn('Not enough teams to create matches');
            return;
        }

        // Créer 50 matchs sur la saison
        for ($i = 0; $i < 50; $i++) {
            $homeTeam = $teams->random();
            $awayTeam = $teams->where('id', '!=', $homeTeam->id)->random();
            $competition = $competitions->random();
            
            // Date aléatoire dans la saison
            $matchDate = Carbon::create(2024, 8, 1)->addDays(rand(0, 300));
            
            MatchModel::firstOrCreate(
                [
                    'home_team_id' => $homeTeam->id,
                    'away_team_id' => $awayTeam->id,
                    'match_date' => $matchDate
                ],
                [
                    'name' => $homeTeam->name . ' vs ' . $awayTeam->name,
                    'competition_id' => $competition->id,
                    'home_club_id' => $homeTeam->club_id,
                    'away_club_id' => $awayTeam->club_id,
                    'kickoff_time' => $matchDate,
                    'venue' => $homeTeam->home_ground,
                    'stadium' => $homeTeam->home_ground,
                    'capacity' => $homeTeam->capacity,
                    'attendance' => rand(30000, 80000),
                    'weather_conditions' => ['sunny', 'cloudy', 'rainy', 'windy'][rand(0, 3)],
                    'pitch_condition' => ['excellent', 'good', 'fair'][rand(0, 2)],
                    'referee' => 'Referee ' . rand(1, 20),
                    'match_status' => 'completed',
                    'home_score' => rand(0, 4),
                    'away_score' => rand(0, 4),
                    'created_at' => $matchDate,
                    'updated_at' => $matchDate
                ]
            );
        }
    }

    private function createPlayerMatchStats()
    {
        $players = Player::all();
        $matches = MatchModel::all();
        $teams = Team::all();

        if ($players->isEmpty() || $matches->isEmpty()) {
            $this->command->warn('No players or matches found');
            return;
        }

        $this->command->info("Creating performance stats for {$players->count()} players across {$matches->count()} matches...");

        foreach ($players as $player) {
            // Assigner le joueur à une équipe aléatoire s'il n'en a pas
            if (!$player->club_id) {
                $player->update(['club_id' => $teams->random()->id]);
            }

            // Créer des statistiques pour 15-25 matchs par joueur
            $playerMatches = $matches->random(rand(15, 25));
            
            foreach ($playerMatches as $match) {
                $this->createPlayerMatchStat($player, $match);
            }
        }
    }

    private function createPlayerMatchStat($player, $match)
    {
        // Vérifier si les stats existent déjà
        if (PlayerMatchDetailedStats::where('player_id', $player->id)
                                   ->where('match_id', $match->id)
                                   ->exists()) {
            return;
        }

        // Générer des statistiques réalistes basées sur la position du joueur
        $position = $this->getRandomPosition();
        $stats = $this->generateRealisticStats($position, $player->overall_rating ?? 75);

        PlayerMatchDetailedStats::create([
            'player_id' => $player->id,
            'match_id' => $match->id,
            'team_id' => $player->club_id ?? $match->home_team_id,
            'competition_id' => $match->competition_id,
            'season_id' => $match->season_id,
            'position_played' => $position,
            'minutes_played' => $stats['minutes_played'],
            'started_match' => $stats['started_match'],
            'substituted_in' => $stats['substituted_in'],
            'substituted_out' => $stats['substituted_out'],
            'substitution_minute' => $stats['substitution_minute'],
            'formation_position' => $position,
            
            // Statistiques offensives
            'goals_scored' => $stats['goals_scored'],
            'assists_provided' => $stats['assists_provided'],
            'shots_total' => $stats['shots_total'],
            'shots_on_target' => $stats['shots_on_target'],
            'shots_off_target' => $stats['shots_off_target'],
            'shots_blocked' => $stats['shots_blocked'],
            'shots_inside_box' => $stats['shots_inside_box'],
            'shots_outside_box' => $stats['shots_outside_box'],
            'shooting_accuracy' => $stats['shooting_accuracy'],
            
            // Passes
            'passes_total' => $stats['passes_total'],
            'passes_completed' => $stats['passes_completed'],
            'passes_failed' => $stats['passes_failed'],
            'pass_accuracy' => $stats['pass_accuracy'],
            'key_passes' => $stats['key_passes'],
            'long_passes' => $stats['long_passes'],
            'long_passes_completed' => $stats['long_passes_completed'],
            'crosses_total' => $stats['crosses_total'],
            'crosses_completed' => $stats['crosses_completed'],
            'cross_accuracy' => $stats['cross_accuracy'],
            
            // Dribbles
            'dribbles_attempted' => $stats['dribbles_attempted'],
            'dribbles_completed' => $stats['dribbles_completed'],
            'dribble_success_rate' => $stats['dribble_success_rate'],
            'times_dispossessed' => $stats['times_dispossessed'],
            'bad_controls' => $stats['bad_controls'],
            'successful_take_ons' => $stats['successful_take_ons'],
            'failed_take_ons' => $stats['failed_take_ons'],
            
            // Défense
            'tackles_total' => $stats['tackles_total'],
            'tackles_won' => $stats['tackles_won'],
            'tackles_lost' => $stats['tackles_lost'],
            'tackle_success_rate' => $stats['tackle_success_rate'],
            'interceptions' => $stats['interceptions'],
            'clearances' => $stats['clearances'],
            'blocks' => $stats['blocks'],
            'clearances_off_line' => $stats['clearances_off_line'],
            'recoveries' => $stats['recoveries'],
            
            // Duels
            'aerial_duels_total' => $stats['aerial_duels_total'],
            'aerial_duels_won' => $stats['aerial_duels_won'],
            'aerial_duels_lost' => $stats['aerial_duels_lost'],
            'aerial_duel_success_rate' => $stats['aerial_duel_success_rate'],
            'ground_duels_total' => $stats['ground_duels_total'],
            'ground_duels_won' => $stats['ground_duels_won'],
            'ground_duels_lost' => $stats['ground_duels_lost'],
            'ground_duel_success_rate' => $stats['ground_duel_success_rate'],
            
            // Discipline
            'fouls_committed' => $stats['fouls_committed'],
            'fouls_drawn' => $stats['fouls_drawn'],
            'yellow_cards' => $stats['yellow_cards'],
            'red_cards' => $stats['red_cards'],
            'second_yellow_cards' => $stats['second_yellow_cards'],
            'offsides' => $stats['offsides'],
            'handballs' => $stats['handballs'],
            
            // Physique
            'distance_covered_km' => $stats['distance_covered_km'],
            'distance_sprinting_km' => $stats['distance_sprinting_km'],
            'distance_jogging_km' => $stats['distance_jogging_km'],
            'distance_walking_km' => $stats['distance_walking_km'],
            'max_speed_kmh' => $stats['max_speed_kmh'],
            'avg_speed_kmh' => $stats['avg_speed_kmh'],
            'sprint_count' => $stats['sprint_count'],
            'acceleration_count' => $stats['acceleration_count'],
            'deceleration_count' => $stats['deceleration_count'],
            'direction_changes' => $stats['direction_changes'],
            
            // Performance
            'match_rating' => $stats['match_rating'],
            'performance_level' => $stats['performance_level'],
            'man_of_match' => $stats['man_of_match'],
            'team_of_week' => $stats['team_of_week'],
            'fifa_rating' => $stats['fifa_rating'],
            'fifa_rating_change' => $stats['fifa_rating_change'],
            
            // Contexte
            'match_importance' => $stats['match_importance'],
            'home_match' => $stats['home_match'],
            'away_match' => $stats['away_match'],
            'opponent_team' => $stats['opponent_team'],
            'team_goals_scored' => $stats['team_goals_scored'],
            'team_goals_conceded' => $stats['team_goals_conceded'],
            'match_result' => $stats['match_result'],
            'goal_difference' => $stats['goal_difference'],
            
            // Métadonnées
            'data_source' => 'official_stats',
            'data_confidence' => 95.00,
            'data_quality' => 'excellent',
            'created_at' => $match->match_date,
            'updated_at' => $match->match_date
        ]);
    }

    private function getRandomPosition()
    {
        $positions = ['GK', 'CB', 'LB', 'RB', 'DM', 'CM', 'AM', 'LW', 'RW', 'ST'];
        return $positions[array_rand($positions)];
    }

    private function generateRealisticStats($position, $playerRating)
    {
        $rating = $playerRating ?? 75;
        $ratingMultiplier = $rating / 100;

        // Minutes jouées
        $minutesPlayed = rand(45, 90);
        $startedMatch = $minutesPlayed >= 70;
        $substitutedIn = !$startedMatch && rand(0, 1);
        $substitutedOut = $startedMatch && rand(0, 1);
        $substitutionMinute = $substitutedOut ? rand(60, 85) : null;

        // Statistiques basées sur la position
        switch ($position) {
            case 'GK':
                return $this->generateGoalkeeperStats($ratingMultiplier, $minutesPlayed);
            case 'CB':
            case 'LB':
            case 'RB':
                return $this->generateDefenderStats($ratingMultiplier, $minutesPlayed);
            case 'DM':
            case 'CM':
                return $this->generateMidfielderStats($ratingMultiplier, $minutesPlayed);
            case 'AM':
            case 'LW':
            case 'RW':
                return $this->generateAttackingMidfielderStats($ratingMultiplier, $minutesPlayed);
            case 'ST':
                return $this->generateStrikerStats($ratingMultiplier, $minutesPlayed);
            default:
                return $this->generateMidfielderStats($ratingMultiplier, $minutesPlayed);
        }
    }

    private function generateGoalkeeperStats($ratingMultiplier, $minutesPlayed)
    {
        return [
            'minutes_played' => $minutesPlayed,
            'started_match' => $minutesPlayed >= 70,
            'substituted_in' => false,
            'substituted_out' => false,
            'substitution_minute' => null,
            'goals_scored' => 0,
            'assists_provided' => rand(0, 1),
            'shots_total' => 0,
            'shots_on_target' => 0,
            'shots_off_target' => 0,
            'shots_blocked' => 0,
            'shots_inside_box' => 0,
            'shots_outside_box' => 0,
            'shooting_accuracy' => 0,
            'passes_total' => rand(15, 35),
            'passes_completed' => rand(12, 30),
            'passes_failed' => rand(3, 8),
            'pass_accuracy' => rand(75, 95),
            'key_passes' => rand(0, 2),
            'long_passes' => rand(8, 20),
            'long_passes_completed' => rand(6, 16),
            'crosses_total' => 0,
            'crosses_completed' => 0,
            'cross_accuracy' => 0,
            'dribbles_attempted' => 0,
            'dribbles_completed' => 0,
            'dribble_success_rate' => 0,
            'times_dispossessed' => 0,
            'bad_controls' => 0,
            'successful_take_ons' => 0,
            'failed_take_ons' => 0,
            'tackles_total' => rand(0, 2),
            'tackles_won' => rand(0, 2),
            'tackles_lost' => 0,
            'tackle_success_rate' => 100,
            'interceptions' => rand(0, 1),
            'clearances' => rand(0, 2),
            'blocks' => 0,
            'clearances_off_line' => 0,
            'recoveries' => rand(0, 1),
            'aerial_duels_total' => rand(0, 2),
            'aerial_duels_won' => rand(0, 2),
            'aerial_duels_lost' => 0,
            'aerial_duel_success_rate' => 100,
            'ground_duels_total' => rand(0, 1),
            'ground_duels_won' => rand(0, 1),
            'ground_duels_lost' => 0,
            'ground_duel_success_rate' => 100,
            'fouls_committed' => 0,
            'fouls_drawn' => rand(0, 1),
            'yellow_cards' => 0,
            'red_cards' => 0,
            'second_yellow_cards' => 0,
            'offsides' => 0,
            'handballs' => 0,
            'distance_covered_km' => rand(3, 6),
            'distance_sprinting_km' => rand(0.5, 1.5),
            'distance_jogging_km' => rand(1, 3),
            'distance_walking_km' => rand(1, 2),
            'max_speed_kmh' => rand(15, 25),
            'avg_speed_kmh' => rand(3, 8),
            'sprint_count' => rand(2, 8),
            'acceleration_count' => rand(5, 15),
            'deceleration_count' => rand(5, 15),
            'direction_changes' => rand(10, 25),
            'match_rating' => rand(60, 95) / 10,
            'performance_level' => ['good', 'very_good', 'excellent'][rand(0, 2)],
            'man_of_match' => rand(0, 20) === 1,
            'team_of_week' => rand(0, 50) === 1,
            'fifa_rating' => rand(60, 95) / 10,
            'fifa_rating_change' => rand(-20, 20) / 100,
            'match_importance' => ['league', 'cup', 'champions_league'][rand(0, 2)],
            'home_match' => rand(0, 1),
            'away_match' => rand(0, 1),
            'opponent_team' => 'Opponent Team',
            'team_goals_scored' => rand(0, 4),
            'team_goals_conceded' => rand(0, 3),
            'match_result' => ['win', 'draw', 'loss'][rand(0, 2)],
            'goal_difference' => rand(-3, 4)
        ];
    }

    private function generateDefenderStats($ratingMultiplier, $minutesPlayed)
    {
        $goalsScored = rand(0, 2);
        $assists = rand(0, 3);
        
        return [
            'minutes_played' => $minutesPlayed,
            'started_match' => $minutesPlayed >= 70,
            'substituted_in' => $minutesPlayed < 70,
            'substituted_out' => $minutesPlayed >= 70 && rand(0, 1),
            'substitution_minute' => $minutesPlayed >= 70 && rand(0, 1) ? rand(60, 85) : null,
            'goals_scored' => $goalsScored,
            'assists_provided' => $assists,
            'shots_total' => rand(0, 3),
            'shots_on_target' => rand(0, 2),
            'shots_off_target' => rand(0, 2),
            'shots_blocked' => 0,
            'shots_inside_box' => rand(0, 2),
            'shots_outside_box' => rand(0, 1),
            'shooting_accuracy' => rand(40, 80),
            'passes_total' => rand(40, 80),
            'passes_completed' => rand(35, 70),
            'passes_failed' => rand(5, 15),
            'pass_accuracy' => rand(75, 95),
            'key_passes' => rand(0, 3),
            'long_passes' => rand(5, 15),
            'long_passes_completed' => rand(3, 12),
            'crosses_total' => rand(0, 8),
            'crosses_completed' => rand(0, 5),
            'cross_accuracy' => rand(60, 85),
            'dribbles_attempted' => rand(0, 5),
            'dribbles_completed' => rand(0, 3),
            'dribble_success_rate' => rand(50, 80),
            'times_dispossessed' => rand(0, 3),
            'bad_controls' => rand(0, 2),
            'successful_take_ons' => rand(0, 3),
            'failed_take_ons' => rand(0, 2),
            'tackles_total' => rand(2, 8),
            'tackles_won' => rand(1, 6),
            'tackles_lost' => rand(0, 3),
            'tackle_success_rate' => rand(60, 90),
            'interceptions' => rand(2, 8),
            'clearances' => rand(3, 12),
            'blocks' => rand(0, 4),
            'clearances_off_line' => rand(0, 2),
            'recoveries' => rand(2, 6),
            'aerial_duels_total' => rand(3, 10),
            'aerial_duels_won' => rand(2, 7),
            'aerial_duels_lost' => rand(1, 4),
            'aerial_duel_success_rate' => rand(60, 85),
            'ground_duels_total' => rand(5, 15),
            'ground_duels_won' => rand(3, 10),
            'ground_duels_lost' => rand(2, 6),
            'ground_duel_success_rate' => rand(55, 80),
            'fouls_committed' => rand(0, 3),
            'fouls_drawn' => rand(0, 2),
            'yellow_cards' => rand(0, 1),
            'red_cards' => rand(0, 20) === 1,
            'second_yellow_cards' => 0,
            'offsides' => 0,
            'handballs' => rand(0, 1),
            'distance_covered_km' => rand(8, 12),
            'distance_sprinting_km' => rand(1, 3),
            'distance_jogging_km' => rand(3, 6),
            'distance_walking_km' => rand(2, 4),
            'max_speed_kmh' => rand(25, 35),
            'avg_speed_kmh' => rand(8, 12),
            'sprint_count' => rand(15, 35),
            'acceleration_count' => rand(20, 40),
            'deceleration_count' => rand(20, 40),
            'direction_changes' => rand(25, 50),
            'match_rating' => rand(60, 95) / 10,
            'performance_level' => ['good', 'very_good', 'excellent'][rand(0, 2)],
            'man_of_match' => rand(0, 30) === 1,
            'team_of_week' => rand(0, 60) === 1,
            'fifa_rating' => rand(60, 95) / 10,
            'fifa_rating_change' => rand(-20, 20) / 100,
            'match_importance' => ['league', 'cup', 'champions_league'][rand(0, 2)],
            'home_match' => rand(0, 1),
            'away_match' => rand(0, 1),
            'opponent_team' => 'Opponent Team',
            'team_goals_scored' => rand(0, 4),
            'team_goals_conceded' => rand(0, 3),
            'match_result' => ['win', 'draw', 'loss'][rand(0, 2)],
            'goal_difference' => rand(-3, 4)
        ];
    }

    private function generateMidfielderStats($ratingMultiplier, $minutesPlayed)
    {
        $goalsScored = rand(0, 3);
        $assists = rand(0, 4);
        
        return [
            'minutes_played' => $minutesPlayed,
            'started_match' => $minutesPlayed >= 70,
            'substituted_in' => $minutesPlayed < 70,
            'substituted_out' => $minutesPlayed >= 70 && rand(0, 1),
            'substitution_minute' => $minutesPlayed >= 70 && rand(0, 1) ? rand(60, 85) : null,
            'goals_scored' => $goalsScored,
            'assists_provided' => $assists,
            'shots_total' => rand(1, 5),
            'shots_on_target' => rand(0, 3),
            'shots_off_target' => rand(1, 3),
            'shots_blocked' => rand(0, 2),
            'shots_inside_box' => rand(0, 3),
            'shots_outside_box' => rand(1, 3),
            'shooting_accuracy' => rand(45, 85),
            'passes_total' => rand(50, 100),
            'passes_completed' => rand(40, 85),
            'passes_failed' => rand(5, 20),
            'pass_accuracy' => rand(70, 95),
            'key_passes' => rand(1, 6),
            'long_passes' => rand(3, 12),
            'long_passes_completed' => rand(2, 10),
            'crosses_total' => rand(0, 5),
            'crosses_completed' => rand(0, 3),
            'cross_accuracy' => rand(55, 80),
            'dribbles_attempted' => rand(2, 8),
            'dribbles_completed' => rand(1, 5),
            'dribble_success_rate' => rand(60, 85),
            'times_dispossessed' => rand(1, 4),
            'bad_controls' => rand(0, 3),
            'successful_take_ons' => rand(1, 5),
            'failed_take_ons' => rand(1, 4),
            'tackles_total' => rand(3, 10),
            'tackles_won' => rand(2, 7),
            'tackles_lost' => rand(1, 4),
            'tackle_success_rate' => rand(65, 90),
            'interceptions' => rand(3, 10),
            'clearances' => rand(1, 6),
            'blocks' => rand(0, 3),
            'clearances_off_line' => rand(0, 1),
            'recoveries' => rand(3, 8),
            'aerial_duels_total' => rand(2, 8),
            'aerial_duels_won' => rand(1, 5),
            'aerial_duels_lost' => rand(1, 4),
            'aerial_duel_success_rate' => rand(55, 80),
            'ground_duels_total' => rand(8, 20),
            'ground_duels_won' => rand(5, 12),
            'ground_duels_lost' => rand(3, 8),
            'ground_duel_success_rate' => rand(60, 85),
            'fouls_committed' => rand(0, 4),
            'fouls_drawn' => rand(1, 3),
            'yellow_cards' => rand(0, 1),
            'red_cards' => rand(0, 25) === 1,
            'second_yellow_cards' => 0,
            'offsides' => rand(0, 1),
            'handballs' => rand(0, 1),
            'distance_covered_km' => rand(10, 14),
            'distance_sprinting_km' => rand(1.5, 3.5),
            'distance_jogging_km' => rand(4, 7),
            'distance_walking_km' => rand(2, 4),
            'max_speed_kmh' => rand(28, 38),
            'avg_speed_kmh' => rand(9, 13),
            'sprint_count' => rand(20, 45),
            'acceleration_count' => rand(25, 50),
            'deceleration_count' => rand(25, 50),
            'direction_changes' => rand(30, 60),
            'match_rating' => rand(60, 95) / 10,
            'performance_level' => ['good', 'very_good', 'excellent'][rand(0, 2)],
            'man_of_match' => rand(0, 25) === 1,
            'team_of_week' => rand(0, 50) === 1,
            'fifa_rating' => rand(60, 95) / 10,
            'fifa_rating_change' => rand(-20, 20) / 100,
            'match_importance' => ['league', 'cup', 'champions_league'][rand(0, 2)],
            'home_match' => rand(0, 1),
            'away_match' => rand(0, 1),
            'opponent_team' => 'Opponent Team',
            'team_goals_scored' => rand(0, 4),
            'team_goals_conceded' => rand(0, 3),
            'match_result' => ['win', 'draw', 'loss'][rand(0, 2)],
            'goal_difference' => rand(-3, 4)
        ];
    }

    private function generateAttackingMidfielderStats($ratingMultiplier, $minutesPlayed)
    {
        $goalsScored = rand(0, 4);
        $assists = rand(1, 5);
        
        return [
            'minutes_played' => $minutesPlayed,
            'started_match' => $minutesPlayed >= 70,
            'substituted_in' => $minutesPlayed < 70,
            'substituted_out' => $minutesPlayed >= 70 && rand(0, 1),
            'substitution_minute' => $minutesPlayed >= 70 && rand(0, 1) ? rand(60, 85) : null,
            'goals_scored' => $goalsScored,
            'assists_provided' => $assists,
            'shots_total' => rand(2, 7),
            'shots_on_target' => rand(1, 4),
            'shots_off_target' => rand(1, 4),
            'shots_blocked' => rand(0, 2),
            'shots_inside_box' => rand(1, 4),
            'shots_outside_box' => rand(1, 4),
            'shooting_accuracy' => rand(50, 90),
            'passes_total' => rand(45, 90),
            'passes_completed' => rand(35, 75),
            'passes_failed' => rand(5, 20),
            'pass_accuracy' => rand(65, 90),
            'key_passes' => rand(2, 8),
            'long_passes' => rand(2, 10),
            'long_passes_completed' => rand(1, 8),
            'crosses_total' => rand(1, 8),
            'crosses_completed' => rand(0, 5),
            'cross_accuracy' => rand(50, 80),
            'dribbles_attempted' => rand(3, 10),
            'dribbles_completed' => rand(2, 6),
            'dribble_success_rate' => rand(65, 90),
            'times_dispossessed' => rand(1, 5),
            'bad_controls' => rand(0, 3),
            'successful_take_ons' => rand(2, 7),
            'failed_take_ons' => rand(1, 4),
            'tackles_total' => rand(2, 8),
            'tackles_won' => rand(1, 5),
            'tackles_lost' => rand(1, 4),
            'tackle_success_rate' => rand(60, 85),
            'interceptions' => rand(2, 8),
            'clearances' => rand(0, 4),
            'blocks' => rand(0, 2),
            'clearances_off_line' => 0,
            'recoveries' => rand(2, 6),
            'aerial_duels_total' => rand(1, 6),
            'aerial_duels_won' => rand(0, 3),
            'aerial_duels_lost' => rand(1, 4),
            'aerial_duel_success_rate' => rand(50, 75),
            'ground_duels_total' => rand(6, 18),
            'ground_duels_won' => rand(4, 10),
            'ground_duels_lost' => rand(2, 8),
            'ground_duel_success_rate' => rand(55, 80),
            'fouls_committed' => rand(0, 3),
            'fouls_drawn' => rand(1, 4),
            'yellow_cards' => rand(0, 1),
            'red_cards' => rand(0, 30) === 1,
            'second_yellow_cards' => 0,
            'offsides' => rand(0, 2),
            'handballs' => rand(0, 1),
            'distance_covered_km' => rand(9, 13),
            'distance_sprinting_km' => rand(1.5, 3.5),
            'distance_jogging_km' => rand(3.5, 6.5),
            'distance_walking_km' => rand(2, 4),
            'max_speed_kmh' => rand(30, 40),
            'avg_speed_kmh' => rand(8, 12),
            'sprint_count' => rand(18, 40),
            'acceleration_count' => rand(22, 45),
            'deceleration_count' => rand(22, 45),
            'direction_changes' => rand(25, 55),
            'match_rating' => rand(60, 95) / 10,
            'performance_level' => ['good', 'very_good', 'excellent'][rand(0, 2)],
            'man_of_match' => rand(0, 20) === 1,
            'team_of_week' => rand(0, 40) === 1,
            'fifa_rating' => rand(60, 95) / 10,
            'fifa_rating_change' => rand(-20, 20) / 100,
            'match_importance' => ['league', 'cup', 'champions_league'][rand(0, 2)],
            'home_match' => rand(0, 1),
            'away_match' => rand(0, 1),
            'opponent_team' => 'Opponent Team',
            'team_goals_scored' => rand(0, 4),
            'team_goals_conceded' => rand(0, 3),
            'match_result' => ['win', 'draw', 'loss'][rand(0, 2)],
            'goal_difference' => rand(-3, 4)
        ];
    }

    private function generateStrikerStats($ratingMultiplier, $minutesPlayed)
    {
        $goalsScored = rand(0, 5);
        $assists = rand(0, 3);
        
        return [
            'minutes_played' => $minutesPlayed,
            'started_match' => $minutesPlayed >= 70,
            'substituted_in' => $minutesPlayed < 70,
            'substituted_out' => $minutesPlayed >= 70 && rand(0, 1),
            'substitution_minute' => $minutesPlayed >= 70 && rand(0, 1) ? rand(60, 85) : null,
            'goals_scored' => $goalsScored,
            'assists_provided' => $assists,
            'shots_total' => rand(2, 8),
            'shots_on_target' => rand(1, 5),
            'shots_off_target' => rand(1, 5),
            'shots_blocked' => rand(0, 3),
            'shots_inside_box' => rand(2, 6),
            'shots_outside_box' => rand(0, 3),
            'shooting_accuracy' => rand(45, 85),
            'passes_total' => rand(20, 60),
            'passes_completed' => rand(15, 45),
            'passes_failed' => rand(3, 20),
            'pass_accuracy' => rand(60, 85),
            'key_passes' => rand(0, 4),
            'long_passes' => rand(0, 5),
            'long_passes_completed' => rand(0, 3),
            'crosses_total' => rand(0, 3),
            'crosses_completed' => rand(0, 2),
            'cross_accuracy' => rand(40, 70),
            'dribbles_attempted' => rand(2, 8),
            'dribbles_completed' => rand(1, 5),
            'dribble_success_rate' => rand(60, 85),
            'times_dispossessed' => rand(1, 5),
            'bad_controls' => rand(0, 3),
            'successful_take_ons' => rand(1, 6),
            'failed_take_ons' => rand(1, 4),
            'tackles_total' => rand(0, 5),
            'tackles_won' => rand(0, 3),
            'tackles_lost' => rand(0, 3),
            'tackle_success_rate' => rand(50, 80),
            'interceptions' => rand(0, 4),
            'clearances' => rand(0, 2),
            'blocks' => rand(0, 1),
            'clearances_off_line' => 0,
            'recoveries' => rand(1, 4),
            'aerial_duels_total' => rand(2, 8),
            'aerial_duels_won' => rand(1, 5),
            'aerial_duels_lost' => rand(1, 4),
            'aerial_duel_success_rate' => rand(55, 80),
            'ground_duels_total' => rand(4, 15),
            'ground_duels_won' => rand(2, 8),
            'ground_duels_lost' => rand(2, 7),
            'ground_duel_success_rate' => rand(50, 75),
            'fouls_committed' => rand(0, 3),
            'fouls_drawn' => rand(1, 4),
            'yellow_cards' => rand(0, 1),
            'red_cards' => rand(0, 35) === 1,
            'second_yellow_cards' => 0,
            'offsides' => rand(0, 3),
            'handballs' => rand(0, 1),
            'distance_covered_km' => rand(8, 12),
            'distance_sprinting_km' => rand(1.5, 3.5),
            'distance_jogging_km' => rand(3, 6),
            'distance_walking_km' => rand(2, 4),
            'max_speed_kmh' => rand(30, 40),
            'avg_speed_kmh' => rand(7, 11),
            'sprint_count' => rand(15, 35),
            'acceleration_count' => rand(20, 40),
            'deceleration_count' => rand(20, 40),
            'direction_changes' => rand(20, 45),
            'match_rating' => rand(60, 95) / 10,
            'performance_level' => ['good', 'very_good', 'excellent'][rand(0, 2)],
            'man_of_match' => rand(0, 15) === 1,
            'team_of_week' => rand(0, 30) === 1,
            'fifa_rating' => rand(60, 95) / 10,
            'fifa_rating_change' => rand(-20, 20) / 100,
            'match_importance' => ['league', 'cup', 'champions_league'][rand(0, 2)],
            'home_match' => rand(0, 1),
            'away_match' => rand(0, 1),
            'opponent_team' => 'Opponent Team',
            'team_goals_scored' => rand(0, 4),
            'team_goals_conceded' => rand(0, 3),
            'match_result' => ['win', 'draw', 'loss'][rand(0, 2)],
            'goal_difference' => rand(-3, 4)
        ];
    }
}
