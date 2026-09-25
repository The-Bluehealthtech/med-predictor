<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class CleanupNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:cleanup {--days=30 : Number of days to keep notifications}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired notifications from the database';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService)
    {
        $this->info('Starting notification cleanup...');

        try {
            $deletedCount = $notificationService->cleanupExpiredNotifications();

            if ($deletedCount > 0) {
                $this->info("Successfully cleaned up {$deletedCount} expired notifications.");
            } else {
                $this->info('No expired notifications found to clean up.');
            }

            // Get notification statistics
            $stats = $notificationService->getNotificationStats();
            
            $this->info('Current notification statistics:');
            $this->table(
                ['Metric', 'Count'],
                [
                    ['Total Notifications', $stats['total_notifications'] ?? 0],
                    ['Unread Notifications', $stats['unread_notifications'] ?? 0],
                    ['Notifications Today', $stats['notifications_today'] ?? 0],
                    ['Notifications This Week', $stats['notifications_this_week'] ?? 0],
                    ['Notifications This Month', $stats['notifications_this_month'] ?? 0],
                ]
            );

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Failed to cleanup notifications: ' . $e->getMessage());
            Log::error('Notification cleanup command failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }
    }
} 