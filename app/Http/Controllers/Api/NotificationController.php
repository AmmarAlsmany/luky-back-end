<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\DeviceToken;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Get user notifications (paginated)
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Notification::where('user_id', $user->id);

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Filter by read status
        if ($request->has('is_read')) {
            $query->where('is_read', $request->boolean('is_read'));
        }

        $notifications = $query->orderByDesc('created_at')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => [
                'notifications' => $notifications->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'type' => $notification->type,
                        'title' => $notification->title,
                        'body' => $notification->body,
                        'data' => $notification->data,
                        'is_read' => $notification->is_read,
                        'read_at' => $notification->read_at?->format('Y-m-d H:i:s'),
                        'created_at' => $notification->created_at->format('Y-m-d H:i:s'),
                    ];
                }),
                'pagination' => [
                    'current_page' => $notifications->currentPage(),
                    'last_page' => $notifications->lastPage(),
                    'total' => $notifications->total(),
                    'per_page' => $notifications->perPage(),
                ]
            ]
        ]);
    }

    /**
     * Get unread notifications count
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $user = $request->user();

        $count = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'unread_count' => $count
            ]
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $notification = Notification::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $user = $request->user();

        $updated = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
            'data' => [
                'updated_count' => $updated
            ]
        ]);
    }

    /**
     * Delete a notification
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $notification = Notification::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $notification->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted'
        ]);
    }

    /**
     * Delete all read notifications
     */
    public function deleteAllRead(Request $request): JsonResponse
    {
        $user = $request->user();

        $deleted = Notification::where('user_id', $user->id)
            ->where('is_read', true)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'All read notifications deleted',
            'data' => [
                'deleted_count' => $deleted
            ]
        ]);
    }

    /**
     * Register/Update device FCM token
     */
    public function registerDeviceToken(Request $request): JsonResponse
    {
        $user = $request->user();

        Log::info('=== DEVICE TOKEN REGISTRATION START ===');
        Log::info('User ID: ' . $user->id);
        Log::info('Request data: ' . json_encode($request->all()));

        $validated = $request->validate([
            'token' => 'required|string',
            'device_type' => 'nullable|string|in:ios,android',
            'device_name' => 'nullable|string|max:255',
        ]);

        Log::info('Validated token: ' . substr($validated['token'], 0, 50) . '...');
        Log::info('Device type: ' . ($validated['device_type'] ?? 'null'));

        // Check if token already exists
        $deviceToken = DeviceToken::where('token', $validated['token'])->first();

        if ($deviceToken) {
            Log::info('Token already exists, updating. ID: ' . $deviceToken->id);
            // Update existing token
            $deviceToken->update([
                'user_id' => $user->id,
                'device_type' => $validated['device_type'] ?? $deviceToken->device_type,
                'device_name' => $validated['device_name'] ?? $deviceToken->device_name,
                'is_active' => true,
                'last_used_at' => now(),
            ]);
            Log::info('Token updated successfully');
        } else {
            Log::info('Creating new token');
            // Create new token
            $deviceToken = DeviceToken::create([
                'user_id' => $user->id,
                'token' => $validated['token'],
                'device_type' => $validated['device_type'] ?? null,
                'device_name' => $validated['device_name'] ?? null,
                'is_active' => true,
                'last_used_at' => now(),
            ]);
            Log::info('Token created successfully. ID: ' . $deviceToken->id);
        }

        Log::info('Final token state - ID: ' . $deviceToken->id . ', User: ' . $deviceToken->user_id . ', Active: ' . ($deviceToken->is_active ? 'true' : 'false'));
        Log::info('=== DEVICE TOKEN REGISTRATION END ===');

        return response()->json([
            'success' => true,
            'message' => 'Device token registered successfully',
            'data' => [
                'id' => $deviceToken->id,
                'device_type' => $deviceToken->device_type,
                'device_name' => $deviceToken->device_name,
            ]
        ]);
    }

    /**
     * Remove device token (logout)
     */
    public function removeDeviceToken(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'token' => 'required|string',
        ]);

        $deleted = DeviceToken::where('user_id', $user->id)
            ->where('token', $validated['token'])
            ->delete();

        return response()->json([
            'success' => true,
            'message' => $deleted > 0 ? 'Device token removed successfully' : 'Device token not found'
        ]);
    }

    /**
     * TEST: Send test notification to current user
     */
    public function sendTestNotification(Request $request): JsonResponse
    {
        $user = $request->user();

        $notificationService = app(\App\Services\NotificationService::class);

        $notification = $notificationService->send(
            $user->id,
            'test',
            'Test Notification',
            'This is a test notification to verify FCM is working!',
            ['test' => true, 'timestamp' => now()->toDateTimeString()]
        );

        if ($notification) {
            return response()->json([
                'success' => true,
                'message' => 'Test notification sent successfully',
                'data' => [
                    'notification_id' => $notification->id,
                    'user_id' => $user->id,
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to send test notification'
        ], 500);
    }

    /**
     * Get user's registered devices
     */
    public function getDevices(Request $request): JsonResponse
    {
        $user = $request->user();

        $devices = DeviceToken::where('user_id', $user->id)
            ->where('is_active', true)
            ->orderByDesc('last_used_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $devices->map(function ($device) {
                return [
                    'id' => $device->id,
                    'device_type' => $device->device_type,
                    'device_name' => $device->device_name,
                    'last_used_at' => $device->last_used_at?->format('Y-m-d H:i:s'),
                    'registered_at' => $device->created_at->format('Y-m-d H:i:s'),
                ];
            })
        ]);
    }
}
