<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define all permissions
        $permissions = [
            // Client permissions
            'view_clients',
            'create_clients',
            'edit_clients',
            'delete_clients',
            'manage_clients',
            
            // Provider permissions
            'view_providers',
            'create_providers',
            'edit_providers',
            'delete_providers',
            'approve_providers',
            'manage_providers',
            
            // Booking permissions
            'view_bookings',
            'create_bookings',
            'edit_bookings',
            'delete_bookings',
            'manage_bookings',
            
            // Service permissions
            'view_services',
            'create_services',
            'edit_services',
            'delete_services',
            'manage_services',
            
            // Promo permissions
            'view_promos',
            'create_promos',
            'edit_promos',
            'delete_promos',
            'manage_promos',
            
            // User & Role permissions
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'manage_users',
            'view_roles',
            'create_roles',
            'edit_roles',
            'delete_roles',
            'manage_roles',
            
            // Review permissions
            'view_reviews',
            'manage_reviews',
            
            // Notification permissions
            'send_notifications',
            'manage_notifications',
            
            // Payment permissions
            'view_payments',
            'manage_payments',
            
            // Report permissions
            'view_reports',
            'export_reports',
            
            // Settings permissions
            'view_settings',
            'manage_settings',
        ];

        // Create all permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => 'web']
            );
        }

        $this->command->info('Permissions created successfully!');

        // Assign permissions to roles
        $this->assignPermissionsToRoles();
    }

    private function assignPermissionsToRoles()
    {
        // Super Admin - All permissions
        $superAdmin = Role::where('name', 'super_admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo(Permission::all());
            $this->command->info('Super Admin: All permissions assigned');
        }

        // Admin - Most permissions except critical ones
        $admin = Role::where('name', 'admin')->first();
        if ($admin) {
            $admin->givePermissionTo([
                'view_clients', 'manage_clients',
                'view_providers', 'manage_providers', 'approve_providers',
                'view_bookings', 'manage_bookings',
                'view_services', 'manage_services',
                'view_promos', 'manage_promos',
                'view_users', 'edit_users',
                'view_reviews', 'manage_reviews',
                'send_notifications',
                'view_payments',
                'view_reports', 'export_reports',
            ]);
            $this->command->info('Admin: Standard permissions assigned');
        }

        // Manager - Read and edit permissions
        $manager = Role::where('name', 'manager')->first();
        if ($manager) {
            $manager->givePermissionTo([
                'view_clients',
                'view_providers', 'edit_providers',
                'view_bookings', 'edit_bookings',
                'view_services',
                'view_promos', 'edit_promos',
                'view_reviews',
                'view_reports',
            ]);
            $this->command->info('Manager: Management permissions assigned');
        }

        // Support Agent - View and limited edit
        $support = Role::where('name', 'support_agent')->first();
        if ($support) {
            $support->givePermissionTo([
                'view_clients',
                'view_providers',
                'view_bookings', 'edit_bookings',
                'view_services',
                'view_reviews', 'manage_reviews',
                'send_notifications',
            ]);
            $this->command->info('Support Agent: Support permissions assigned');
        }

        // Content Manager - Content-related permissions
        $contentManager = Role::where('name', 'content_manager')->first();
        if ($contentManager) {
            $contentManager->givePermissionTo([
                'view_services', 'manage_services',
                'view_promos', 'manage_promos',
                'send_notifications',
            ]);
            $this->command->info('Content Manager: Content permissions assigned');
        }

        // Analyst - Read-only access to reports and data
        $analyst = Role::where('name', 'analyst')->first();
        if ($analyst) {
            $analyst->givePermissionTo([
                'view_clients',
                'view_providers',
                'view_bookings',
                'view_services',
                'view_promos',
                'view_reviews',
                'view_payments',
                'view_reports', 'export_reports',
            ]);
            $this->command->info('Analyst: Read-only permissions assigned');
        }
    }
}
