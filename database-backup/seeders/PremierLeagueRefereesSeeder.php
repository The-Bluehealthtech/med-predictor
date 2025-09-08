<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\MatchOfficial;
use Carbon\Carbon;

class PremierLeagueRefereesSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('👨‍⚖️ Création des arbitres Premier League...');
        
        // Créer les arbitres principaux
        $this->createMainReferees();
        
        // Créer les arbitres assistants
        $this->createAssistantReferees();
        
        // Créer les quatrièmes arbitres
        $this->createFourthOfficials();
        
        // Créer les arbitres VAR
        $this->createVARReferees();
        
        $this->command->info('✅ Tous les arbitres Premier League créés !');
    }

    private function createMainReferees()
    {
        $this->command->info('🏟️ Création des arbitres principaux...');
        
        $mainReferees = [
            [
                'name' => 'Michael Oliver',
                'email' => 'michael.oliver@premierleague.com',
                'role' => 'referee',
                'fifa_id' => 'REF_OLIVER_001',
                'experience_years' => 15,
                'specializations' => ['Premier League', 'Champions League', 'International'],
                'total_matches' => 450,
                'yellow_cards_per_match' => 3.2,
                'red_cards_per_match' => 0.1,
                'penalties_per_match' => 0.3,
            ],
            [
                'name' => 'Anthony Taylor',
                'email' => 'anthony.taylor@premierleague.com',
                'role' => 'referee',
                'fifa_id' => 'REF_TAYLOR_002',
                'experience_years' => 12,
                'specializations' => ['Premier League', 'Europa League', 'International'],
                'total_matches' => 380,
                'yellow_cards_per_match' => 3.5,
                'red_cards_per_match' => 0.15,
                'penalties_per_match' => 0.4,
            ],
            [
                'name' => 'Paul Tierney',
                'email' => 'paul.tierney@premierleague.com',
                'role' => 'referee',
                'fifa_id' => 'REF_TIERNEY_003',
                'experience_years' => 10,
                'specializations' => ['Premier League', 'FA Cup'],
                'total_matches' => 280,
                'yellow_cards_per_match' => 3.1,
                'red_cards_per_match' => 0.08,
                'penalties_per_match' => 0.25,
            ],
            [
                'name' => 'Stuart Attwell',
                'email' => 'stuart.attwell@premierleague.com',
                'role' => 'referee',
                'fifa_id' => 'REF_ATTWELL_004',
                'experience_years' => 8,
                'specializations' => ['Premier League', 'Championship'],
                'total_matches' => 220,
                'yellow_cards_per_match' => 3.3,
                'red_cards_per_match' => 0.12,
                'penalties_per_match' => 0.35,
            ],
            [
                'name' => 'David Coote',
                'email' => 'david.coote@premierleague.com',
                'role' => 'referee',
                'fifa_id' => 'REF_COOTE_005',
                'experience_years' => 6,
                'specializations' => ['Premier League', 'VAR'],
                'total_matches' => 180,
                'yellow_cards_per_match' => 3.0,
                'red_cards_per_match' => 0.09,
                'penalties_per_match' => 0.28,
            ],
            [
                'name' => 'Andy Madley',
                'email' => 'andy.madley@premierleague.com',
                'role' => 'referee',
                'fifa_id' => 'REF_MADLEY_006',
                'experience_years' => 5,
                'specializations' => ['Premier League', 'VAR'],
                'total_matches' => 150,
                'yellow_cards_per_match' => 2.9,
                'red_cards_per_match' => 0.07,
                'penalties_per_match' => 0.22,
            ],
            [
                'name' => 'Simon Hooper',
                'email' => 'simon.hooper@premierleague.com',
                'role' => 'referee',
                'fifa_id' => 'REF_HOOPER_007',
                'experience_years' => 7,
                'specializations' => ['Premier League', 'VAR'],
                'total_matches' => 200,
                'yellow_cards_per_match' => 3.1,
                'red_cards_per_match' => 0.11,
                'penalties_per_match' => 0.30,
            ],
            [
                'name' => 'John Brooks',
                'email' => 'john.brooks@premierleague.com',
                'role' => 'referee',
                'fifa_id' => 'REF_BROOKS_008',
                'experience_years' => 4,
                'specializations' => ['Premier League', 'VAR'],
                'total_matches' => 120,
                'yellow_cards_per_match' => 2.8,
                'red_cards_per_match' => 0.06,
                'penalties_per_match' => 0.20,
            ],
            [
                'name' => 'Darren England',
                'email' => 'darren.england@premierleague.com',
                'role' => 'referee',
                'fifa_id' => 'REF_ENGLAND_009',
                'experience_years' => 6,
                'specializations' => ['Premier League', 'VAR'],
                'total_matches' => 170,
                'yellow_cards_per_match' => 3.2,
                'red_cards_per_match' => 0.10,
                'penalties_per_match' => 0.32,
            ],
            [
                'name' => 'Jarred Gillett',
                'email' => 'jarred.gillett@premierleague.com',
                'role' => 'referee',
                'fifa_id' => 'REF_GILLETT_010',
                'experience_years' => 3,
                'specializations' => ['Premier League', 'VAR'],
                'total_matches' => 90,
                'yellow_cards_per_match' => 2.7,
                'red_cards_per_match' => 0.05,
                'penalties_per_match' => 0.18,
            ],
            [
                'name' => 'Robert Jones',
                'email' => 'robert.jones@premierleague.com',
                'role' => 'referee',
                'fifa_id' => 'REF_JONES_011',
                'experience_years' => 5,
                'specializations' => ['Premier League', 'VAR'],
                'total_matches' => 140,
                'yellow_cards_per_match' => 3.0,
                'red_cards_per_match' => 0.08,
                'penalties_per_match' => 0.25,
            ],
            [
                'name' => 'Tim Robinson',
                'email' => 'tim.robinson@premierleague.com',
                'role' => 'referee',
                'fifa_id' => 'REF_ROBINSON_012',
                'experience_years' => 4,
                'specializations' => ['Premier League', 'VAR'],
                'total_matches' => 110,
                'yellow_cards_per_match' => 2.9,
                'red_cards_per_match' => 0.07,
                'penalties_per_match' => 0.23,
            ],
        ];

        foreach ($mainReferees as $refereeData) {
            $this->createReferee($refereeData);
        }
        
        $this->command->info("✅ " . count($mainReferees) . " arbitres principaux créés");
    }

    private function createAssistantReferees()
    {
        $this->command->info('🏟️ Création des arbitres assistants...');
        
        $assistantReferees = [
            [
                'name' => 'Gary Beswick',
                'email' => 'gary.beswick@premierleague.com',
                'role' => 'assistant_referee',
                'fifa_id' => 'AR_BESWICK_001',
                'experience_years' => 12,
                'specializations' => ['Premier League', 'International'],
                'total_matches' => 350,
            ],
            [
                'name' => 'Adam Nunn',
                'email' => 'adam.nunn@premierleague.com',
                'role' => 'assistant_referee',
                'fifa_id' => 'AR_NUNN_002',
                'experience_years' => 10,
                'specializations' => ['Premier League', 'Champions League'],
                'total_matches' => 300,
            ],
            [
                'name' => 'Simon Bennett',
                'email' => 'simon.bennett@premierleague.com',
                'role' => 'assistant_referee',
                'fifa_id' => 'AR_BENNETT_003',
                'experience_years' => 8,
                'specializations' => ['Premier League', 'Europa League'],
                'total_matches' => 250,
            ],
            [
                'name' => 'Dan Cook',
                'email' => 'dan.cook@premierleague.com',
                'role' => 'assistant_referee',
                'fifa_id' => 'AR_COOK_004',
                'experience_years' => 6,
                'specializations' => ['Premier League'],
                'total_matches' => 180,
            ],
            [
                'name' => 'Harry Lennard',
                'email' => 'harry.lennard@premierleague.com',
                'role' => 'assistant_referee',
                'fifa_id' => 'AR_LENNARD_005',
                'experience_years' => 7,
                'specializations' => ['Premier League', 'FA Cup'],
                'total_matches' => 220,
            ],
            [
                'name' => 'Neil Davies',
                'email' => 'neil.davies@premierleague.com',
                'role' => 'assistant_referee',
                'fifa_id' => 'AR_DAVIES_006',
                'experience_years' => 5,
                'specializations' => ['Premier League'],
                'total_matches' => 150,
            ],
            [
                'name' => 'James Mainwaring',
                'email' => 'james.mainwaring@premierleague.com',
                'role' => 'assistant_referee',
                'fifa_id' => 'AR_MAINWARING_007',
                'experience_years' => 4,
                'specializations' => ['Premier League'],
                'total_matches' => 120,
            ],
            [
                'name' => 'Nick Hopton',
                'email' => 'nick.hopton@premierleague.com',
                'role' => 'assistant_referee',
                'fifa_id' => 'AR_HOPTON_008',
                'experience_years' => 6,
                'specializations' => ['Premier League', 'Championship'],
                'total_matches' => 190,
            ],
        ];

        foreach ($assistantReferees as $refereeData) {
            $this->createReferee($refereeData);
        }
        
        $this->command->info("✅ " . count($assistantReferees) . " arbitres assistants créés");
    }

    private function createFourthOfficials()
    {
        $this->command->info('🏟️ Création des quatrièmes arbitres...');
        
        $fourthOfficials = [
            [
                'name' => 'Graham Scott',
                'email' => 'graham.scott@premierleague.com',
                'role' => 'fourth_official',
                'fifa_id' => 'FO_SCOTT_001',
                'experience_years' => 10,
                'specializations' => ['Premier League', 'VAR'],
                'total_matches' => 280,
            ],
            [
                'name' => 'Craig Pawson',
                'email' => 'craig.pawson@premierleague.com',
                'role' => 'fourth_official',
                'fifa_id' => 'FO_PAWSON_002',
                'experience_years' => 8,
                'specializations' => ['Premier League', 'VAR'],
                'total_matches' => 240,
            ],
            [
                'name' => 'Peter Bankes',
                'email' => 'peter.bankes@premierleague.com',
                'role' => 'fourth_official',
                'fifa_id' => 'FO_BANKES_003',
                'experience_years' => 6,
                'specializations' => ['Premier League', 'VAR'],
                'total_matches' => 180,
            ],
            [
                'name' => 'Michael Salisbury',
                'email' => 'michael.salisbury@premierleague.com',
                'role' => 'fourth_official',
                'fifa_id' => 'FO_SALISBURY_004',
                'experience_years' => 5,
                'specializations' => ['Premier League', 'VAR'],
                'total_matches' => 150,
            ],
            [
                'name' => 'Thomas Bramall',
                'email' => 'thomas.bramall@premierleague.com',
                'role' => 'fourth_official',
                'fifa_id' => 'FO_BRAMALL_005',
                'experience_years' => 4,
                'specializations' => ['Premier League', 'VAR'],
                'total_matches' => 120,
            ],
            [
                'name' => 'Josh Smith',
                'email' => 'josh.smith@premierleague.com',
                'role' => 'fourth_official',
                'fifa_id' => 'FO_SMITH_006',
                'experience_years' => 3,
                'specializations' => ['Premier League', 'VAR'],
                'total_matches' => 90,
            ],
            [
                'name' => 'Darren Bond',
                'email' => 'darren.bond@premierleague.com',
                'role' => 'fourth_official',
                'fifa_id' => 'FO_BOND_007',
                'experience_years' => 5,
                'specializations' => ['Premier League', 'VAR'],
                'total_matches' => 160,
            ],
            [
                'name' => 'Sam Allison',
                'email' => 'sam.allison@premierleague.com',
                'role' => 'fourth_official',
                'fifa_id' => 'FO_ALLISON_008',
                'experience_years' => 4,
                'specializations' => ['Premier League', 'VAR'],
                'total_matches' => 130,
            ],
        ];

        foreach ($fourthOfficials as $refereeData) {
            $this->createReferee($refereeData);
        }
        
        $this->command->info("✅ " . count($fourthOfficials) . " quatrièmes arbitres créés");
    }

    private function createVARReferees()
    {
        $this->command->info('🏟️ Création des arbitres VAR...');
        
        $varReferees = [
            [
                'name' => 'Lee Mason',
                'email' => 'lee.mason@premierleague.com',
                'role' => 'var_referee',
                'fifa_id' => 'VAR_MASON_001',
                'experience_years' => 15,
                'specializations' => ['VAR', 'Premier League', 'International'],
                'total_matches' => 500,
                'var_decisions_per_match' => 2.1,
                'var_accuracy' => 95.5,
            ],
            [
                'name' => 'Neil Swarbrick',
                'email' => 'neil.swarbrick@premierleague.com',
                'role' => 'var_referee',
                'fifa_id' => 'VAR_SWARBRICK_002',
                'experience_years' => 12,
                'specializations' => ['VAR', 'Premier League'],
                'total_matches' => 380,
                'var_decisions_per_match' => 1.9,
                'var_accuracy' => 94.8,
            ],
            [
                'name' => 'Mike Dean',
                'email' => 'mike.dean@premierleague.com',
                'role' => 'var_referee',
                'fifa_id' => 'VAR_DEAN_003',
                'experience_years' => 20,
                'specializations' => ['VAR', 'Premier League', 'International'],
                'total_matches' => 600,
                'var_decisions_per_match' => 2.3,
                'var_accuracy' => 96.2,
            ],
            [
                'name' => 'Kevin Friend',
                'email' => 'kevin.friend@premierleague.com',
                'role' => 'var_referee',
                'fifa_id' => 'VAR_FRIEND_004',
                'experience_years' => 10,
                'specializations' => ['VAR', 'Premier League'],
                'total_matches' => 320,
                'var_decisions_per_match' => 1.8,
                'var_accuracy' => 93.5,
            ],
            [
                'name' => 'Stuart Attwell',
                'email' => 'stuart.attwell.var@premierleague.com',
                'role' => 'var_referee',
                'fifa_id' => 'VAR_ATTWELL_005',
                'experience_years' => 8,
                'specializations' => ['VAR', 'Premier League'],
                'total_matches' => 250,
                'var_decisions_per_match' => 2.0,
                'var_accuracy' => 94.1,
            ],
            [
                'name' => 'David Coote',
                'email' => 'david.coote.var@premierleague.com',
                'role' => 'var_referee',
                'fifa_id' => 'VAR_COOTE_006',
                'experience_years' => 6,
                'specializations' => ['VAR', 'Premier League'],
                'total_matches' => 180,
                'var_decisions_per_match' => 1.7,
                'var_accuracy' => 92.8,
            ],
            [
                'name' => 'Jarred Gillett',
                'email' => 'jarred.gillett.var@premierleague.com',
                'role' => 'var_referee',
                'fifa_id' => 'VAR_GILLETT_007',
                'experience_years' => 3,
                'specializations' => ['VAR', 'Premier League'],
                'total_matches' => 120,
                'var_decisions_per_match' => 1.6,
                'var_accuracy' => 91.5,
            ],
            [
                'name' => 'Andy Madley',
                'email' => 'andy.madley.var@premierleague.com',
                'role' => 'var_referee',
                'fifa_id' => 'VAR_MADLEY_008',
                'experience_years' => 5,
                'specializations' => ['VAR', 'Premier League'],
                'total_matches' => 160,
                'var_decisions_per_match' => 1.9,
                'var_accuracy' => 93.2,
            ],
        ];

        foreach ($varReferees as $refereeData) {
            $this->createReferee($refereeData);
        }
        
        $this->command->info("✅ " . count($varReferees) . " arbitres VAR créés");
    }

    private function createReferee($refereeData)
    {
        $user = User::updateOrCreate(
            ['email' => $refereeData['email']],
            [
                'name' => $refereeData['name'],
                'email' => $refereeData['email'],
                'role' => $refereeData['role'],
                'password' => bcrypt('password'),
                'status' => 'active',
                'association_id' => \App\Models\Association::where('name', 'The Football Association')->first()->id ?? 1,
            ]
        );

        // Créer un profil d'arbitre avec des statistiques
        $this->createRefereeProfile($user, $refereeData);
        
        $this->command->info("✅ Arbitre créé: {$refereeData['name']} ({$refereeData['role']})");
        
        return $user;
    }

    private function createRefereeProfile($user, $refereeData)
    {
        $user->update([
            'fifa_connect_id' => $refereeData['fifa_id'],
            'phone' => $this->getRandomPhone(),
            'preferences' => json_encode([
                'experience_years' => $refereeData['experience_years'] ?? 5,
                'specializations' => $refereeData['specializations'] ?? ['Premier League'],
                'total_matches' => $refereeData['total_matches'] ?? 100,
                'yellow_cards_per_match' => $refereeData['yellow_cards_per_match'] ?? 3.0,
                'red_cards_per_match' => $refereeData['red_cards_per_match'] ?? 0.1,
                'penalties_per_match' => $refereeData['penalties_per_match'] ?? 0.3,
                'var_decisions_per_match' => 0,
                'var_accuracy' => 0,
            ]),
        ]);
    }

    private function getRandomPhone()
    {
        return '+44 ' . rand(7000, 7999) . ' ' . rand(100000, 999999);
    }
}
