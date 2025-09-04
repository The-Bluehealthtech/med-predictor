<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Joueur;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class TunisianLeaguePlayersSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🏆 Création des 50 joueurs de la Ligue Professionnelle 1 tunisienne...');
        
        // Clubs de la Ligue Professionnelle 1
        $clubs = [
            'Espérance de Tunis' => ['city' => 'Tunis', 'stadium' => 'Stade Olympique de Radès'],
            'Club Africain' => ['city' => 'Tunis', 'stadium' => 'Stade Olympique de Radès'],
            'Étoile du Sahel' => ['city' => 'Sousse', 'stadium' => 'Stade Olympique de Sousse'],
            'CS Sfaxien' => ['city' => 'Sfax', 'stadium' => 'Stade Taïeb Mhiri'],
            'US Monastir' => ['city' => 'Monastir', 'stadium' => 'Stade Mustapha Ben Jannet'],
            'CA Bizertin' => ['city' => 'Bizerte', 'stadium' => 'Stade 15 Octobre'],
            'Stade Tunisien' => ['city' => 'Tunis', 'stadium' => 'Stade Chedli Zouiten'],
            'JS Kairouan' => ['city' => 'Kairouan', 'stadium' => 'Stade Hamda Laouani'],
            'AS Gabès' => ['city' => 'Gabès', 'stadium' => 'Stade Municipal de Gabès'],
            'US Ben Guerdane' => ['city' => 'Ben Guerdane', 'stadium' => 'Stade Municipal de Ben Guerdane']
        ];
        
        // Postes avec répartition équilibrée
        $positions = [
            'Gardien' => ['count' => 8, 'stats' => ['vitesse_max' => [18, 22], 'distance_match' => [8, 12], 'sprints' => [15, 25]]],
            'Défenseur Central' => ['count' => 12, 'stats' => ['vitesse_max' => [28, 32], 'distance_match' => [9, 11], 'sprints' => [20, 35]]],
            'Latéral' => ['count' => 10, 'stats' => ['vitesse_max' => [30, 35], 'distance_match' => [10, 12], 'sprints' => [25, 40]]],
            'Milieu Défensif' => ['count' => 8, 'stats' => ['vitesse_max' => [29, 33], 'distance_match' => [10, 12], 'sprints' => [30, 45]]],
            'Milieu Offensif' => ['count' => 6, 'stats' => ['vitesse_max' => [30, 34], 'distance_match' => [9, 11], 'sprints' => [25, 40]]],
            'Attaquant' => ['count' => 6, 'stats' => ['vitesse_max' => [31, 36], 'distance_match' => [8, 10], 'sprints' => [20, 35]]]
        ];
        
        // Nationalités avec répartition réaliste
        $nationalities = [
            'Tunisie' => ['weight' => 50, 'names' => ['Ben', 'Ben Salah', 'Ben Ali', 'Haddad', 'Trabelsi', 'Jebali', 'Masmoudi', 'Khelifi', 'Bouazizi', 'Nafti']],
            'Algérie' => ['weight' => 15, 'names' => ['Bentoumi', 'Bouguerra', 'Bouazza', 'Bentoumi', 'Bouazza']],
            'Maroc' => ['weight' => 10, 'names' => ['Benjelloun', 'Benatia', 'Bennani', 'Benjelloun']],
            'Côte d\'Ivoire' => ['weight' => 8, 'names' => ['Koné', 'Yao', 'Bamba', 'Traoré', 'Kouassi']],
            'Nigeria' => ['weight' => 6, 'names' => ['Okechukwu', 'Eze', 'Obi', 'Nwankwo']],
            'Mali' => ['weight' => 5, 'names' => ['Keita', 'Diabaté', 'Coulibaly', 'Traoré']],
            'Sénégal' => ['weight' => 3, 'names' => ['Diop', 'Ndiaye', 'Sow', 'Diallo']],
            'Cameroun' => ['weight' => 2, 'names' => ['Eto\'o', 'Mbia', 'Song', 'N\'Koulou']],
            'Brésil' => ['weight' => 1, 'names' => ['Silva', 'Santos', 'Oliveira', 'Costa']]
        ];
        
        $playerCount = 0;
        $positionCounts = array_fill_keys(array_keys($positions), 0);
        
        foreach ($clubs as $clubName => $clubInfo) {
            $playersPerClub = rand(4, 6); // 4-6 joueurs par club
            
            for ($i = 0; $i < $playersPerClub && $playerCount < 50; $i++) {
                // Sélectionner un poste disponible
                $availablePositions = array_filter($positions, function($pos, $posName) use ($positionCounts) {
                    return $positionCounts[$posName] < $pos['count'];
                }, ARRAY_FILTER_USE_BOTH);
                
                if (empty($availablePositions)) break;
                
                $position = array_rand($availablePositions);
                $positionCounts[$position]++;
                
                // Générer les données du joueur
                $playerData = $this->generatePlayerData($position, $clubName, $nationalities);
                
                // Créer le joueur
                $joueur = Joueur::create($playerData);
                
                // Créer l'utilisateur associé (commenté temporairement)
                // $this->createPlayerUser($joueur);
                
                $playerCount++;
                
                if ($playerCount >= 50) break;
            }
        }
        
        $this->command->info("✅ {$playerCount} joueurs créés avec succès !");
        $this->command->info("📊 Répartition par poste : " . json_encode($positionCounts));
    }
    
    private function generatePlayerData($position, $clubName, $nationalities)
    {
        // Sélectionner la nationalité
        $nationality = $this->selectNationality($nationalities);
        
        // Générer le nom
        $firstName = $this->generateFirstName($nationality);
        $lastName = $this->generateLastName($nationality);
        
        // Âge entre 18 et 35 ans
        $age = rand(18, 35);
        $birthDate = Carbon::now()->subYears($age)->subDays(rand(0, 365));
        
        // Générer les données de santé
        $healthData = $this->generateHealthData($age, $position);
        
        // Générer les données de performance
        $performanceData = $this->generatePerformanceData($position);
        
        // Générer les données SDOH
        $sdohData = $this->generateSDOHData($nationality, $age);
        
        // Générer la valeur marchande
        $marketValue = $this->generateMarketValue($age, $position, $performanceData);
        
        // Générer les données d'adhérence
        $adherenceData = $this->generateAdherenceData($healthData);
        
        static $playerCounter = 1;
        
        return [
            'fifa_id' => 'TUN' . str_pad($playerCounter++, 3, '0', STR_PAD_LEFT),
            'nom' => $lastName,
            'prenom' => $firstName,
            'date_naissance' => $birthDate,
            'nationalite' => $nationality,
            'poste' => $position,
            'club' => $clubName,
            'taille_cm' => $this->getHeightForPosition($position),
            'poids_kg' => $this->getWeightForPosition($position),
            'pied_fort' => rand(0, 1) ? 'Droit' : 'Gauche',
            'langues_parlees' => $this->getLanguagesForNationality($nationality),
            
            // Données de santé (Pillar 1)
            'frequence_cardiaque' => rand(55, 75),
            'tension_arterielle' => rand(110, 140) . '/' . rand(60, 90),
            'temperature' => round(36.5 + rand(-10, 10) / 10, 1),
            'saturation_o2' => rand(95, 99),
            'niveau_hydratation' => rand(70, 95),
            'cortisol_stress' => rand(5, 25),
            'score_recuperation' => rand(60, 95),
            'fatigue_musculaire' => rand(20, 60),
            'fatigue_centrale' => rand(15, 50),
            'temps_recuperation_heures' => rand(8, 72),
            'score_preparation' => rand(70, 95),
            'duree_sommeil_heures' => rand(6, 9),
            'sommeil_profond_pourcentage' => rand(15, 25),
            'sommeil_rem_pourcentage' => rand(20, 30),
            'efficacite_sommeil' => rand(75, 95),
            'qualite_sommeil' => rand(6, 10),
            'niveau_stress' => rand(20, 60),
            'niveau_anxiete' => rand(15, 50),
            'score_humeur' => rand(60, 90),
            'niveau_energie' => rand(60, 90),
            'score_concentration' => rand(65, 95),
            'nombre_pas' => rand(8000, 15000),
            'calories_brulées' => rand(2000, 3500),
            'minutes_actives' => rand(120, 300),
            'minutes_exercice' => rand(60, 180),
            'heures_debout' => rand(8, 14),
            'distance_marchee_km' => rand(5, 12),
            
            // Données SDOH (Pillar 3)
            'score_environnement_vie' => $sdohData['environment'],
            'score_soutien_social' => $sdohData['social'],
            'score_acces_soins' => $sdohData['healthcare'],
            'score_situation_financiere' => $sdohData['financial'],
            'score_bien_etre_mental' => $sdohData['mental'],
            'score_sdoh_global' => $sdohData['overall'],
            
            // Données d'adhérence (Pillar 5) - stockées dans donnees_sante
            'donnees_sante' => json_encode([
                'taux_presence_entrainements' => $adherenceData['training_attendance'],
                'score_adherence_protocole' => $adherenceData['protocol_adherence'],
                'disponibilite_generale' => $adherenceData['general_availability']
            ]),
            
            // Données de performance (Pillar 2) - stockées dans statistiques_techniques
            'statistiques_techniques' => json_encode([
                'tirs_cadres' => $performanceData['shots_on_target'],
                'tirs_totaux' => $performanceData['total_shots'],
                'precision_tirs' => $performanceData['shooting_accuracy'],
                'passes_cles' => $performanceData['key_passes'],
                'centres_reussis' => $performanceData['successful_crosses'],
                'dribbles_reussis' => $performanceData['successful_dribbles'],
                'passes_longues' => $performanceData['long_passes'],
                'tacles_reussis' => $performanceData['successful_tackles'],
                'interceptions' => $performanceData['interceptions'],
                'degaugements' => $performanceData['clearances'],
                'fautes_commises' => $performanceData['fouls_committed'],
                'cartons_jaunes' => $performanceData['yellow_cards'],
                'cartons_rouges' => $performanceData['red_cards'],
                'distance_parcourue_km' => $performanceData['distance_covered'],
                'vitesse_maximale_kmh' => $performanceData['max_speed'],
                'vitesse_moyenne_kmh' => $performanceData['avg_speed'],
                'sprints' => $performanceData['sprints'],
                'accelerations' => $performanceData['accelerations'],
                'decelerations' => $performanceData['decelerations'],
                'changements_direction' => $performanceData['direction_changes'],
                'sautes' => $performanceData['jumps']
            ]),
            
            // Valeur marchande (Pillar 4) - stockée dans valeur_marchande
            'valeur_marchande' => $marketValue,
            
            // Données supplémentaires
            'derniere_mise_a_jour_donnees' => now(),
            'qualite_donnees' => 'good',
            
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
    
    private function selectNationality($nationalities)
    {
        $totalWeight = array_sum(array_column($nationalities, 'weight'));
        $random = rand(1, $totalWeight);
        
        foreach ($nationalities as $nationality => $data) {
            $random -= $data['weight'];
            if ($random <= 0) {
                return $nationality;
            }
        }
        
        return 'Tunisie'; // Fallback
    }
    
    private function generateFirstName($nationality)
    {
        $firstNames = [
            'Tunisie' => ['Youssef', 'Ahmed', 'Hamza', 'Omar', 'Karim', 'Ibrahim', 'Mohamed', 'Ali', 'Sofiane', 'Wassim'],
            'Algérie' => ['Riyad', 'Islam', 'Yacine', 'Sofiane', 'Rachid', 'Karim', 'Adel', 'Samir'],
            'Maroc' => ['Hakim', 'Younès', 'Karim', 'Mehdi', 'Fayçal', 'Rachid', 'Adil', 'Youssef'],
            'Côte d\'Ivoire' => ['Seydou', 'Yaya', 'Salomon', 'Didier', 'Gervinho', 'Wilfried', 'Serge', 'Max'],
            'Nigeria' => ['John', 'Emmanuel', 'Victor', 'Kelechi', 'Alex', 'Peter', 'Daniel', 'Michael'],
            'Mali' => ['Seydou', 'Mahamadou', 'Moussa', 'Cheick', 'Bakary', 'Adama', 'Yacouba'],
            'Sénégal' => ['Sadio', 'Kalidou', 'Idrissa', 'Moussa', 'Cheikhou', 'Pape', 'Salif'],
            'Cameroun' => ['Samuel', 'Joel', 'Vincent', 'Nicolas', 'Eric', 'Stephane', 'Pierre'],
            'Brésil' => ['Lucas', 'Gabriel', 'Rafael', 'Thiago', 'Bruno', 'Felipe', 'Carlos']
        ];
        
        $names = $firstNames[$nationality] ?? $firstNames['Tunisie'];
        return $names[array_rand($names)];
    }
    
    private function generateLastName($nationality)
    {
        $lastNames = [
            'Tunisie' => ['Ben Salah', 'Ben Ali', 'Haddad', 'Trabelsi', 'Jebali', 'Masmoudi', 'Khelifi', 'Bouazizi', 'Nafti', 'Ghannouchi'],
            'Algérie' => ['Bentoumi', 'Bouguerra', 'Bouazza', 'Bentoumi', 'Bouazza', 'Mahrez', 'Bennacer', 'Bounedjah'],
            'Maroc' => ['Benjelloun', 'Benatia', 'Bennani', 'Benjelloun', 'Hakimi', 'Ziyech', 'En-Nesyri'],
            'Côte d\'Ivoire' => ['Koné', 'Yao', 'Bamba', 'Traoré', 'Kouassi', 'Drogba', 'Yaya', 'Gervinho'],
            'Nigeria' => ['Okechukwu', 'Eze', 'Obi', 'Nwankwo', 'Kanu', 'Yakubu', 'Martins'],
            'Mali' => ['Keita', 'Diabaté', 'Coulibaly', 'Traoré', 'Sissoko', 'Diarra', 'Kanouté'],
            'Sénégal' => ['Diop', 'Ndiaye', 'Sow', 'Diallo', 'Mané', 'Koulibaly', 'Gueye'],
            'Cameroun' => ['Eto\'o', 'Mbia', 'Song', 'N\'Koulou', 'Aboubakar', 'Anguissa', 'Ondoua'],
            'Brésil' => ['Silva', 'Santos', 'Oliveira', 'Costa', 'Neymar', 'Vinicius', 'Rodrygo']
        ];
        
        $names = $lastNames[$nationality] ?? $lastNames['Tunisie'];
        return $names[array_rand($names)];
    }
    
    private function getHeightForPosition($position)
    {
        $heights = [
            'Gardien' => [185, 195],
            'Défenseur Central' => [180, 190],
            'Latéral' => [175, 185],
            'Milieu Défensif' => [175, 185],
            'Milieu Offensif' => [170, 180],
            'Attaquant' => [175, 185]
        ];
        
        $range = $heights[$position] ?? [170, 180];
        return rand($range[0], $range[1]);
    }
    
    private function getWeightForPosition($position)
    {
        $heights = $this->getHeightForPosition($position);
        $bmi = rand(20, 25); // BMI normal pour un sportif
        return round(($heights / 100) * ($heights / 100) * $bmi);
    }
    
    private function getLanguagesForNationality($nationality)
    {
        $languages = [
            'Tunisie' => 'Arabe, Français',
            'Algérie' => 'Arabe, Français, Berbère',
            'Maroc' => 'Arabe, Français, Berbère',
            'Côte d\'Ivoire' => 'Français, Dioula',
            'Nigeria' => 'Anglais, Yoruba, Igbo',
            'Mali' => 'Français, Bambara',
            'Sénégal' => 'Français, Wolof',
            'Cameroun' => 'Français, Anglais',
            'Brésil' => 'Portugais, Espagnol'
        ];
        
        return $languages[$nationality] ?? 'Français, Anglais';
    }
    
    private function generateHealthData($age, $position)
    {
        // Plus l'âge est élevé, plus le risque de blessure est élevé
        $injuryRisk = min(0.3 + ($age - 18) * 0.02, 0.8);
        
        return [
            'injury_risk' => $injuryRisk,
            'recovery_score' => rand(60, 95),
            'stress_level' => rand(20, 60)
        ];
    }
    
    private function generatePerformanceData($position)
    {
        $baseStats = [
            'Gardien' => ['shots' => [0, 2], 'passes' => [15, 25], 'tackles' => [0, 1], 'distance' => [8, 12]],
            'Défenseur Central' => ['shots' => [0, 3], 'passes' => [40, 60], 'tackles' => [2, 5], 'distance' => [9, 11]],
            'Latéral' => ['shots' => [0, 2], 'passes' => [25, 40], 'tackles' => [1, 4], 'distance' => [10, 12]],
            'Milieu Défensif' => ['shots' => [0, 3], 'passes' => [50, 70], 'tackles' => [3, 6], 'distance' => [10, 12]],
            'Milieu Offensif' => ['shots' => [1, 4], 'passes' => [30, 50], 'tackles' => [1, 3], 'distance' => [9, 11]],
            'Attaquant' => ['shots' => [2, 6], 'passes' => [15, 30], 'tackles' => [0, 2], 'distance' => [8, 10]]
        ];
        
        $stats = $baseStats[$position] ?? $baseStats['Milieu Défensif'];
        
        return [
            'shots_on_target' => rand($stats['shots'][0], $stats['shots'][1]),
            'total_shots' => rand($stats['shots'][0] * 2, $stats['shots'][1] * 3),
            'shooting_accuracy' => rand(60, 85),
            'key_passes' => rand(2, 8),
            'successful_crosses' => rand(1, 5),
            'successful_dribbles' => rand(2, 8),
            'long_passes' => rand(5, 15),
            'successful_tackles' => rand($stats['tackles'][0], $stats['tackles'][1]),
            'interceptions' => rand(1, 6),
            'clearances' => rand(2, 8),
            'fouls_committed' => rand(1, 4),
            'yellow_cards' => rand(0, 3),
            'red_cards' => rand(0, 1),
            'distance_covered' => rand($stats['distance'][0] * 1000, $stats['distance'][1] * 1000),
            'max_speed' => rand(28, 36),
            'avg_speed' => rand(8, 12),
            'sprints' => rand(15, 45),
            'accelerations' => rand(20, 50),
            'decelerations' => rand(15, 40),
            'direction_changes' => rand(30, 80),
            'jumps' => rand(10, 25)
        ];
    }
    
    private function generateSDOHData($nationality, $age)
    {
        // Les joueurs plus jeunes ont généralement un meilleur soutien social
        $ageFactor = max(0.5, 1 - ($age - 18) * 0.02);
        
        // Les joueurs locaux ont généralement un meilleur environnement
        $localFactor = $nationality === 'Tunisie' ? 1.2 : 0.8;
        
        $baseScore = rand(60, 90) * $ageFactor * $localFactor;
        
        return [
            'environment' => min(100, max(0, $baseScore + rand(-10, 10))),
            'social' => min(100, max(0, $baseScore + rand(-15, 15))),
            'healthcare' => min(100, max(0, $baseScore + rand(-5, 15))),
            'financial' => min(100, max(0, $baseScore + rand(-20, 10))),
            'mental' => min(100, max(0, $baseScore + rand(-10, 10))),
            'overall' => min(100, max(0, $baseScore + rand(-10, 10)))
        ];
    }
    
    private function generateMarketValue($age, $position, $performanceData)
    {
        // Base de valeur selon le poste
        $positionMultiplier = [
            'Gardien' => 0.8,
            'Défenseur Central' => 1.0,
            'Latéral' => 1.1,
            'Milieu Défensif' => 1.2,
            'Milieu Offensif' => 1.3,
            'Attaquant' => 1.4
        ];
        
        $multiplier = $positionMultiplier[$position] ?? 1.0;
        
        // Facteur d'âge (pic à 25-27 ans)
        $ageFactor = 1.0;
        if ($age <= 23) $ageFactor = 0.7;
        elseif ($age <= 25) $ageFactor = 1.0;
        elseif ($age <= 27) $ageFactor = 1.1;
        elseif ($age <= 30) $ageFactor = 0.9;
        else $ageFactor = 0.6;
        
        // Base de valeur (50k à 1M euros)
        $baseValue = rand(50000, 1000000);
        
        return round($baseValue * $multiplier * $ageFactor);
    }
    
    private function generateAdherenceData($healthData)
    {
        $injuryRisk = $healthData['injury_risk'];
        
        return [
            'training_attendance' => max(85, 100 - ($injuryRisk * 20)),
            'protocol_adherence' => $injuryRisk > 0.5 ? 'Moyen' : 'Excellent',
            'general_availability' => max(70, 100 - ($injuryRisk * 30))
        ];
    }
    
    private function createPlayerUser($joueur)
    {
        static $emailCounter = 1;
        $email = strtolower($joueur->prenom . '.' . $joueur->nom) . $emailCounter . '@fifa.com';
        $email = str_replace([' ', '\''], ['', ''], $email);
        $emailCounter++;
        
        User::create([
            'name' => $joueur->prenom . ' ' . $joueur->nom,
            'email' => $email,
            'password' => Hash::make('Joueur123!'),
            'role' => 'player',
            'entity_type' => 'player',
            'entity_id' => $joueur->id,
            'association_id' => 1,
            'club_id' => 1,
            'team_id' => 1,
            'fifa_connect_id' => 'PLAYER' . str_pad($joueur->id, 3, '0', STR_PAD_LEFT),
            'phone' => '+216' . rand(20000000, 99999999),
            'preferences' => json_encode(['theme' => 'light', 'language' => 'fr']),
            'last_login_at' => now(),
            'status' => 'active',
            'login_count' => 0,
            'timezone' => 'Africa/Tunis',
            'language' => 'fr',
            'notifications_email' => true,
            'notifications_sms' => false,
            'player_id' => $joueur->id
        ]);
    }
}
