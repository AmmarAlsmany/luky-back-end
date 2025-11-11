<?php

namespace Tests\Feature\Admin;

use App\Models\ServiceProvider;
use App\Models\City;
use Tests\TestCase;

class ProviderManagementControllerTest extends TestCase
{
    use AdminTestTrait;

    /** @test */
    public function admin_can_list_providers()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/providers', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
        $response->assertJsonStructure([
            'data' => [
                'providers',
            ],
        ]);
    }

    /** @test */
    public function admin_can_get_provider_stats()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/providers/stats', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
        $response->assertJsonStructure([
            'data' => [
                'total_providers',
                'verified_providers',
                'pending_providers',
            ],
        ]);
    }

    /** @test */
    public function admin_can_get_pending_approval_providers()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/providers/pending-approval', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
        $response->assertJsonStructure([
            'data' => [
                'providers',
            ],
        ]);
    }

    /** @test */
    public function admin_can_get_single_provider()
    {
        $token = $this->loginAsAdmin();
        $provider = $this->createProviderUser();

        $response = $this->getJson("/admin/providers/{$provider->id}", $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_verify_provider()
    {
        $token = $this->loginAsAdmin();
        $provider = $this->createProviderUser();

        $response = $this->putJson("/admin/providers/{$provider->id}/verify", [
            'verification_status' => 'approved',
            'notes' => 'All documents verified',
        ], $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_update_provider_status()
    {
        $token = $this->loginAsAdmin();
        $provider = $this->createProviderUser();

        $response = $this->putJson("/admin/providers/{$provider->id}/status", [
            'status' => 'active',
        ], $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_get_provider_services()
    {
        $token = $this->loginAsAdmin();
        $provider = $this->createProviderUser();

        $response = $this->getJson("/admin/providers/{$provider->id}/services", $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_get_provider_bookings()
    {
        $token = $this->loginAsAdmin();
        $provider = $this->createProviderUser();

        $response = $this->getJson("/admin/providers/{$provider->id}/bookings", $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_get_provider_revenue()
    {
        $token = $this->loginAsAdmin();
        $provider = $this->createProviderUser();

        $response = $this->getJson("/admin/providers/{$provider->id}/revenue", $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_get_provider_reviews()
    {
        $token = $this->loginAsAdmin();
        $provider = $this->createProviderUser();

        $response = $this->getJson("/admin/providers/{$provider->id}/reviews", $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_export_providers()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/providers/export', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_delete_provider()
    {
        $token = $this->loginAsAdmin();
        $provider = $this->createProviderUser();

        $response = $this->deleteJson("/admin/providers/{$provider->id}", [], $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }
}
