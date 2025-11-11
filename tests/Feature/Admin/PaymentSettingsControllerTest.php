<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;

class PaymentSettingsControllerTest extends TestCase
{
    use AdminTestTrait;

    /** @test */
    public function admin_can_list_payment_gateways()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/payment-gateways', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_get_payment_settings()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/payment-settings', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_update_payment_settings()
    {
        $token = $this->loginAsAdmin();

        $response = $this->putJson('/admin/payment-settings', [
            'default_gateway' => 'myfatoorah',
            'allow_cash_payment' => true,
        ], $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_get_tax_settings()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/payment-settings/tax', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_update_tax_settings()
    {
        $token = $this->loginAsAdmin();

        $response = $this->putJson('/admin/payment-settings/tax', [
            'tax_enabled' => true,
            'tax_rate' => 15,
        ], $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_get_commission_settings()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/payment-settings/commission', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_update_commission_settings()
    {
        $token = $this->loginAsAdmin();

        $response = $this->putJson('/admin/payment-settings/commission', [
            'commission_type' => 'percentage',
            'commission_value' => 10,
        ], $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_payment_settings()
    {
        $response = $this->getJson('/admin/payment-settings');

        $response->assertStatus(401);
    }
}
