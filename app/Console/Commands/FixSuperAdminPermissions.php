<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class FixSuperAdminPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:fix-super-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix super admin permissions - grant all permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Fixing Super Admin Permissions...');
        $this->info('');

        // Get or create super_admin role
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $this->info('✓ Super Admin role found/created');

        // Get all permissions
        $allPermissions = Permission::all();
        $this->info('✓ Found ' . $allPermissions->count() . ' permissions');

        // Assign all permissions to super_admin
        $superAdmin->syncPermissions($allPermissions);
        $this->info('✓ All permissions assigned to Super Admin role');

        // Find all super admin users
        $superAdminUsers = User::role('super_admin')->get();
        
        if ($superAdminUsers->isEmpty()) {
            $this->warn('⚠ No users with super_admin role found');
        } else {
            $this->info('');
            $this->info('Super Admin Users:');
            foreach ($superAdminUsers as $user) {
                $this->info('  - ' . $user->email . ' (ID: ' . $user->id . ')');
                
                // Ensure user has the role
                if (!$user->hasRole('super_admin')) {
                    $user->assignRole('super_admin');
                    $this->info('    → Role assigned');
                }
            }
        }

        $this->info('');
        $this->info('📊 Permission Summary:');
        $this->table(
            ['Permission Category', 'Count'],
            [
                ['Total Permissions', $allPermissions->count()],
                ['Super Admin Permissions', $superAdmin->permissions->count()],
            ]
        );

        $this->info('');
        $this->info('✅ Super Admin permissions fixed successfully!');
        
        return Command::SUCCESS;
    }
}
