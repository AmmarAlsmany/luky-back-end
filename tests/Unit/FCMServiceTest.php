<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\FCMService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Mockery;

class FCMServiceTest extends TestCase
{
    /**
     * Test that FCM service can be instantiated
     *
     * @return void
     */
    public function test_fcm_service_can_be_instantiated()
    {
        $fcmService = new FCMService();
        $this->assertInstanceOf(FCMService::class, $fcmService);
    }

    /**
     * Test that sendToUser returns false when no tokens are found
     *
     * @return void
     */
    public function test_send_to_user_returns_false_when_no_tokens()
    {
        $fcmService = new FCMService();
        $result = $fcmService->sendToUser(999, 'Test Title', 'Test Body');
        $this->assertFalse($result);
    }

    /**
     * Test that sendToTopic returns false when not configured
     *
     * @return void
     */
    public function test_send_to_topic_returns_false_when_not_configured()
    {
        $fcmService = new FCMService();
        $result = $fcmService->sendToTopic('test-topic', 'Test Title', 'Test Body');
        $this->assertFalse($result);
    }
}