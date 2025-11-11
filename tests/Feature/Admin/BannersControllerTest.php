<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;

class BannersControllerTest extends TestCase
{
    use AdminTestTrait;

    /** @test */
    public function admin_can_list_banners()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/banners', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_get_banner_stats()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/banners/stats', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_filter_banners_by_status()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/banners?status=active', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_banners()
    {
        $response = $this->getJson('/admin/banners');

        $response->assertStatus(401);
    }
}
