<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create all application roles
        $roles = [
            // Client and Provider roles
            'client',
            'provider',
            
            // Dashboard admin roles
            'super_admin',
            'admin',
            'manager',
            'support_agent',
            'content_manager',
            'analyst',
        ];

        foreach ($roles as $name) {
            Role::firstOrCreate(
                ['name' => $name, 'guard_name' => 'web']
            );
        }

        $this->command->info('All roles created successfully!');
    }
}
