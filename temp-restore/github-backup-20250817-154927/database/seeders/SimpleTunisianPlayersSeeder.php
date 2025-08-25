<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Joueur;
use Carbon\Carbon;

class SimpleTunisianPlayersSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🏆 Création des 50 joueurs tunisiens avec données de base...');
        
        // Clubs de la Ligue Professionnelle 1
        $clubs = [
            'Espérance de Tunis', 'Club Africain', 'Étoile du Sahel', 'CS Sfaxien', 
            'US Monastir', 'CA Bizertin', 'Stade Tunisien', 'JS Kairouan', 
            'AS Gabès', 'US Ben Guerdane'
        ];
        
        // Postes avec répartition équilibrée
        $positions = ['Gardien', 'Défenseur Central', 'Latéral', 'Milieu Défensif', 'Milieu Offensif', 'Attaquant'];
        $positionCounts = [8, 12, 10, 8, 6, 6]; // Total: 50
        
        // Nationalités avec répartition réaliste
        $nationalities = [
            'Tunisie' => 25,      // 50%
            'Algérie' => 8,       // 16%
            'Maroc' => 5,         // 10%
            'Côte d\'Ivoire' => 4, // 8%
            'Nigeria' => 3,       // 6%
            'Mali' => 2,          // 4%
            'Sénégal' => 2,       // 4%
            'Cameroun' => 1       // 2%
        ];
        
        $playerCount = 0;
        $positionIndex = 0;
        $positionCounter = 0;
        
        foreach ($clubs as $clubIndex => $clubName) {
            $playersPerClub = rand(4, 6); // 4-6 joueurs par club
            
            for ($i = 0; $i < $playersPerClub && $playerCount < 50; $i++) {
                // Sélectionner un poste disponible
                if ($positionCounter >= $positionCounts[$positionIndex]) {
                    $positionIndex++;
                    $positionCounter = 0;
                }
                
                $position = $positions[$positionIndex];
                $positionCounter++;
                
                // Sélectionner la nationalité
                $nationality = $this->selectNationality($nationalities);
                
                // Générer le nom
                $firstName = $this->generateFirstName($nationality);
                $lastName = $this->generateLastName($nationality);
                
                // Âge entre 18 et 35 ans
                $age = rand(18, 35);
                $birthDate = Carbon::now()->subYears($age)->subDays(rand(0, 365));
                
                // Générer les données du joueur
                $playerData = $this->generatePlayerData($firstName, $lastName, $birthDate, $nationality, $position, $clubName, $age);
                
                // Créer le joueur
                Joueur::create($playerData);
                
                $playerCount++;
                
                if ($playerCount >= 50) break;
            }
        }
        
        $this->command->info("✅ {$playerCount} joueurs créés avec succès !");
        $this->command->info("📊 Répartition par poste : " . json_encode(array_combine($positions, $positionCounts)));
    }
    
    private function selectNationality($nationalities)
    {
        $totalWeight = array_sum($nationalities);
        $random = rand(1, $totalWeight);
        
        foreach ($nationalities as $nationality => $weight) {
            $random -= $weight;
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
            'Cameroun' => ['Samuel', 'Joel', 'Vincent', 'Nicolas', 'Eric', 'Stephane', 'Pierre']
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
            'Cameroun' => ['Eto\'o', 'Mbia', 'Song', 'N\'Koulou', 'Aboubakar', 'Anguissa', 'Ondoua']
        ];
        
        $names = $lastNames[$nationality] ?? $lastNames['Tunisie'];
        return $names[array_rand($names)];
    }
    
    private function generatePlayerData($firstName, $lastName, $birthDate, $nationality, $position, $clubName, $age)
    {
        static $playerCounter = 1;
        
        // Générer des données de base selon le poste
        $baseStats = $this->getPositionBasedStats($position);
        
        // Générer des données SDOH selon la nationalité et l'âge
        $sdohData = $this->generateSDOHData($nationality, $age);
        
        // Générer la valeur marchande selon l'âge et le poste
        $marketValue = $this->generateMarketValue($age, $position);
        
        return [
            'fifa_id' => 'TUN' . str_pad($playerCounter++, 3, '0', STR_PAD_LEFT),
            'nom' => $lastName,
            'prenom' => $firstName,
            'date_naissance' => $birthDate,
            'nationalite' => $nationality,
            'poste' => $position,
            'club' => $clubName,
            'taille_cm' => $baseStats['height'],
            'poids_kg' => $baseStats['weight'],
            'pied_fort' => rand(0, 1) ? 'Droit' : 'Gauche',
            'langues_parlees' => $this->getLanguagesForNationality($nationality),
            
            // Données de base
            'buts' => $baseStats['goals'],
            'passes_decisives' => $baseStats['assists'],
            'matchs' => rand(25, 35),
            'minutes_jouees' => rand(2000, 3000),
            'note_moyenne' => rand(65, 90) / 10,
            'fifa_ovr' => rand(70, 85),
            'fifa_pot' => rand(75, 90),
            'score_fit' => rand(70, 95),
            'risque_blessure' => rand(10, 40),
            'valeur_marchande' => $marketValue,
            
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
            
            // Données de performance (Pillar 2) - stockées dans statistiques_techniques
            'statistiques_techniques' => json_encode([
                'tirs_cadres' => $baseStats['shots_on_target'],
                'tirs_totaux' => $baseStats['total_shots'],
                'precision_tirs' => rand(60, 85),
                'passes_cles' => $baseStats['key_passes'],
                'centres_reussis' => $baseStats['crosses'],
                'dribbles_reussis' => $baseStats['dribbles'],
                'passes_longues' => rand(5, 15),
                'tacles_reussis' => $baseStats['tackles'],
                'interceptions' => $baseStats['interceptions'],
                'degaugements' => $baseStats['clearances'],
                'fautes_commises' => rand(1, 4),
                'cartons_jaunes' => rand(0, 3),
                'cartons_rouges' => rand(0, 1),
                'distance_parcourue_km' => $baseStats['distance'],
                'vitesse_maximale_kmh' => $baseStats['max_speed'],
                'vitesse_moyenne_kmh' => $baseStats['avg_speed'],
                'sprints' => $baseStats['sprints'],
                'accelerations' => rand(20, 50),
                'decelerations' => rand(15, 40),
                'changements_direction' => rand(30, 80),
                'sautes' => rand(10, 25)
            ]),
            
            // Données d'adhérence (Pillar 5) - stockées dans donnees_sante
            'donnees_sante' => json_encode([
                'taux_presence_entrainements' => rand(85, 98),
                'score_adherence_protocole' => rand(0, 1) ? 'Excellent' : 'Moyen',
                'disponibilite_generale' => rand(70, 95)
            ]),
            
            // Données supplémentaires
            'derniere_mise_a_jour_donnees' => now(),
            'qualite_donnees' => 'good',
            
            'created_at' => now(),
            'updated_at' => now()
        ];
    }
    
    private function getPositionBasedStats($position)
    {
        $stats = [
            'Gardien' => [
                'height' => rand(185, 195), 'weight' => rand(75, 90),
                'goals' => 0, 'assists' => rand(0, 2), 'shots_on_target' => 0, 'total_shots' => 0,
                'key_passes' => rand(0, 3), 'crosses' => 0, 'dribbles' => 0, 'tackles' => rand(0, 2),
                'interceptions' => rand(0, 3), 'clearances' => rand(5, 15), 'distance' => rand(8, 12),
                'max_speed' => rand(18, 22), 'avg_speed' => rand(6, 9), 'sprints' => rand(15, 25)
            ],
            'Défenseur Central' => [
                'height' => rand(180, 190), 'weight' => rand(75, 85),
                'goals' => rand(0, 3), 'assists' => rand(0, 4), 'shots_on_target' => rand(0, 3), 'total_shots' => rand(0, 5),
                'key_passes' => rand(0, 5), 'crosses' => 0, 'dribbles' => rand(0, 3), 'tackles' => rand(15, 35),
                'interceptions' => rand(10, 25), 'clearances' => rand(20, 50), 'distance' => rand(9, 11),
                'max_speed' => rand(28, 32), 'avg_speed' => rand(8, 10), 'sprints' => rand(20, 35)
            ],
            'Latéral' => [
                'height' => rand(175, 185), 'weight' => rand(70, 80),
                'goals' => rand(0, 4), 'assists' => rand(2, 8), 'shots_on_target' => rand(0, 2), 'total_shots' => rand(0, 6),
                'key_passes' => rand(3, 10), 'crosses' => rand(10, 30), 'dribbles' => rand(5, 15), 'tackles' => rand(8, 20),
                'interceptions' => rand(5, 15), 'clearances' => rand(10, 25), 'distance' => rand(10, 12),
                'max_speed' => rand(30, 35), 'avg_speed' => rand(9, 11), 'sprints' => rand(25, 40)
            ],
            'Milieu Défensif' => [
                'height' => rand(175, 185), 'weight' => rand(70, 80),
                'goals' => rand(0, 5), 'assists' => rand(3, 10), 'shots_on_target' => rand(0, 3), 'total_shots' => rand(0, 8),
                'key_passes' => rand(5, 15), 'crosses' => rand(5, 20), 'dribbles' => rand(3, 12), 'tackles' => rand(20, 40),
                'interceptions' => rand(15, 30), 'clearances' => rand(15, 35), 'distance' => rand(10, 12),
                'max_speed' => rand(29, 33), 'avg_speed' => rand(9, 11), 'sprints' => rand(30, 45)
            ],
            'Milieu Offensif' => [
                'height' => rand(170, 180), 'weight' => rand(65, 75),
                'goals' => rand(2, 8), 'assists' => rand(5, 12), 'shots_on_target' => rand(2, 5), 'total_shots' => rand(5, 15),
                'key_passes' => rand(8, 20), 'crosses' => rand(3, 15), 'dribbles' => rand(8, 20), 'tackles' => rand(5, 15),
                'interceptions' => rand(3, 12), 'clearances' => rand(5, 15), 'distance' => rand(9, 11),
                'max_speed' => rand(30, 34), 'avg_speed' => rand(8, 10), 'sprints' => rand(25, 40)
            ],
            'Attaquant' => [
                'height' => rand(175, 185), 'weight' => rand(70, 80),
                'goals' => rand(5, 15), 'assists' => rand(3, 10), 'shots_on_target' => rand(5, 10), 'total_shots' => rand(10, 25),
                'key_passes' => rand(5, 15), 'crosses' => rand(2, 10), 'dribbles' => rand(10, 25), 'tackles' => rand(2, 10),
                'interceptions' => rand(2, 8), 'clearances' => rand(2, 10), 'distance' => rand(8, 10),
                'max_speed' => rand(31, 36), 'avg_speed' => rand(7, 9), 'sprints' => rand(20, 35)
            ]
        ];
        
        return $stats[$position] ?? $stats['Milieu Défensif'];
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
            'Cameroun' => 'Français, Anglais'
        ];
        
        return $languages[$nationality] ?? 'Français, Anglais';
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
    
    private function generateMarketValue($age, $position)
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
}








