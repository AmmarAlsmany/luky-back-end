<?php

namespace App\Observers;

use App\Models\User;
use App\Services\NotificationService;

class UserObserver
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Notify admins about new client registration
        if ($user->user_type === 'client') {
            $this->notificationService->sendToAdmins(
                'new_client',
                'New Client Registration',
                "New client '{$user->name}' has registered on the platform",
                [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'city_id' => $user->city_id,
                ]
            );
        }
        
        // Notify admins about new admin user creation
        if ($user->user_type === 'admin') {
            // Get the role name if available
            $roleName = $user->roles->first()?->name ?? 'admin';
            
            $this->notificationService->sendToAdmins(
                'new_admin_user',
                'New Admin User Created',
                "New admin user '{$user->name}' ({$roleName}) has been created",
                [
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $roleName,
                ]
            );
        }
    }
}
