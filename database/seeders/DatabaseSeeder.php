<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            CitySeeder::class,
            ServiceCategorySeeder::class,
            RolesAndPermissionsSeeder::class, // New comprehensive roles/permissions
            SuperAdminSeeder::class, // Creates super admin user
            TestProvidersSeeder::class,
        ]);
    }
}
