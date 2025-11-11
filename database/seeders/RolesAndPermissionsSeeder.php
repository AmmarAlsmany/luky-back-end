<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User Management
            'view_users',
            'manage_users',
            'view_clients',
            'create_clients',
            'edit_clients',
            'delete_clients',
            'view_providers',
            'create_providers',
            'edit_providers',
            'delete_providers',
            'view_employees',
            'create_employees',
            'edit_employees',
            'delete_employees',

            // Booking Management
            'view_bookings',
            'edit_bookings',
            'cancel_bookings',
            'export_bookings',

            // Payment Management
            'view_payments',
            'manage_payment_settings',
            'refund_payments',

            // Content Management
            'manage_banners',
            'manage_static_pages',
            'manage_categories',

            // Promotion Management
            'view_promo_codes',
            'create_promo_codes',
            'edit_promo_codes',
            'delete_promo_codes',

            // Review Management
            'view_reviews',
            'hide_reviews',
            'delete_reviews',

            // Support Management
            'view_tickets',
            'create_tickets',
            'assign_tickets',
            'close_tickets',
            'view_chat',

            // Notification Management
            'send_notifications',
            'view_notifications',

            // Report Access
            'view_reports',
            'export_reports',

            // Settings Management
            'manage_general_settings',
            'manage_app_settings',

            // Role Management
            'view_roles',
            'manage_roles',
            'create_roles',
            'edit_roles',
            'delete_roles',
            'assign_permissions',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions

        // Super Admin - has all permissions
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Manager - has most permissions except role management
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $manager->givePermissionTo([
            'view_clients', 'create_clients', 'edit_clients', 'delete_clients',
            'view_providers', 'create_providers', 'edit_providers', 'delete_providers',
            'view_employees', 'edit_employees',
            'view_bookings', 'edit_bookings', 'cancel_bookings', 'export_bookings',
            'view_payments', 'manage_payment_settings',
            'manage_banners', 'manage_static_pages', 'manage_categories',
            'view_promo_codes', 'create_promo_codes', 'edit_promo_codes', 'delete_promo_codes',
            'view_reviews', 'hide_reviews',
            'view_tickets', 'create_tickets', 'assign_tickets', 'close_tickets', 'view_chat',
            'send_notifications', 'view_notifications',
            'view_reports', 'export_reports',
            'manage_general_settings', 'manage_app_settings',
        ]);

        // Support Agent - focused on customer service
        $supportAgent = Role::firstOrCreate(['name' => 'support_agent']);
        $supportAgent->givePermissionTo([
            'view_clients', 'edit_clients',
            'view_providers', 'edit_providers',
            'view_bookings', 'edit_bookings',
            'view_payments',
            'view_reviews',
            'view_tickets', 'create_tickets', 'assign_tickets', 'close_tickets', 'view_chat',
            'send_notifications', 'view_notifications',
        ]);

        // Content Manager - manages content and promotions
        $contentManager = Role::firstOrCreate(['name' => 'content_manager']);
        $contentManager->givePermissionTo([
            'manage_banners', 'manage_static_pages', 'manage_categories',
            'view_promo_codes', 'create_promo_codes', 'edit_promo_codes', 'delete_promo_codes',
            'view_reviews', 'hide_reviews',
            'send_notifications', 'view_notifications',
        ]);

        // Analyst - read-only access to reports and data
        $analyst = Role::firstOrCreate(['name' => 'analyst']);
        $analyst->givePermissionTo([
            'view_clients', 'view_providers', 'view_employees',
            'view_bookings', 'view_payments',
            'view_promo_codes', 'view_reviews', 'view_tickets',
            'view_reports', 'export_reports',
        ]);

        // Client role (for mobile app users)
        $client = Role::firstOrCreate(['name' => 'client']);
        // Clients don't need dashboard permissions

        // Provider role (for mobile app users)
        $provider = Role::firstOrCreate(['name' => 'provider']);
        // Providers don't need dashboard permissions

        $this->command->info('Roles and permissions seeded successfully!');
    }
}
