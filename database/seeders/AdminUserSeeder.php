<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission as ModelsPermission;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Create Roles
        $adminRole = Role::create(['name' => 'admin']);
        $clientRole = Role::create(['name' => 'client']);
        $providerRole = Role::create(['name' => 'provider']);

        // Create Permissions
        $permissions = [
            'manage-users',
            'manage-providers', 
            'manage-bookings',
            'manage-payments',
            'manage-settings',
            'view-analytics',
            'manage-support'
        ];

        foreach ($permissions as $permission) {
            ModelsPermission::create(['name' => $permission]);
        }

        // Assign all permissions to admin role
        $adminRole->givePermissionTo($permissions);

        // Create admin user
        $admin = User::create([
            'name' => 'Admin',
            'phone' => '+966500000000',
            'email' => 'admin@luky.sa',
            'password' => Hash::make('password'),
            'user_type' => 'admin',
            'is_active' => true,
            'phone_verified_at' => now(),
        ]);

        $admin->assignRole('admin');

        // Create test client
        $client = User::create([
            'name' => 'Client',
            'phone' => '+966500000001',
            'email' => 'client@test.com',
            'user_type' => 'client',
            'date_of_birth' => '1990-01-01',
            'gender' => 'female',
            'city_id' => 1, // Riyadh
            'is_active' => true,
            'phone_verified_at' => now(),
        ]);

        $client->assignRole('client');

        // Create test provider
        $provider = User::create([
            'name' => 'Test',
            'phone' => '+966500000002',
            'email' => 'provider@test.com',
            'user_type' => 'provider',
            'date_of_birth' => '1985-01-01',
            'gender' => 'female',
            'city_id' => 1, // Riyadh
            'is_active' => true,
            'phone_verified_at' => now(),
        ]);

        $provider->assignRole('provider');
    }
}
