<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use App\Models\LicenseRequest;
use Illuminate\Support\Facades\Log;

class LicenseRequestStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public $licenseRequest;
    public $newStatus;
    public $comment;

    public function __construct(LicenseRequest $licenseRequest, $newStatus, $comment = null)
    {
        $this->licenseRequest = $licenseRequest;
        $this->newStatus = $newStatus;
        $this->comment = $comment;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        try {
            // Check if the license request still exists
            if (!$this->licenseRequest || !$this->licenseRequest->exists) {
                Log::warning('LicenseRequestStatusChanged notification: License request model not found', [
                    'license_request_id' => $this->licenseRequest->id ?? 'unknown'
                ]);
                return null;
            }

            return (new MailMessage)
                ->subject('Changement de statut d\'une demande de licence')
                ->greeting('Bonjour,')
                ->line('Le statut de la demande de licence #' . $this->licenseRequest->id . ' a changé :')
                ->line('Nouveau statut : ' . ucfirst($this->newStatus))
                ->when($this->comment, fn($msg) => $msg->line('Commentaire : ' . $this->comment))
                ->action('Voir la demande', url(route('license-requests.show', $this->licenseRequest->id)))
                ->line('Merci de votre confiance.');

        } catch (\Exception $e) {
            Log::error('LicenseRequestStatusChanged notification error', [
                'license_request_id' => $this->licenseRequest->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    public function toArray($notifiable)
    {
        try {
            // Check if the license request still exists
            if (!$this->licenseRequest || !$this->licenseRequest->exists) {
                return [
                    'license_request_id' => $this->licenseRequest->id ?? 'unknown',
                    'message' => 'License request status change notification (request no longer available)',
                    'type' => 'license_request_status_changed',
                    'status' => 'expired'
                ];
            }

            return [
                'license_request_id' => $this->licenseRequest->id,
                'new_status' => $this->newStatus,
                'comment' => $this->comment,
                'message' => "License request #{$this->licenseRequest->id} status changed to {$this->newStatus}",
                'type' => 'license_request_status_changed',
                'status' => 'active'
            ];

        } catch (\Exception $e) {
            Log::error('LicenseRequestStatusChanged toArray error', [
                'license_request_id' => $this->licenseRequest->id ?? 'unknown',
                'error' => $e->getMessage()
            ]);
            
            return [
                'license_request_id' => $this->licenseRequest->id ?? 'unknown',
                'message' => 'License request status change notification (error occurred)',
                'type' => 'license_request_status_changed',
                'status' => 'error'
            ];
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception)
    {
        Log::error('LicenseRequestStatusChanged notification failed', [
            'license_request_id' => $this->licenseRequest->id ?? 'unknown',
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
