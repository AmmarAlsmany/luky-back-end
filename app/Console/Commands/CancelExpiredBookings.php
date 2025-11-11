<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Services\NotificationService;
use Carbon\Carbon;

class CancelExpiredBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:cancel-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel bookings where payment timeout has expired';

    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired bookings...');

        $timeoutMinutes = config('app.payment_timeout_minutes', 5);

        // Find bookings that are confirmed but payment is pending and timeout has expired
        $expiredBookings = Booking::where('status', 'confirmed')
            ->where('payment_status', 'pending')
            ->whereNotNull('confirmed_at')
            ->where('confirmed_at', '<=', Carbon::now()->subMinutes($timeoutMinutes))
            ->get();

        if ($expiredBookings->isEmpty()) {
            $this->info('No expired bookings found.');
            return 0;
        }

        $this->info("Found {$expiredBookings->count()} expired bookings.");

        $cancelledCount = 0;

        foreach ($expiredBookings as $booking) {
            try {
                // Cancel the booking
                $booking->update([
                    'status' => 'cancelled',
                    'cancelled_by' => 'system',
                    'cancellation_reason' => 'Payment timeout expired',
                    'cancelled_at' => now(),
                ]);

                $this->info("✓ Cancelled booking #{$booking->booking_number}");

                // Send notification to both client and provider
                $this->notificationService->sendBookingCancelled($booking, 'both');

                $cancelledCount++;
            } catch (\Exception $e) {
                $this->error("✗ Failed to cancel booking #{$booking->booking_number}: {$e->getMessage()}");
            }
        }

        $this->info("Successfully cancelled {$cancelledCount} expired bookings.");

        return 0;
    }
}
