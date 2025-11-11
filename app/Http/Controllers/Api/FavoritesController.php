<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceProviderResource;
use App\Models\ServiceProvider;
use App\Models\UserFavorite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FavoritesController extends Controller
{
    /**
     * Get all favorite providers for the authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // Get user's favorite providers with their details
        $favorites = $user->favoriteProviders()
            ->with(['user', 'city', 'services'])
            ->get();

        return response()->json([
            'success' => true,
            'data' => ServiceProviderResource::collection($favorites),
        ]);
    }

    /**
     * Add a provider to favorites
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'provider_id' => 'required|exists:service_providers,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();
        $providerId = $request->provider_id;

        // Check if already favorited
        $existingFavorite = UserFavorite::where('user_id', $user->id)
            ->where('provider_id', $providerId)
            ->first();

        if ($existingFavorite) {
            return response()->json([
                'success' => false,
                'message' => 'Provider already in favorites',
            ], 409);
        }

        // Add to favorites
        $favorite = UserFavorite::create([
            'user_id' => $user->id,
            'provider_id' => $providerId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Provider added to favorites',
            'data' => $favorite,
        ], 201);
    }

    /**
     * Remove a provider from favorites
     */
    public function destroy(Request $request, int $providerId): JsonResponse
    {
        $user = $request->user();

        $favorite = UserFavorite::where('user_id', $user->id)
            ->where('provider_id', $providerId)
            ->first();

        if (!$favorite) {
            return response()->json([
                'success' => false,
                'message' => 'Favorite not found',
            ], 404);
        }

        $favorite->delete();

        return response()->json([
            'success' => true,
            'message' => 'Provider removed from favorites',
        ]);
    }

    /**
     * Check if a provider is favorited
     */
    public function check(Request $request, int $providerId): JsonResponse
    {
        $user = $request->user();

        $isFavorite = UserFavorite::where('user_id', $user->id)
            ->where('provider_id', $providerId)
            ->exists();

        return response()->json([
            'success' => true,
            'is_favorite' => $isFavorite,
        ]);
    }

    /**
     * Toggle favorite status for a provider
     */
    public function toggle(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'provider_id' => 'required|exists:service_providers,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();
        $providerId = $request->provider_id;

        $favorite = UserFavorite::where('user_id', $user->id)
            ->where('provider_id', $providerId)
            ->first();

        if ($favorite) {
            // Remove from favorites
            $favorite->delete();
            $message = 'Provider removed from favorites';
            $isFavorite = false;
        } else {
            // Add to favorites
            UserFavorite::create([
                'user_id' => $user->id,
                'provider_id' => $providerId,
            ]);
            $message = 'Provider added to favorites';
            $isFavorite = true;
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'is_favorite' => $isFavorite,
        ]);
    }
}
