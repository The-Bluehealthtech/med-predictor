<?php

namespace App\Notifications;

use App\Models\FhirOrder;
use Illuminate\Notifications\Notification;

/**
 * Compte rendu reçu pour un examen prescrit (cloche du site). Le message ne contient aucun
 * résultat médical : seulement le joueur, l'examen et le lien vers l'écran autorisé.
 */
class MedicalResultNotification extends Notification
{
    public function __construct(private readonly FhirOrder $order, private readonly string $message, private readonly string $url)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return ['message' => $this->message, 'url' => $this->url, 'order_id' => $this->order->id, 'action' => 'results_received', 'module' => 'medical'];
    }
}
