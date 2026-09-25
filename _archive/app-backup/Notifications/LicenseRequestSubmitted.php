<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\PlayerLicense;
use Illuminate\Support\Facades\Log;

class LicenseRequestSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public $license;

    public function __construct(PlayerLicense $license)
    {
        $this->license = $license;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        try {
            // Check if the license and related models still exist
            if (!$this->license || !$this->license->exists) {
                Log::warning('LicenseRequestSubmitted notification: License model not found', [
                    'license_id' => $this->license->id ?? 'unknown'
                ]);
                return null;
            }

            $player = $this->license->player;
            $club = $this->license->club;
            $requestedBy = $this->license->requestedByUser;

            if (!$player || !$club || !$requestedBy) {
                Log::warning('LicenseRequestSubmitted notification: Related models not found', [
                    'license_id' => $this->license->id,
                    'player_exists' => $player ? true : false,
                    'club_exists' => $club ? true : false,
                    'requested_by_exists' => $requestedBy ? true : false
                ]);
                return null;
            }

            return (new MailMessage)
                ->subject("New Player License Request - {$player->name}")
                ->greeting("Hello {$notifiable->name},")
                ->line("A new player license request has been submitted and requires your review.")
                ->line("**Player:** {$player->name}")
                ->line("**Club:** {$club->name}")
                ->line("**License Number:** {$this->license->license_number}")
                ->line("**Requested by:** {$requestedBy->name}")
                ->line("**Submitted:** {$this->license->created_at->format('M d, Y H:i')}")
                ->action('Review Request', route('player-licenses.show', $this->license))
                ->line('Please review the application and supporting documents.')
                ->line('Thank you for your attention to this matter.')
                ->salutation('Best regards, FIFA Connect Team');

        } catch (\Exception $e) {
            Log::error('LicenseRequestSubmitted notification error', [
                'license_id' => $this->license->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    public function toArray($notifiable)
    {
        try {
            // Check if the license and related models still exist
            if (!$this->license || !$this->license->exists) {
                return [
                    'license_id' => $this->license->id ?? 'unknown',
                    'message' => 'License request notification (license no longer available)',
                    'type' => 'license_request_submitted',
                    'status' => 'expired'
                ];
            }

            $player = $this->license->player;
            $club = $this->license->club;
            $requestedBy = $this->license->requestedByUser;

            return [
                'license_id' => $this->license->id,
                'player_name' => $player ? $player->name : 'Unknown Player',
                'club_name' => $club ? $club->name : 'Unknown Club',
                'license_number' => $this->license->license_number,
                'requested_by' => $requestedBy ? $requestedBy->name : 'Unknown User',
                'message' => $player && $club 
                    ? "New license request for {$player->name} from {$club->name}"
                    : "New license request submitted",
                'action_url' => route('player-licenses.show', $this->license),
                'type' => 'license_request_submitted',
                'status' => 'active'
            ];

        } catch (\Exception $e) {
            Log::error('LicenseRequestSubmitted toArray error', [
                'license_id' => $this->license->id ?? 'unknown',
                'error' => $e->getMessage()
            ]);
            
            return [
                'license_id' => $this->license->id ?? 'unknown',
                'message' => 'License request notification (error occurred)',
                'type' => 'license_request_submitted',
                'status' => 'error'
            ];
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception)
    {
        Log::error('LicenseRequestSubmitted notification failed', [
            'license_id' => $this->license->id ?? 'unknown',
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
} 