<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create super admin user
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@luky.sa'],
            [
                'name' => 'System Administrator',
                'phone' => '+966500000000',
                'password' => Hash::make('admin123'),
                'user_type' => 'admin',
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
                'status' => 'active',
                'is_active' => true,
            ]
        );

        // Assign super_admin role (has all permissions)
        $superAdmin->assignRole('super_admin');

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@luky.sa');
        $this->command->info('Password: admin123');
        $this->command->info('User Type: admin');
        $this->command->info('Role: super_admin (all permissions)');
    }
}
