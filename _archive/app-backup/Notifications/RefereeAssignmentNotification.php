<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class RefereeAssignmentNotification extends Notification
{
    use Queueable;

    protected $notificationData;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $notificationData)
    {
        $this->notificationData = $notificationData;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $matchInfo = $this->notificationData['match_info'];
        $role = $this->getRoleLabel($this->notificationData['role']);
        
        return (new MailMessage)
            ->subject($this->notificationData['title'])
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line($this->notificationData['message'])
            ->line('Détails du match :')
            ->line('• Équipes : ' . $matchInfo['home_team'] . ' vs ' . $matchInfo['away_team'])
            ->line('• Compétition : ' . $matchInfo['competition'])
            ->line('• Date : ' . \Carbon\Carbon::parse($matchInfo['date'])->format('d/m/Y à H:i'))
            ->line('• Lieu : ' . $matchInfo['venue'])
            ->line('• Rôle : ' . $role)
            ->action('Voir les détails', url('/referee/dashboard'))
            ->line('Merci d\'utiliser notre système de gestion des arbitres !');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => $this->notificationData['type'],
            'title' => $this->notificationData['title'],
            'message' => $this->notificationData['message'],
            'match_id' => $this->notificationData['match_id'],
            'role' => $this->notificationData['role'],
            'match_info' => $this->notificationData['match_info']
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->notificationData['type'],
            'title' => $this->notificationData['title'],
            'message' => $this->notificationData['message'],
            'match_id' => $this->notificationData['match_id'],
            'role' => $this->notificationData['role'],
            'match_info' => $this->notificationData['match_info']
        ];
    }

    /**
     * Obtenir le libellé du rôle
     */
    private function getRoleLabel(string $role): string
    {
        $labels = [
            'referee' => 'Arbitre Principal',
            'assistant_referee_1' => 'Assistant 1',
            'assistant_referee_2' => 'Assistant 2',
            'fourth_official' => '4ème Arbitre',
            'var_referee' => 'Arbitre VAR'
        ];

        return $labels[$role] ?? $role;
    }
}

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => $this->notificationData['type'],
            'title' => $this->notificationData['title'],
            'message' => $this->notificationData['message'],
            'match_id' => $this->notificationData['match_id'],
            'role' => $this->notificationData['role'],
            'match_info' => $this->notificationData['match_info']
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => $this->notificationData['type'],
            'title' => $this->notificationData['title'],
            'message' => $this->notificationData['message'],
            'match_id' => $this->notificationData['match_id'],
            'role' => $this->notificationData['role'],
            'match_info' => $this->notificationData['match_info']
        ];
    }
    /**
     * Obtenir le libellé du rôle
     */
    private function getRoleLabel(string $role): string
    {
        $labels = [
            'referee' => 'Arbitre Principal',
            'assistant_referee_1' => 'Assistant 1',
            'assistant_referee_2' => 'Assistant 2',
            'fourth_official' => '4ème Arbitre',
            'var_referee' => 'Arbitre VAR'
        ];

        return $labels[$role] ?? $role;
    }
}
