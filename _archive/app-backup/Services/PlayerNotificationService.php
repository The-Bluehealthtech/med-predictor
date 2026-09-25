<?php

namespace App\Services;

use App\Models\Player;
use App\Models\Notification;
use App\Models\HealthRecord;
use App\Models\PCMA;
use App\Models\MatchModel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PlayerNotificationService
{
    protected $player;
    protected $userId;

    public function __construct(Player $player)
    {
        $this->player = $player;
        $this->userId = $player->user_id ?? null;
    }

    /**
     * Récupère toutes les notifications du joueur
     */
    public function getAllNotifications()
    {
        if (!$this->userId) {
            return $this->getDefaultNotifications();
        }

        $notifications = Notification::where('notifiable_id', $this->userId)
            ->where('notifiable_type', 'App\\Models\\User')
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->formatNotifications($notifications);
    }

    /**
     * Récupère les notifications par type
     */
    public function getNotificationsByType($type)
    {
        if (!$this->userId) {
            return $this->getDefaultNotificationsByType($type);
        }

        $notifications = Notification::where('notifiable_id', $this->userId)
            ->where('notifiable_type', 'App\\Models\\User')
            ->where('type', 'like', '%' . $type . '%')
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->formatNotifications($notifications);
    }

    /**
     * Récupère les notifications d'équipe nationale
     */
    public function getNationalTeamNotifications()
    {
        $notifications = $this->getNotificationsByType('NationalTeam');
        
        if (empty($notifications)) {
            return [
                [
                    'id' => 'nat_001',
                    'title' => 'Convocation Équipe Nationale',
                    'message' => 'Vous êtes convoqué pour les matches amicaux contre l\'Italie et l\'Espagne.',
                    'date' => 'Rassemblement: 18 Mars 2025',
                    'location' => 'Centre National de Clairefontaine',
                    'icon' => 'fas fa-flag',
                    'urgent' => true,
                    'type' => 'national'
                ]
            ];
        }

        return $notifications;
    }

    /**
     * Récupère les notifications d'entraînement
     */
    public function getTrainingNotifications()
    {
        // Simuler des entraînements basés sur le calendrier du joueur
        return [
            [
                'id' => 'train_001',
                'title' => 'Entraînement Technique',
                'description' => 'Session de travail technique et tactique avant le match de Ligue 1.',
                'date' => 'Aujourd\'hui',
                'time' => '10:00 - 12:00',
                'location' => 'Centre d\'Entraînement',
                'icon' => 'fas fa-futbol',
                'mandatory' => true,
                'type' => 'training'
            ],
            [
                'id' => 'train_002',
                'title' => 'Séance Physique',
                'description' => 'Préparation physique et récupération active.',
                'date' => 'Demain',
                'time' => '09:00 - 10:30',
                'location' => 'Salle de Fitness',
                'icon' => 'fas fa-dumbbell',
                'mandatory' => true,
                'type' => 'training'
            ]
        ];
    }

    /**
     * Récupère les notifications de matchs
     */
    public function getMatchNotifications()
    {
        // Récupérer les vrais matchs du joueur
        $matches = MatchModel::where(function($query) {
            $query->where('home_team_id', $this->player->club_id)
                  ->orWhere('away_team_id', $this->player->club_id);
        })
        ->with(['homeTeam', 'awayTeam', 'competition'])
        ->orderBy('match_date', 'desc')
        ->limit(3)
        ->get();

        if ($matches->isEmpty()) {
            return [
                [
                    'id' => 'match_001',
                    'title' => 'PSG vs Olympique de Marseille',
                    'opponent' => 'Olympique de Marseille',
                    'competition' => 'Ligue 1',
                    'date' => 'Dimanche 15 Mars',
                    'time' => '21:00',
                    'stadium' => 'Parc des Princes',
                    'meetingTime' => '18:00 au stade',
                    'status' => 'convoqué',
                    'icon' => 'fas fa-futbol',
                    'type' => 'match'
                ]
            ];
        }

        return $matches->map(function($match) {
            $isHome = $match->home_team_id == $this->player->club_id;
            $opponent = $isHome ? $match->awayTeam : $match->homeTeam;
            
            return [
                'id' => 'match_' . $match->id,
                'title' => $match->homeTeam->name . ' vs ' . $match->awayTeam->name,
                'opponent' => $opponent->name,
                'competition' => $match->competition->name ?? 'Compétition',
                'date' => Carbon::parse($match->match_date)->format('l j F'),
                'time' => Carbon::parse($match->match_date)->format('H:i'),
                'stadium' => $match->stadium ?? 'Stade principal',
                'meetingTime' => Carbon::parse($match->match_date)->subHours(3)->format('H:i') . ' au stade',
                'status' => 'convoqué',
                'icon' => 'fas fa-futbol',
                'type' => 'match'
            ];
        })->toArray();
    }

    /**
     * Récupère les notifications médicales
     */
    public function getMedicalNotifications()
    {
        // Récupérer les vrais PCMA et health records
        $pcmas = PCMA::where('athlete_id', $this->player->id)
            ->orderBy('assessment_date', 'desc')
            ->limit(2)
            ->get();

        $healthRecords = HealthRecord::where('player_id', $this->player->id)
            ->orderBy('record_date', 'desc')
            ->limit(2)
            ->get();

        $notifications = [];

        foreach ($pcmas as $pcma) {
            $notifications[] = [
                'id' => 'med_pcma_' . $pcma->id,
                'type' => 'Évaluation PCMA',
                'doctor' => 'Dr. ' . ($pcma->assessor_id ? 'Assesseur' : 'Médecin'),
                'purpose' => 'Évaluation médicale ' . strtoupper($pcma->type),
                'date' => Carbon::parse($pcma->assessment_date)->format('l j F'),
                'time' => '09:00',
                'location' => 'Centre Médical du Club',
                'icon' => 'fas fa-stethoscope',
                'urgent' => $pcma->status === 'pending',
                'type' => 'medical'
            ];
        }

        foreach ($healthRecords as $record) {
            $notifications[] = [
                'id' => 'med_health_' . $record->id,
                'type' => 'Bilan de Santé',
                'doctor' => $record->doctor_name ?? 'Dr. Médecin',
                'purpose' => 'Bilan de santé ' . $record->visit_type ?? 'général',
                'date' => Carbon::parse($record->record_date)->format('l j F'),
                'time' => '14:00',
                'location' => 'Centre Médical du Club',
                'icon' => 'fas fa-heartbeat',
                'urgent' => false,
                'type' => 'medical'
            ];
        }

        if (empty($notifications)) {
            return [
                [
                    'id' => 'med_001',
                    'type' => 'Examen Médical Complet',
                    'doctor' => 'Dr. Martin Dubois',
                    'purpose' => 'Bilan de santé trimestriel obligatoire',
                    'date' => 'Lundi 11 Mars',
                    'time' => '14:00',
                    'location' => 'Centre Médical du Club',
                    'icon' => 'fas fa-stethoscope',
                    'urgent' => false,
                    'type' => 'medical'
                ]
            ];
        }

        return $notifications;
    }

    /**
     * Récupère les notifications de réseaux sociaux
     */
    public function getSocialMediaNotifications()
    {
        // Simuler des alertes de réseaux sociaux
        return [
            [
                'id' => 'social_001',
                'platform' => 'Instagram',
                'type' => 'Mention',
                'content' => 'Vous avez été mentionné dans une publication de @psg_official',
                'date' => 'Il y a 2h',
                'icon' => 'fab fa-instagram',
                'urgent' => false,
                'type' => 'social'
            ],
            [
                'id' => 'social_002',
                'platform' => 'Twitter',
                'type' => 'Retweet',
                'content' => 'Votre tweet sur le dernier match a été retweeté 150 fois',
                'date' => 'Il y a 4h',
                'icon' => 'fab fa-twitter',
                'urgent' => false,
                'type' => 'social'
            ],
            [
                'id' => 'social_003',
                'platform' => 'Facebook',
                'type' => 'Commentaire',
                'content' => 'Nouveau commentaire sur votre page officielle',
                'date' => 'Il y a 6h',
                'icon' => 'fab fa-facebook',
                'urgent' => false,
                'type' => 'social'
            ]
        ];
    }

    /**
     * Formate les notifications pour l'affichage
     */
    protected function formatNotifications($notifications)
    {
        return $notifications->map(function($notification) {
            $data = json_decode($notification->data, true);
            
            return [
                'id' => $notification->id,
                'title' => $data['title'] ?? 'Notification',
                'message' => $data['message'] ?? 'Nouvelle notification',
                'date' => $data['date'] ?? Carbon::parse($notification->created_at)->format('d/m/Y'),
                'location' => $data['location'] ?? 'Non spécifié',
                'icon' => 'fas fa-bell',
                'urgent' => false,
                'type' => 'system',
                'read' => !is_null($notification->read_at)
            ];
        })->toArray();
    }

    /**
     * Notifications par défaut si pas de données
     */
    protected function getDefaultNotifications()
    {
        return [
            [
                'id' => 'default_001',
                'title' => 'Bienvenue sur le Portail FIFA Ultimate',
                'message' => 'Votre tableau de bord est maintenant disponible',
                'date' => 'Aujourd\'hui',
                'location' => 'Portail FIFA',
                'icon' => 'fas fa-info-circle',
                'urgent' => false,
                'type' => 'system'
            ]
        ];
    }

    /**
     * Notifications par défaut par type
     */
    protected function getDefaultNotificationsByType($type)
    {
        switch ($type) {
            case 'national':
                return $this->getNationalTeamNotifications();
            case 'training':
                return $this->getTrainingNotifications();
            case 'matches':
                return $this->getMatchNotifications();
            case 'medical':
                return $this->getMedicalNotifications();
            case 'social':
                return $this->getSocialMediaNotifications();
            default:
                return $this->getDefaultNotifications();
        }
    }

    /**
     * Compte total des notifications
     */
    public function getTotalNotificationCount()
    {
        if (!$this->userId) {
            return 12; // Nombre par défaut
        }

        return Notification::where('notifiable_id', $this->userId)
            ->where('notifiable_type', 'App\\Models\\User')
            ->count();
    }

    /**
     * Compte des notifications par type
     */
    public function getNotificationCountsByType()
    {
        return [
            'all' => $this->getTotalNotificationCount(),
            'national' => count($this->getNationalTeamNotifications()),
            'training' => count($this->getTrainingNotifications()),
            'matches' => count($this->getMatchNotifications()),
            'medical' => count($this->getMedicalNotifications()),
            'social' => count($this->getSocialMediaNotifications())
        ];
    }
}
