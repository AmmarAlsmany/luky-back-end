<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TokenRefreshController extends Controller
{
    /**
     * Generate API token for current session
     * This is a temporary endpoint to fix sessions created before token generation was added
     */
    public function refresh(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401);
        }

        $user = Auth::user();

        // Delete old dashboard tokens for this user
        $user->tokens()->where('name', 'dashboard-token')->delete();

        // Create new token
        $tokenExpiry = now()->addHours(24);
        $token = $user->createToken('dashboard-token', ['*'], $tokenExpiry)->plainTextToken;
        
        // Store token in session and save it
        $request->session()->put('api_token', $token);
        $request->session()->save(); // Force save to ensure persistence

        return response()->json([
            'success' => true,
            'message' => 'Token refreshed successfully',
            'token' => $token
        ]);
    }
}
