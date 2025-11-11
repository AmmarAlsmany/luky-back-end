<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\MyFatoorahService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    protected $myFatoorah;

    public function __construct(MyFatoorahService $myFatoorah)
    {
        $this->myFatoorah = $myFatoorah;
    }

    /**
     * Get available payment methods for booking
     */
    public function getPaymentMethods(Request $request): JsonResponse
    {
        Log::info('=== GET PAYMENT METHODS STARTED ===');
        Log::info('Request data:', $request->all());

        $request->validate([
            'booking_id' => 'required|exists:bookings,id'
        ]);

        $booking = Booking::findOrFail($request->booking_id);

        Log::info('Booking found:', [
            'id' => $booking->id,
            'client_id' => $booking->client_id,
            'status' => $booking->status,
            'payment_status' => $booking->payment_status,
            'total_amount' => $booking->total_amount,
        ]);

        // Verify booking belongs to authenticated user
        if ($booking->client_id !== $request->user()->id) {
            throw ValidationException::withMessages([
                'booking_id' => ['Unauthorized access to booking.']
            ]);
        }

        // Verify booking is confirmed and payment is pending
        if ($booking->status !== 'confirmed' || $booking->payment_status !== 'pending') {
            throw ValidationException::withMessages([
                'booking_id' => ['Booking is not ready for payment.']
            ]);
        }

        Log::info('Calling MyFatoorah getPaymentMethods...');

        $result = $this->myFatoorah->getPaymentMethods($booking->total_amount);

        Log::info('MyFatoorah result:', $result);

        if (!$result['success']) {
            Log::error('Failed to load payment methods:', $result);
            return response()->json([
                'success' => false,
                'message' => 'Failed to load payment methods'
            ], 500);
        }

        Log::info('=== GET PAYMENT METHODS SUCCESS ===');

        return response()->json([
            'success' => true,
            'data' => [
                'booking_id' => $booking->id,
                'amount' => (float) $booking->total_amount,
                'currency' => 'SAR',
                'payment_methods' => $result['data']
            ]
        ]);
    }

    /**
     * Initiate payment for booking
     */
    public function initiatePayment(Request $request): JsonResponse
    {
        Log::info('=== PAYMENT INITIATION STARTED ===');
        Log::info('Request data:', $request->all());

        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'payment_method_id' => 'required|integer',
        ]);

        $user = $request->user();
        $booking = Booking::with(['client', 'provider'])->findOrFail($request->booking_id);

        Log::info('Booking details:', [
            'id' => $booking->id,
            'status' => $booking->status,
            'payment_status' => $booking->payment_status,
            'confirmed_at' => $booking->confirmed_at,
        ]);

        // Verify booking belongs to user
        if ($booking->client_id !== $user->id) {
            throw ValidationException::withMessages([
                'booking_id' => ['Unauthorized access to booking.']
            ]);
        }

        // Verify booking status
        if ($booking->status !== 'confirmed') {
            throw ValidationException::withMessages([
                'booking_id' => ['Booking must be confirmed by provider first.']
            ]);
        }

        if ($booking->payment_status !== 'pending') {
            throw ValidationException::withMessages([
                'booking_id' => ['Payment already processed for this booking.']
            ]);
        }

        // Check payment timeout
        $timeoutMinutes = config('app.payment_timeout_minutes', 5);
        $confirmedAt = $booking->confirmed_at;
        
        if ($confirmedAt && $confirmedAt->addMinutes($timeoutMinutes)->isPast()) {
            // Auto-cancel booking
            $booking->update([
                'status' => 'cancelled',
                'cancelled_by' => 'system',
                'cancellation_reason' => 'Payment timeout',
                'cancelled_at' => now()
            ]);

            throw ValidationException::withMessages([
                'booking_id' => ['Payment time has expired. Booking has been cancelled.']
            ]);
        }

        DB::beginTransaction();
        try {
            Log::info('Calling MyFatoorah executePayment...');

            // Execute payment with MyFatoorah
            $result = $this->myFatoorah->executePayment([
                'payment_method_id' => $request->payment_method_id,
                'amount' => $booking->total_amount,
                'customer_name' => $user->name,
                'customer_mobile' => $user->phone,
                'customer_email' => $user->email,
                'booking_id' => $booking->id,
                'client_id' => $user->id,
                'language' => $request->header('Accept-Language', 'ar'),
            ]);

            Log::info('MyFatoorah response:', $result);

            if (!$result['success']) {
                DB::rollBack();
                Log::error('MyFatoorah failed:', [
                    'message' => $result['message'] ?? 'Unknown error',
                    'result' => $result
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Payment initiation failed',
                    'error' => $result['message'] ?? 'Unknown error'
                ], 400);
            }

            $paymentData = $result['data']['Data'];
            Log::info('Payment data from MyFatoorah:', $paymentData);

            // Create payment record
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'payment_id' => $paymentData['InvoiceId'],
                'amount' => $booking->total_amount,
                'currency' => 'SAR',
                'gateway' => 'myfatoorah',
                'method' => $this->getPaymentMethodName($request->payment_method_id),
                'status' => 'pending',
                'gateway_response' => $result['data'],
                'gateway_transaction_id' => $paymentData['InvoiceId'] ?? null,
            ]);

            // Update booking payment reference (status remains 'pending' until payment confirmed)
            $booking->update([
                'payment_reference' => $payment->payment_id,
            ]);

            DB::commit();

            Log::info('=== PAYMENT INITIATED SUCCESSFULLY ===');

            return response()->json([
                'success' => true,
                'message' => 'Payment initiated successfully',
                'data' => [
                    'payment_id' => $payment->id,
                    'payment_url' => $paymentData['PaymentURL'],
                    'invoice_id' => $paymentData['InvoiceId'],
                    'success_url' => 'callback/success',
                    'error_url' => 'callback/error',
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('=== PAYMENT INITIATION EXCEPTION ===', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment initiation failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Payment callback - Handles both success and error redirects from MyFatoorah
     */
    public function paymentCallback(Request $request)
    {
        // Log all callback details
        Log::info('=== PAYMENT CALLBACK RECEIVED ===', [
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'all_params' => $request->all(),
            'is_error_callback' => $request->is('*/callback/error'),
            'is_success_callback' => $request->is('*/callback/success'),
        ]);

        $paymentId = $request->get('paymentId');
        $isErrorCallback = $request->is('*/callback/error');

        if (!$paymentId) {
            Log::error('Payment callback missing payment ID');
            return $this->renderCallbackPage(false, 'Payment ID not provided');
        }

        Log::info('Fetching payment status from MyFatoorah', ['payment_id' => $paymentId]);

        // Get payment status from MyFatoorah
        $result = $this->myFatoorah->getPaymentStatus($paymentId);

        Log::info('MyFatoorah payment status response', [
            'success' => $result['success'],
            'data' => $result['data'] ?? null,
            'message' => $result['message'] ?? null,
        ]);

        if (!$result['success']) {
            return $this->renderCallbackPage(false, 'Failed to verify payment status');
        }

        $paymentData = $result['data']['Data'];
        $invoiceStatus = $paymentData['InvoiceStatus'];

        Log::info('Payment invoice status', [
            'invoice_status' => $invoiceStatus,
            'invoice_error' => $paymentData['InvoiceError'] ?? null,
            'payment_gateway' => $paymentData['PaymentGateway'] ?? null,
        ]);

        // Find payment record
        $payment = Payment::where('payment_id', $paymentId)->first();

        if (!$payment) {
            Log::error('Payment record not found in database', ['payment_id' => $paymentId]);
            return $this->renderCallbackPage(false, 'Payment record not found');
        }

        Log::info('Payment record found', [
            'payment_db_id' => $payment->id,
            'booking_id' => $payment->booking_id,
            'current_status' => $payment->status,
        ]);

        DB::beginTransaction();
        try {
            if ($invoiceStatus === 'Paid') {
                // Payment successful
                Log::info('Processing successful payment', ['payment_id' => $paymentId]);

                $payment->update([
                    'status' => 'completed',
                    'gateway_response' => $result['data'],
                    'paid_at' => now(),
                ]);

                // Update booking
                $payment->booking->update([
                    'payment_status' => 'paid',
                    'payment_method' => $paymentData['PaymentGateway'] ?? 'myfatoorah',
                ]);

                DB::commit();

                Log::info('=== PAYMENT COMPLETED SUCCESSFULLY ===', [
                    'payment_id' => $paymentId,
                    'booking_id' => $payment->booking_id,
                ]);

                return $this->renderCallbackPage(true, 'Payment completed successfully', [
                    'booking_id' => $payment->booking_id,
                    'amount' => $payment->amount,
                ]);

            } else {
                // Payment failed
                Log::warning('Processing failed payment', [
                    'payment_id' => $paymentId,
                    'invoice_status' => $invoiceStatus,
                    'invoice_error' => $paymentData['InvoiceError'] ?? 'No error message',
                ]);

                $payment->update([
                    'status' => 'failed',
                    'failure_reason' => $paymentData['InvoiceError'] ?? 'Payment failed',
                    'gateway_response' => $result['data'],
                ]);

                $payment->booking->update([
                    'payment_status' => 'failed',
                ]);

                DB::commit();

                Log::info('=== PAYMENT FAILED ===', [
                    'payment_id' => $paymentId,
                    'reason' => $paymentData['InvoiceError'] ?? 'Unknown',
                ]);

                return $this->renderCallbackPage(false, 'Payment failed', [
                    'reason' => $paymentData['InvoiceError'] ?? 'Unknown error'
                ]);
            }

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('=== PAYMENT CALLBACK EXCEPTION ===', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->renderCallbackPage(false, 'Payment processing failed: ' . $e->getMessage());
        }
    }

    /**
     * Render HTML callback page that closes the WebView
     */
    private function renderCallbackPage(bool $success, string $message, array $data = [])
    {
        $title = $success ? 'Payment Successful' : 'Payment Failed';
        $statusClass = $success ? 'success' : 'error';

        $html = <<<HTML
<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            text-align: center;
            max-width: 400px;
        }
        .success {
            color: #10b981;
        }
        .error {
            color: #ef4444;
        }
        .icon {
            font-size: 64px;
            margin-bottom: 20px;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }
        p {
            color: #6b7280;
            margin-bottom: 20px;
        }
        .message {
            font-size: 14px;
            color: #374151;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon {$statusClass}">
            {$this->getIcon($success)}
        </div>
        <h1 class="{$statusClass}">{$title}</h1>
        <p class="message">{$message}</p>
        <p style="font-size: 12px; color: #9ca3af;">This window will close automatically...</p>
    </div>
    <script>
        // Send message to Flutter WebView
        if (window.flutter_inappwebview) {
            window.flutter_inappwebview.callHandler('paymentResult', {
                success: {$this->boolToJs($success)},
                message: "{$message}",
                data: {$this->arrayToJson($data)}
            });
        }

        // Try to close the window
        setTimeout(function() {
            if (window.flutter_inappwebview) {
                window.flutter_inappwebview.callHandler('closeWebView');
            }
            window.close();
        }, 2000);
    </script>
</body>
</html>
HTML;

        return response($html)->header('Content-Type', 'text/html');
    }

    private function getIcon(bool $success): string
    {
        return $success ? '✓' : '✗';
    }

    private function boolToJs(bool $value): string
    {
        return $value ? 'true' : 'false';
    }

    private function arrayToJson(array $data): string
    {
        return json_encode($data);
    }

    /**
     * Webhook handler for payment notifications
     */
    public function webhook(Request $request): JsonResponse
    {
        // Verify webhook signature
        // MyFatoorah sends webhook with signature for security
        
        $paymentId = $request->get('Data.InvoiceId');
        
        if (!$paymentId) {
            return response()->json(['success' => false], 400);
        }

        // Process payment status update
        $result = $this->myFatoorah->getPaymentStatus($paymentId);

        if ($result['success']) {
            $this->processPaymentStatus($result['data']);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Helper: Get payment method name
     */
    protected function getPaymentMethodName($methodId): string
    {
        // Map payment method IDs to names
        $methods = [
            1 => 'mada',
            2 => 'visa',
            3 => 'master',
            4 => 'apple_pay',
            // Add more as needed
        ];

        return $methods[$methodId] ?? 'unknown';
    }

    /**
     * Helper: Process payment status
     */
    protected function processPaymentStatus(array $data)
    {
        $paymentData = $data['Data'];
        $payment = Payment::where('payment_id', $paymentData['InvoiceId'])->first();

        if (!$payment) {
            return;
        }

        DB::transaction(function () use ($payment, $paymentData) {
            if ($paymentData['InvoiceStatus'] === 'Paid') {
                $payment->update([
                    'status' => 'completed',
                    'paid_at' => now(),
                ]);

                $payment->booking->update([
                    'payment_status' => 'paid',
                ]);
            }
        });
    }
}