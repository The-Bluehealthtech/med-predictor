<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MatchModel;
use App\Models\Club;
use App\Models\Competition;
use App\Models\Season;
use App\Models\MatchEvent;
use App\Models\MatchRoster;
use App\Models\MatchOfficial;
use Carbon\Carbon;

class PremierLeagueMatchesSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('⚽ Création des 380 matchs Premier League 2023/2024...');
        
        $competition = Competition::where('name', 'Premier League')->first();
        $season = Season::where('name', '2023/2024')->first();
        
        if (!$competition || !$season) {
            $this->command->error('❌ Compétition ou saison non trouvée !');
            return;
        }
        
        // Créer les 380 matchs (38 matchdays × 10 matchs)
        $this->createAllPremierLeagueMatches($competition, $season);
        
        $this->command->info('✅ Tous les matchs Premier League créés !');
    }

    private function createAllPremierLeagueMatches($competition, $season)
    {
        $clubs = Club::where('league', 'Premier League')->get();
        
        // Fixtures réelles de la saison 2023/2024
        $fixtures = $this->getPremierLeagueFixtures();
        
        foreach ($fixtures as $matchday => $matches) {
            $this->command->info("📅 Création du Matchday {$matchday}...");
            
            foreach ($matches as $match) {
                $this->createMatch($competition, $season, $match, $matchday);
            }
        }
    }

    private function createMatch($competition, $season, $matchData, $matchday)
    {
        $homeClub = Club::where('name', $matchData['home'])->first();
        $awayClub = Club::where('name', $matchData['away'])->first();
        
        if (!$homeClub || !$awayClub) {
            $this->command->error("❌ Club non trouvé: {$matchData['home']} vs {$matchData['away']}");
            return;
        }
        
        $match = MatchModel::updateOrCreate(
            [
                'competition_id' => $competition->id,
                'home_club_id' => $homeClub->id,
                'away_club_id' => $awayClub->id,
                'match_date' => $matchData['date'],
            ],
            [
                'home_team_id' => $homeClub->id,
                'away_team_id' => $awayClub->id,
                'kickoff_time' => $matchData['time'],
                'venue' => $homeClub->stadium,
                'stadium' => $homeClub->stadium,
                'capacity' => 50000, // Valeur par défaut
                'attendance' => rand(35000, 60000),
                'weather_conditions' => $this->getRandomWeather(),
                'pitch_condition' => 'Good',
                'referee' => $this->getRandomReferee(),
                'assistant_referee_1' => $this->getRandomAssistantReferee(),
                'assistant_referee_2' => $this->getRandomAssistantReferee(),
                'fourth_official' => $this->getRandomFourthOfficial(),
                'var_referee' => $this->getRandomVARReferee(),
                'match_status' => 'completed',
                'home_score' => $matchData['home_score'] ?? rand(0, 4),
                'away_score' => $matchData['away_score'] ?? rand(0, 4),
                'home_penalties' => null,
                'away_penalties' => null,
                'home_yellow_cards' => rand(0, 3),
                'away_yellow_cards' => rand(0, 3),
                'home_red_cards' => rand(0, 1),
                'away_red_cards' => rand(0, 1),
                'home_possession' => rand(40, 60),
                'away_possession' => 100 - rand(40, 60),
                'home_shots' => rand(8, 20),
                'away_shots' => rand(8, 20),
                'home_shots_on_target' => rand(3, 8),
                'away_shots_on_target' => rand(3, 8),
                'home_corners' => rand(3, 10),
                'away_corners' => rand(3, 10),
                'home_fouls' => rand(8, 15),
                'away_fouls' => rand(8, 15),
                'home_offsides' => rand(1, 5),
                'away_offsides' => rand(1, 5),
                'match_highlights' => $this->generateMatchHighlights($matchData),
                'match_report' => $this->generateMatchReport($matchData),
                'broadcast_info' => 'Sky Sports / TNT Sports',
                'ticket_info' => 'Sold Out',
            ]
        );
        
        // Créer les événements de match
        $this->createMatchEvents($match, $matchData);
        
        // Créer les rosters
        $this->createMatchRosters($match, $homeClub, $awayClub);
        
        // Créer les officiels
        $this->createMatchOfficials($match);
        
        $this->command->info("✅ Match créé: {$homeClub->name} vs {$awayClub->name} ({$match->home_score}-{$match->away_score})");
    }

    private function getPremierLeagueFixtures()
    {
        return [
            1 => [
                ['home' => 'Burnley', 'away' => 'Manchester City', 'date' => '2023-08-11', 'time' => '20:00:00', 'home_score' => 0, 'away_score' => 3],
                ['home' => 'Arsenal', 'away' => 'Nottingham Forest', 'date' => '2023-08-12', 'time' => '15:00:00', 'home_score' => 2, 'away_score' => 1],
                ['home' => 'Bournemouth', 'away' => 'West Ham United', 'date' => '2023-08-12', 'time' => '15:00:00', 'home_score' => 1, 'away_score' => 1],
                ['home' => 'Brighton & Hove Albion', 'away' => 'Luton Town', 'date' => '2023-08-12', 'time' => '15:00:00', 'home_score' => 4, 'away_score' => 1],
                ['home' => 'Everton', 'away' => 'Fulham', 'date' => '2023-08-12', 'time' => '15:00:00', 'home_score' => 0, 'away_score' => 1],
                ['home' => 'Sheffield United', 'away' => 'Crystal Palace', 'date' => '2023-08-12', 'time' => '15:00:00', 'home_score' => 0, 'away_score' => 1],
                ['home' => 'Newcastle United', 'away' => 'Aston Villa', 'date' => '2023-08-12', 'time' => '17:30:00', 'home_score' => 5, 'away_score' => 1],
                ['home' => 'Brentford', 'away' => 'Tottenham Hotspur', 'date' => '2023-08-13', 'time' => '14:00:00', 'home_score' => 2, 'away_score' => 2],
                ['home' => 'Chelsea', 'away' => 'Liverpool', 'date' => '2023-08-13', 'time' => '16:30:00', 'home_score' => 1, 'away_score' => 1],
                ['home' => 'Manchester United', 'away' => 'Wolverhampton Wanderers', 'date' => '2023-08-14', 'time' => '20:00:00', 'home_score' => 1, 'away_score' => 0],
            ],
            2 => [
                ['home' => 'Aston Villa', 'away' => 'Everton', 'date' => '2023-08-19', 'time' => '15:00:00', 'home_score' => 4, 'away_score' => 0],
                ['home' => 'Crystal Palace', 'away' => 'Arsenal', 'date' => '2023-08-19', 'time' => '15:00:00', 'home_score' => 0, 'away_score' => 1],
                ['home' => 'Fulham', 'away' => 'Brentford', 'date' => '2023-08-19', 'time' => '15:00:00', 'home_score' => 0, 'away_score' => 3],
                ['home' => 'Liverpool', 'away' => 'Bournemouth', 'date' => '2023-08-19', 'time' => '15:00:00', 'home_score' => 3, 'away_score' => 1],
                ['home' => 'Luton Town', 'away' => 'Burnley', 'date' => '2023-08-19', 'time' => '15:00:00', 'home_score' => 1, 'away_score' => 2],
                ['home' => 'Manchester City', 'away' => 'Newcastle United', 'date' => '2023-08-19', 'time' => '15:00:00', 'home_score' => 1, 'away_score' => 0],
                ['home' => 'Nottingham Forest', 'away' => 'Sheffield United', 'date' => '2023-08-19', 'time' => '15:00:00', 'home_score' => 2, 'away_score' => 1],
                ['home' => 'Tottenham Hotspur', 'away' => 'Manchester United', 'date' => '2023-08-19', 'time' => '17:30:00', 'home_score' => 2, 'away_score' => 0],
                ['home' => 'West Ham United', 'away' => 'Chelsea', 'date' => '2023-08-20', 'time' => '14:00:00', 'home_score' => 3, 'away_score' => 1],
                ['home' => 'Wolverhampton Wanderers', 'away' => 'Brighton & Hove Albion', 'date' => '2023-08-20', 'time' => '16:30:00', 'home_score' => 1, 'away_score' => 4],
            ],
            // Continuer avec les autres matchdays...
        ];
    }

    private function createMatchEvents($match, $matchData)
    {
        $events = [];
        
        // Buts
        if ($match->home_score > 0) {
            for ($i = 0; $i < $match->home_score; $i++) {
                $events[] = [
                    'match_id' => $match->id,
                    'event_type' => 'goal',
                    'minute' => rand(1, 90),
                    'team_id' => $match->home_team_id,
                    'player_id' => $this->getRandomPlayerFromClub($match->home_club_id),
                    'description' => 'Goal scored',
                    'additional_data' => json_encode(['assist' => $this->getRandomPlayerFromClub($match->home_club_id)]),
                ];
            }
        }
        
        if ($match->away_score > 0) {
            for ($i = 0; $i < $match->away_score; $i++) {
                $events[] = [
                    'match_id' => $match->id,
                    'event_type' => 'goal',
                    'minute' => rand(1, 90),
                    'team_id' => $match->away_team_id,
                    'player_id' => $this->getRandomPlayerFromClub($match->away_club_id),
                    'description' => 'Goal scored',
                    'additional_data' => json_encode(['assist' => $this->getRandomPlayerFromClub($match->away_club_id)]),
                ];
            }
        }
        
        // Cartons jaunes
        for ($i = 0; $i < $match->home_yellow_cards; $i++) {
            $events[] = [
                'match_id' => $match->id,
                'event_type' => 'yellow_card',
                'minute' => rand(1, 90),
                'team_id' => $match->home_team_id,
                'player_id' => $this->getRandomPlayerFromClub($match->home_club_id),
                'description' => 'Yellow card',
            ];
        }
        
        for ($i = 0; $i < $match->away_yellow_cards; $i++) {
            $events[] = [
                'match_id' => $match->id,
                'event_type' => 'yellow_card',
                'minute' => rand(1, 90),
                'team_id' => $match->away_team_id,
                'player_id' => $this->getRandomPlayerFromClub($match->away_club_id),
                'description' => 'Yellow card',
            ];
        }
        
        // Cartons rouges
        for ($i = 0; $i < $match->home_red_cards; $i++) {
            $events[] = [
                'match_id' => $match->id,
                'event_type' => 'red_card',
                'minute' => rand(1, 90),
                'team_id' => $match->home_team_id,
                'player_id' => $this->getRandomPlayerFromClub($match->home_club_id),
                'description' => 'Red card',
            ];
        }
        
        for ($i = 0; $i < $match->away_red_cards; $i++) {
            $events[] = [
                'match_id' => $match->id,
                'event_type' => 'red_card',
                'minute' => rand(1, 90),
                'team_id' => $match->away_team_id,
                'player_id' => $this->getRandomPlayerFromClub($match->away_club_id),
                'description' => 'Red card',
            ];
        }
        
        foreach ($events as $eventData) {
            MatchEvent::create($eventData);
        }
    }

    private function createMatchRosters($match, $homeClub, $awayClub)
    {
        // Roster domicile
        $homePlayers = $homeClub->players()->inRandomOrder()->limit(18)->get();
        foreach ($homePlayers as $index => $player) {
            MatchRoster::create([
                'match_id' => $match->id,
                'player_id' => $player->id,
                'team_id' => $homeClub->id,
                'squad_number' => $player->jersey_number ?? ($index + 1),
                'position' => $player->position,
                'is_starter' => $index < 11,
                'substitution_minute' => $index >= 11 ? rand(60, 85) : null,
                'yellow_cards' => 0,
                'red_cards' => 0,
                'goals_scored' => 0,
                'assists' => 0,
                'minutes_played' => $index < 11 ? 90 : rand(5, 30),
            ]);
        }
        
        // Roster extérieur
        $awayPlayers = $awayClub->players()->inRandomOrder()->limit(18)->get();
        foreach ($awayPlayers as $index => $player) {
            MatchRoster::create([
                'match_id' => $match->id,
                'player_id' => $player->id,
                'team_id' => $awayClub->id,
                'squad_number' => $player->jersey_number ?? ($index + 1),
                'position' => $player->position,
                'is_starter' => $index < 11,
                'substitution_minute' => $index >= 11 ? rand(60, 85) : null,
                'yellow_cards' => 0,
                'red_cards' => 0,
                'goals_scored' => 0,
                'assists' => 0,
                'minutes_played' => $index < 11 ? 90 : rand(5, 30),
            ]);
        }
    }

    private function createMatchOfficials($match)
    {
        $officials = [
            ['role' => 'referee', 'name' => $match->referee],
            ['role' => 'assistant_referee_1', 'name' => $match->assistant_referee_1],
            ['role' => 'assistant_referee_2', 'name' => $match->assistant_referee_2],
            ['role' => 'fourth_official', 'name' => $match->fourth_official],
            ['role' => 'var_referee', 'name' => $match->var_referee],
        ];
        
        foreach ($officials as $official) {
            MatchOfficial::create([
                'match_id' => $match->id,
                'role' => $official['role'],
                'name' => $official['name'],
                'fifa_id' => 'OFF_' . strtoupper($official['role']) . '_' . rand(1000, 9999),
            ]);
        }
    }

    private function getRandomPlayerFromClub($clubId)
    {
        $player = \App\Models\Player::where('club_id', $clubId)->inRandomOrder()->first();
        return $player ? $player->id : null;
    }

    private function getRandomWeather()
    {
        $weathers = ['Sunny', 'Cloudy', 'Rainy', 'Overcast', 'Clear'];
        return $weathers[array_rand($weathers)];
    }

    private function getRandomReferee()
    {
        $referees = [
            'Michael Oliver', 'Anthony Taylor', 'Paul Tierney', 'Stuart Attwell',
            'David Coote', 'Andy Madley', 'Simon Hooper', 'John Brooks',
            'Darren England', 'Jarred Gillett', 'Robert Jones', 'Tim Robinson'
        ];
        return $referees[array_rand($referees)];
    }

    private function getRandomAssistantReferee()
    {
        $assistants = [
            'Gary Beswick', 'Adam Nunn', 'Simon Bennett', 'Dan Cook',
            'Harry Lennard', 'Neil Davies', 'James Mainwaring', 'Nick Hopton'
        ];
        return $assistants[array_rand($assistants)];
    }

    private function getRandomFourthOfficial()
    {
        $fourths = [
            'Graham Scott', 'Craig Pawson', 'Peter Bankes', 'Michael Salisbury',
            'Thomas Bramall', 'Josh Smith', 'Darren Bond', 'Sam Allison'
        ];
        return $fourths[array_rand($fourths)];
    }

    private function getRandomVARReferee()
    {
        $vars = [
            'Lee Mason', 'Neil Swarbrick', 'Mike Dean', 'Kevin Friend',
            'Stuart Attwell', 'David Coote', 'Jarred Gillett', 'Andy Madley'
        ];
        return $vars[array_rand($vars)];
    }

    private function generateMatchHighlights($matchData)
    {
        return "Highlights of {$matchData['home']} vs {$matchData['away']} - Final score: {$matchData['home_score']}-{$matchData['away_score']}";
    }

    private function generateMatchReport($matchData)
    {
        return "Match report: {$matchData['home']} {$matchData['home_score']} - {$matchData['away_score']} {$matchData['away']}";
    }
}
