<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Association;
use App\Models\Competition;
use App\Models\Club;
use App\Models\GameMatch;
use Carbon\Carbon;

class MatchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer l'association tunisienne
        $tunisianAssociation = Association::where('name', 'Fédération Tunisienne de Football')->first();
        
        if (!$tunisianAssociation) {
            $this->command->error('Association tunisienne non trouvée');
            return;
        }

        // Récupérer ou créer une compétition
        $competition = Competition::firstOrCreate([
            'name' => 'Championnat Tunisien Ligue 1',
            'association_id' => $tunisianAssociation->id,
        ], [
            'season' => '2024-2025',
            'start_date' => Carbon::create(2024, 8, 15),
            'end_date' => Carbon::create(2025, 5, 31),
            'status' => 'active',
            'type' => 'league',
            'description' => 'Championnat de première division tunisienne',
        ]);

        // Récupérer les clubs tunisiens
        $clubs = Club::where('association_id', $tunisianAssociation->id)->get();
        
        if ($clubs->count() < 2) {
            $this->command->error('Pas assez de clubs tunisiens trouvés');
            return;
        }

        $this->command->info("Création des matchs pour {$clubs->count()} clubs...");

        // Générer les matchs pour 5 journées
        $this->generateMatches($competition, $clubs);
        
        $this->command->info('Matchs créés avec succès !');
    }

    private function generateMatches($competition, $clubs)
    {
        $arbitres = [
            'Ridha Mejri', 'Mohamed Jebali', 'Nabil Jebali', 'Slim Belkhouja', 'Youssef Srairi',
            'Haythem Guirat', 'Mehdi Abid Charef', 'Sadok Selmi', 'Bechir Hassani', 'Kamel Harrouche'
        ];
        
        $journees = 5;
        $baseDate = Carbon::create(2024, 8, 15);
        
        for ($journee = 1; $journee <= $journees; $journee++) {
            // Utiliser un seed fixe pour la cohérence
            mt_srand(12345 + $journee * 1000);
            $clubsShuffled = $clubs->shuffle();
            
            for ($i = 0; $i < count($clubsShuffled) && $i + 1 < count($clubsShuffled); $i += 2) {
                $homeTeam = $clubsShuffled[$i];
                $awayTeam = $clubsShuffled[$i + 1];
                
                // Date du match
                $matchDate = $baseDate->copy()->addDays(($journee - 1) * 7 + ($i % 7));
                
                // Déterminer si le match est terminé ou à venir
                $isMatchFinished = $matchDate < now();
                
                // Générer un résultat seulement si le match est terminé
                $homeScore = null;
                $awayScore = null;
                $status = 'scheduled';
                
                if ($isMatchFinished) {
                    $homeScore = mt_rand(0, 4);
                    $awayScore = mt_rand(0, 4);
                    $status = 'completed';
                }
                
                // Sélectionner les arbitres
                $referee = $arbitres[mt_rand(0, count($arbitres) - 1)];
                $assistant1 = $arbitres[mt_rand(0, count($arbitres) - 1)];
                $assistant2 = $arbitres[mt_rand(0, count($arbitres) - 1)];
                $var = $arbitres[mt_rand(0, count($arbitres) - 1)];
                
                // Créer le match dans la base de données
                GameMatch::create([
                    'competition_id' => $competition->id,
                    'home_team_id' => $homeTeam->id,
                    'away_team_id' => $awayTeam->id,
                    'match_date' => $matchDate,
                    'match_time' => '15:00',
                    'venue' => $homeTeam->name . ' Stadium',
                    'home_score' => $homeScore,
                    'away_score' => $awayScore,
                    'status' => $status,
                    'referee' => $referee,
                    'assistant_referee_1' => $assistant1,
                    'assistant_referee_2' => $assistant2,
                    'var_referee' => $var,
                    'match_official' => 'Délégué ' . mt_rand(1, 10),
                    'observer' => 'Observateur ' . mt_rand(1, 5),
                    'round' => $journee,
                ]);
            }
        }
    }
}