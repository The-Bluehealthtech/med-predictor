<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Player;
use App\Models\NationalTeamCallup;
use App\Models\TrainingSession;
use App\Models\MedicalAppointment;
use App\Models\SocialMediaAlert;
use Carbon\Carbon;

class PortalDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding portal data...');
        
        $players = Player::all();
        
        if ($players->isEmpty()) {
            $this->command->warn('No players found');
            return;
        }

        $this->command->info("Seeding data for {$players->count()} players...");

        foreach ($players as $player) {
            $this->seedNationalTeamCallups($player);
            $this->seedTrainingSessions($player);
            $this->seedMedicalAppointments($player);
            $this->seedSocialMediaAlerts($player);
        }

        $this->command->info('Portal data seeded successfully!');
    }

    private function seedNationalTeamCallups(Player $player)
    {
        $nationalities = ['France', 'Brazil', 'Argentina', 'Germany', 'Spain', 'Portugal', 'Netherlands', 'Italy', 'England', 'Belgium'];
        $opponents = ['Brazil', 'Uruguay', 'Argentina', 'Germany', 'Spain', 'Portugal', 'Netherlands', 'Italy', 'England', 'Belgium'];
        $venues = ['Stade de France', 'Maracanã', 'La Bombonera', 'Allianz Arena', 'Santiago Bernabéu', 'Estádio da Luz', 'Johan Cruyff Arena', 'San Siro', 'Wembley', 'King Power at Den Dreef'];

        // Créer 2-4 convocations par joueur
        $callupCount = rand(2, 4);
        
        for ($i = 0; $i < $callupCount; $i++) {
            $callupDate = now()->addDays(rand(1, 60));
            $opponent1 = $opponents[array_rand($opponents)];
            $opponent2 = $opponents[array_rand($opponents)];
            
            NationalTeamCallup::create([
                'player_id' => $player->id,
                'title' => 'Convocación ' . $player->nationality,
                'message' => 'Convocado para partidos vs ' . $opponent1 . ' y ' . $opponent2,
                'callup_date' => $callupDate,
                'priority' => ['low', 'medium', 'high'][rand(0, 2)],
                'type' => ['national', 'training', 'friendly', 'qualifier', 'tournament'][rand(0, 4)],
                'opponents' => $opponent1 . ', ' . $opponent2,
                'venue' => $venues[array_rand($venues)],
                'meeting_time' => $callupDate->copy()->setTime(rand(8, 11), 0),
                'is_confirmed' => rand(0, 1)
            ]);
        }
    }

    private function seedTrainingSessions(Player $player)
    {
        $sessionTypes = [
            'technical' => ['Session de passes et finalisation', 'Contrôle de balle avancé', 'Tirs au but'],
            'physical' => ['Circuits de vitesse et résistance', 'Musculation fonctionnelle', 'Endurance cardio'],
            'tactical' => ['Analyse vidéo tactique', 'Exercices de positionnement', 'Transitions défense-attaque'],
            'recovery' => ['Session d\'étirements et yoga', 'Récupération active', 'Mobilité articulaire'],
            'mental' => ['Visualisation mentale', 'Gestion du stress', 'Concentration et focus']
        ];

        $coaches = ['Thomas Tuchel', 'Pep Guardiola', 'Jürgen Klopp', 'Carlo Ancelotti', 'Zinedine Zidane'];

        // Créer 3-6 sessions par joueur
        $sessionCount = rand(3, 6);
        
        for ($i = 0; $i < $sessionCount; $i++) {
            $sessionDate = now()->addDays(rand(1, 21));
            $sessionType = array_keys($sessionTypes)[rand(0, 4)];
            $descriptions = $sessionTypes[$sessionType];
            $description = $descriptions[rand(0, count($descriptions) - 1)];
            
            TrainingSession::create([
                'player_id' => $player->id,
                'title' => 'Entraînement ' . ucfirst($sessionType),
                'description' => $description,
                'session_date' => $sessionDate,
                'start_time' => $sessionDate->copy()->setTime(rand(8, 16), 0),
                'end_time' => $sessionDate->copy()->setTime(rand(17, 20), 0),
                'priority' => ['low', 'medium', 'high'][rand(0, 2)],
                'type' => $sessionType,
                'location' => 'Centre d\'entraînement',
                'coach' => $coaches[array_rand($coaches)],
                'is_mandatory' => rand(0, 1)
            ]);
        }
    }

    private function seedMedicalAppointments(Player $player)
    {
        $appointmentTypes = [
            'consultation' => ['Révision routinière', 'Contrôle post-match', 'Évaluation pré-saison'],
            'physiotherapy' => ['Session de récupération musculaire', 'Traitement des blessures', 'Rééducation'],
            'rehabilitation' => ['Programme de récupération', 'Exercices de renforcement', 'Retour au jeu'],
            'screening' => ['Bilan de santé complet', 'Test de performance', 'Évaluation cardiovasculaire'],
            'emergency' => ['Consultation urgente', 'Traitement immédiat', 'Évaluation d\'urgence']
        ];

        $doctors = ['Dr. Martínez', 'Dr. Dubois', 'Dr. Moreau', 'Dr. Garcia', 'Dr. Leroy', 'Dr. Smith', 'Dr. Johnson'];
        $locations = ['Centre médical', 'Centre de rééducation', 'Clinique sportive', 'Hôpital spécialisé'];

        // Créer 2-4 rendez-vous par joueur
        $appointmentCount = rand(2, 4);
        
        for ($i = 0; $i < $appointmentCount; $i++) {
            $appointmentDate = now()->addDays(rand(1, 30));
            $appointmentType = array_keys($appointmentTypes)[rand(0, 4)];
            $purposes = $appointmentTypes[$appointmentType];
            $purpose = $purposes[rand(0, count($purposes) - 1)];
            
            MedicalAppointment::create([
                'player_id' => $player->id,
                'title' => 'Contrôle Médical',
                'description' => $purpose . ' avec ' . $doctors[array_rand($doctors)],
                'appointment_date' => $appointmentDate,
                'appointment_time' => $appointmentDate->copy()->setTime(rand(9, 17), 0),
                'priority' => ['low', 'medium', 'high'][rand(0, 2)],
                'type' => $appointmentType,
                'purpose' => $purpose,
                'location' => $locations[array_rand($locations)],
                'doctor' => $doctors[array_rand($doctors)],
                'is_urgent' => rand(0, 1),
                'notes' => rand(0, 1) ? 'Notes importantes pour le suivi' : null
            ]);
        }
    }

    private function seedSocialMediaAlerts(Player $player)
    {
        $platforms = ['instagram', 'twitter', 'facebook', 'tiktok', 'youtube'];
        $sentiments = ['positive', 'negative', 'neutral'];
        
        $clubName = $player->club?->name ?? 'Chelsea';
        $clubNameLower = strtolower($clubName);

        // Créer 3-6 alertes par joueur
        $alertCount = rand(3, 6);
        
        for ($i = 0; $i < $alertCount; $i++) {
            $alertTimestamp = now()->subDays(rand(1, 30));
            $views = rand(1, 50) . '.' . rand(0, 9) . 'K';
            $engagement = rand(100, 2000);
            $platform = $platforms[array_rand($platforms)];
            $sentiment = $sentiments[array_rand($sentiments)];
            
            SocialMediaAlert::create([
                'player_id' => $player->id,
                'title' => 'Message de Fan',
                'content' => 'Message de soutien de @fan_' . $clubNameLower,
                'alert_timestamp' => $alertTimestamp,
                'views' => $views,
                'engagement' => $engagement,
                'sentiment' => $sentiment,
                'platform' => $platform,
                'needs_response' => rand(0, 1),
                'hashtag' => '#' . $player->first_name . $clubName,
                'user_mention' => '@fan_' . $clubNameLower
            ]);
        }

        // Ajouter quelques alertes spéciales
        SocialMediaAlert::create([
            'player_id' => $player->id,
            'title' => 'Tendance Twitter',
            'content' => '#' . $player->first_name . $clubName . ' trending en ' . $player->nationality,
            'alert_timestamp' => now()->subDays(rand(1, 7)),
            'views' => rand(10, 50) . '.' . rand(0, 9) . 'K',
            'engagement' => rand(500, 2000),
            'sentiment' => 'positive',
            'platform' => 'twitter',
            'needs_response' => true,
            'hashtag' => '#' . $player->first_name . $clubName,
            'user_mention' => null
        ]);

        SocialMediaAlert::create([
            'player_id' => $player->id,
            'title' => 'Instagram Live',
            'content' => 'Demande d\'interview en direct',
            'alert_timestamp' => now()->subDays(rand(1, 7)),
            'views' => rand(0, 5) . '.' . rand(0, 9) . 'K',
            'engagement' => rand(200, 800),
            'sentiment' => 'neutral',
            'platform' => 'instagram',
            'needs_response' => true,
            'hashtag' => null,
            'user_mention' => null
        ]);
    }
}
