<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

trait AdminTestTrait
{
    use RefreshDatabase;

    protected $adminUser;
    protected $adminToken;

    /**
     * Create an admin user with specified role
     */
    protected function createAdminUser($role = 'super_admin')
    {
        // Ensure role exists
        if (!Role::where('name', $role)->exists()) {
            Role::create(['name' => $role, 'guard_name' => 'web']);
        }

        $user = User::factory()->create([
            'name' => 'Admin Test User',
            'email' => 'admin@test.com',
            'phone' => '+966500000001',
            'password' => bcrypt('password123'),
            'status' => 'active',
            'is_active' => true,
        ]);

        $user->assignRole($role);

        $this->adminUser = $user;
        return $user;
    }

    /**
     * Login as admin and get authentication token
     */
    protected function loginAsAdmin($role = 'super_admin')
    {
        $user = $this->createAdminUser($role);
        $token = $user->createToken('test-admin-token')->plainTextToken;
        $this->adminToken = $token;

        return $token;
    }

    /**
     * Get authentication headers
     */
    protected function getAuthHeaders($token = null)
    {
        $token = $token ?? $this->adminToken;

        return [
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ];
    }

    /**
     * Create a regular client user
     */
    protected function createClientUser($overrides = [])
    {
        // Ensure client role exists
        if (!Role::where('name', 'client')->exists()) {
            Role::create(['name' => 'client', 'guard_name' => 'web']);
        }

        $user = User::factory()->create(array_merge([
            'name' => 'Client User',
            'email' => 'client@test.com',
            'phone' => '+966500000002',
            'status' => 'active',
            'is_active' => true,
        ], $overrides));

        $user->assignRole('client');

        return $user;
    }

    /**
     * Create a provider user
     */
    protected function createProviderUser($overrides = [])
    {
        // Ensure provider role exists
        if (!Role::where('name', 'provider')->exists()) {
            Role::create(['name' => 'provider', 'guard_name' => 'web']);
        }

        $user = User::factory()->create(array_merge([
            'name' => 'Provider User',
            'email' => 'provider@test.com',
            'phone' => '+966500000003',
            'status' => 'active',
            'is_active' => true,
        ], $overrides));

        $user->assignRole('provider');

        return $user;
    }

    /**
     * Assert JSON response structure
     */
    protected function assertSuccessResponse($response, $statusCode = 200)
    {
        $response->assertStatus($statusCode);
        $response->assertJson([
            'success' => true,
        ]);
    }

    /**
     * Assert error response
     */
    protected function assertErrorResponse($response, $statusCode = 422, $message = null)
    {
        $response->assertStatus($statusCode);
        $response->assertJson([
            'success' => false,
        ]);

        if ($message) {
            $response->assertJson([
                'message' => $message,
            ]);
        }
    }
}
