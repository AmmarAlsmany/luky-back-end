<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;

class TestAdminNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:admin-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test sending a notification to all admin users';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService)
    {
        $this->info('🔔 Testing Admin Notification System...');
        $this->info('');

        try {
            // Send test notification to all admins
            $notificationService->sendToAdmins(
                'system_test',
                'Test Notification',
                'This is a test notification to verify the admin notification system is working correctly.',
                [
                    'test' => true,
                    'timestamp' => now()->toIso8601String(),
                ]
            );

            $this->info('✅ Test notification sent successfully!');
            $this->info('');
            $this->info('📊 Check:');
            $this->info('  1. Login to admin dashboard');
            $this->info('  2. Look at the bell icon in the topbar');
            $this->info('  3. You should see a notification badge');
            $this->info('  4. Click the bell to see the test notification');
            $this->info('');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Failed to send test notification');
            $this->error('Error: ' . $e->getMessage());
            $this->error('');
            $this->error('Check logs at: storage/logs/laravel.log');
            
            return Command::FAILURE;
        }
    }
}
