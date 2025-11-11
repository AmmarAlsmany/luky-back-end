<?php

namespace App\View\Composers;

use App\Models\Notification;
use Illuminate\View\View;

class AdminNotificationComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        // Get admin user (you can adjust this based on your auth setup)
        $adminUser = auth()->user();
        
        if (!$adminUser) {
            $view->with([
                'adminNotifications' => collect([]),
                'unreadNotificationCount' => 0,
            ]);
            return;
        }

        // Get latest 10 unread notifications for admin
        $notifications = Notification::where('user_id', $adminUser->id)
            ->where('is_read', false)
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        $unreadCount = Notification::where('user_id', $adminUser->id)
            ->where('is_read', false)
            ->count();

        $view->with([
            'adminNotifications' => $notifications,
            'unreadNotificationCount' => $unreadCount,
        ]);
    }
}
