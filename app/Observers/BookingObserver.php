<?php

namespace App\Observers;

use App\Models\Booking;
use App\Services\NotificationService;

class BookingObserver
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the Booking "created" event.
     */
    public function created(Booking $booking): void
    {
        // Notify admins about new booking
        $this->notificationService->sendToAdmins(
            'booking_request',
            'New Booking Request',
            "New booking #{$booking->booking_number} from {$booking->client->name} for {$booking->provider->business_name}",
            [
                'booking_id' => $booking->id,
                'booking_number' => $booking->booking_number,
                'client_name' => $booking->client->name,
                'provider_name' => $booking->provider->business_name,
                'total_amount' => $booking->total_amount,
            ]
        );
    }

    /**
     * Handle the Booking "updated" event.
     */
    public function updated(Booking $booking): void
    {
        // Check if status changed to cancelled
        if ($booking->isDirty('status') && $booking->status === 'cancelled') {
            $this->notificationService->sendToAdmins(
                'booking_cancelled',
                'Booking Cancelled',
                "Booking #{$booking->booking_number} has been cancelled",
                [
                    'booking_id' => $booking->id,
                    'booking_number' => $booking->booking_number,
                    'reason' => $booking->cancellation_reason,
                ]
            );
        }
    }
}
