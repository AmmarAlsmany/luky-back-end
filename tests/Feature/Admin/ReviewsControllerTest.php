<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;

class ReviewsControllerTest extends TestCase
{
    use AdminTestTrait;

    /** @test */
    public function admin_can_list_reviews()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/reviews', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_filter_reviews_by_rating()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/reviews?rating=5', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_filter_reviews_by_status()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/reviews?status=visible', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_get_review_stats()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/reviews/stats', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_get_flagged_reviews()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/reviews/flagged', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_get_reviews_by_provider()
    {
        $token = $this->loginAsAdmin();
        $provider = $this->createProviderUser();

        $response = $this->getJson("/admin/reviews/provider/{$provider->id}", $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_get_reviews_by_user()
    {
        $token = $this->loginAsAdmin();
        $client = $this->createClientUser();

        $response = $this->getJson("/admin/reviews/user/{$client->id}", $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_review_management()
    {
        $response = $this->getJson('/admin/reviews');

        $response->assertStatus(401);
    }
}
