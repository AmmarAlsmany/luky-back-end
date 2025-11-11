<?php

namespace Tests\Feature\Admin;

use Tests\TestCase;

class NotificationsControllerTest extends TestCase
{
    use AdminTestTrait;

    /** @test */
    public function admin_can_list_notifications()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/notifications', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_send_notification()
    {
        $token = $this->loginAsAdmin();
        $client = $this->createClientUser();

        $response = $this->postJson('/admin/notifications/send', [
            'title' => 'Test Notification',
            'message' => 'This is a test notification',
            'target_type' => 'all',
            'notification_type' => 'general',
        ], $this->getAuthHeaders());

        $this->assertSuccessResponse($response, 201);
    }

    /** @test */
    public function admin_can_send_targeted_notification_to_clients()
    {
        $token = $this->loginAsAdmin();
        $client = $this->createClientUser();

        $response = $this->postJson('/admin/notifications/send', [
            'title' => 'Client Notification',
            'message' => 'This is for clients only',
            'target_type' => 'clients',
            'notification_type' => 'promotional',
        ], $this->getAuthHeaders());

        $this->assertSuccessResponse($response, 201);
    }

    /** @test */
    public function admin_can_send_targeted_notification_to_providers()
    {
        $token = $this->loginAsAdmin();

        $response = $this->postJson('/admin/notifications/send', [
            'title' => 'Provider Notification',
            'message' => 'This is for providers only',
            'target_type' => 'providers',
            'notification_type' => 'announcement',
        ], $this->getAuthHeaders());

        $this->assertSuccessResponse($response, 201);
    }

    /** @test */
    public function admin_can_send_test_notification()
    {
        $token = $this->loginAsAdmin();
        $client = $this->createClientUser();

        $response = $this->postJson('/admin/notifications/send-test', [
            'user_id' => $client->id,
            'title' => 'Test',
            'message' => 'Test message',
        ], $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_get_notification_stats()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/notifications/stats', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
        $response->assertJsonStructure([
            'data' => [
                'total_sent',
                'sent_today',
                'sent_this_week',
            ],
        ]);
    }

    /** @test */
    public function admin_can_get_scheduled_notifications()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/notifications/scheduled', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_get_notification_templates()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/notifications/templates', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
    }

    /** @test */
    public function admin_can_get_user_counts()
    {
        $token = $this->loginAsAdmin();

        $response = $this->getJson('/admin/notifications/user-counts', $this->getAuthHeaders());

        $this->assertSuccessResponse($response);
        $response->assertJsonStructure([
            'data' => [
                'all_users',
                'clients',
                'providers',
            ],
        ]);
    }

    /** @test */
    public function admin_can_delete_notification()
    {
        $token = $this->loginAsAdmin();
        
        // Create notification first
        $createResponse = $this->postJson('/admin/notifications/send', [
            'title' => 'Delete Test',
            'message' => 'Will be deleted',
            'target_type' => 'all',
            'notification_type' => 'general',
        ], $this->getAuthHeaders());

        if ($createResponse->status() === 201) {
            $notificationId = $createResponse->json('data.notification.id');
            
            $response = $this->deleteJson("/admin/notifications/{$notificationId}", [], $this->getAuthHeaders());

            $this->assertSuccessResponse($response);
        }
    }
}
