<?php

namespace App\Notifications;

use App\Models\PlayerLicense;
use Illuminate\Notifications\Notification;

/**
 * Notification du circuit de licence (cloche du site) : nouvelle demande ou
 * complément pour la fédération, décision pour le club. Envoyée immédiatement
 * (pas de file d'attente), canal base de données.
 */
class LicenseWorkflowNotification extends Notification
{
    public function __construct(
        private readonly PlayerLicense $license,
        private readonly string $action,
        private readonly string $message,
        private readonly string $url,
    ) {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'message' => $this->message,
            'url' => $this->url,
            'license_id' => $this->license->id,
            'action' => $this->action,
            'module' => 'licences',
        ];
    }
}
