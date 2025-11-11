<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Permission;

class CheckUserPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:check {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check permissions for a specific user by email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error('User not found with email: ' . $email);
            return Command::FAILURE;
        }

        $this->info('');
        $this->info('👤 User Information:');
        $this->info('   Name: ' . $user->name);
        $this->info('   Email: ' . $user->email);
        $this->info('   Type: ' . $user->user_type);
        $this->info('   Status: ' . ($user->is_active ? 'Active' : 'Inactive'));
        
        $roles = $user->getRoleNames();
        $this->info('   Roles: ' . ($roles->isEmpty() ? 'None' : $roles->implode(', ')));
        
        $this->info('');
        $this->info('🔑 Permissions:');
        
        $permissions = $user->getAllPermissions();
        
        if ($permissions->isEmpty()) {
            $this->warn('   No permissions assigned');
        } else {
            $grouped = $permissions->groupBy(function($permission) {
                $parts = explode('_', $permission->name);
                return count($parts) > 1 ? $parts[1] : 'other';
            });
            
            foreach ($grouped as $group => $perms) {
                $this->info('');
                $this->info('   ' . ucfirst($group) . ':');
                foreach ($perms as $perm) {
                    $this->line('     ✓ ' . $perm->name);
                }
            }
        }
        
        $this->info('');
        $this->info('📊 Summary:');
        $this->info('   Total Permissions: ' . $permissions->count());
        $this->info('   Total Available: ' . Permission::count());
        
        // Check specific permissions
        $this->info('');
        $this->info('🔍 Key Permission Checks:');
        $keyPermissions = [
            'view_promos',
            'create_promos',
            'edit_promos',
            'delete_promos',
            'view_clients',
            'view_providers',
            'view_bookings',
        ];
        
        foreach ($keyPermissions as $perm) {
            $has = $user->hasPermissionTo($perm);
            $icon = $has ? '✅' : '❌';
            $this->line('   ' . $icon . ' ' . $perm);
        }
        
        return Command::SUCCESS;
    }
}
