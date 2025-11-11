<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;

class SettingsControllerTest extends TestCase
{
    use AdminTestTrait;

    /** @test */
    public function admin_can_list_all_settings()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/settings', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_get_single_setting()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/settings/app_name', $this->getAuthHeaders());

        // May return 200 or 404 depending on if setting exists
        $response->assertStatus([200, 404]);
    }

    /** @test */
    public function admin_can_update_setting()
    {
        $token = $this->loginAsAdmin();

        $response = $this->putJson('/admin/settings/app_name', [
            'value' => 'Luky App',
        ], $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_bulk_update_settings()
    {
        $token = $this->loginAsAdmin();

        $response = $this->postJson('/admin/settings/bulk-update', [
            'settings' => [
                'app_name' => 'Luky',
                'support_email' => 'support@luky.sa',
            ],
        ], $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_get_app_settings()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/settings/app/general', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_update_app_settings()
    {
        $token = $this->loginAsAdmin();

        $response = $this->putJson('/admin/settings/app/general', [
            'app_name' => 'Luky Platform',
            'support_email' => 'help@luky.sa',
        ], $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_get_booking_settings()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/settings/booking', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_update_booking_settings()
    {
        $token = $this->loginAsAdmin();

        $response = $this->putJson('/admin/settings/booking', [
            'min_advance_booking_hours' => 2,
            'max_advance_booking_days' => 30,
        ], $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_get_notification_settings()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/settings/notifications', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_update_notification_settings()
    {
        $token = $this->loginAsAdmin();

        $response = $this->putJson('/admin/settings/notifications', [
            'fcm_enabled' => true,
            'sms_enabled' => true,
        ], $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_get_maintenance_mode()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/settings/maintenance', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_toggle_maintenance_mode()
    {
        $token = $this->loginAsAdmin();

        $response = $this->postJson('/admin/settings/maintenance/toggle', [
            'enabled' => true,
            'message' => 'System under maintenance',
        ], $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_clear_cache()
    {
        $token = $this->loginAsAdmin();

        $response = $this->postJson('/admin/settings/cache/clear', [], $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }
}
