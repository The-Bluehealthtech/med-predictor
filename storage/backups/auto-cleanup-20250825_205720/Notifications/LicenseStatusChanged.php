<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\PlayerLicense;
use App\Models\User;

class LicenseStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public $license;
    public $oldStatus;
    public $newStatus;
    public $actionBy;
    public $reason;

    public function __construct(PlayerLicense $license, $oldStatus, $newStatus, User $actionBy = null, $reason = null)
    {
        $this->license = $license;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
        $this->actionBy = $actionBy;
        $this->reason = $reason;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $player = $this->license->player;
        $club = $this->license->club;
        $requestedBy = $this->license->requestedByUser;

        $subject = match($this->newStatus) {
            'active' => 'Player License Approved',
            'revoked' => 'Player License Rejected',
            'justification_requested' => 'Explanation Requested for Player License',
            default => 'Player License Status Updated'
        };

        $message = match($this->newStatus) {
            'active' => "The player license for {$player->full_name} has been approved.",
            'revoked' => "The player license for {$player->full_name} has been rejected. Reason: {$this->reason}",
            'justification_requested' => "Additional information is requested for the player license of {$player->full_name}. Please provide the requested details.",
            default => "The status of the player license for {$player->full_name} has been updated to {$this->newStatus}."
        };

        return (new MailMessage)
            ->subject($subject)
            ->line($message)
            ->line("Player: {$player->full_name}")
            ->line("Club: {$club->name}")
            ->line("Requested by: {$requestedBy->name}")
            ->action('View License', route('club.player-licenses.show', $this->license))
            ->line('Thank you for using our application!');
    }

    public function toArray($notifiable)
    {
        return [
            'license_id' => $this->license->id,
            'player_name' => $this->license->player->name,
            'license_number' => $this->license->license_number,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'action_by' => $this->actionBy ? $this->actionBy->name : null,
            'reason' => $this->reason,
            'club_name' => $this->license->club->name,
            'message' => "License status changed from {$this->oldStatus} to {$this->newStatus}",
            'action_url' => route('club-management.licenses.show', $this->license)
        ];
    }
}
