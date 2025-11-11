<?php

namespace App\Observers;

use App\Models\Review;
use App\Services\NotificationService;

class ReviewObserver
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the Review "created" event.
     */
    public function created(Review $review): void
    {
        // Notify admins about new review
        $provider = $review->provider;
        $client = $review->client;
        
        $stars = str_repeat('⭐', $review->rating);
        
        $this->notificationService->sendToAdmins(
            'new_review',
            'New Review Received',
            "{$client->name} rated {$provider->business_name} {$stars} ({$review->rating}/5)",
            [
                'review_id' => $review->id,
                'provider_id' => $provider->id,
                'client_id' => $client->id,
                'rating' => $review->rating,
            ]
        );
    }
}
