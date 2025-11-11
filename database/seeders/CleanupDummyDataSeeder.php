<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class CleanupDummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('🧹 Starting database cleanup...');

        // Disable foreign key checks
        DB::statement('SET CONSTRAINTS ALL DEFERRED');

        // Helper function to safely delete from table
        $safeDelete = function($table) {
            if (DB::getSchemaBuilder()->hasTable($table)) {
                $count = DB::table($table)->count();
                DB::table($table)->delete();
                $this->command->info("   ✓ Deleted {$count} records from {$table}");
            } else {
                $this->command->warn("   ⚠ Table {$table} does not exist, skipping...");
            }
        };

        // 1. Delete all bookings and related data
        $this->command->info('Deleting bookings...');
        $safeDelete('booking_services');
        $safeDelete('bookings');

        // 2. Delete all reviews
        $this->command->info('Deleting reviews...');
        $safeDelete('reviews');

        // 3. Delete all messages and conversations
        $this->command->info('Deleting messages and conversations...');
        $safeDelete('messages');
        $safeDelete('conversations');
        $safeDelete('admin_messages');
        $safeDelete('admin_conversations');

        // 4. Delete all tickets
        $this->command->info('Deleting tickets...');
        $safeDelete('ticket_replies');
        $safeDelete('tickets');

        // 5. Delete all notifications
        $this->command->info('Deleting notifications...');
        $safeDelete('notifications');

        // 6. Delete all payments
        $this->command->info('Deleting payments...');
        $safeDelete('payments');

        // 7. Delete all promo codes
        $this->command->info('Deleting promo codes...');
        $safeDelete('promo_codes');

        // 8. Delete all banners
        $this->command->info('Deleting banners...');
        $safeDelete('banners');

        // 9. Delete service providers and their services
        $this->command->info('Deleting service providers...');
        $safeDelete('provider_services');
        $safeDelete('provider_documents');
        $safeDelete('service_providers');

        // 10. Delete all client users (keep only admins)
        $this->command->info('Deleting client users...');
        DB::table('users')->where('user_type', 'client')->delete();
        DB::table('users')->where('user_type', 'provider')->delete();

        // 11. Keep only ONE super admin
        $this->command->info('Cleaning up admin users...');
        
        // Find or create the main super admin
        $superAdmin = User::where('user_type', 'admin')
            ->whereHas('roles', function($q) {
                $q->where('name', 'super_admin');
            })
            ->first();

        if (!$superAdmin) {
            // Create a super admin if none exists
            $this->command->info('Creating super admin...');
            $superAdmin = User::create([
                'name' => 'Super Admin',
                'email' => 'admin@luky.com',
                'phone' => '+966500000000',
                'password' => Hash::make('admin123'),
                'user_type' => 'admin',
                'gender' => 'male',
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
            
            // Assign super_admin role
            $superAdmin->assignRole('super_admin');
            
            $this->command->info('✅ Super admin created: admin@luky.com / password');
        } else {
            $this->command->info('✅ Keeping existing super admin: ' . $superAdmin->email);
        }

        // Delete all other admin users
        User::where('user_type', 'admin')
            ->where('id', '!=', $superAdmin->id)
            ->delete();

        // 12. Reset auto-increment IDs (optional)
        $this->command->info('Resetting auto-increment IDs...');
        $tables = [
            'bookings',
            'reviews',
            'messages',
            'conversations',
            'admin_messages',
            'admin_conversations',
            'tickets',
            'ticket_replies',
            'payments',
            'promo_codes',
            'banners',
            'service_providers',
            'provider_services',
        ];

        foreach ($tables as $table) {
            try {
                DB::statement("ALTER SEQUENCE {$table}_id_seq RESTART WITH 1");
            } catch (\Exception $e) {
                // Ignore if sequence doesn't exist
            }
        }

        // Re-enable foreign key checks
        DB::statement('SET CONSTRAINTS ALL IMMEDIATE');

        $this->command->info('');
        $this->command->info('✅ Database cleanup completed!');
        $this->command->info('');
        $this->command->info('📊 Summary:');
        $this->command->info('   - All dummy data removed');
        $this->command->info('   - Only 1 super admin user remaining');
        $this->command->info('   - Email: ' . $superAdmin->email);
        $this->command->info('   - Password: password (if newly created)');
        $this->command->info('');
        $this->command->warn('⚠️  Make sure to change the password after login!');
    }
}
