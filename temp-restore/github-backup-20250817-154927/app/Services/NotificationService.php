<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\AccountRequest;
use App\Models\LicenseRequest;
use App\Models\PlayerLicense;
use App\Notifications\AccountRequestSubmitted;
use App\Notifications\AccountRequestApproved;
use App\Notifications\AccountRequestRejected;
use App\Notifications\LicenseRequestSubmitted;
use App\Notifications\LicenseRequestStatusChanged;
use App\Notifications\LicenseStatusChanged;
use App\Notifications\MatchCompletedNotification;

class NotificationService
{
    /**
     * Send account request submitted notification
     */
    public function sendAccountRequestSubmitted(AccountRequest $accountRequest)
    {
        try {
            // Find users to notify (association agents and system admins)
            $usersToNotify = User::where(function($query) {
                $query->where('role', 'association_admin')
                      ->orWhere('role', 'association_registrar')
                      ->orWhere('role', 'system_admin');
            })->get();

            if ($usersToNotify->isEmpty()) {
                Log::warning('No users found to notify about account request', [
                    'account_request_id' => $accountRequest->id
                ]);
                return false;
            }

            // Send notification to all users
            foreach ($usersToNotify as $user) {
                try {
                    $user->notify(new AccountRequestSubmitted($accountRequest));
                } catch (\Exception $e) {
                    Log::error('Failed to send account request submitted notification to user', [
                        'user_id' => $user->id,
                        'user_role' => $user->role,
                        'account_request_id' => $accountRequest->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            Log::info('Account request submitted notifications sent', [
                'account_request_id' => $accountRequest->id,
                'recipients_count' => $usersToNotify->count(),
                'recipients_roles' => $usersToNotify->pluck('role')->toArray()
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to send account request submitted notifications', [
                'account_request_id' => $accountRequest->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Send account request approved notification
     */
    public function sendAccountRequestApproved(AccountRequest $accountRequest, User $approvedBy = null)
    {
        try {
            // Send email notification to the requester
            Notification::route('mail', $accountRequest->email)
                ->notify(new AccountRequestApproved($accountRequest, $approvedBy));

            Log::info('Account request approved notification sent', [
                'account_request_id' => $accountRequest->id,
                'recipient_email' => $accountRequest->email,
                'approved_by' => $approvedBy ? $approvedBy->id : null
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to send account request approved notification', [
                'account_request_id' => $accountRequest->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Send account request rejected notification
     */
    public function sendAccountRequestRejected(AccountRequest $accountRequest, User $rejectedBy = null, string $reason = null)
    {
        try {
            // Send email notification to the requester
            Notification::route('mail', $accountRequest->email)
                ->notify(new AccountRequestRejected($accountRequest, $rejectedBy, $reason));

            Log::info('Account request rejected notification sent', [
                'account_request_id' => $accountRequest->id,
                'recipient_email' => $accountRequest->email,
                'rejected_by' => $rejectedBy ? $rejectedBy->id : null,
                'reason' => $reason
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to send account request rejected notification', [
                'account_request_id' => $accountRequest->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Send license request submitted notification
     */
    public function sendLicenseRequestSubmitted(PlayerLicense $license)
    {
        try {
            // Find association agents to notify
            $associationAgents = User::where('role', 'association_admin')
                ->orWhere('role', 'association_registrar')
                ->get();

            if ($associationAgents->isEmpty()) {
                Log::warning('No association agents found to notify about license request', [
                    'license_id' => $license->id
                ]);
                return false;
            }

            // Send notification to all association agents
            foreach ($associationAgents as $agent) {
                try {
                    $agent->notify(new LicenseRequestSubmitted($license));
                } catch (\Exception $e) {
                    Log::error('Failed to send license request submitted notification to agent', [
                        'agent_id' => $agent->id,
                        'license_id' => $license->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            Log::info('License request submitted notifications sent', [
                'license_id' => $license->id,
                'recipients_count' => $associationAgents->count()
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to send license request submitted notifications', [
                'license_id' => $license->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Send license request status changed notification
     */
    public function sendLicenseRequestStatusChanged(LicenseRequest $licenseRequest, string $newStatus, string $comment = null)
    {
        try {
            // Notify the club that submitted the request
            if ($licenseRequest->club && $licenseRequest->club->admin) {
                try {
                    $licenseRequest->club->admin->notify(new LicenseRequestStatusChanged($licenseRequest, $newStatus, $comment));
                } catch (\Exception $e) {
                    Log::error('Failed to send license status change notification to club admin', [
                        'license_request_id' => $licenseRequest->id,
                        'club_admin_id' => $licenseRequest->club->admin->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            Log::info('License request status change notification sent', [
                'license_request_id' => $licenseRequest->id,
                'new_status' => $newStatus,
                'club_admin_notified' => $licenseRequest->club && $licenseRequest->club->admin ? true : false
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to send license request status change notification', [
                'license_request_id' => $licenseRequest->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Send license status changed notification
     */
    public function sendLicenseStatusChanged(PlayerLicense $license, string $newStatus, string $comment = null)
    {
        try {
            // Notify the player and club
            $recipients = collect();

            if ($license->player && $license->player->user) {
                $recipients->push($license->player->user);
            }

            if ($license->club && $license->club->admin) {
                $recipients->push($license->club->admin);
            }

            if ($recipients->isEmpty()) {
                Log::warning('No recipients found for license status change notification', [
                    'license_id' => $license->id
                ]);
                return false;
            }

            // Send notification to all recipients
            foreach ($recipients as $recipient) {
                try {
                    $recipient->notify(new LicenseStatusChanged($license, $newStatus, $comment));
                } catch (\Exception $e) {
                    Log::error('Failed to send license status change notification to recipient', [
                        'recipient_id' => $recipient->id,
                        'license_id' => $license->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            Log::info('License status change notifications sent', [
                'license_id' => $license->id,
                'new_status' => $newStatus,
                'recipients_count' => $recipients->count()
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to send license status change notifications', [
                'license_id' => $license->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Send match completed notification
     */
    public function sendMatchCompletedNotification($match)
    {
        try {
            // Find relevant users to notify (referees, club officials, etc.)
            $recipients = collect();

            // Add match referee if exists
            if ($match->referee) {
                $recipients->push($match->referee);
            }

            // Add club officials
            if ($match->homeClub && $match->homeClub->admin) {
                $recipients->push($match->homeClub->admin);
            }

            if ($match->awayClub && $match->awayClub->admin) {
                $recipients->push($match->awayClub->admin);
            }

            if ($recipients->isEmpty()) {
                Log::warning('No recipients found for match completed notification', [
                    'match_id' => $match->id
                ]);
                return false;
            }

            // Send notification to all recipients
            foreach ($recipients as $recipient) {
                try {
                    $recipient->notify(new MatchCompletedNotification($match));
                } catch (\Exception $e) {
                    Log::error('Failed to send match completed notification to recipient', [
                        'recipient_id' => $recipient->id,
                        'match_id' => $match->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            Log::info('Match completed notifications sent', [
                'match_id' => $match->id,
                'recipients_count' => $recipients->count()
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to send match completed notifications', [
                'match_id' => $match->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Clean up expired notifications
     */
    public function cleanupExpiredNotifications()
    {
        try {
            // Delete notifications older than 30 days
            $deletedCount = \DB::table('notifications')
                ->where('created_at', '<', now()->subDays(30))
                ->delete();

            Log::info('Expired notifications cleaned up', [
                'deleted_count' => $deletedCount
            ]);

            return $deletedCount;

        } catch (\Exception $e) {
            Log::error('Failed to cleanup expired notifications', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return 0;
        }
    }

    /**
     * Get notification statistics
     */
    public function getNotificationStats()
    {
        try {
            $stats = [
                'total_notifications' => \DB::table('notifications')->count(),
                'unread_notifications' => \DB::table('notifications')->whereNull('read_at')->count(),
                'notifications_today' => \DB::table('notifications')->whereDate('created_at', today())->count(),
                'notifications_this_week' => \DB::table('notifications')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'notifications_this_month' => \DB::table('notifications')->whereMonth('created_at', now()->month)->count(),
            ];

            return $stats;

        } catch (\Exception $e) {
            Log::error('Failed to get notification statistics', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [];
        }
    }
} 