<?php

namespace App\Notifications;

use App\Models\AccountRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class AccountRequestRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public $accountRequest;
    public $rejectedBy;
    public $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(AccountRequest $accountRequest, $rejectedBy = null, $reason = null)
    {
        $this->accountRequest = $accountRequest;
        $this->rejectedBy = $rejectedBy;
        $this->reason = $reason;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        try {
            // Check if the account request still exists
            if (!$this->accountRequest || !$this->accountRequest->exists) {
                Log::warning('AccountRequestRejected notification: Account request model not found', [
                    'account_request_id' => $this->accountRequest->id ?? 'unknown'
                ]);
                return null;
            }

            $locale = app()->getLocale();
            
            if ($locale === 'fr') {
                return (new MailMessage)
                    ->subject('Demande de compte refusée - FIT Platform')
                    ->greeting('Bonjour ' . $this->accountRequest->full_name . ',')
                    ->line('Nous regrettons de vous informer que votre demande de compte sur la plateforme FIT a été refusée.')
                    ->when($this->reason, fn($msg) => $msg->line('**Raison du refus :** ' . $this->reason))
                    ->when($this->rejectedBy, fn($msg) => $msg->line('• **Refusé par :** ' . $this->rejectedBy->name))
                    ->line('Si vous pensez qu\'il s\'agit d\'une erreur ou si vous souhaitez plus d\'informations,')
                    ->line('n\'hésitez pas à nous contacter.')
                    ->line('Nous vous remercions de votre intérêt pour la plateforme FIT.')
                    ->salutation('Cordialement,');
            }

            return (new MailMessage)
                ->subject('Account Request Rejected - FIT Platform')
                ->greeting('Hello ' . $this->accountRequest->full_name . ',')
                ->line('We regret to inform you that your account request on the FIT platform has been rejected.')
                ->when($this->reason, fn($msg) => $msg->line('**Reason for rejection:** ' . $this->reason))
                ->when($this->rejectedBy, fn($msg) => $msg->line('• **Rejected by:** ' . $this->rejectedBy->name))
                ->line('If you believe this is an error or would like more information,')
                ->line('please don\'t hesitate to contact us.')
                ->line('Thank you for your interest in the FIT platform.')
                ->salutation('Best regards,');

        } catch (\Exception $e) {
            Log::error('AccountRequestRejected notification error', [
                'account_request_id' => $this->accountRequest->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        try {
            // Check if the account request still exists
            if (!$this->accountRequest || !$this->accountRequest->exists) {
                return [
                    'account_request_id' => $this->accountRequest->id ?? 'unknown',
                    'message' => 'Account request rejection notification (request no longer available)',
                    'type' => 'account_request_rejected',
                    'status' => 'expired'
                ];
            }

            return [
                'account_request_id' => $this->accountRequest->id,
                'message' => app()->getLocale() === 'fr' 
                    ? 'Demande de compte refusée pour ' . $this->accountRequest->full_name
                    : 'Account request rejected for ' . $this->accountRequest->full_name,
                'type' => 'account_request_rejected',
                'status' => 'active'
            ];

        } catch (\Exception $e) {
            Log::error('AccountRequestRejected toArray error', [
                'account_request_id' => $this->accountRequest->id ?? 'unknown',
                'error' => $e->getMessage()
            ]);
            
            return [
                'account_request_id' => $this->accountRequest->id ?? 'unknown',
                'message' => 'Account request rejection notification (error occurred)',
                'type' => 'account_request_rejected',
                'status' => 'error'
            ];
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception)
    {
        Log::error('AccountRequestRejected notification failed', [
            'account_request_id' => $this->accountRequest->id ?? 'unknown',
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
} 