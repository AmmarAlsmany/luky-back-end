<?php

namespace App\Observers;

use App\Models\Payment;
use App\Services\NotificationService;

class PaymentObserver
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the Payment "created" event.
     */
    public function created(Payment $payment): void
    {
        // Notify admins about new payment
        $booking = $payment->booking;
        
        $this->notificationService->sendToAdmins(
            'new_payment',
            'New Payment Received',
            "Payment of {$payment->amount} {$payment->currency} received for booking #{$booking->booking_number}",
            [
                'payment_id' => $payment->id,
                'booking_id' => $booking->id,
                'booking_number' => $booking->booking_number,
                'amount' => $payment->amount,
                'currency' => $payment->currency,
                'payment_gateway' => $payment->payment_gateway,
                'status' => $payment->status,
            ]
        );
    }

    /**
     * Handle the Payment "updated" event.
     */
    public function updated(Payment $payment): void
    {
        $booking = $payment->booking;
        
        // Check if status changed to completed
        if ($payment->isDirty('status') && $payment->status === 'completed') {
            $this->notificationService->sendToAdmins(
                'payment_completed',
                'Payment Completed',
                "Payment of {$payment->amount} {$payment->currency} completed for booking #{$booking->booking_number}",
                [
                    'payment_id' => $payment->id,
                    'booking_id' => $booking->id,
                    'booking_number' => $booking->booking_number,
                    'amount' => $payment->amount,
                    'currency' => $payment->currency,
                    'paid_at' => $payment->paid_at?->toIso8601String(),
                ]
            );
        }
        
        // Check if status changed to failed
        if ($payment->isDirty('status') && $payment->status === 'failed') {
            $this->notificationService->sendToAdmins(
                'payment_failed',
                'Payment Failed',
                "Payment of {$payment->amount} {$payment->currency} failed for booking #{$booking->booking_number}" .
                ($payment->failure_reason ? " - Reason: {$payment->failure_reason}" : ''),
                [
                    'payment_id' => $payment->id,
                    'booking_id' => $booking->id,
                    'booking_number' => $booking->booking_number,
                    'amount' => $payment->amount,
                    'currency' => $payment->currency,
                    'failure_reason' => $payment->failure_reason,
                ]
            );
        }
        
        // Check if status changed to refunded
        if ($payment->isDirty('status') && $payment->status === 'refunded') {
            $this->notificationService->sendToAdmins(
                'payment_refunded',
                'Payment Refunded',
                "Payment of {$payment->amount} {$payment->currency} refunded for booking #{$booking->booking_number}",
                [
                    'payment_id' => $payment->id,
                    'booking_id' => $booking->id,
                    'booking_number' => $booking->booking_number,
                    'amount' => $payment->amount,
                    'currency' => $payment->currency,
                ]
            );
        }
    }
}
