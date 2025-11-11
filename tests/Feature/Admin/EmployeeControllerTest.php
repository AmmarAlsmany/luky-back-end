<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;

class EmployeeControllerTest extends TestCase
{
    use AdminTestTrait;

    /** @test */
    public function admin_can_list_employees()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/employees', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_get_employee_stats()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/employees/stats', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_create_employee()
    {
        $token = $this->loginAsAdmin();

        $response = $this->postJson('/admin/employees', [
            'name' => 'New Employee',
            'email' => 'employee@test.com',
            'phone' => '+966500000020',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'support_agent',
        ], $this->getAuthHeaders());

        $this->assertSuccessResponse($response, 201);
    }

    /** @test */
    public function admin_can_get_single_employee()
    {
        $token = $this->loginAsAdmin();
        $employee = $this->createAdminUser('support_agent');

        $response = $this->getJson("/admin/employees/{$employee->id}", $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_update_employee()
    {
        $token = $this->loginAsAdmin();
        $employee = $this->createAdminUser('support_agent');

        $response = $this->putJson("/admin/employees/{$employee->id}", [
            'name' => 'Updated Employee Name',
        ], $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_delete_employee()
    {
        $token = $this->loginAsAdmin();
        $employee = $this->createAdminUser('support_agent');

        $response = $this->deleteJson("/admin/employees/{$employee->id}", [], $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_update_employee_status()
    {
        $token = $this->loginAsAdmin();
        $employee = $this->createAdminUser('support_agent');

        $response = $this->putJson("/admin/employees/{$employee->id}/status", [
            'status' => 'inactive',
        ], $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_assign_role_to_employee()
    {
        $token = $this->loginAsAdmin();
        $employee = $this->createAdminUser('support_agent');

        $response = $this->putJson("/admin/employees/{$employee->id}/role", [
            'role' => 'manager',
        ], $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_get_roles()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/roles', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_get_permissions()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/permissions', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_export_employees()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/employees/export', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_employee_management()
    {
        $response = $this->getJson('/admin/employees');

        $response->assertStatus(401);
    }
}
