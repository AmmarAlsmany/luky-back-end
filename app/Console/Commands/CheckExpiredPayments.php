<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payment;
use App\Models\Booking;
use App\Models\AppSetting;
use App\Services\FCMService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckExpiredPayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payments:check-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for expired payments and send FCM timeout notifications';

    protected $fcmService;

    public function __construct(FCMService $fcmService)
    {
        parent::__construct();
        $this->fcmService = $fcmService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired payments...');

        // Get payment timeout from settings (default 5 minutes - same as booking cancellation)
        $timeoutMinutes = (int) AppSetting::get('payment_timeout_minutes', 5);

        Log::info('CheckExpiredPayments running', [
            'timeout_minutes' => $timeoutMinutes,
        ]);

        // Find payments that are pending and timeout has expired
        // Payment is created when booking is confirmed, so use payment.created_at
        $expiredPayments = Payment::where('status', 'pending')
            ->where('created_at', '<=', Carbon::now()->subMinutes($timeoutMinutes))
            ->with('booking')
            ->get();

        if ($expiredPayments->isEmpty()) {
            $this->info('No expired payments found.');
            Log::info('No expired payments found');
            return 0;
        }

        $this->info("Found {$expiredPayments->count()} expired payments.");

        $processedCount = 0;

        foreach ($expiredPayments as $payment) {
            try {
                // Skip if booking is already cancelled or payment already failed/completed
                if (!$payment->booking || $payment->booking->status === 'cancelled' || in_array($payment->status, ['failed', 'completed', 'refunded'])) {
                    continue;
                }

                // Update payment status to failed with timeout reason
                $payment->update([
                    'status' => 'failed',
                    'failure_reason' => 'Payment timeout expired',
                ]);

                // Update booking payment status
                $payment->booking->update([
                    'payment_status' => 'failed',
                ]);

                $this->info("✓ Marked payment #{$payment->id} as timeout (Booking #{$payment->booking->booking_number})");

                // Send FCM notification to client
                try {
                    $this->fcmService->sendPaymentTimeout($payment->booking, $payment);
                    $this->info("  → FCM timeout notification sent");

                    Log::info('Payment timeout FCM sent', [
                        'payment_id' => $payment->id,
                        'booking_id' => $payment->booking_id,
                    ]);
                } catch (\Exception $fcmException) {
                    $this->error("  → Failed to send FCM: {$fcmException->getMessage()}");

                    Log::error('Failed to send FCM payment timeout notification', [
                        'payment_id' => $payment->id,
                        'booking_id' => $payment->booking_id,
                        'error' => $fcmException->getMessage(),
                    ]);
                }

                // Send in-app notification to client
                \App\Models\Notification::create([
                    'user_id' => $payment->booking->client_id,
                    'type' => 'payment_timeout',
                    'title' => 'Payment Timeout',
                    'body' => "Payment time expired for booking #{$payment->booking_id}. Please request a new payment link.",
                    'data' => [
                        'booking_id' => $payment->booking_id,
                        'payment_id' => $payment->id,
                    ],
                    'is_read' => false,
                ]);

                // Send notification to provider
                if ($payment->booking->provider && $payment->booking->provider->user_id) {
                    \App\Models\Notification::create([
                        'user_id' => $payment->booking->provider->user_id,
                        'type' => 'payment_timeout',
                        'title' => 'Payment Timeout',
                        'body' => "Payment timeout for booking #{$payment->booking->booking_number}. Client did not complete payment.",
                        'data' => [
                            'booking_id' => $payment->booking_id,
                            'payment_id' => $payment->id,
                            'booking_number' => $payment->booking->booking_number,
                        ],
                        'is_read' => false,
                    ]);
                }

                // Send notification to admin/dashboard users
                $notificationService = app(\App\Services\NotificationService::class);
                $notificationService->sendToAdmins(
                    'payment_timeout',
                    'Payment Timeout',
                    "Payment timeout for booking #{$payment->booking->booking_number}. Amount: {$payment->amount} SAR",
                    [
                        'booking_id' => $payment->booking_id,
                        'payment_id' => $payment->id,
                        'booking_number' => $payment->booking->booking_number,
                        'amount' => $payment->amount,
                    ]
                );

                $processedCount++;
            } catch (\Exception $e) {
                $this->error("✗ Failed to process payment #{$payment->id}: {$e->getMessage()}");

                Log::error('Failed to process expired payment', [
                    'payment_id' => $payment->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        $this->info("Successfully processed {$processedCount} expired payments.");

        Log::info('CheckExpiredPayments completed', [
            'processed_count' => $processedCount,
        ]);

        return 0;
    }
}
