<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use App\Services\NotificationService;

class UpdateBookingStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'booking:update-status {booking_id?} {--from=pending : Current status to change from} {--to=confirmed : Status to change to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update booking status from one value to another';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $bookingId = $this->argument('booking_id');

        if (!$bookingId) {
            // Show all bookings if no ID provided
            $this->showAllBookings();
            return 0;
        }

        $fromStatus = $this->option('from');
        $toStatus = $this->option('to');

        $booking = Booking::find($bookingId);

        if (!$booking) {
            $this->error("Booking with ID {$bookingId} not found.");
            return 1;
        }

        if ($booking->status !== $fromStatus) {
            $this->error("Booking status is '{$booking->status}', not '{$fromStatus}'.");
            return 1;
        }

        $booking->status = $toStatus;

        if ($toStatus === 'confirmed') {
            $booking->confirmed_at = now();
        }

        if ($booking->save()) {
            $this->info("Booking #{$booking->booking_number} status updated from '{$fromStatus}' to '{$toStatus}' successfully.");

            // Send notification based on status change
            $notificationService = app(NotificationService::class);

            if ($toStatus === 'confirmed') {
                $this->info("Sending booking accepted notification to client...");
                $notificationService->sendBookingAccepted($booking);
                $this->info("✓ Notification sent!");
            } elseif ($toStatus === 'rejected') {
                $this->info("Sending booking rejected notification to client...");
                $notificationService->sendBookingRejected($booking);
                $this->info("✓ Notification sent!");
            } elseif ($toStatus === 'completed') {
                $this->info("Sending booking completed notification...");
                $notificationService->sendBookingCompleted($booking);
                $this->info("✓ Notification sent!");
            }

            return 0;
        } else {
            $this->error("Failed to update booking status.");
            return 1;
        }
    }
    
    /**
     * Show all bookings with their status
     */
    protected function showAllBookings()
    {
        $bookings = Booking::select('id', 'booking_number', 'status', 'created_at')->get();
        
        if ($bookings->isEmpty()) {
            $this->info("No bookings found.");
            return;
        }
        
        $headers = ['ID', 'Booking Number', 'Status', 'Created At'];
        $rows = [];
        
        foreach ($bookings as $booking) {
            $rows[] = [
                $booking->id,
                $booking->booking_number,
                $booking->status,
                $booking->created_at->format('Y-m-d H:i:s')
            ];
        }
        
        $this->table($headers, $rows);
        $this->info("Use: php artisan booking:update-status {id} --from={current_status} --to={new_status}");
    }
}