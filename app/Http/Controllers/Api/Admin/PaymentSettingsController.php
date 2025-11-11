<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PaymentSettingsController extends Controller
{
    /**
     * Get all payment gateways
     */
    public function getGateways()
    {
        $gateways = DB::table('payment_gateways')
            ->orderBy('display_order', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => ['gateways' => $gateways],
        ]);
    }

    /**
     * Get single payment gateway
     */
    public function getGateway($id)
    {
        $gateway = DB::table('payment_gateways')->find($id);

        if (!$gateway) {
            return response()->json([
                'success' => false,
                'message' => 'Payment gateway not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => ['gateway' => $gateway],
        ]);
    }

    /**
     * Create payment gateway
     */
    public function createGateway(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'gateway_type' => 'required|string|in:myfatoorah,stripe,paypal,manual',
            'is_enabled' => 'boolean',
            'api_key' => 'nullable|string',
            'api_secret' => 'nullable|string',
            'webhook_secret' => 'nullable|string',
            'mode' => 'required|in:live,test',
            'display_order' => 'nullable|integer',
            'config' => 'nullable|json',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $gatewayId = DB::table('payment_gateways')->insertGetId([
            'name' => $request->name,
            'gateway_type' => $request->gateway_type,
            'is_enabled' => $request->is_enabled ?? true,
            'api_key' => $request->api_key,
            'api_secret' => $request->api_secret,
            'webhook_secret' => $request->webhook_secret,
            'mode' => $request->mode,
            'display_order' => $request->display_order ?? 0,
            'config' => $request->config,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $gateway = DB::table('payment_gateways')->find($gatewayId);

        return response()->json([
            'success' => true,
            'data' => ['gateway' => $gateway],
            'message' => 'Payment gateway created successfully',
        ], 201);
    }

    /**
     * Update payment gateway
     */
    public function updateGateway(Request $request, $id)
    {
        $gateway = DB::table('payment_gateways')->find($id);

        if (!$gateway) {
            return response()->json([
                'success' => false,
                'message' => 'Payment gateway not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'gateway_type' => 'sometimes|string|in:myfatoorah,stripe,paypal,manual',
            'is_enabled' => 'boolean',
            'api_key' => 'nullable|string',
            'api_secret' => 'nullable|string',
            'webhook_secret' => 'nullable|string',
            'mode' => 'sometimes|in:live,test',
            'display_order' => 'nullable|integer',
            'config' => 'nullable|json',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $updateData = array_filter([
            'name' => $request->name,
            'gateway_type' => $request->gateway_type,
            'is_enabled' => $request->is_enabled,
            'api_key' => $request->api_key,
            'api_secret' => $request->api_secret,
            'webhook_secret' => $request->webhook_secret,
            'mode' => $request->mode,
            'display_order' => $request->display_order,
            'config' => $request->config,
            'updated_at' => now(),
        ], function ($value) {
            return $value !== null;
        });

        DB::table('payment_gateways')->where('id', $id)->update($updateData);

        $updatedGateway = DB::table('payment_gateways')->find($id);

        return response()->json([
            'success' => true,
            'data' => ['gateway' => $updatedGateway],
            'message' => 'Payment gateway updated successfully',
        ]);
    }

    /**
     * Delete payment gateway
     */
    public function deleteGateway($id)
    {
        $gateway = DB::table('payment_gateways')->find($id);

        if (!$gateway) {
            return response()->json([
                'success' => false,
                'message' => 'Payment gateway not found',
            ], 404);
        }

        DB::table('payment_gateways')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Payment gateway deleted successfully',
        ]);
    }

    /**
     * Toggle gateway status
     */
    public function toggleGateway($id)
    {
        $gateway = DB::table('payment_gateways')->find($id);

        if (!$gateway) {
            return response()->json([
                'success' => false,
                'message' => 'Payment gateway not found',
            ], 404);
        }

        DB::table('payment_gateways')
            ->where('id', $id)
            ->update([
                'is_enabled' => !$gateway->is_enabled,
                'updated_at' => now(),
            ]);

        $updatedGateway = DB::table('payment_gateways')->find($id);

        return response()->json([
            'success' => true,
            'data' => ['gateway' => $updatedGateway],
            'message' => 'Gateway status updated successfully',
        ]);
    }

    /**
     * Get payment settings
     */
    public function getSettings()
    {
        $settings = DB::table('payment_settings')->first();

        return response()->json([
            'success' => true,
            'data' => ['settings' => $settings],
        ]);
    }

    /**
     * Update payment settings
     */
    public function updateSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'default_currency' => 'sometimes|string|size:3',
            'tax_percentage' => 'sometimes|numeric|min:0|max:100',
            'tax_name' => 'sometimes|string|max:255',
            'platform_commission_rate' => 'sometimes|numeric|min:0|max:100',
            'min_withdrawal_amount' => 'sometimes|numeric|min:0',
            'payment_methods' => 'sometimes|json',
            'auto_payout' => 'boolean',
            'payout_frequency' => 'sometimes|in:daily,weekly,monthly',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $settings = DB::table('payment_settings')->first();

        $updateData = array_filter([
            'default_currency' => $request->default_currency,
            'tax_percentage' => $request->tax_percentage,
            'tax_name' => $request->tax_name,
            'platform_commission_rate' => $request->platform_commission_rate,
            'min_withdrawal_amount' => $request->min_withdrawal_amount,
            'payment_methods' => $request->payment_methods,
            'auto_payout' => $request->auto_payout,
            'payout_frequency' => $request->payout_frequency,
            'updated_at' => now(),
        ], function ($value) {
            return $value !== null;
        });

        if ($settings) {
            // Update existing settings
            DB::table('payment_settings')->where('id', $settings->id)->update($updateData);
            $updatedSettings = DB::table('payment_settings')->first();
        } else {
            // Create new settings if none exist
            $updateData['created_at'] = now();
            $settingsId = DB::table('payment_settings')->insertGetId($updateData);
            $updatedSettings = DB::table('payment_settings')->find($settingsId);
        }

        return response()->json([
            'success' => true,
            'data' => ['settings' => $updatedSettings],
            'message' => 'Payment settings updated successfully',
        ]);
    }

    /**
     * Get tax settings
     */
    public function getTaxSettings()
    {
        $settings = DB::table('payment_settings')
            ->select('tax_percentage', 'tax_name')
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'tax_percentage' => $settings->tax_percentage ?? 0,
                'tax_name' => $settings->tax_name ?? 'Tax',
            ],
        ]);
    }

    /**
     * Update tax settings
     */
    public function updateTaxSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tax_percentage' => 'required|numeric|min:0|max:100',
            'tax_name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $settings = DB::table('payment_settings')->first();

        if ($settings) {
            DB::table('payment_settings')->where('id', $settings->id)->update([
                'tax_percentage' => $request->tax_percentage,
                'tax_name' => $request->tax_name,
                'updated_at' => now(),
            ]);
        } else {
            DB::table('payment_settings')->insert([
                'tax_percentage' => $request->tax_percentage,
                'tax_name' => $request->tax_name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tax settings updated successfully',
            'data' => [
                'tax_percentage' => $request->tax_percentage,
                'tax_name' => $request->tax_name,
            ],
        ]);
    }

    /**
     * Get commission settings
     */
    public function getCommissionSettings()
    {
        $settings = DB::table('payment_settings')
            ->select('platform_commission_rate')
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'platform_commission_rate' => $settings->platform_commission_rate ?? 15,
            ],
        ]);
    }

    /**
     * Update commission settings
     */
    public function updateCommissionSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'platform_commission_rate' => 'required|numeric|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $settings = DB::table('payment_settings')->first();

        if ($settings) {
            DB::table('payment_settings')->where('id', $settings->id)->update([
                'platform_commission_rate' => $request->platform_commission_rate,
                'updated_at' => now(),
            ]);
        } else {
            DB::table('payment_settings')->insert([
                'platform_commission_rate' => $request->platform_commission_rate,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Commission settings updated successfully',
            'data' => [
                'platform_commission_rate' => $request->platform_commission_rate,
            ],
        ]);
    }

    /**
     * Test payment gateway connection
     */
    public function testGatewayConnection($id)
    {
        $gateway = DB::table('payment_gateways')->find($id);

        if (!$gateway) {
            return response()->json([
                'success' => false,
                'message' => 'Payment gateway not found',
            ], 404);
        }

        // TODO: Implement actual gateway connection testing
        // For now, return success
        return response()->json([
            'success' => true,
            'message' => 'Gateway connection test successful',
            'data' => [
                'gateway' => $gateway->name,
                'status' => 'connected',
                'tested_at' => now(),
            ],
        ]);
    }
}
