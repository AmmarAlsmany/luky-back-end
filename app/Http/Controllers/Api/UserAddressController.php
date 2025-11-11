<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UserAddressController extends Controller
{
    /**
     * Get all addresses for the authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        $addresses = UserAddress::where('user_id', $request->user()->id)
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $addresses,
        ]);
    }

    /**
     * Store a new address
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:home,work,other',
            'address' => 'required|string|max:500',
            'building_number' => 'nullable|string|max:50',
            'street' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'notes' => 'nullable|string|max:500',
            'is_default' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            $validated['user_id'] = $request->user()->id;

            // If this is set as default, unset other defaults
            if ($validated['is_default'] ?? false) {
                UserAddress::where('user_id', $request->user()->id)
                    ->update(['is_default' => false]);
            }

            // If user has no addresses, make this the default
            $userAddressCount = UserAddress::where('user_id', $request->user()->id)->count();
            if ($userAddressCount === 0) {
                $validated['is_default'] = true;
            }

            $address = UserAddress::create($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Address added successfully',
                'data' => $address,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to add address: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display a specific address
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $address = UserAddress::where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $address,
        ]);
    }

    /**
     * Update an address
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $address = UserAddress::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|in:home,work,other',
            'address' => 'sometimes|required|string|max:500',
            'building_number' => 'nullable|string|max:50',
            'street' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'latitude' => 'sometimes|required|numeric|between:-90,90',
            'longitude' => 'sometimes|required|numeric|between:-180,180',
            'notes' => 'nullable|string|max:500',
            'is_default' => 'boolean',
        ]);

        DB::beginTransaction();
        try {
            // If setting as default, unset other defaults
            if (isset($validated['is_default']) && $validated['is_default']) {
                UserAddress::where('user_id', $request->user()->id)
                    ->where('id', '!=', $id)
                    ->update(['is_default' => false]);
            }

            $address->update($validated);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Address updated successfully',
                'data' => $address->fresh(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update address: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete an address
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $address = UserAddress::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $wasDefault = $address->is_default;
        $address->delete();

        // If deleted address was default, set another as default
        if ($wasDefault) {
            $newDefault = UserAddress::where('user_id', $request->user()->id)
                ->first();

            if ($newDefault) {
                $newDefault->update(['is_default' => true]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully',
        ]);
    }

    /**
     * Set an address as default
     */
    public function setDefault(Request $request, int $id): JsonResponse
    {
        $address = UserAddress::where('user_id', $request->user()->id)
            ->findOrFail($id);

        DB::beginTransaction();
        try {
            $address->setAsDefault();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Default address updated',
                'data' => $address->fresh(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to set default address: ' . $e->getMessage(),
            ], 500);
        }
    }
}
