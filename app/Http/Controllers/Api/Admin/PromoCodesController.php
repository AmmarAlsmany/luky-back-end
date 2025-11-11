<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PromoCodesController extends Controller
{
    /**
     * Get list of promo codes with pagination and filters
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 20);
        $search = $request->input('search');
        $status = $request->input('status');
        $discountType = $request->input('discount_type');
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        $query = DB::table('promo_codes');

        // Search filter
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Status filter
        if ($status) {
            $query->where('status', $status);
        }

        // Discount type filter
        if ($discountType) {
            $query->where('discount_type', $discountType);
        }

        // Sorting
        $query->orderBy($sortBy, $sortOrder);

        $promoCodes = $query->paginate($perPage);

        // Get stats
        $stats = [
            'total_codes' => DB::table('promo_codes')->count(),
            'active_codes' => DB::table('promo_codes')->where('status', 'active')->count(),
            'expired_codes' => DB::table('promo_codes')
                ->where('expires_at', '<', now())
                ->count(),
            'total_usage' => DB::table('promo_code_usage')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'promo_codes' => $promoCodes,
                'stats' => $stats,
            ],
        ]);
    }

    /**
     * Get single promo code
     */
    public function show($id)
    {
        $promoCode = DB::table('promo_codes')->find($id);

        if (!$promoCode) {
            return response()->json([
                'success' => false,
                'message' => 'Promo code not found',
            ], 404);
        }

        // Get usage statistics
        $usageStats = DB::table('promo_code_usage')
            ->where('promo_code_id', $id)
            ->select(
                DB::raw('COUNT(*) as total_uses'),
                DB::raw('COUNT(DISTINCT user_id) as unique_users'),
                DB::raw('SUM(discount_amount) as total_discount_given')
            )
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'promo_code' => $promoCode,
                'usage_stats' => $usageStats,
            ],
        ]);
    }

    /**
     * Create promo code
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:50|unique:promo_codes,code',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date|after:starts_at',
            'status' => 'in:active,inactive',
            'applicable_to' => 'nullable|in:all,specific_services,specific_providers',
            'applicable_ids' => 'nullable|json',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Convert code to uppercase
        $code = strtoupper($request->code);

        $promoCodeId = DB::table('promo_codes')->insertGetId([
            'code' => $code,
            'description' => $request->description,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'min_order_amount' => $request->min_order_amount,
            'max_discount_amount' => $request->max_discount_amount,
            'usage_limit' => $request->usage_limit,
            'usage_limit_per_user' => $request->usage_limit_per_user ?? 1,
            'starts_at' => $request->starts_at,
            'expires_at' => $request->expires_at,
            'status' => $request->status ?? 'active',
            'applicable_to' => $request->applicable_to ?? 'all',
            'applicable_ids' => $request->applicable_ids,
            'created_by' => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $promoCode = DB::table('promo_codes')->find($promoCodeId);

        return response()->json([
            'success' => true,
            'data' => ['promo_code' => $promoCode],
            'message' => 'Promo code created successfully',
        ], 201);
    }

    /**
     * Update promo code
     */
    public function update(Request $request, $id)
    {
        $promoCode = DB::table('promo_codes')->find($id);

        if (!$promoCode) {
            return response()->json([
                'success' => false,
                'message' => 'Promo code not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'code' => 'sometimes|string|max:50|unique:promo_codes,code,' . $id,
            'description' => 'nullable|string',
            'discount_type' => 'sometimes|in:percentage,fixed',
            'discount_value' => 'sometimes|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'expires_at' => 'nullable|date',
            'status' => 'in:active,inactive',
            'applicable_to' => 'nullable|in:all,specific_services,specific_providers',
            'applicable_ids' => 'nullable|json',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $updateData = array_filter([
            'code' => $request->has('code') ? strtoupper($request->code) : null,
            'description' => $request->description,
            'discount_type' => $request->discount_type,
            'discount_value' => $request->discount_value,
            'min_order_amount' => $request->min_order_amount,
            'max_discount_amount' => $request->max_discount_amount,
            'usage_limit' => $request->usage_limit,
            'usage_limit_per_user' => $request->usage_limit_per_user,
            'starts_at' => $request->starts_at,
            'expires_at' => $request->expires_at,
            'status' => $request->status,
            'applicable_to' => $request->applicable_to,
            'applicable_ids' => $request->applicable_ids,
            'updated_at' => now(),
        ], function ($value) {
            return $value !== null;
        });

        DB::table('promo_codes')->where('id', $id)->update($updateData);

        $updatedPromoCode = DB::table('promo_codes')->find($id);

        return response()->json([
            'success' => true,
            'data' => ['promo_code' => $updatedPromoCode],
            'message' => 'Promo code updated successfully',
        ]);
    }

    /**
     * Delete promo code
     */
    public function destroy($id)
    {
        $promoCode = DB::table('promo_codes')->find($id);

        if (!$promoCode) {
            return response()->json([
                'success' => false,
                'message' => 'Promo code not found',
            ], 404);
        }

        // Check if promo code has been used
        $usageCount = DB::table('promo_code_usage')
            ->where('promo_code_id', $id)
            ->count();

        if ($usageCount > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete promo code that has been used. Consider deactivating it instead.',
            ], 422);
        }

        DB::table('promo_codes')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Promo code deleted successfully',
        ]);
    }

    /**
     * Toggle promo code status
     */
    public function toggleStatus($id)
    {
        $promoCode = DB::table('promo_codes')->find($id);

        if (!$promoCode) {
            return response()->json([
                'success' => false,
                'message' => 'Promo code not found',
            ], 404);
        }

        $newStatus = $promoCode->status === 'active' ? 'inactive' : 'active';

        DB::table('promo_codes')
            ->where('id', $id)
            ->update([
                'status' => $newStatus,
                'updated_at' => now(),
            ]);

        return response()->json([
            'success' => true,
            'data' => ['status' => $newStatus],
            'message' => 'Promo code status updated successfully',
        ]);
    }

    /**
     * Get promo code usage history
     */
    public function usageHistory($id)
    {
        $promoCode = DB::table('promo_codes')->find($id);

        if (!$promoCode) {
            return response()->json([
                'success' => false,
                'message' => 'Promo code not found',
            ], 404);
        }

        $usageHistory = DB::table('promo_code_usage')
            ->join('users', 'promo_code_usage.user_id', '=', 'users.id')
            ->join('bookings', 'promo_code_usage.booking_id', '=', 'bookings.id')
            ->where('promo_code_usage.promo_code_id', $id)
            ->select(
                'promo_code_usage.*',
                'users.name as user_name',
                'users.email as user_email',
                'bookings.booking_number',
                'bookings.total_amount as booking_amount'
            )
            ->orderBy('promo_code_usage.created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => ['usage_history' => $usageHistory],
        ]);
    }

    /**
     * Validate promo code (for testing)
     */
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
            'user_id' => 'required|exists:users,id',
            'order_amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $code = strtoupper($request->code);
        $promoCode = DB::table('promo_codes')->where('code', $code)->first();

        // Check if promo code exists
        if (!$promoCode) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid promo code',
            ], 404);
        }

        // Check if promo code is active
        if ($promoCode->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'This promo code is not active',
            ], 422);
        }

        // Check if promo code has started
        if ($promoCode->starts_at && now() < $promoCode->starts_at) {
            return response()->json([
                'success' => false,
                'message' => 'This promo code is not yet valid',
            ], 422);
        }

        // Check if promo code has expired
        if ($promoCode->expires_at && now() > $promoCode->expires_at) {
            return response()->json([
                'success' => false,
                'message' => 'This promo code has expired',
            ], 422);
        }

        // Check minimum order amount
        if ($promoCode->min_order_amount && $request->order_amount < $promoCode->min_order_amount) {
            return response()->json([
                'success' => false,
                'message' => "Minimum order amount of {$promoCode->min_order_amount} is required",
            ], 422);
        }

        // Check usage limit
        if ($promoCode->usage_limit) {
            $totalUsage = DB::table('promo_code_usage')
                ->where('promo_code_id', $promoCode->id)
                ->count();

            if ($totalUsage >= $promoCode->usage_limit) {
                return response()->json([
                    'success' => false,
                    'message' => 'This promo code has reached its usage limit',
                ], 422);
            }
        }

        // Check per-user usage limit
        if ($promoCode->usage_limit_per_user) {
            $userUsage = DB::table('promo_code_usage')
                ->where('promo_code_id', $promoCode->id)
                ->where('user_id', $request->user_id)
                ->count();

            if ($userUsage >= $promoCode->usage_limit_per_user) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have reached the usage limit for this promo code',
                ], 422);
            }
        }

        // Calculate discount
        $discountAmount = 0;
        if ($promoCode->discount_type === 'percentage') {
            $discountAmount = ($request->order_amount * $promoCode->discount_value) / 100;

            // Apply max discount cap if set
            if ($promoCode->max_discount_amount && $discountAmount > $promoCode->max_discount_amount) {
                $discountAmount = $promoCode->max_discount_amount;
            }
        } else {
            $discountAmount = $promoCode->discount_value;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'promo_code' => $promoCode,
                'is_valid' => true,
                'discount_amount' => $discountAmount,
                'final_amount' => max(0, $request->order_amount - $discountAmount),
            ],
            'message' => 'Promo code is valid',
        ]);
    }

    /**
     * Generate random promo code
     */
    public function generateCode()
    {
        $code = strtoupper(Str::random(8));

        // Ensure code is unique
        while (DB::table('promo_codes')->where('code', $code)->exists()) {
            $code = strtoupper(Str::random(8));
        }

        return response()->json([
            'success' => true,
            'data' => ['code' => $code],
        ]);
    }

    /**
     * Get promo code statistics
     */
    public function stats()
    {
        $stats = [
            'total_codes' => DB::table('promo_codes')->count(),
            'active_codes' => DB::table('promo_codes')->where('status', 'active')->count(),
            'inactive_codes' => DB::table('promo_codes')->where('status', 'inactive')->count(),
            'expired_codes' => DB::table('promo_codes')
                ->where('expires_at', '<', now())
                ->count(),
            'total_usage' => DB::table('promo_code_usage')->count(),
            'total_discount_given' => DB::table('promo_code_usage')->sum('discount_amount'),
            'most_used_codes' => DB::table('promo_codes')
                ->leftJoin('promo_code_usage', 'promo_codes.id', '=', 'promo_code_usage.promo_code_id')
                ->select('promo_codes.code', 'promo_codes.description', DB::raw('COUNT(promo_code_usage.id) as usage_count'))
                ->groupBy('promo_codes.id', 'promo_codes.code', 'promo_codes.description')
                ->orderByDesc('usage_count')
                ->limit(5)
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
