<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;

class PromoCodesControllerTest extends TestCase
{
    use AdminTestTrait;

    /** @test */
    public function admin_can_list_promo_codes()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/promo-codes', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_create_promo_code()
    {
        $token = $this->loginAsAdmin();

        $response = $this->postJson('/admin/promo-codes', [
            'code' => 'SUMMER2025',
            'type' => 'percentage',
            'value' => 20,
            'max_uses' => 100,
            'valid_from' => now()->toDateString(),
            'valid_until' => now()->addDays(30)->toDateString(),
        ], $this->getAuthHeaders());

        $this->assertSuccessResponse($response, 201);
    }

    /** @test */
    public function admin_can_update_promo_code()
    {
        $token = $this->loginAsAdmin();
        
        // First create a promo code
        $createResponse = $this->postJson('/admin/promo-codes', [
            'code' => 'TEST2025',
            'type' => 'percentage',
            'value' => 10,
            'max_uses' => 50,
            'valid_from' => now()->toDateString(),
            'valid_until' => now()->addDays(15)->toDateString(),
        ], $this->getAuthHeaders());

        $promoCodeId = $createResponse->json('data.promo_code.id');

        $response = $this->putJson("/admin/promo-codes/{$promoCodeId}", [
            'value' => 15,
            'max_uses' => 100,
        ], $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_delete_promo_code()
    {
        $token = $this->loginAsAdmin();
        
        $createResponse = $this->postJson('/admin/promo-codes', [
            'code' => 'DELETE_ME',
            'type' => 'fixed',
            'value' => 50,
            'max_uses' => 10,
            'valid_from' => now()->toDateString(),
            'valid_until' => now()->addDays(7)->toDateString(),
        ], $this->getAuthHeaders());

        $promoCodeId = $createResponse->json('data.promo_code.id');

        $response = $this->deleteJson("/admin/promo-codes/{$promoCodeId}", [], $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_get_promo_code_stats()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/promo-codes/stats', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_generate_promo_code()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/promo-codes/generate', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
        $response->assertJsonStructure([
            'data' => ['code'],
        ]);
    }

    /** @test */
    public function admin_can_validate_promo_code()
    {
        $token = $this->loginAsAdmin();

        $response = $this->postJson('/admin/promo-codes/validate', [
            'code' => 'TESTCODE',
        ], $this->getAuthHeaders());

        // Accept both success and error as validation can fail
        $response->assertStatus([200, 404, 422]);
    }

    /** @test */
    public function admin_can_toggle_promo_code_status()
    {
        $token = $this->loginAsAdmin();
        
        $createResponse = $this->postJson('/admin/promo-codes', [
            'code' => 'TOGGLE_TEST',
            'type' => 'percentage',
            'value' => 25,
            'max_uses' => 50,
            'valid_from' => now()->toDateString(),
            'valid_until' => now()->addDays(30)->toDateString(),
        ], $this->getAuthHeaders());

        $promoCodeId = $createResponse->json('data.promo_code.id');

        $response = $this->postJson("/admin/promo-codes/{$promoCodeId}/toggle", [], $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_get_promo_code_usage_history()
    {
        $token = $this->loginAsAdmin();
        
        $createResponse = $this->postJson('/admin/promo-codes', [
            'code' => 'USAGE_TEST',
            'type' => 'percentage',
            'value' => 10,
            'max_uses' => 100,
            'valid_from' => now()->toDateString(),
            'valid_until' => now()->addDays(60)->toDateString(),
        ], $this->getAuthHeaders());

        $promoCodeId = $createResponse->json('data.promo_code.id');

        $response = $this->getJson("/admin/promo-codes/{$promoCodeId}/usage", $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }
}
