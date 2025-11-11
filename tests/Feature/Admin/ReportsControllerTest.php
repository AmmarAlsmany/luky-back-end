<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;

class ReportsControllerTest extends TestCase
{
    use AdminTestTrait;

    /** @test */
    public function admin_can_get_revenue_overview()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/reports/revenue/overview', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_get_revenue_by_period()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/reports/revenue/by-period?period=month', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_get_booking_statistics()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/reports/bookings/statistics', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_get_provider_revenue_report()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/reports/providers/revenue', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_get_client_spending_report()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/reports/clients/spending', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_get_commission_report()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/reports/commission', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_get_payment_methods_stats()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/reports/payment-methods', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_export_revenue_report()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/reports/revenue/export', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_export_bookings_report()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/reports/bookings/export', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_reports()
    {
        $response = $this->getJson('/admin/reports/revenue/overview');

        $response->assertStatus(401);
    }
}
