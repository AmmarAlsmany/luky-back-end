<?php

namespace Tests\Feature\Admin;

use App\Models\Booking;
use App\Models\Payment;
use Tests\TestCase;

class ClientControllerTest extends TestCase
{
    use AdminTestTrait;

    /** @test */
    public function admin_can_list_clients()
    {
        $token = $this->loginAsAdmin();
        $client = $this->createClientUser();

        $response = $this->getJson('/api/admin/clients', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
        $response->assertJsonStructure([
            'data' => [
                'clients' => [
                    '*' => ['id', 'name', 'email', 'phone', 'status'],
                ],
            ],
        ]);
    }

    /** @test */
    public function admin_can_search_clients()
    {
        $token = $this->loginAsAdmin();
        $client = $this->createClientUser(['name' => 'John Doe']);

        $response = $this->getJson('/api/admin/clients?search=John', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_filter_clients_by_status()
    {
        $token = $this->loginAsAdmin();
        $activeClient = $this->createClientUser(['status' => 'active']);
        $inactiveClient = $this->createClientUser([
            'email' => 'inactive@test.com',
            'phone' => '+966500000010',
            'status' => 'inactive',
        ]);

        $response = $this->getJson('/api/admin/clients?status=active', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_get_client_stats()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/api/admin/clients/stats', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
        $response->assertJsonStructure([
            'data' => [
                'total_clients',
                'active_clients',
                'inactive_clients',
            ],
        ]);
    }

    /** @test */
    public function admin_can_get_single_client()
    {
        $token = $this->loginAsAdmin();
        $client = $this->createClientUser();

        $response = $this->getJson("/api/admin/clients/{$client->id}", $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
        $response->assertJsonStructure([
            'data' => [
                'client' => ['id', 'name', 'email', 'phone', 'status'],
            ],
        ]);
    }

    /** @test */
    public function admin_can_update_client()
    {
        $token = $this->loginAsAdmin();
        $client = $this->createClientUser();

        $response = $this->putJson("/api/admin/clients/{$client->id}", [
            'name' => 'Updated Name',
            'status' => 'active',
        ], $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
        $this->assertDatabaseHas('users', [
            'id' => $client->id,
            'name' => 'Updated Name',
        ]);
    }

    /** @test */
    public function admin_can_update_client_status()
    {
        $token = $this->loginAsAdmin();
        $client = $this->createClientUser();

        $response = $this->putJson("/api/admin/clients/{$client->id}/status", [
            'status' => 'inactive',
        ], $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
        $this->assertDatabaseHas('users', [
            'id' => $client->id,
            'status' => 'inactive',
        ]);
    }

    /** @test */
    public function admin_can_delete_client()
    {
        $token = $this->loginAsAdmin();
        $client = $this->createClientUser();

        $response = $this->deleteJson("/api/admin/clients/{$client->id}", [], $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_get_client_bookings()
    {
        $token = $this->loginAsAdmin();
        $client = $this->createClientUser();

        $response = $this->getJson("/api/admin/clients/{$client->id}/bookings", $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
        $response->assertJsonStructure([
            'data' => [
                'bookings',
            ],
        ]);
    }

    /** @test */
    public function admin_can_get_client_transactions()
    {
        $token = $this->loginAsAdmin();
        $client = $this->createClientUser();

        $response = $this->getJson("/api/admin/clients/{$client->id}/transactions", $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
        $response->assertJsonStructure([
            'data' => [
                'transactions',
            ],
        ]);
    }

    /** @test */
    public function admin_can_export_clients()
    {
        $token = $this->loginAsAdmin();
        $client = $this->createClientUser();

        $response = $this->getJson('/api/admin/clients/export', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_clients()
    {
        $response = $this->getJson('/api/admin/clients');

        $response->assertStatus(401);
    }
}
