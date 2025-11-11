<?php

namespace Tests\Feature\Admin;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Review;
use App\Models\ServiceProvider;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use AdminTestTrait;

    /** @test */
    public function admin_can_get_dashboard_overview()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/dashboard/overview', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
        $response->assertJsonStructure([
            'data' => [
                'users' => ['total_clients', 'active_clients', 'total_providers', 'verified_providers'],
                'bookings' => ['total', 'pending', 'confirmed', 'completed', 'cancelled'],
                'revenue' => ['total', 'this_month', 'today'],
                'reviews' => ['total', 'average_rating', 'flagged'],
            ],
        ]);
    }

    /** @test */
    public function admin_can_get_dashboard_kpis()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/dashboard?period=month', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
        $response->assertJsonStructure([
            'data' => [
                'kpis' => [
                    'total_revenue',
                    'total_bookings',
                    'total_clients',
                    'total_providers',
                    'revenue_growth',
                    'bookings_growth',
                ],
                'period',
            ],
        ]);
    }

    /** @test */
    public function admin_can_get_revenue_chart()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/dashboard/charts/revenue?days=30', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
        $response->assertJsonStructure([
            'data' => [
                'chart_data',
                'period',
                'days',
            ],
        ]);
    }

    /** @test */
    public function admin_can_get_bookings_chart()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/dashboard/charts/bookings?days=30', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
        $response->assertJsonStructure([
            'data' => [
                'chart_data',
                'days',
            ],
        ]);
    }

    /** @test */
    public function admin_can_get_users_growth_chart()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/dashboard/charts/users-growth?days=30', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
        $response->assertJsonStructure([
            'data' => [
                'chart_data',
                'days',
            ],
        ]);
    }

    /** @test */
    public function admin_can_get_top_providers()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/dashboard/top-providers?limit=10', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
        $response->assertJsonStructure([
            'data' => [
                'providers',
            ],
        ]);
    }

    /** @test */
    public function admin_can_get_recent_activities()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/dashboard/recent-activities?limit=20', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
        $response->assertJsonStructure([
            'data' => [
                'activities',
            ],
        ]);
    }

    /** @test */
    public function admin_can_get_booking_status_distribution()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/dashboard/booking-status-distribution', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
        $response->assertJsonStructure([
            'data' => [
                'distribution',
            ],
        ]);
    }

    /** @test */
    public function admin_can_get_rating_distribution()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/dashboard/rating-distribution', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
        $response->assertJsonStructure([
            'data' => [
                'distribution',
            ],
        ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_dashboard()
    {
        $response = $this->getJson('/admin/dashboard/overview');

        $response->assertStatus(401);
    }
}
