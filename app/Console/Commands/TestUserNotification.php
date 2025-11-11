<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestUserNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:user-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test user creation notification by creating a test admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔔 Testing User Creation Notification...');
        $this->info('');

        try {
            // Create a test admin user
            $testUser = User::create([
                'name' => 'Test Admin User',
                'email' => 'test.admin.' . time() . '@luky.com',
                'phone' => '+966500' . rand(100000, 999999),
                'password' => Hash::make('password'),
                'user_type' => 'admin',
                'gender' => 'male',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            // Assign a role
            $testUser->assignRole('super_admin');

            $this->info('✅ Test admin user created successfully!');
            $this->info('   Name: ' . $testUser->name);
            $this->info('   Email: ' . $testUser->email);
            $this->info('');
            $this->info('📊 Check:');
            $this->info('  1. Login to admin dashboard');
            $this->info('  2. Look at the bell icon in the topbar');
            $this->info('  3. You should see a notification about the new admin user');
            $this->info('');
            $this->warn('⚠️  Remember to delete this test user later!');
            $this->info('   User ID: ' . $testUser->id);
            $this->info('');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Failed to create test user');
            $this->error('Error: ' . $e->getMessage());
            $this->error('');
            $this->error('Check logs at: storage/logs/laravel.log');
            
            return Command::FAILURE;
        }
    }
}
