<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class ProductionSeeder extends Seeder
{
    /**
     * Run the production database seeds.
     * This seeder creates only essential data for production
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting Production Database Seeding...');
        
        // 1. Create Roles and Permissions
        $this->command->info('Creating roles and permissions...');
        $this->call(RolesAndPermissionsSeeder::class);
        
        // 2. Create Cities (Required for the system)
        $this->command->info('Creating cities...');
        $this->call(CitySeeder::class);
        
        // 3. Create Service Categories (Required)
        $this->command->info('Creating service categories...');
        $this->call(ServiceCategorySeeder::class);
        
        // 4. Create Super Admin User
        $this->command->info('Creating super admin user...');
        $this->createSuperAdmin();
        
        $this->command->info('✅ Production seeding completed successfully!');
        $this->command->newLine();
        $this->command->warn('⚠️  IMPORTANT: Change the super admin password immediately!');
        $this->command->newLine();
        $this->displayCredentials();
    }
    
    /**
     * Create the super admin user
     */
    private function createSuperAdmin(): void
    {
        // Check if super admin already exists
        $existingAdmin = User::where('email', 'admin@luky.sa')->first();
        
        if ($existingAdmin) {
            $this->command->warn('Super admin already exists. Skipping creation.');
            return;
        }
        
        // Create super admin user
        $superAdmin = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@luky.sa',
            'phone' => '+966500000000',
            'password' => Hash::make('Luky@2025!Admin'),
            'user_type' => 'admin',
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'status' => 'active',
            'is_active' => true,
        ]);

        // Assign super_admin role (has all permissions)
        $superAdmin->assignRole('super_admin');
        
        $this->command->info('✓ Super admin user created successfully!');
    }
    
    /**
     * Display login credentials
     */
    private function displayCredentials(): void
    {
        $this->command->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->line('    SUPER ADMIN CREDENTIALS');
        $this->command->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->line('');
        $this->command->line('  Email:    admin@luky.sa');
        $this->command->line('  Password: Luky@2025!Admin');
        $this->command->line('  Role:     super_admin');
        $this->command->line('');
        $this->command->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->command->line('');
        $this->command->warn('⚠️  SECURITY REMINDER:');
        $this->command->warn('   1. Login immediately and change this password');
        $this->command->warn('   2. Use a strong, unique password');
        $this->command->warn('   3. Enable two-factor authentication if available');
        $this->command->warn('   4. Never share these credentials');
        $this->command->line('');
    }
}
