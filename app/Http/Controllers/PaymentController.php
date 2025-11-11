<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\MyFatoorahService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    protected $myFatoorahService;

    public function __construct(MyFatoorahService $myFatoorahService)
    {
        $this->myFatoorahService = $myFatoorahService;
    }

    /**
     * Display transaction tracking page
     */
    public function transactions(Request $request)
    {
        // Fetch payments from database with relationships
        $query = Payment::with(['booking.client', 'booking.provider'])
            ->orderBy('created_at', 'desc');

        // Apply filters if provided
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('gateway') && $request->gateway != '') {
            $query->where('payment_gateway', $request->gateway);
        }

        if ($request->has('date_from') && $request->has('date_to')) {
            $query->whereBetween('created_at', [
                $request->date_from . ' 00:00:00',
                $request->date_to . ' 23:59:59'
            ]);
        }

        $payments = $query->paginate(10);

        // Calculate statistics
        $stats = [
            'total' => Payment::count(),
            'completed' => Payment::where('status', 'completed')->count(),
            'pending' => Payment::where('status', 'pending')->count(),
            'failed' => Payment::where('status', 'failed')->count(),
        ];

        return view('payment.transactions', compact('payments', 'stats'));
    }

    /**
     * Display payment methods page
     */
    public function methods()
    {
        try {
            // Get payment methods from MyFatoorah
            $amount = 100; // Sample amount for testing
            $result = $this->myFatoorahService->getPaymentMethods($amount);

            $paymentMethods = [];
            $error = null;

            if ($result['success']) {
                $apiMethods = $result['data'];
                
                // Sync with database settings
                foreach ($apiMethods as &$method) {
                    $setting = \App\Models\PaymentMethodSetting::where('method_id', $method['PaymentMethodId'])->first();
                    
                    if (!$setting) {
                        // Create new setting with default enabled
                        $setting = \App\Models\PaymentMethodSetting::create([
                            'method_id' => $method['PaymentMethodId'],
                            'method_code' => $method['PaymentMethodCode'] ?? '',
                            'method_name_en' => $method['PaymentMethodEn'] ?? '',
                            'method_name_ar' => $method['PaymentMethodAr'] ?? '',
                            'is_enabled' => true,
                            'is_default' => false,
                            'display_order' => 0,
                            'image_url' => $method['ImageUrl'] ?? null,
                            'service_charge' => $method['ServiceCharge'] ?? 0,
                            'currency_iso' => $method['CurrencyIso'] ?? 'SAR',
                        ]);
                    }
                    
                    // Add saved settings to method data
                    $method['is_enabled'] = $setting->is_enabled;
                    $method['is_default'] = $setting->is_default;
                }
                
                $paymentMethods = $apiMethods;
            } else {
                $error = $result['message'] ?? 'Failed to fetch payment methods from MyFatoorah API. Please check your API configuration.';
                Log::warning('Payment methods page - API error', ['error' => $error]);
            }

            return view('payment.methods', compact('paymentMethods', 'error'));
        } catch (\Exception $e) {
            Log::error('Error loading payment methods page', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('payment.methods', [
                'paymentMethods' => [],
                'error' => 'An unexpected error occurred: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Display commission tracking page
     */
    public function commissions()
    {
        try {
            // Get commission data from database
            $commissionData = Payment::selectRaw('
                    service_providers.business_name as provider_name,
                    service_providers.id as provider_id,
                    COUNT(payments.id) as transaction_count,
                    SUM(payments.amount) as gross_revenue,
                    SUM(payments.platform_commission) as total_commission,
                    SUM(payments.amount - COALESCE(payments.platform_commission, 0)) as net_payout
                ')
                ->join('bookings', 'payments.booking_id', '=', 'bookings.id')
                ->join('service_providers', 'bookings.provider_id', '=', 'service_providers.id')
                ->where('payments.status', 'completed')
                ->groupBy('service_providers.id', 'service_providers.business_name')
                ->get();

            // Calculate summary statistics
            $totalCommission = Payment::where('status', 'completed')->sum('platform_commission') ?? 0;
            $totalRevenue = Payment::where('status', 'completed')->sum('amount') ?? 0;
            
            // Calculate actual average rate from real data
            $avgRate = $totalRevenue > 0 ? ($totalCommission / $totalRevenue) * 100 : 0;
            
            $summary = [
                'total_commission' => $totalCommission,
                'this_month' => Payment::where('status', 'completed')
                    ->whereMonth('created_at', now()->month)
                    ->sum('platform_commission') ?? 0,
                'pending_payout' => Payment::where('status', 'pending')->sum('amount') ?? 0,
                'avg_rate' => $avgRate,
            ];

            return view('payment.commissions', compact('commissionData', 'summary'));
        } catch (\Exception $e) {
            Log::error('Error loading commissions page', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('payment.commissions', [
                'commissionData' => collect(),
                'summary' => [
                    'total_commission' => 0,
                    'this_month' => 0,
                    'pending_payout' => 0,
                    'avg_rate' => 10.5,
                ]
            ]);
        }
    }

    /**
     * Get transaction details from MyFatoorah
     */
    public function getTransactionDetails($paymentId)
    {
        try {
            $result = $this->myFatoorahService->getPaymentStatus($paymentId);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'data' => $result['data']
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Failed to fetch transaction details'
            ], 400);
        } catch (\Exception $e) {
            Log::error('Error fetching transaction details', [
                'payment_id' => $paymentId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching transaction details'
            ], 500);
        }
    }

    /**
     * Export transactions
     */
    public function exportTransactions(Request $request)
    {
        $query = Payment::with(['booking.client', 'booking.provider'])
            ->orderBy('created_at', 'desc');

        // Apply same filters as transactions page
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('gateway') && $request->gateway != '') {
            $query->where('payment_gateway', $request->gateway);
        }

        $payments = $query->get();

        // Generate CSV
        $filename = 'transactions_' . now()->format('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($payments) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'Transaction ID',
                'Booking ID',
                'Customer',
                'Provider',
                'Amount',
                'Gateway',
                'Status',
                'Date'
            ]);

            // Add data rows
            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->transaction_id,
                    $payment->booking_id,
                    $payment->booking->client->name ?? 'N/A',
                    $payment->booking->provider->business_name ?? 'N/A',
                    $payment->amount . ' ' . $payment->currency,
                    $payment->payment_gateway,
                    $payment->status,
                    $payment->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Test MyFatoorah connection
     */
    public function testConnection()
    {
        try {
            $result = $this->myFatoorahService->getPaymentMethods(100);

            return response()->json([
                'success' => $result['success'],
                'message' => $result['success'] ? 'Connection successful!' : 'Connection failed',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection test failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update provider payment settings
     */
    public function updateProviderSettings(Request $request)
    {
        try {
            $validated = $request->validate([
                'provider_id' => 'required|exists:service_providers,id',
                'tax' => 'required|numeric|min:0|max:100',
                'commission' => 'required|numeric|min:0|max:100',
                'currency' => 'required|string|in:SAR,AED,USD,KWD,BHD,QAR',
            ]);

            $provider = \App\Models\ServiceProvider::findOrFail($validated['provider_id']);
            
            // Update or create payment settings
            $settings = \App\Models\ProviderPaymentSetting::updateOrCreate(
                ['provider_id' => $validated['provider_id']],
                [
                    'tax_rate' => $validated['tax'],
                    'commission_rate' => $validated['commission'],
                    'currency' => $validated['currency']
                ]
            );

            // Also update commission on the provider model for backward compatibility
            $provider->commission_rate = $validated['commission'];
            $provider->save();

            return response()->json([
                'success' => true,
                'message' => 'Provider payment settings updated successfully',
                'data' => [
                    'provider_id' => $provider->id,
                    'business_name' => $provider->business_name,
                    'commission_rate' => $settings->commission_rate,
                    'tax_rate' => $settings->tax_rate,
                    'currency' => $settings->currency
                ]
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating provider payment settings', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update provider settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update MyFatoorah gateway settings
     */
    public function updateGatewaySettings(Request $request)
    {
        try {
            $validated = $request->validate([
                'enabled' => 'required|boolean',
                'mode' => 'required|in:test,live',
                'country' => 'required|in:SA,KW,BH,AE,QA',
                'currency' => 'required|in:SAR,KWD,BHD,AED,QAR,USD',
                'base_url' => 'required|url',
                'api_key' => 'required|string',
                'merchant_id' => 'nullable|string',
                'success_url' => 'required|url',
                'failure_url' => 'required|url',
                'webhook_url' => 'nullable|url',
                'payment_methods' => 'nullable|array',
                'min_amount' => 'nullable|numeric|min:0',
                'max_amount' => 'nullable|numeric|min:0',
                'invoice_expiry' => 'nullable|integer|min:1',
                'language' => 'nullable|in:En,Ar',
                'descriptor' => 'nullable|string',
                'notes' => 'nullable|string',
            ]);

            // Update .env file with new settings
            $this->updateEnvFile([
                'MYFATOORAH_API_KEY' => $validated['api_key'],
                'MYFATOORAH_API_URL' => $validated['base_url'],
                'MYFATOORAH_SUCCESS_URL' => $validated['success_url'],
                'MYFATOORAH_ERROR_URL' => $validated['failure_url'],
            ]);

            // Save additional settings to database
            DB::table('settings')->updateOrInsert(
                ['key' => 'myfatoorah_enabled', 'group' => 'payment'],
                ['value' => $validated['enabled'] ? '1' : '0', 'updated_at' => now()]
            );

            DB::table('settings')->updateOrInsert(
                ['key' => 'myfatoorah_country', 'group' => 'payment'],
                ['value' => $validated['country'], 'updated_at' => now()]
            );

            DB::table('settings')->updateOrInsert(
                ['key' => 'myfatoorah_currency', 'group' => 'payment'],
                ['value' => $validated['currency'], 'updated_at' => now()]
            );

            DB::table('settings')->updateOrInsert(
                ['key' => 'myfatoorah_merchant_id', 'group' => 'payment'],
                ['value' => $validated['merchant_id'] ?? '', 'updated_at' => now()]
            );

            DB::table('settings')->updateOrInsert(
                ['key' => 'myfatoorah_webhook_url', 'group' => 'payment'],
                ['value' => $validated['webhook_url'] ?? '', 'updated_at' => now()]
            );

            // Save payment methods
            DB::table('settings')->updateOrInsert(
                ['key' => 'myfatoorah_payment_methods', 'group' => 'payment'],
                ['value' => json_encode($validated['payment_methods'] ?? []), 'updated_at' => now()]
            );

            // Save min/max amounts and invoice expiry
            DB::table('settings')->updateOrInsert(
                ['key' => 'myfatoorah_min_amount', 'group' => 'payment'],
                ['value' => $validated['min_amount'] ?? '0', 'updated_at' => now()]
            );

            DB::table('settings')->updateOrInsert(
                ['key' => 'myfatoorah_max_amount', 'group' => 'payment'],
                ['value' => $validated['max_amount'] ?? '10000', 'updated_at' => now()]
            );

            DB::table('settings')->updateOrInsert(
                ['key' => 'myfatoorah_invoice_expiry', 'group' => 'payment'],
                ['value' => $validated['invoice_expiry'] ?? '30', 'updated_at' => now()]
            );

            DB::table('settings')->updateOrInsert(
                ['key' => 'myfatoorah_language', 'group' => 'payment'],
                ['value' => $validated['language'] ?? 'En', 'updated_at' => now()]
            );

            DB::table('settings')->updateOrInsert(
                ['key' => 'myfatoorah_descriptor', 'group' => 'payment'],
                ['value' => $validated['descriptor'] ?? '', 'updated_at' => now()]
            );

            DB::table('settings')->updateOrInsert(
                ['key' => 'myfatoorah_notes', 'group' => 'payment'],
                ['value' => $validated['notes'] ?? '', 'updated_at' => now()]
            );

            // Clear config cache to load new values
            Artisan::call('config:clear');

            return response()->json([
                'success' => true,
                'message' => 'MyFatoorah gateway settings updated successfully',
                'data' => $validated
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error updating gateway settings', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update gateway settings: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update .env file values
     */
    private function updateEnvFile(array $data)
    {
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);

        foreach ($data as $key => $value) {
            // Escape special characters in value
            $value = str_replace('"', '\\"', $value);
            
            // Check if key exists in .env
            if (preg_match("/^{$key}=.*/m", $envContent)) {
                // Update existing key
                $envContent = preg_replace(
                    "/^{$key}=.*/m",
                    "{$key}={$value}",
                    $envContent
                );
            } else {
                // Add new key
                $envContent .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envPath, $envContent);
    }

    /**
     * View payment receipt
     */
    public function viewReceipt($id)
    {
        try {
            $payment = Payment::with(['booking.client', 'booking.provider', 'booking.items.service'])
                ->findOrFail($id);

            return view('payment.receipt', compact('payment'));
        } catch (\Exception $e) {
            Log::error('Error viewing receipt', [
                'payment_id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('payments.transactions')
                ->with('error', 'Receipt not found');
        }
    }

    /**
     * Toggle payment method enabled/disabled
     */
    public function togglePaymentMethod(Request $request)
    {
        try {
            $validated = $request->validate([
                'method_id' => 'required|integer',
                'is_enabled' => 'required|boolean',
            ]);

            $setting = \App\Models\PaymentMethodSetting::where('method_id', $validated['method_id'])->first();
            
            if (!$setting) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment method not found'
                ], 404);
            }

            $setting->update(['is_enabled' => $validated['is_enabled']]);

            return response()->json([
                'success' => true,
                'message' => 'Payment method ' . ($validated['is_enabled'] ? 'enabled' : 'disabled') . ' successfully',
                'data' => $setting
            ]);
        } catch (\Exception $e) {
            Log::error('Error toggling payment method', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update payment method: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Select payment method as default
     */
    public function selectPaymentMethod(Request $request)
    {
        try {
            $validated = $request->validate([
                'method_id' => 'required|integer',
            ]);

            // Unset all defaults first
            \App\Models\PaymentMethodSetting::query()->update(['is_default' => false]);

            // Set this one as default
            $setting = \App\Models\PaymentMethodSetting::where('method_id', $validated['method_id'])->first();
            
            if (!$setting) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment method not found'
                ], 404);
            }

            $setting->update(['is_default' => true]);

            return response()->json([
                'success' => true,
                'message' => $setting->method_name_en . ' set as default payment method',
                'data' => $setting
            ]);
        } catch (\Exception $e) {
            Log::error('Error selecting payment method', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to select payment method: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download payment receipt as PDF
     */
    public function downloadReceipt($id)
    {
        try {
            $payment = Payment::with(['booking.client', 'booking.provider', 'booking.items.service'])
                ->findOrFail($id);

            // For now, return HTML view that can be printed
            // TODO: Implement PDF generation with DomPDF or similar
            return view('payment.receipt-print', compact('payment'));
        } catch (\Exception $e) {
            Log::error('Error downloading receipt', [
                'payment_id' => $id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('payments.transactions')
                ->with('error', 'Failed to download receipt');
        }
    }
}
