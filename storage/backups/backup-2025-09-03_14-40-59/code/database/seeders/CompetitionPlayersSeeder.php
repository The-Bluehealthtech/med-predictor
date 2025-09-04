<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Player;
use App\Models\Team;
use App\Models\Club;
use App\Models\Competition;
use App\Models\Association;
use App\Models\TeamPlayer;
use Illuminate\Support\Facades\DB;

class CompetitionPlayersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the English Football Association
        $englishFA = Association::where('name', 'The Football Association')->first();
        $associationId = $englishFA ? $englishFA->id : null;

        // Get the Premier League competition
        $premierLeague = Competition::where('name', 'Premier League')->first();
        if (!$premierLeague) {
            $this->command->error('Premier League competition not found!');
            return;
        }

        // Get all teams in the Premier League
        $teams = $premierLeague->teams()->get();
        if ($teams->isEmpty()) {
            $this->command->error('No teams found in Premier League!');
            return;
        }

        $this->command->info("Found {$teams->count()} teams in Premier League");

        // Player data for each team (30 players per team)
        $teamPlayers = [
            'Arsenal' => [
                ['name' => 'David Raya', 'position' => 'GK', 'overall_rating' => 82, 'squad_number' => 1],
                ['name' => 'Aaron Ramsdale', 'position' => 'GK', 'overall_rating' => 80, 'squad_number' => 12],
                ['name' => 'Karl Hein', 'position' => 'GK', 'overall_rating' => 65, 'squad_number' => 31],
                ['name' => 'William Saliba', 'position' => 'CB', 'overall_rating' => 85, 'squad_number' => 2],
                ['name' => 'Gabriel Magalhães', 'position' => 'CB', 'overall_rating' => 83, 'squad_number' => 6],
                ['name' => 'Ben White', 'position' => 'RB', 'overall_rating' => 82, 'squad_number' => 4],
                ['name' => 'Oleksandr Zinchenko', 'position' => 'LB', 'overall_rating' => 80, 'squad_number' => 35],
                ['name' => 'Takehiro Tomiyasu', 'position' => 'RB', 'overall_rating' => 78, 'squad_number' => 18],
                ['name' => 'Jakub Kiwior', 'position' => 'CB', 'overall_rating' => 75, 'squad_number' => 15],
                ['name' => 'Jurriën Timber', 'position' => 'CB', 'overall_rating' => 79, 'squad_number' => 12],
                ['name' => 'Declan Rice', 'position' => 'CDM', 'overall_rating' => 86, 'squad_number' => 41],
                ['name' => 'Martin Ødegaard', 'position' => 'CAM', 'overall_rating' => 86, 'squad_number' => 8],
                ['name' => 'Kai Havertz', 'position' => 'CAM', 'overall_rating' => 82, 'squad_number' => 29],
                ['name' => 'Jorginho', 'position' => 'CDM', 'overall_rating' => 82, 'squad_number' => 20],
                ['name' => 'Thomas Partey', 'position' => 'CDM', 'overall_rating' => 83, 'squad_number' => 5],
                ['name' => 'Emile Smith Rowe', 'position' => 'CAM', 'overall_rating' => 76, 'squad_number' => 10],
                ['name' => 'Fabio Vieira', 'position' => 'CAM', 'overall_rating' => 75, 'squad_number' => 21],
                ['name' => 'Bukayo Saka', 'position' => 'RW', 'overall_rating' => 86, 'squad_number' => 7],
                ['name' => 'Gabriel Martinelli', 'position' => 'LW', 'overall_rating' => 84, 'squad_number' => 11],
                ['name' => 'Leandro Trossard', 'position' => 'LW', 'overall_rating' => 81, 'squad_number' => 19],
                ['name' => 'Reiss Nelson', 'position' => 'RW', 'overall_rating' => 74, 'squad_number' => 24],
                ['name' => 'Eddie Nketiah', 'position' => 'ST', 'overall_rating' => 75, 'squad_number' => 14],
                ['name' => 'Gabriel Jesus', 'position' => 'ST', 'overall_rating' => 82, 'squad_number' => 9],
                ['name' => 'Marquinhos', 'position' => 'RW', 'overall_rating' => 70, 'squad_number' => 27],
                ['name' => 'Cédric Soares', 'position' => 'RB', 'overall_rating' => 73, 'squad_number' => 17],
                ['name' => 'Mohamed Elneny', 'position' => 'CDM', 'overall_rating' => 74, 'squad_number' => 25],
                ['name' => 'Albert Sambi Lokonga', 'position' => 'CDM', 'overall_rating' => 72, 'squad_number' => 23],
                ['name' => 'Rob Holding', 'position' => 'CB', 'overall_rating' => 73, 'squad_number' => 16],
                ['name' => 'Kieran Tierney', 'position' => 'LB', 'overall_rating' => 77, 'squad_number' => 3],
            ],
            'Aston Villa' => [
                ['name' => 'Emiliano Martínez', 'position' => 'GK', 'overall_rating' => 84, 'squad_number' => 1],
                ['name' => 'Robin Olsen', 'position' => 'GK', 'overall_rating' => 75, 'squad_number' => 25],
                ['name' => 'Filip Marschall', 'position' => 'GK', 'overall_rating' => 60, 'squad_number' => 42],
                ['name' => 'Ezri Konsa', 'position' => 'CB', 'overall_rating' => 80, 'squad_number' => 4],
                ['name' => 'Diego Carlos', 'position' => 'CB', 'overall_rating' => 81, 'squad_number' => 3],
                ['name' => 'Pau Torres', 'position' => 'CB', 'overall_rating' => 82, 'squad_number' => 14],
                ['name' => 'Tyrone Mings', 'position' => 'CB', 'overall_rating' => 78, 'squad_number' => 5],
                ['name' => 'Matty Cash', 'position' => 'RB', 'overall_rating' => 78, 'squad_number' => 2],
                ['name' => 'Lucas Digne', 'position' => 'LB', 'overall_rating' => 79, 'squad_number' => 12],
                ['name' => 'Alex Moreno', 'position' => 'LB', 'overall_rating' => 77, 'squad_number' => 15],
                ['name' => 'Boubacar Kamara', 'position' => 'CDM', 'overall_rating' => 82, 'squad_number' => 44],
                ['name' => 'Douglas Luiz', 'position' => 'CDM', 'overall_rating' => 82, 'squad_number' => 6],
                ['name' => 'John McGinn', 'position' => 'CM', 'overall_rating' => 80, 'squad_number' => 7],
                ['name' => 'Youri Tielemans', 'position' => 'CM', 'overall_rating' => 81, 'squad_number' => 8],
                ['name' => 'Jacob Ramsey', 'position' => 'CM', 'overall_rating' => 76, 'squad_number' => 41],
                ['name' => 'Leon Bailey', 'position' => 'RW', 'overall_rating' => 79, 'squad_number' => 31],
                ['name' => 'Moussa Diaby', 'position' => 'RW', 'overall_rating' => 82, 'squad_number' => 19],
                ['name' => 'Ollie Watkins', 'position' => 'ST', 'overall_rating' => 82, 'squad_number' => 11],
                ['name' => 'Jhon Durán', 'position' => 'ST', 'overall_rating' => 72, 'squad_number' => 24],
                ['name' => 'Bertrand Traoré', 'position' => 'RW', 'overall_rating' => 74, 'squad_number' => 9],
                ['name' => 'Philippe Coutinho', 'position' => 'CAM', 'overall_rating' => 78, 'squad_number' => 23],
                ['name' => 'Emiliano Buendía', 'position' => 'CAM', 'overall_rating' => 77, 'squad_number' => 10],
                ['name' => 'Nicolò Zaniolo', 'position' => 'CAM', 'overall_rating' => 76, 'squad_number' => 22],
                ['name' => 'Tim Iroegbunam', 'position' => 'CDM', 'overall_rating' => 68, 'squad_number' => 47],
                ['name' => 'Leander Dendoncker', 'position' => 'CDM', 'overall_rating' => 75, 'squad_number' => 32],
                ['name' => 'Calum Chambers', 'position' => 'CB', 'overall_rating' => 73, 'squad_number' => 16],
                ['name' => 'Kortney Hause', 'position' => 'CB', 'overall_rating' => 72, 'squad_number' => 30],
                ['name' => 'Ashley Young', 'position' => 'RB', 'overall_rating' => 72, 'squad_number' => 18],
            ],
            'Bournemouth' => [
                ['name' => 'Neto', 'position' => 'GK', 'overall_rating' => 78, 'squad_number' => 1],
                ['name' => 'Darren Randolph', 'position' => 'GK', 'overall_rating' => 70, 'squad_number' => 12],
                ['name' => 'Mark Travers', 'position' => 'GK', 'overall_rating' => 72, 'squad_number' => 42],
                ['name' => 'Lloyd Kelly', 'position' => 'CB', 'overall_rating' => 76, 'squad_number' => 5],
                ['name' => 'Chris Mepham', 'position' => 'CB', 'overall_rating' => 73, 'squad_number' => 6],
                ['name' => 'Marcos Senesi', 'position' => 'CB', 'overall_rating' => 75, 'squad_number' => 25],
                ['name' => 'Adam Smith', 'position' => 'RB', 'overall_rating' => 72, 'squad_number' => 15],
                ['name' => 'Milos Kerkez', 'position' => 'LB', 'overall_rating' => 74, 'squad_number' => 3],
                ['name' => 'Max Aarons', 'position' => 'RB', 'overall_rating' => 73, 'squad_number' => 37],
                ['name' => 'Lewis Cook', 'position' => 'CDM', 'overall_rating' => 74, 'squad_number' => 16],
                ['name' => 'Philip Billing', 'position' => 'CM', 'overall_rating' => 75, 'squad_number' => 29],
                ['name' => 'Ryan Christie', 'position' => 'CM', 'overall_rating' => 74, 'squad_number' => 10],
                ['name' => 'Alex Scott', 'position' => 'CM', 'overall_rating' => 72, 'squad_number' => 14],
                ['name' => 'Marcus Tavernier', 'position' => 'RW', 'overall_rating' => 75, 'squad_number' => 16],
                ['name' => 'Dango Ouattara', 'position' => 'RW', 'overall_rating' => 73, 'squad_number' => 11],
                ['name' => 'Dominic Solanke', 'position' => 'ST', 'overall_rating' => 76, 'squad_number' => 9],
                ['name' => 'Antoine Semenyo', 'position' => 'ST', 'overall_rating' => 72, 'squad_number' => 24],
                ['name' => 'Justin Kluivert', 'position' => 'LW', 'overall_rating' => 73, 'squad_number' => 19],
                ['name' => 'David Brooks', 'position' => 'RW', 'overall_rating' => 72, 'squad_number' => 20],
                ['name' => 'Hamed Traorè', 'position' => 'CAM', 'overall_rating' => 73, 'squad_number' => 21],
                ['name' => 'Gavin Kilkenny', 'position' => 'CM', 'overall_rating' => 68, 'squad_number' => 26],
                ['name' => 'Joe Rothwell', 'position' => 'CM', 'overall_rating' => 71, 'squad_number' => 14],
                ['name' => 'Emiliano Marcondes', 'position' => 'CAM', 'overall_rating' => 70, 'squad_number' => 29],
                ['name' => 'Kieffer Moore', 'position' => 'ST', 'overall_rating' => 72, 'squad_number' => 21],
                ['name' => 'Jaidon Anthony', 'position' => 'LW', 'overall_rating' => 71, 'squad_number' => 32],
                ['name' => 'James Hill', 'position' => 'CB', 'overall_rating' => 69, 'squad_number' => 27],
                ['name' => 'Zeno Ibsen Rossi', 'position' => 'CB', 'overall_rating' => 65, 'squad_number' => 33],
                ['name' => 'Ben Greenwood', 'position' => 'LB', 'overall_rating' => 64, 'squad_number' => 34],
            ],
            // Add more teams with their players...
        ];

        // Generate players for remaining teams if not specified
        $remainingTeams = $teams->whereNotIn('name', array_keys($teamPlayers));
        
        foreach ($remainingTeams as $team) {
            $teamPlayers[$team->name] = $this->generateTeamPlayers($team->name);
        }

        $totalPlayers = 0;
        $totalTeamPlayers = 0;

        foreach ($teams as $team) {
            $teamName = $team->name;
            $club = $team->club;
            
            if (!isset($teamPlayers[$teamName])) {
                $this->command->warn("No players defined for team: {$teamName}");
                continue;
            }

            $this->command->info("Processing team: {$teamName}");

            foreach ($teamPlayers[$teamName] as $playerData) {
                // Create player
                $player = Player::create([
                    'name' => $playerData['name'],
                    'first_name' => explode(' ', $playerData['name'])[0],
                    'last_name' => explode(' ', $playerData['name'])[1] ?? '',
                    'date_of_birth' => $this->generateRandomDate(1990, 2005),
                    'nationality' => $this->getRandomNationality(),
                    'position' => $playerData['position'],
                    'overall_rating' => $playerData['overall_rating'],
                    'potential_rating' => $playerData['overall_rating'] + rand(-2, 5),
                    'age' => rand(18, 35),
                    'height' => rand(165, 195),
                    'weight' => rand(65, 85),
                    'preferred_foot' => rand(0, 1) ? 'Right' : 'Left',
                    'work_rate' => $this->getRandomWorkRate(),
                    'player_face_url' => 'https://picsum.photos/400/400?random=' . rand(1000, 9999),
                    'fifa_connect_id' => 'PL' . rand(100000, 999999),
                    'club_id' => $club ? $club->id : null,
                    'association_id' => $associationId,
                    'status' => 'active'
                ]);

                // Create team player relationship
                TeamPlayer::create([
                    'team_id' => $team->id,
                    'player_id' => $player->id,
                    'role' => $this->getPlayerRole($playerData['position']),
                    'squad_number' => $playerData['squad_number'],
                    'joined_date' => now()->subDays(rand(30, 365)),
                    'contract_end_date' => now()->addYears(rand(1, 5)),
                    'position_preference' => $playerData['position'],
                    'status' => 'active'
                ]);

                $totalPlayers++;
                $totalTeamPlayers++;
            }

            $this->command->info("Added " . count($teamPlayers[$teamName]) . " players to {$teamName}");
        }

        $this->command->info("Total players created: {$totalPlayers}");
        $this->command->info("Total team player relationships created: {$totalTeamPlayers}");
    }

    private function generateTeamPlayers(string $teamName): array
    {
        $players = [];
        $positions = ['GK', 'CB', 'RB', 'LB', 'CDM', 'CM', 'CAM', 'RW', 'LW', 'ST'];
        $squadNumbers = range(1, 50);
        shuffle($squadNumbers);

        // Generate 30 players per team
        for ($i = 0; $i < 30; $i++) {
            $position = $positions[array_rand($positions)];
            $players[] = [
                'name' => $this->generatePlayerName(),
                'position' => $position,
                'overall_rating' => $this->getPositionRating($position),
                'squad_number' => $squadNumbers[$i],
            ];
        }

        return $players;
    }

    private function generatePlayerName(): string
    {
        $firstNames = ['James', 'John', 'Robert', 'Michael', 'William', 'David', 'Richard', 'Joseph', 'Thomas', 'Christopher', 'Charles', 'Daniel', 'Matthew', 'Anthony', 'Mark', 'Donald', 'Steven', 'Paul', 'Andrew', 'Joshua', 'Kenneth', 'Kevin', 'Brian', 'George', 'Edward', 'Ronald', 'Timothy', 'Jason', 'Jeffrey', 'Ryan'];
        $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez', 'Hernandez', 'Lopez', 'Gonzalez', 'Wilson', 'Anderson', 'Thomas', 'Taylor', 'Moore', 'Jackson', 'Martin', 'Lee', 'Perez', 'Thompson', 'White', 'Harris', 'Sanchez', 'Clark', 'Ramirez', 'Lewis', 'Robinson'];

        return $firstNames[array_rand($firstNames)] . ' ' . $lastNames[array_rand($lastNames)];
    }

    private function generateRandomDate(int $startYear, int $endYear): string
    {
        $year = rand($startYear, $endYear);
        $month = str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT);
        $day = str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);
        return "{$year}-{$month}-{$day}";
    }

    private function getRandomNationality(): string
    {
        $nationalities = ['England', 'Spain', 'France', 'Germany', 'Italy', 'Brazil', 'Argentina', 'Portugal', 'Netherlands', 'Belgium', 'Croatia', 'Switzerland', 'Denmark', 'Sweden', 'Norway', 'Poland', 'Ukraine', 'Serbia', 'Greece', 'Turkey'];
        return $nationalities[array_rand($nationalities)];
    }

    private function getRandomWorkRate(): string
    {
        $rates = ['Low/Low', 'Low/Medium', 'Low/High', 'Medium/Low', 'Medium/Medium', 'Medium/High', 'High/Low', 'High/Medium', 'High/High'];
        return $rates[array_rand($rates)];
    }

    private function getPositionRating(string $position): int
    {
        $baseRatings = [
            'GK' => 75,
            'CB' => 74,
            'RB' => 73,
            'LB' => 73,
            'CDM' => 74,
            'CM' => 74,
            'CAM' => 74,
            'RW' => 74,
            'LW' => 74,
            'ST' => 74,
        ];

        $baseRating = $baseRatings[$position] ?? 74;
        return $baseRating + rand(-5, 8);
    }

    private function getPlayerRole(string $position): string
    {
        // Determine if player is starter, substitute, or reserve based on position and rating
        $roles = ['starter', 'substitute', 'reserve'];
        $weights = [0.4, 0.4, 0.2]; // 40% starters, 40% substitutes, 20% reserves
        
        $random = rand(1, 100);
        $cumulative = 0;
        
        foreach ($weights as $index => $weight) {
            $cumulative += $weight * 100;
            if ($random <= $cumulative) {
                return $roles[$index];
            }
        }
        
        return 'substitute';
    }
}
