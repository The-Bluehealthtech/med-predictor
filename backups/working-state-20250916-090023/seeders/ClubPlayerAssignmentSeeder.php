<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Player;
use App\Models\Club;
use App\Models\Association;
use Illuminate\Support\Str;

class ClubPlayerAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the English Football Association
        $englishFA = Association::where('name', 'The Football Association')->first();
        $associationId = $englishFA ? $englishFA->id : null;

        // Get all clubs
        $clubs = Club::all();
        
        if ($clubs->count() < 20) {
            $this->command->error('Not enough clubs found. Please run ClubSeeder first.');
            return;
        }

        // Take first 20 clubs
        $clubs = $clubs->take(20);

        // Clear existing player-club assignments
        Player::query()->update(['club_id' => null]);

        // Generate 600 players (30 per club)
        $playerCounter = 1;
        
        foreach ($clubs as $club) {
            $this->command->info("Assigning 30 players to {$club->name}...");
            
            for ($i = 1; $i <= 30; $i++) {
                $player = $this->createPlayer($playerCounter, $club, $associationId);
                $playerCounter++;
            }
        }

        $this->command->info("Successfully assigned 600 players across 20 clubs (30 players per club).");
    }

    private function createPlayer($counter, $club, $associationId)
    {
        $positions = ['GK', 'CB', 'LB', 'RB', 'CDM', 'CM', 'CAM', 'LW', 'RW', 'ST'];
        $nationalities = [
            'England', 'Scotland', 'Wales', 'Northern Ireland', 'Ireland',
            'France', 'Germany', 'Spain', 'Italy', 'Netherlands',
            'Belgium', 'Portugal', 'Brazil', 'Argentina', 'Colombia',
            'Nigeria', 'Ghana', 'Senegal', 'Morocco', 'Algeria',
            'Japan', 'South Korea', 'Australia', 'Canada', 'USA'
        ];
        
        $firstNames = [
            'James', 'John', 'Robert', 'Michael', 'William', 'David', 'Richard', 'Joseph', 'Thomas', 'Christopher',
            'Charles', 'Daniel', 'Matthew', 'Anthony', 'Mark', 'Donald', 'Steven', 'Paul', 'Andrew', 'Joshua',
            'Kenneth', 'Kevin', 'Brian', 'George', 'Edward', 'Ronald', 'Timothy', 'Jason', 'Jeffrey', 'Ryan',
            'Jacob', 'Gary', 'Nicholas', 'Eric', 'Jonathan', 'Stephen', 'Larry', 'Justin', 'Scott', 'Brandon',
            'Benjamin', 'Samuel', 'Frank', 'Gregory', 'Raymond', 'Alexander', 'Patrick', 'Jack', 'Dennis', 'Jerry',
            'Tyler', 'Aaron', 'Jose', 'Adam', 'Nathan', 'Henry', 'Douglas', 'Zachary', 'Peter', 'Kyle',
            'Walter', 'Ethan', 'Jeremy', 'Harold', 'Carl', 'Keith', 'Roger', 'Gerald', 'Christian', 'Terry',
            'Sean', 'Arthur', 'Austin', 'Noah', 'Lawrence', 'Jesse', 'Joe', 'Bryan', 'Billy', 'Jordan',
            'Albert', 'Dylan', 'Bruce', 'Willie', 'Gabriel', 'Alan', 'Juan', 'Logan', 'Wayne', 'Roy',
            'Ralph', 'Randy', 'Eugene', 'Vincent', 'Russell', 'Elijah', 'Louis', 'Bobby', 'Philip', 'Johnny'
        ];
        
        $lastNames = [
            'Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez',
            'Hernandez', 'Lopez', 'Gonzalez', 'Wilson', 'Anderson', 'Thomas', 'Taylor', 'Moore', 'Jackson', 'Martin',
            'Lee', 'Perez', 'Thompson', 'White', 'Harris', 'Sanchez', 'Clark', 'Ramirez', 'Lewis', 'Robinson',
            'Walker', 'Young', 'Allen', 'King', 'Wright', 'Scott', 'Torres', 'Nguyen', 'Hill', 'Flores',
            'Green', 'Adams', 'Nelson', 'Baker', 'Hall', 'Rivera', 'Campbell', 'Mitchell', 'Carter', 'Roberts',
            'Gomez', 'Phillips', 'Evans', 'Turner', 'Diaz', 'Parker', 'Cruz', 'Edwards', 'Collins', 'Reyes',
            'Stewart', 'Morris', 'Morales', 'Murphy', 'Cook', 'Rogers', 'Gutierrez', 'Ortiz', 'Morgan', 'Cooper',
            'Peterson', 'Bailey', 'Reed', 'Kelly', 'Howard', 'Ramos', 'Kim', 'Cox', 'Ward', 'Richardson',
            'Watson', 'Brooks', 'Chavez', 'Wood', 'James', 'Bennett', 'Gray', 'Mendoza', 'Ruiz', 'Hughes',
            'Price', 'Alvarez', 'Castillo', 'Sanders', 'Patel', 'Myers', 'Long', 'Ross', 'Foster', 'Jimenez'
        ];

        $firstName = $firstNames[array_rand($firstNames)];
        $lastName = $lastNames[array_rand($lastNames)];
        $name = $firstName . ' ' . $lastName;
        $position = $positions[array_rand($positions)];
        $nationality = $nationalities[array_rand($nationalities)];
        
        // Generate realistic age based on position
        $age = $this->getAgeForPosition($position);
        $dateOfBirth = now()->subYears($age)->subDays(rand(0, 365));
        
        // Generate realistic ratings based on position and age
        $overallRating = $this->getOverallRating($position, $age);
        $potentialRating = $this->getPotentialRating($overallRating, $age);
        
        // Generate FIFA Connect ID
        $fifaConnectId = 'PL' . str_pad($counter, 6, '0', STR_PAD_LEFT);

        return Player::updateOrCreate(
            ['fifa_connect_id' => $fifaConnectId],
            [
                'name' => $name,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'date_of_birth' => $dateOfBirth->format('Y-m-d'),
                'nationality' => $nationality,
                'position' => $position,
                'overall_rating' => $overallRating,
                'potential_rating' => $potentialRating,
                'age' => $age,
                'height' => $this->getHeightForPosition($position),
                'weight' => $this->getWeightForPosition($position),
                'preferred_foot' => rand(0, 1) ? 'Right' : 'Left',
                'work_rate' => $this->getWorkRateForPosition($position),
                'player_face_url' => 'https://picsum.photos/400/400?random=' . $counter,
                'fifa_connect_id' => $fifaConnectId,
                'club_id' => $club->id,
                'association_id' => $associationId,
                'status' => 'active',
                'value_eur' => $this->getValueForRating($overallRating, $age),
                'wage_eur' => $this->getWageForRating($overallRating),
                'contract_valid_until' => now()->addYears(rand(1, 5))->format('Y-m-d'),
                'release_clause_eur' => $this->getReleaseClauseForRating($overallRating),
            ]
        );
    }

    private function getAgeForPosition($position)
    {
        switch ($position) {
            case 'GK':
                return rand(22, 35); // Goalkeepers peak later
            case 'CB':
                return rand(20, 32); // Defenders peak in mid-20s
            case 'LB':
            case 'RB':
                return rand(19, 30); // Fullbacks peak early
            case 'CDM':
            case 'CM':
                return rand(20, 31); // Midfielders peak in mid-20s
            case 'CAM':
                return rand(19, 29); // Attacking midfielders peak early
            case 'LW':
            case 'RW':
                return rand(18, 28); // Wingers peak early
            case 'ST':
                return rand(19, 30); // Strikers peak in early-mid 20s
            default:
                return rand(20, 30);
        }
    }

    private function getOverallRating($position, $age)
    {
        $baseRating = 65; // Base rating for all players
        
        // Position modifiers
        $positionModifier = 0;
        switch ($position) {
            case 'GK':
                $positionModifier = rand(-5, 10);
                break;
            case 'CB':
                $positionModifier = rand(-3, 8);
                break;
            case 'LB':
            case 'RB':
                $positionModifier = rand(-2, 7);
                break;
            case 'CDM':
            case 'CM':
                $positionModifier = rand(-1, 9);
                break;
            case 'CAM':
                $positionModifier = rand(0, 10);
                break;
            case 'LW':
            case 'RW':
                $positionModifier = rand(1, 11);
                break;
            case 'ST':
                $positionModifier = rand(2, 12);
                break;
        }
        
        // Age modifiers
        $ageModifier = 0;
        if ($age >= 18 && $age <= 22) {
            $ageModifier = rand(-2, 3); // Young players
        } elseif ($age >= 23 && $age <= 27) {
            $ageModifier = rand(0, 5); // Peak age
        } elseif ($age >= 28 && $age <= 32) {
            $ageModifier = rand(-1, 3); // Experienced
        } else {
            $ageModifier = rand(-3, 1); // Older players
        }
        
        $rating = $baseRating + $positionModifier + $ageModifier;
        return max(60, min(95, $rating)); // Clamp between 60-95
    }

    private function getPotentialRating($overallRating, $age)
    {
        if ($age <= 22) {
            return min(95, $overallRating + rand(1, 8)); // Young players can improve
        } elseif ($age <= 25) {
            return min(95, $overallRating + rand(0, 5)); // Some improvement possible
        } else {
            return $overallRating; // Peak reached
        }
    }

    private function getHeightForPosition($position)
    {
        switch ($position) {
            case 'GK':
                return rand(185, 200); // Tall goalkeepers
            case 'CB':
                return rand(180, 195); // Tall defenders
            case 'ST':
                return rand(175, 190); // Varied striker heights
            default:
                return rand(170, 185); // Standard height for other positions
        }
    }

    private function getWeightForPosition($position)
    {
        switch ($position) {
            case 'GK':
                return rand(75, 90); // Heavier goalkeepers
            case 'CB':
                return rand(70, 85); // Strong defenders
            case 'ST':
                return rand(65, 80); // Varied striker weights
            default:
                return rand(60, 75); // Standard weight for other positions
        }
    }

    private function getWorkRateForPosition($position)
    {
        $rates = ['Low/Low', 'Low/Medium', 'Low/High', 'Medium/Low', 'Medium/Medium', 'Medium/High', 'High/Low', 'High/Medium', 'High/High'];
        
        switch ($position) {
            case 'CDM':
                return $rates[array_rand(array_slice($rates, 4, 5))]; // Medium/High to High/High
            case 'CM':
                return $rates[array_rand(array_slice($rates, 3, 6))]; // Medium/Low to High/High
            case 'LW':
            case 'RW':
                return $rates[array_rand(array_slice($rates, 4, 5))]; // Medium/High to High/High
            case 'ST':
                return $rates[array_rand(array_slice($rates, 2, 7))]; // Low/High to High/High
            default:
                return $rates[array_rand($rates)];
        }
    }

    private function getValueForRating($rating, $age)
    {
        $baseValue = pow($rating - 60, 2) * 10000; // Exponential growth with rating
        
        // Age modifier
        if ($age <= 22) {
            $baseValue *= 1.5; // Young players are more valuable
        } elseif ($age >= 30) {
            $baseValue *= 0.5; // Older players are less valuable
        }
        
        return max(100000, min(200000000, $baseValue)); // Clamp between 100k and 200M
    }

    private function getWageForRating($rating)
    {
        $baseWage = ($rating - 60) * 5000; // Linear growth with rating
        return max(1000, min(500000, $baseWage)); // Clamp between 1k and 500k
    }

    private function getReleaseClauseForRating($rating)
    {
        $baseClause = pow($rating - 60, 2) * 50000; // Exponential growth with rating
        return max(500000, min(500000000, $baseClause)); // Clamp between 500k and 500M
    }
} 