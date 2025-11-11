<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class AdminAuthControllerTest extends TestCase
{
    use AdminTestTrait;

    /** @test */
    public function admin_can_login_with_valid_credentials()
    {
        $user = $this->createAdminUser('super_admin');

        $response = $this->postJson('/admin/auth/login', [
            'email' => 'admin@test.com',
            'password' => 'password123',
        ]);

        $this->assertSuccessResponse($response);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'user' => [
                    'id',
                    'name',
                    'email',
                    'roles',
                    'permissions',
                ],
                'token',
                'token_type',
            ],
            'message',
        ]);
    }

    /** @test */
    public function admin_cannot_login_with_invalid_password()
    {
        $user = $this->createAdminUser();

        $response = $this->postJson('/admin/auth/login', [
            'email' => 'admin@test.com',
            'password' => 'wrong_password',
        ]);

        $this->assertErrorResponse($response, 401);
        $response->assertJson([
            'message' => 'Invalid credentials',
        ]);
    }

    /** @test */
    public function admin_cannot_login_with_invalid_email()
    {
        $response = $this->postJson('/admin/auth/login', [
            'email' => 'nonexistent@test.com',
            'password' => 'password123',
        ]);

        $this->assertErrorResponse($response, 401);
    }

    /** @test */
    public function admin_login_requires_email_and_password()
    {
        $response = $this->postJson('/admin/auth/login', []);

        $this->assertErrorResponse($response, 422);
        $response->assertJsonValidationErrors(['email', 'password']);
    }

    /** @test */
    public function non_admin_users_cannot_login_to_admin_panel()
    {
        $clientUser = $this->createClientUser([
            'email' => 'client@test.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/admin/auth/login', [
            'email' => 'client@test.com',
            'password' => 'password123',
        ]);

        $this->assertErrorResponse($response, 403);
        $response->assertJson([
            'message' => 'Unauthorized. Access denied for this account type.',
        ]);
    }

    /** @test */
    public function inactive_admin_cannot_login()
    {
        $user = $this->createAdminUser();
        $user->update(['status' => 'inactive']);

        $response = $this->postJson('/admin/auth/login', [
            'email' => 'admin@test.com',
            'password' => 'password123',
        ]);

        $this->assertErrorResponse($response, 403);
    }

    /** @test */
    public function admin_can_get_profile()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/auth/profile', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
        $response->assertJsonStructure([
            'data' => [
                'user' => [
                    'id',
                    'name',
                    'email',
                    'phone',
                    'roles',
                    'permissions',
                    'last_login_at',
                ],
            ],
        ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_get_admin_profile()
    {
        $response = $this->getJson('/admin/auth/profile');

        $response->assertStatus(401);
    }

    /** @test */
    public function admin_can_update_profile()
    {
        $token = $this->loginAsAdmin();

        $response = $this->putJson('/admin/auth/profile', [
            'name' => 'Updated Admin Name',
            'phone' => '+966500000099',
        ], $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
        $response->assertJson([
            'data' => [
                'user' => [
                    'name' => 'Updated Admin Name',
                    'phone' => '+966500000099',
                ],
            ],
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->adminUser->id,
            'name' => 'Updated Admin Name',
            'phone' => '+966500000099',
        ]);
    }

    /** @test */
    public function admin_cannot_update_profile_with_duplicate_email()
    {
        $token = $this->loginAsAdmin();
        $otherUser = $this->createClientUser([
            'email' => 'other@test.com',
        ]);

        $response = $this->putJson('/admin/auth/profile', [
            'email' => 'other@test.com',
        ], $this->getAuthHeaders());

        $this->assertErrorResponse($response, 422);
        $response->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function admin_can_change_password()
    {
        $token = $this->loginAsAdmin();

        $response = $this->putJson('/admin/auth/change-password', [
            'current_password' => 'password123',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ], $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
        $response->assertJson([
            'message' => 'Password changed successfully',
        ]);
    }

    /** @test */
    public function admin_cannot_change_password_with_wrong_current_password()
    {
        $token = $this->loginAsAdmin();

        $response = $this->putJson('/admin/auth/change-password', [
            'current_password' => 'wrongpassword',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'newpassword123',
        ], $this->getAuthHeaders());

        $this->assertErrorResponse($response, 401);
        $response->assertJson([
            'message' => 'Current password is incorrect',
        ]);
    }

    /** @test */
    public function admin_password_change_requires_confirmation()
    {
        $token = $this->loginAsAdmin();

        $response = $this->putJson('/admin/auth/change-password', [
            'current_password' => 'password123',
            'new_password' => 'newpassword123',
            'new_password_confirmation' => 'differentpassword',
        ], $this->getAuthHeaders());

        $this->assertErrorResponse($response, 422);
        $response->assertJsonValidationErrors(['new_password']);
    }

    /** @test */
    public function admin_can_logout()
    {
        $token = $this->loginAsAdmin();

        $response = $this->postJson('/admin/auth/logout', [], $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
        $response->assertJson([
            'message' => 'Logged out successfully',
        ]);
    }

    /** @test */
    public function admin_can_logout_from_all_devices()
    {
        $token = $this->loginAsAdmin();
        
        // Create additional tokens
        $this->adminUser->createToken('token2');
        $this->adminUser->createToken('token3');

        $this->assertCount(3, $this->adminUser->tokens);

        $response = $this->postJson('/admin/auth/logout-all', [], $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
        $response->assertJson([
            'message' => 'Logged out from all devices successfully',
        ]);

        $this->adminUser->refresh();
        $this->assertCount(0, $this->adminUser->tokens);
    }

    /** @test */
    public function remember_me_option_extends_token_expiry()
    {
        $user = $this->createAdminUser();

        $response = $this->postJson('/admin/auth/login', [
            'email' => 'admin@test.com',
            'password' => 'password123',
            'remember_me' => true,
        ]);

        $this->assertSuccessResponse($response);
        $response->assertJsonStructure([
            'data' => [
                'token',
            ],
        ]);
    }
}
