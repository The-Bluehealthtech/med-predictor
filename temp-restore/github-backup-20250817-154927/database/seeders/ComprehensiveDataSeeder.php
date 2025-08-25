<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Club;
use App\Models\Association;
use App\Models\Competition;
use App\Models\Team;
use App\Models\Player;
use App\Models\FifaConnectId;
use App\Models\PlayerLicense;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ComprehensiveDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🌍 Starting comprehensive data seeding...');

        // 1. Create FIFA Connect IDs for all stakeholders
        $this->createFifaConnectIds();

        // 2. Create comprehensive stakeholder users
        $this->createStakeholders();

        // 3. Create additional clubs and teams
        $this->createAdditionalClubs();

        // 4. Create divisions and leagues
        $this->createDivisionsAndLeagues();

        // 5. Create match officials and referees
        $this->createMatchOfficials();

        // 6. Create medical staff
        $this->createMedicalStaff();

        // 7. Create additional players for all teams
        $this->createAdditionalPlayers();

        $this->command->info('✅ Comprehensive data seeding completed!');
    }

    private function createFifaConnectIds(): void
    {
        $this->command->info('Creating FIFA Connect IDs...');

        $entityTypes = ['player', 'referee', 'official', 'medical', 'club', 'association'];
        
        foreach ($entityTypes as $type) {
            for ($i = 1; $i <= 50; $i++) {
                $fifaId = strtoupper($type) . str_pad($i, 6, '0', STR_PAD_LEFT);
                
                // Check if this FIFA ID already exists
                if (!FifaConnectId::where('fifa_id', $fifaId)->exists()) {
                    FifaConnectId::create([
                        'fifa_id' => $fifaId,
                        'entity_type' => $type,
                        'status' => 'active',
                        'metadata' => [
                            'created_by' => 'seeder',
                            'category' => $type
                        ]
                    ]);
                }
            }
        }
    }

    private function createStakeholders(): void
    {
        $this->command->info('Creating comprehensive stakeholders...');

        // Referees
        $referees = [
            ['name' => 'Michael Oliver', 'email' => 'michael.oliver@fifa.com', 'role' => 'referee'],
            ['name' => 'Anthony Taylor', 'email' => 'anthony.taylor@fifa.com', 'role' => 'referee'],
            ['name' => 'Martin Atkinson', 'email' => 'martin.atkinson@fifa.com', 'role' => 'referee'],
            ['name' => 'Craig Pawson', 'email' => 'craig.pawson@fifa.com', 'role' => 'referee'],
            ['name' => 'Paul Tierney', 'email' => 'paul.tierney@fifa.com', 'role' => 'referee'],
            ['name' => 'Stuart Attwell', 'email' => 'stuart.attwell@fifa.com', 'role' => 'referee'],
            ['name' => 'David Coote', 'email' => 'david.coote@fifa.com', 'role' => 'referee'],
            ['name' => 'Chris Kavanagh', 'email' => 'chris.kavanagh@fifa.com', 'role' => 'referee'],
            ['name' => 'Darren England', 'email' => 'darren.england@fifa.com', 'role' => 'referee'],
            ['name' => 'Jarred Gillett', 'email' => 'jarred.gillett@fifa.com', 'role' => 'referee'],
        ];

        foreach ($referees as $referee) {
            if (!User::where('email', $referee['email'])->exists()) {
                User::create([
                    'name' => $referee['name'],
                    'email' => $referee['email'],
                    'password' => Hash::make('password123'),
                    'role' => $referee['role'],
                    'permissions' => ['match_sheet_management', 'referee_access'],
                    'status' => 'active',
                    'fifa_connect_id' => FifaConnectId::where('entity_type', 'referee')->inRandomOrder()->first()->id,
                ]);
            }
        }

        // Match Officials
        $officials = [
            ['name' => 'Simon Bennett', 'email' => 'simon.bennett@fifa.com', 'role' => 'match_official'],
            ['name' => 'Gary Beswick', 'email' => 'gary.beswick@fifa.com', 'role' => 'match_official'],
            ['name' => 'Adam Nunn', 'email' => 'adam.nunn@fifa.com', 'role' => 'match_official'],
            ['name' => 'Lee Betts', 'email' => 'lee.betts@fifa.com', 'role' => 'match_official'],
            ['name' => 'Constantine Hatzidakis', 'email' => 'constantine.hatzidakis@fifa.com', 'role' => 'match_official'],
            ['name' => 'Harry Lennard', 'email' => 'harry.lennard@fifa.com', 'role' => 'match_official'],
            ['name' => 'Sian Massey-Ellis', 'email' => 'sian.massey-ellis@fifa.com', 'role' => 'match_official'],
            ['name' => 'Natalie Aspinall', 'email' => 'natalie.aspinall@fifa.com', 'role' => 'match_official'],
        ];

        foreach ($officials as $official) {
            if (!User::where('email', $official['email'])->exists()) {
                User::create([
                    'name' => $official['name'],
                    'email' => $official['email'],
                    'password' => Hash::make('password123'),
                    'role' => $official['role'],
                    'permissions' => ['match_sheet_management'],
                    'status' => 'active',
                    'fifa_connect_id' => FifaConnectId::where('entity_type', 'official')->inRandomOrder()->first()->id,
                ]);
            }
        }
    }

    private function createAdditionalClubs(): void
    {
        $this->command->info('Creating additional clubs...');

        $additionalClubs = [
            ['name' => 'Leicester City', 'city' => 'Leicester', 'country' => 'England'],
            ['name' => 'West Ham United', 'city' => 'London', 'country' => 'England'],
            ['name' => 'Brighton & Hove Albion', 'city' => 'Brighton', 'country' => 'England'],
            ['name' => 'Crystal Palace', 'city' => 'London', 'country' => 'England'],
            ['name' => 'Aston Villa', 'city' => 'Birmingham', 'country' => 'England'],
            ['name' => 'Wolverhampton Wanderers', 'city' => 'Wolverhampton', 'country' => 'England'],
            ['name' => 'Newcastle United', 'city' => 'Newcastle', 'country' => 'England'],
            ['name' => 'Southampton', 'city' => 'Southampton', 'country' => 'England'],
            ['name' => 'Burnley', 'city' => 'Burnley', 'country' => 'England'],
            ['name' => 'Watford', 'city' => 'Watford', 'country' => 'England'],
        ];

        $association = Association::first();

        foreach ($additionalClubs as $clubData) {
            $club = Club::firstOrCreate(
                ['name' => $clubData['name']],
                [
                    'name' => $clubData['name'],
                    'city' => $clubData['city'],
                    'country' => $clubData['country'],
                    'association_id' => $association->id,
                    'founded_year' => rand(1880, 1920),
                    'stadium_name' => $clubData['name'] . ' Stadium',
                    'stadium_capacity' => rand(20000, 60000),
                    'logo_url' => '/images/defaults/club-logo.png',
                    'status' => 'active',
                    'fifa_connect_id' => FifaConnectId::where('entity_type', 'club')->inRandomOrder()->first()->id,
                ]
            );

            // Create team for each club
            Team::firstOrCreate(
                ['club_id' => $club->id, 'name' => $club->name . ' First Team'],
                [
                    'club_id' => $club->id,
                    'name' => $club->name . ' First Team',
                    'type' => 'first_team',
                    'season' => '2024/25',
                    'status' => 'active',
                ]
            );
        }
    }

    private function createDivisionsAndLeagues(): void
    {
        $this->command->info('Creating divisions and leagues...');

        $competitions = [
            [
                'name' => 'Premier League',
                'type' => 'league',
                'country' => 'England',
                'season' => '2024/25',
                'level' => 1,
                'description' => 'Top tier of English football'
            ],
            [
                'name' => 'Championship',
                'type' => 'league',
                'country' => 'England',
                'season' => '2024/25',
                'level' => 2,
                'description' => 'Second tier of English football'
            ],
            [
                'name' => 'League One',
                'type' => 'league',
                'country' => 'England',
                'season' => '2024/25',
                'level' => 3,
                'description' => 'Third tier of English football'
            ],
            [
                'name' => 'League Two',
                'type' => 'league',
                'country' => 'England',
                'season' => '2024/25',
                'level' => 4,
                'description' => 'Fourth tier of English football'
            ],
            [
                'name' => 'FA Cup',
                'type' => 'cup',
                'country' => 'England',
                'season' => '2024/25',
                'level' => 1,
                'description' => 'Premier domestic cup competition'
            ],
            [
                'name' => 'Carabao Cup',
                'type' => 'cup',
                'country' => 'England',
                'season' => '2024/25',
                'level' => 2,
                'description' => 'League Cup competition'
            ],
            [
                'name' => 'UEFA Champions League',
                'type' => 'international',
                'country' => 'Europe',
                'season' => '2024/25',
                'level' => 1,
                'description' => 'Premier European club competition'
            ],
            [
                'name' => 'UEFA Europa League',
                'type' => 'international',
                'country' => 'Europe',
                'season' => '2024/25',
                'level' => 2,
                'description' => 'Secondary European club competition'
            ],
        ];

        foreach ($competitions as $compData) {
            Competition::firstOrCreate(
                ['name' => $compData['name'], 'season' => $compData['season']],
                [
                    'name' => $compData['name'],
                    'type' => $compData['type'],
                    'country' => $compData['country'],
                    'season' => $compData['season'],
                    'level' => $compData['level'],
                    'description' => $compData['description'],
                    'status' => 'active',
                    'require_federation_license' => true,
                ]
            );
        }
    }

    private function createMatchOfficials(): void
    {
        $this->command->info('Creating match officials...');

        $officialRoles = [
            'referee' => ['match_sheet_management', 'referee_access'],
            'assistant_referee' => ['match_sheet_management'],
            'fourth_official' => ['match_sheet_management'],
            'var_official' => ['match_sheet_management'],
            'match_commissioner' => ['match_sheet_management', 'competition_management_access'],
        ];

        $officialNames = [
            'John Smith', 'David Johnson', 'Robert Williams', 'Michael Brown',
            'James Davis', 'Richard Miller', 'Thomas Wilson', 'Christopher Moore',
            'Daniel Taylor', 'Matthew Anderson', 'Anthony Thomas', 'Mark Jackson',
            'Donald White', 'Steven Harris', 'Paul Martin', 'Andrew Thompson',
            'Joshua Garcia', 'Kenneth Martinez', 'Kevin Robinson', 'Brian Clark',
        ];

        foreach ($officialNames as $index => $name) {
            $role = array_keys($officialRoles)[$index % count($officialRoles)];
            $email = strtolower(str_replace(' ', '.', $name)) . '@fifa.com';
            
            User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make('password123'),
                    'role' => $role,
                    'permissions' => $officialRoles[$role],
                    'status' => 'active',
                    'fifa_connect_id' => FifaConnectId::where('entity_type', 'official')->inRandomOrder()->first()->id,
                ]
            );
        }
    }

    private function createMedicalStaff(): void
    {
        $this->command->info('Creating medical staff...');

        $medicalRoles = [
            'club_medical' => ['healthcare_access', 'health_record_management'],
            'association_medical' => ['healthcare_access', 'health_record_management', 'medical_prediction_access'],
            'team_doctor' => ['healthcare_access', 'health_record_management'],
            'physiotherapist' => ['healthcare_access'],
            'sports_scientist' => ['healthcare_access'],
        ];

        $medicalNames = [
            'Dr. Sarah Johnson', 'Dr. Michael Chen', 'Dr. Emily Rodriguez', 'Dr. James Wilson',
            'Dr. Lisa Thompson', 'Dr. Robert Garcia', 'Dr. Jennifer Lee', 'Dr. David Brown',
            'Dr. Amanda Davis', 'Dr. Christopher Miller', 'Dr. Rachel White', 'Dr. Kevin Martinez',
            'Dr. Nicole Anderson', 'Dr. Steven Taylor', 'Dr. Michelle Clark', 'Dr. Brian Harris',
            'Dr. Jessica Moore', 'Dr. Daniel Jackson', 'Dr. Ashley Thomas', 'Dr. Matthew Robinson',
        ];

        $clubs = Club::all();
        $associations = Association::all();

        foreach ($medicalNames as $index => $name) {
            $role = array_keys($medicalRoles)[$index % count($medicalRoles)];
            $email = strtolower(str_replace(' ', '.', $name)) . '@medpredictor.com';
            
            $userData = [
                'name' => $name,
                'password' => Hash::make('password123'),
                'role' => $role,
                'permissions' => $medicalRoles[$role],
                'status' => 'active',
                'fifa_connect_id' => FifaConnectId::where('entity_type', 'medical')->inRandomOrder()->first()->id,
            ];

            // Assign to club or association based on role
            if (str_contains($role, 'club')) {
                $userData['club_id'] = $clubs->random()->id;
            } elseif (str_contains($role, 'association')) {
                $userData['association_id'] = $associations->random()->id;
            }

            User::firstOrCreate(['email' => $email], $userData);
        }
    }

    private function createAdditionalPlayers(): void
    {
        $this->command->info('Creating additional players for all teams...');

        $teams = Team::with('club')->get();
        $nationalities = $this->getNationalitiesList();
        $positions = ['ST', 'RW', 'LW', 'CAM', 'CM', 'CDM', 'CB', 'RB', 'LB', 'GK'];

        // Get all used FIFA Connect IDs for players
        $usedFifaIds = Player::pluck('fifa_connect_id')->toArray();
        $availableFifaIds = FifaConnectId::where('entity_type', 'player')
            ->whereNotIn('id', $usedFifaIds)
            ->pluck('id')
            ->toArray();
        $fifaIdIndex = 0;

        foreach ($teams as $team) {
            // Create 25 additional players per team (total 30 per team)
            for ($i = 6; $i <= 30; $i++) {
                if ($fifaIdIndex >= count($availableFifaIds)) {
                    $this->command->warn('No more available FIFA Connect IDs for players!');
                    return;
                }
                $firstName = $this->generateFirstName();
                $lastName = $this->generateLastName();
                $nationality = $nationalities[array_rand($nationalities)];
                $position = $positions[array_rand($positions)];

                $player = Player::create([
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'date_of_birth' => $this->generateDateOfBirth(),
                    'nationality' => $nationality,
                    'position' => $position,
                    'jersey_number' => $i,
                    'height' => rand(165, 195),
                    'weight' => rand(65, 85),
                    'overall_rating' => rand(60, 85),
                    'potential_rating' => rand(65, 90),
                    'club_id' => $team->club_id,
                    'fifa_connect_id' => $availableFifaIds[$fifaIdIndex++],
                    'status' => 'active',
                ]);

                // Create player license
                PlayerLicense::create([
                    'player_id' => $player->id,
                    'license_number' => 'LIC-' . strtoupper($nationality) . '-' . str_pad($player->id, 6, '0', STR_PAD_LEFT),
                    'type' => 'federation',
                    'status' => 'approved',
                    'issued_date' => now()->subDays(rand(30, 365)),
                    'expiry_date' => now()->addYears(2),
                    'issued_by' => 'FA',
                ]);
            }
        }
    }

    private function generateFirstName(): string
    {
        $firstNames = [
            'James', 'John', 'Robert', 'Michael', 'William', 'David', 'Richard', 'Joseph',
            'Thomas', 'Christopher', 'Charles', 'Daniel', 'Matthew', 'Anthony', 'Mark',
            'Donald', 'Steven', 'Paul', 'Andrew', 'Joshua', 'Kenneth', 'Kevin', 'Brian',
            'George', 'Timothy', 'Ronald', 'Jason', 'Edward', 'Jeffrey', 'Ryan', 'Jacob',
            'Gary', 'Nicholas', 'Eric', 'Jonathan', 'Stephen', 'Larry', 'Justin', 'Scott',
            'Brandon', 'Benjamin', 'Samuel', 'Frank', 'Gregory', 'Raymond', 'Alexander',
            'Patrick', 'Jack', 'Dennis', 'Jerry', 'Tyler', 'Aaron', 'Jose', 'Adam',
            'Nathan', 'Henry', 'Douglas', 'Zachary', 'Peter', 'Kyle', 'Walter', 'Ethan',
            'Jeremy', 'Harold', 'Carl', 'Keith', 'Roger', 'Gerald', 'Christian', 'Terry',
            'Sean', 'Arthur', 'Austin', 'Noah', 'Lawrence', 'Jesse', 'Joe', 'Bryan',
            'Billy', 'Jordan', 'Albert', 'Dylan', 'Bruce', 'Willie', 'Gabriel', 'Alan',
            'Juan', 'Logan', 'Wayne', 'Roy', 'Ralph', 'Randy', 'Eugene', 'Vincent',
            'Russell', 'Elijah', 'Louis', 'Bobby', 'Philip', 'Johnny'
        ];

        return $firstNames[array_rand($firstNames)];
    }

    private function generateLastName(): string
    {
        $lastNames = [
            'Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis',
            'Rodriguez', 'Martinez', 'Hernandez', 'Lopez', 'Gonzalez', 'Wilson', 'Anderson',
            'Thomas', 'Taylor', 'Moore', 'Jackson', 'Martin', 'Lee', 'Perez', 'Thompson',
            'White', 'Harris', 'Sanchez', 'Clark', 'Ramirez', 'Lewis', 'Robinson', 'Walker',
            'Young', 'Allen', 'King', 'Wright', 'Scott', 'Torres', 'Nguyen', 'Hill',
            'Flores', 'Green', 'Adams', 'Nelson', 'Baker', 'Hall', 'Rivera', 'Campbell',
            'Mitchell', 'Carter', 'Roberts', 'Gomez', 'Phillips', 'Evans', 'Turner',
            'Diaz', 'Parker', 'Cruz', 'Edwards', 'Collins', 'Reyes', 'Stewart', 'Morris',
            'Morales', 'Murphy', 'Cook', 'Rogers', 'Gutierrez', 'Ortiz', 'Morgan', 'Cooper',
            'Peterson', 'Bailey', 'Reed', 'Kelly', 'Howard', 'Ramos', 'Kim', 'Cox',
            'Ward', 'Richardson', 'Watson', 'Brooks', 'Chavez', 'Wood', 'James', 'Bennett',
            'Gray', 'Mendoza', 'Ruiz', 'Hughes', 'Price', 'Alvarez', 'Castillo', 'Sanders',
            'Patel', 'Myers', 'Long', 'Ross', 'Foster', 'Jimenez'
        ];

        return $lastNames[array_rand($lastNames)];
    }

    private function generateDateOfBirth(): string
    {
        $start = strtotime('1985-01-01');
        $end = strtotime('2005-12-31');
        $timestamp = rand($start, $end);
        return date('Y-m-d', $timestamp);
    }

    private function getNationalitiesList(): array
    {
        return [
            'England', 'Scotland', 'Wales', 'Northern Ireland', 'Republic of Ireland',
            'France', 'Germany', 'Spain', 'Italy', 'Portugal', 'Netherlands', 'Belgium',
            'Switzerland', 'Austria', 'Sweden', 'Norway', 'Denmark', 'Finland', 'Poland',
            'Czech Republic', 'Slovakia', 'Hungary', 'Romania', 'Bulgaria', 'Croatia',
            'Serbia', 'Slovenia', 'Bosnia and Herzegovina', 'Montenegro', 'Albania',
            'Greece', 'Turkey', 'Ukraine', 'Russia', 'Belarus', 'Lithuania', 'Latvia',
            'Estonia', 'Moldova', 'Georgia', 'Armenia', 'Azerbaijan', 'Kazakhstan',
            'Brazil', 'Argentina', 'Uruguay', 'Paraguay', 'Chile', 'Peru', 'Colombia',
            'Venezuela', 'Ecuador', 'Bolivia', 'Mexico', 'United States', 'Canada',
            'Costa Rica', 'Panama', 'Honduras', 'El Salvador', 'Guatemala', 'Nicaragua',
            'Jamaica', 'Trinidad and Tobago', 'Haiti', 'Dominican Republic', 'Cuba',
            'Morocco', 'Algeria', 'Tunisia', 'Libya', 'Egypt', 'Sudan', 'South Sudan',
            'Ethiopia', 'Eritrea', 'Djibouti', 'Somalia', 'Kenya', 'Uganda', 'Tanzania',
            'Rwanda', 'Burundi', 'Democratic Republic of the Congo', 'Republic of the Congo',
            'Central African Republic', 'Chad', 'Cameroon', 'Nigeria', 'Niger', 'Mali',
            'Burkina Faso', 'Senegal', 'Gambia', 'Guinea-Bissau', 'Guinea', 'Sierra Leone',
            'Liberia', 'Ivory Coast', 'Ghana', 'Togo', 'Benin', 'South Africa', 'Namibia',
            'Botswana', 'Zimbabwe', 'Zambia', 'Malawi', 'Mozambique', 'Madagascar',
            'Mauritius', 'Seychelles', 'Comoros', 'Mayotte', 'Reunion', 'China', 'Japan',
            'South Korea', 'North Korea', 'Mongolia', 'Taiwan', 'Hong Kong', 'Macau',
            'Vietnam', 'Laos', 'Cambodia', 'Thailand', 'Myanmar', 'Malaysia', 'Singapore',
            'Brunei', 'Indonesia', 'Philippines', 'East Timor', 'Papua New Guinea',
            'Australia', 'New Zealand', 'Fiji', 'Vanuatu', 'New Caledonia', 'Solomon Islands',
            'Samoa', 'Tonga', 'Tuvalu', 'Kiribati', 'Marshall Islands', 'Micronesia',
            'Palau', 'Nauru', 'India', 'Pakistan', 'Bangladesh', 'Sri Lanka', 'Nepal',
            'Bhutan', 'Maldives', 'Afghanistan', 'Iran', 'Iraq', 'Syria', 'Lebanon',
            'Jordan', 'Israel', 'Palestine', 'Saudi Arabia', 'Yemen', 'Oman', 'United Arab Emirates',
            'Qatar', 'Bahrain', 'Kuwait', 'Iceland', 'Faroe Islands', 'Greenland'
        ];
    }
} 