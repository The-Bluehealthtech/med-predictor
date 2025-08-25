<?php

namespace App\Notifications;

use App\Models\AccountRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class AccountRequestApproved extends Notification implements ShouldQueue
{
    use Queueable;

    public $accountRequest;
    public $approvedBy;

    /**
     * Create a new notification instance.
     */
    public function __construct(AccountRequest $accountRequest, $approvedBy = null)
    {
        $this->accountRequest = $accountRequest;
        $this->approvedBy = $approvedBy;
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
                Log::warning('AccountRequestApproved notification: Account request model not found', [
                    'account_request_id' => $this->accountRequest->id ?? 'unknown'
                ]);
                return null;
            }

            $locale = app()->getLocale();
            
            if ($locale === 'fr') {
                return (new MailMessage)
                    ->subject('Demande de compte approuvée - FIT Platform')
                    ->greeting('Bonjour ' . $this->accountRequest->full_name . ',')
                    ->line('Votre demande de compte sur la plateforme FIT a été approuvée.')
                    ->line('**Détails de votre compte :**')
                    ->line('• **Nom :** ' . $this->accountRequest->full_name)
                    ->line('• **Email :** ' . $this->accountRequest->email)
                    ->line('• **Organisation :** ' . $this->accountRequest->organization_name)
                    ->line('• **Type d\'organisation :** ' . $this->accountRequest->organization_type_label)
                    ->line('• **Type de football :** ' . $this->accountRequest->football_type_label)
                    ->when($this->approvedBy, fn($msg) => $msg->line('• **Approuvé par :** ' . $this->approvedBy->name))
                    ->action('Accéder à la plateforme', url('/login'))
                    ->line('Vous pouvez maintenant vous connecter à votre compte et commencer à utiliser la plateforme.')
                    ->line('Si vous avez des questions, n\'hésitez pas à nous contacter.')
                    ->salutation('Cordialement,');
            }

            return (new MailMessage)
                ->subject('Account Request Approved - FIT Platform')
                ->greeting('Hello ' . $this->accountRequest->full_name . ',')
                ->line('Your account request on the FIT platform has been approved.')
                ->line('**Account Details:**')
                ->line('• **Name:** ' . $this->accountRequest->full_name)
                ->line('• **Email:** ' . $this->accountRequest->email)
                ->line('• **Organization:** ' . $this->accountRequest->organization_name)
                ->line('• **Organization Type:** ' . $this->accountRequest->organization_type_label)
                ->line('• **Football Type:** ' . $this->accountRequest->football_type_label)
                ->when($this->approvedBy, fn($msg) => $msg->line('• **Approved by:** ' . $this->approvedBy->name))
                ->action('Access Platform', url('/login'))
                ->line('You can now log in to your account and start using the platform.')
                ->line('If you have any questions, please don\'t hesitate to contact us.')
                ->salutation('Best regards,');

        } catch (\Exception $e) {
            Log::error('AccountRequestApproved notification error', [
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
                    'message' => 'Account request approval notification (request no longer available)',
                    'type' => 'account_request_approved',
                    'status' => 'expired'
                ];
            }

            return [
                'account_request_id' => $this->accountRequest->id,
                'message' => app()->getLocale() === 'fr' 
                    ? 'Demande de compte approuvée pour ' . $this->accountRequest->full_name
                    : 'Account request approved for ' . $this->accountRequest->full_name,
                'type' => 'account_request_approved',
                'status' => 'active'
            ];

        } catch (\Exception $e) {
            Log::error('AccountRequestApproved toArray error', [
                'account_request_id' => $this->accountRequest->id ?? 'unknown',
                'error' => $e->getMessage()
            ]);
            
            return [
                'account_request_id' => $this->accountRequest->id ?? 'unknown',
                'message' => 'Account request approval notification (error occurred)',
                'type' => 'account_request_approved',
                'status' => 'error'
            ];
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception)
    {
        Log::error('AccountRequestApproved notification failed', [
            'account_request_id' => $this->accountRequest->id ?? 'unknown',
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
} 