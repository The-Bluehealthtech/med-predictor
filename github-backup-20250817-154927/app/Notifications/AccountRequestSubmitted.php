<?php

namespace App\Notifications;

use App\Models\AccountRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class AccountRequestSubmitted extends Notification
{
    use Queueable;

    public $accountRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct(AccountRequest $accountRequest)
    {
        $this->accountRequest = $accountRequest;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        try {
            // Check if the account request still exists
            if (!$this->accountRequest || !$this->accountRequest->exists) {
                Log::warning('AccountRequestSubmitted notification: Account request model not found', [
                    'account_request_id' => $this->accountRequest->id ?? 'unknown'
                ]);
                return null;
            }

            $locale = app()->getLocale();
            
            if ($locale === 'fr') {
                return (new MailMessage)
                    ->subject('Nouvelle demande de compte - FIT Platform')
                    ->greeting('Bonjour ' . $notifiable->name . ',')
                    ->line('Une nouvelle demande de compte a été soumise sur la plateforme FIT.')
                    ->line('**Détails de la demande :**')
                    ->line('• **Nom :** ' . $this->accountRequest->full_name)
                    ->line('• **Email :** ' . $this->accountRequest->email)
                    ->line('• **Organisation :** ' . $this->accountRequest->organization_name)
                    ->line('• **Type d\'organisation :** ' . $this->accountRequest->organization_type_label)
                    ->line('• **Type de football :** ' . $this->accountRequest->football_type_label)
                    ->line('• **Pays :** ' . $this->accountRequest->country)
                    ->action('Voir la demande', url('/admin/account-requests/' . $this->accountRequest->id))
                    ->line('Veuillez examiner cette demande et prendre les mesures appropriées.')
                    ->salutation('Cordialement,');
            }

            return (new MailMessage)
                ->subject('New Account Request - FIT Platform')
                ->greeting('Hello ' . $notifiable->name . ',')
                ->line('A new account request has been submitted on the FIT platform.')
                ->line('**Request Details:**')
                ->line('• **Name:** ' . $this->accountRequest->full_name)
                ->line('• **Email:** ' . $this->accountRequest->email)
                ->line('• **Organization:** ' . $this->accountRequest->organization_name)
                ->line('• **Organization Type:** ' . $this->accountRequest->organization_type_label)
                ->line('• **Football Type:** ' . $this->accountRequest->football_type_label)
                ->line('• **Country:** ' . $this->accountRequest->country)
                ->action('View Request', url('/admin/account-requests/' . $this->accountRequest->id))
                ->line('Please review this request and take appropriate action.')
                ->salutation('Best regards,');

        } catch (\Exception $e) {
            Log::error('AccountRequestSubmitted notification error', [
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
                    'message' => 'Account request notification (request no longer available)',
                    'type' => 'account_request_submitted',
                    'status' => 'expired'
                ];
            }

            return [
                'account_request_id' => $this->accountRequest->id,
                'message' => app()->getLocale() === 'fr' 
                    ? 'Nouvelle demande de compte soumise par ' . $this->accountRequest->full_name
                    : 'New account request submitted by ' . $this->accountRequest->full_name,
                'type' => 'account_request_submitted',
                'status' => 'active'
            ];

        } catch (\Exception $e) {
            Log::error('AccountRequestSubmitted toArray error', [
                'account_request_id' => $this->accountRequest->id ?? 'unknown',
                'error' => $e->getMessage()
            ]);
            
            return [
                'account_request_id' => $this->accountRequest->id ?? 'unknown',
                'message' => 'Account request notification (error occurred)',
                'type' => 'account_request_submitted',
                'status' => 'error'
            ];
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception)
    {
        Log::error('AccountRequestSubmitted notification failed', [
            'account_request_id' => $this->accountRequest->id ?? 'unknown',
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
} 