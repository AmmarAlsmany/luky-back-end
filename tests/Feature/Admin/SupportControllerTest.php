<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;

class SupportControllerTest extends TestCase
{
    use AdminTestTrait;

    /** @test */
    public function admin_can_list_support_tickets()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/support/tickets', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_get_ticket_stats()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/support/tickets/stats', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_filter_tickets_by_status()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/support/tickets?status=open', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_get_agents()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/support/agents', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_get_canned_responses()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/support/canned-responses', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_support()
    {
        $response = $this->getJson('/admin/support/tickets');

        $response->assertStatus(401);
    }
}
