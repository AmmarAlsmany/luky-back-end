<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;

class StaticPagesControllerTest extends TestCase
{
    use AdminTestTrait;

    /** @test */
    public function admin_can_list_static_pages()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/pages', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_get_page_stats()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/pages/stats', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_pages()
    {
        $response = $this->getJson('/admin/pages');

        $response->assertStatus(401);
    }
}
