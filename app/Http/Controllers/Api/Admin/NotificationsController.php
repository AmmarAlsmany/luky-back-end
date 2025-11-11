<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class NotificationsController extends Controller
{
    /**
     * Get list of sent notifications with pagination and filters
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 20);
        $search = $request->input('search');
        $targetType = $request->input('target_type');
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        $query = DB::table('admin_notifications');

        // Search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('message', 'LIKE', "%{$search}%");
            });
        }

        // Target type filter
        if ($targetType) {
            $query->where('recipient_type', $targetType);
        }

        // Sorting
        $query->orderBy($sortBy, $sortOrder);

        $notifications = $query->paginate($perPage);

        // Get stats
        $stats = [
            'total_notifications' => DB::table('admin_notifications')->count(),
            'sent_today' => DB::table('admin_notifications')
                ->whereDate('created_at', today())
                ->count(),
            'sent_this_week' => DB::table('admin_notifications')
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'notifications' => $notifications,
                'stats' => $stats,
            ],
        ]);
    }

    /**
     * Get single notification
     */
    public function show($id)
    {
        $notification = DB::table('admin_notifications')->find($id);

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => ['notification' => $notification],
        ]);
    }

    /**
     * Send notification to users
     */
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'target_type' => 'required|in:all,clients,providers,specific_users',
            'target_user_ids' => 'required_if:target_type,specific_users|array',
            'target_user_ids.*' => 'exists:users,id',
            'notification_type' => 'required|in:general,promotional,informational,alert',
            'action_type' => 'nullable|in:url,screen',
            'action_data' => 'nullable|json',
            'scheduled_at' => 'nullable|date|after:now',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Determine target users
        $targetUserIds = [];

        if ($request->target_type === 'all') {
            $targetUserIds = User::whereHas('roles', function ($q) {
                $q->whereIn('name', ['client', 'provider']);
            })->pluck('id')->toArray();
        } elseif ($request->target_type === 'clients') {
            $targetUserIds = User::whereHas('roles', function ($q) {
                $q->where('name', 'client');
            })->pluck('id')->toArray();
        } elseif ($request->target_type === 'providers') {
            $targetUserIds = User::whereHas('roles', function ($q) {
                $q->where('name', 'provider');
            })->pluck('id')->toArray();
        } else {
            $targetUserIds = $request->target_user_ids;
        }

        // Create notification record
        $notificationId = DB::table('admin_notifications')->insertGetId([
            'title' => $request->title,
            'message' => $request->message,
            'recipient_type' => $request->target_type,
            'recipient_ids' => json_encode($targetUserIds),
            'notification_type' => $request->notification_type,
            'scheduled_at' => $request->scheduled_at,
            'created_by' => auth()->id(),
            'status' => $request->scheduled_at ? 'scheduled' : 'sent',
            'sent_count' => !$request->scheduled_at || now()->gte($request->scheduled_at) ? count($targetUserIds) : 0,
            'sent_at' => !$request->scheduled_at || now()->gte($request->scheduled_at) ? now() : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // TODO: Integrate with Firebase Cloud Messaging or your notification service
        // For now, we'll just create in-app notifications
        if (!$request->scheduled_at || now()->gte($request->scheduled_at)) {
            foreach ($targetUserIds as $userId) {
                DB::table('notifications')->insert([
                    'user_id' => $userId,
                    'type' => $request->notification_type ?? 'general',
                    'title' => $request->title,
                    'body' => $request->message,
                    'data' => json_encode([
                        'action_type' => $request->action_type,
                        'action_data' => $request->action_data,
                    ]),
                    'is_read' => false,
                    'read_at' => null,
                    'is_sent' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $message = 'Notification sent successfully to ' . count($targetUserIds) . ' users';
        } else {
            $message = 'Notification scheduled successfully for ' . $request->scheduled_at;
        }

        $notification = DB::table('admin_notifications')->find($notificationId);

        return response()->json([
            'success' => true,
            'data' => ['notification' => $notification],
            'message' => $message,
        ], 201);
    }

    /**
     * Send test notification
     */
    public function sendTest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Send test notification
        DB::table('notifications')->insert([
            'user_id' => $request->user_id,
            'type' => 'general',
            'title' => $request->title,
            'body' => $request->message,
            'data' => json_encode([]),
            'is_read' => false,
            'read_at' => null,
            'is_sent' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Test notification sent successfully',
        ]);
    }

    /**
     * Delete notification
     */
    public function destroy($id)
    {
        $notification = DB::table('admin_notifications')->find($id);

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found',
            ], 404);
        }

        DB::table('admin_notifications')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notification deleted successfully',
        ]);
    }

    /**
     * Get notification statistics
     */
    public function stats()
    {
        $stats = [
            'total_sent' => DB::table('admin_notifications')->count(),
            'sent_today' => DB::table('admin_notifications')
                ->whereDate('created_at', today())
                ->count(),
            'sent_this_week' => DB::table('admin_notifications')
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
            'sent_this_month' => DB::table('admin_notifications')
                ->whereMonth('created_at', now()->month)
                ->count(),
            'total_recipients' => DB::table('admin_notifications')
                ->sum('sent_count'),
            'by_type' => DB::table('admin_notifications')
                ->select('recipient_type', DB::raw('COUNT(*) as count'))
                ->groupBy('recipient_type')
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }

    /**
     * Get scheduled notifications
     */
    public function scheduled()
    {
        $notifications = DB::table('admin_notifications')
            ->where('scheduled_at', '>', now())
            ->orderBy('scheduled_at', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => ['notifications' => $notifications],
        ]);
    }

    /**
     * Cancel scheduled notification
     */
    public function cancelScheduled($id)
    {
        $notification = DB::table('admin_notifications')
            ->where('id', $id)
            ->where('scheduled_at', '>', now())
            ->first();

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Scheduled notification not found',
            ], 404);
        }

        DB::table('admin_notifications')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Scheduled notification cancelled successfully',
        ]);
    }

    /**
     * Get notification templates
     */
    public function templates()
    {
        $templates = [
            [
                'id' => 'welcome',
                'title' => 'Welcome to Luky!',
                'message' => 'Thank you for joining Luky. Start booking services now!',
                'type' => 'general',
            ],
            [
                'id' => 'booking_reminder',
                'title' => 'Booking Reminder',
                'message' => 'You have an upcoming booking. Don\'t forget!',
                'type' => 'informational',
            ],
            [
                'id' => 'special_offer',
                'title' => 'Special Offer!',
                'message' => 'Get 20% off on your next booking. Use code SAVE20',
                'type' => 'promotional',
            ],
            [
                'id' => 'system_alert',
                'title' => 'System Maintenance',
                'message' => 'The app will be under maintenance from 2AM to 4AM',
                'type' => 'alert',
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => ['templates' => $templates],
        ]);
    }

    /**
     * Get user counts for targeting
     */
    public function userCounts()
    {
        $counts = [
            'all_users' => User::whereHas('roles', fn($q) => $q->whereIn('name', ['client', 'provider']))->count(),
            'clients' => User::whereHas('roles', fn($q) => $q->where('name', 'client'))->count(),
            'providers' => User::whereHas('roles', fn($q) => $q->where('name', 'provider'))->count(),
            'active_users' => User::whereHas('roles', fn($q) => $q->whereIn('name', ['client', 'provider']))
                ->where('status', 'active')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $counts,
        ]);
    }

    /**
     * Send message to user (admin to client/provider)
     */
    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Create notification as a message (in-app)
        DB::table('notifications')->insert([
            'user_id' => $request->user_id,
            'type' => 'admin_message',
            'title' => 'Message from Admin',
            'body' => $request->message,
            'data' => json_encode([
                'sender_id' => auth()->id(),
                'sender_name' => auth()->user()->name ?? 'Admin',
                'notification_type' => 'message',
            ]),
            'is_read' => false,
            'read_at' => null,
            'is_sent' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
        ]);
    }
}
