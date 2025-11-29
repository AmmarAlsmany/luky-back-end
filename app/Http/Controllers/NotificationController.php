<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Display notifications management page
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $type = $request->input('type');
        $status = $request->input('status');

        // Get sent notifications
        $query = Notification::with('user')->orderByDesc('created_at');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', "%{$search}%")
                  ->orWhere('body', 'ilike', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'ilike', "%{$search}%");
                  });
            });
        }

        if ($type) {
            $query->where('type', $type);
        }

        if ($status === 'sent') {
            $query->where('is_sent', true);
        } elseif ($status === 'failed') {
            $query->where('is_sent', false);
        }

        $notifications = $query->paginate(20);

        // Format notifications for display
        $notificationsData = $notifications->map(function ($notification) {
            return [
                'id' => 'NTF-' . str_pad($notification->id, 6, '0', STR_PAD_LEFT),
                'raw_id' => $notification->id,
                'message' => substr($notification->body, 0, 50) . (strlen($notification->body) > 50 ? '...' : ''),
                'full_message' => $notification->body,
                'title' => $notification->title,
                'datetime' => $notification->created_at->format('d M Y, h:i A'),
                'recipient_name' => $notification->user->name ?? 'N/A',
                'recipient_type' => $notification->user ? ucfirst($notification->user->user_type) : 'Unknown',
                'type' => $notification->type,
                'status' => $notification->is_sent ? 'Sent' : 'Failed',
                'is_sent' => $notification->is_sent,
            ];
        });

        // Get clients and providers for dropdown
        $clients = User::where('user_type', 'client')
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $providers = User::where('user_type', 'provider')
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        // Statistics
        $stats = [
            'total_sent' => Notification::where('is_sent', true)->count(),
            'total_failed' => Notification::where('is_sent', false)->count(),
            'today_sent' => Notification::where('is_sent', true)
                ->whereDate('created_at', today())
                ->count(),
        ];

        $filters = [
            'search' => $search,
            'type' => $type,
            'status' => $status,
        ];

        return view('notifications.list', compact(
            'notificationsData',
            'notifications',
            'clients',
            'providers',
            'stats',
            'filters'
        ));
    }

    /**
     * Send notification to user
     */
    public function send(Request $request)
    {
        try {
            $validated = $request->validate([
                'audience' => 'required|in:client,provider',
                'recipient_id' => 'required|exists:users,id',
                'message' => 'required|string|max:500',
                'title' => 'nullable|string|max:100',
            ]);

            $recipientId = $validated['recipient_id'];
            $user = User::findOrFail($recipientId);

            // Verify user type matches audience
            if ($user->user_type !== $validated['audience']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected user does not match the audience type',
                ], 422);
            }

            $title = $validated['title'] ?? 'Message from Luky Admin';
            $message = $validated['message'];

            // Send notification
            $notification = $this->notificationService->send(
                $recipientId,
                'admin_message',
                $title,
                $message,
                ['sent_by' => auth()->id(), 'sent_via' => 'dashboard']
            );

            if ($notification) {
                return response()->json([
                    'success' => true,
                    'message' => 'Notification sent successfully',
                    'data' => [
                        'notification_id' => $notification->id,
                        'formatted_id' => 'NTF-' . str_pad($notification->id, 6, '0', STR_PAD_LEFT),
                        'recipient_name' => $user->name,
                        'recipient_type' => ucfirst($user->user_type),
                        'datetime' => $notification->created_at->format('d M Y, h:i A'),
                        'status' => $notification->is_sent ? 'Sent' : 'Failed',
                        'message_preview' => substr($message, 0, 50) . (strlen($message) > 50 ? '...' : ''),
                        'full_message' => $message,
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to send notification',
            ], 500);

        } catch (\Exception $e) {
            Log::error('Error sending notification from dashboard', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'data' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while sending the notification: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get users by audience type (AJAX)
     */
    public function getUsersByAudience($audience)
    {
        if (!in_array($audience, ['client', 'provider'])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid audience type',
            ], 422);
        }

        $users = User::where('user_type', $audience)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'phone']);

        return response()->json([
            'success' => true,
            'data' => $users->map(function ($user) {
                return [
                    'value' => $user->id,
                    'label' => $user->name . ' (' . ($user->email ?? $user->phone) . ')',
                ];
            }),
        ]);
    }

    /**
     * Broadcast message to all users of a type
     */
    public function broadcast(Request $request)
    {
        try {
            $validated = $request->validate([
                'audience' => 'required|in:client,provider,all',
                'message' => 'required|string|max:500',
                'title' => 'required|string|max:100',
            ]);

            $query = User::where('is_active', true);

            if ($validated['audience'] !== 'all') {
                $query->where('user_type', $validated['audience']);
            }

            $users = $query->get();
            $sentCount = 0;
            $failedCount = 0;

            foreach ($users as $user) {
                $notification = $this->notificationService->send(
                    $user->id,
                    'broadcast',
                    $validated['title'],
                    $validated['message'],
                    ['sent_by' => auth()->id(), 'broadcast' => true]
                );

                if ($notification && $notification->is_sent) {
                    $sentCount++;
                } else {
                    $failedCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Broadcast completed. Sent: {$sentCount}, Failed: {$failedCount}",
                'data' => [
                    'sent' => $sentCount,
                    'failed' => $failedCount,
                    'total' => $users->count(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error broadcasting notification', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while broadcasting: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        
        // Verify the notification belongs to the current user
        if ($notification->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read',
        ]);
    }

    /**
     * Mark all notifications as read for current user
     */
    public function markAllAsRead()
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read',
        ]);
    }

    /**
     * Check for new notifications (for real-time polling)
     * Returns only notifications created after the provided timestamp
     */
    public function checkNew(Request $request)
    {
        $lastCheck = $request->input('last_check');
        $userId = auth()->id();

        // Get new notifications since last check
        $query = Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->orderByDesc('created_at');

        if ($lastCheck) {
            $query->where('created_at', '>', $lastCheck);
        }

        $newNotifications = $query->limit(5)->get();

        $unreadCount = Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'success' => true,
            'has_new' => $newNotifications->count() > 0,
            'count' => $newNotifications->count(),
            'total_unread' => $unreadCount,
            'notifications' => $newNotifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'body' => $notification->body,
                    'data' => $notification->data,
                    'created_at' => $notification->created_at->toIso8601String(),
                    'time_ago' => $notification->created_at->diffForHumans(),
                ];
            }),
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
