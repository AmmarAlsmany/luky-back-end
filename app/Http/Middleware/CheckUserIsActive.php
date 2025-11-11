<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Check both is_active flag and status field
            if (!$user->is_active || $user->status !== 'active') {
                // User is logged in but not active
                Auth::logout();
                
                $statusMessage = !$user->is_active 
                    ? 'Your account is inactive. Please contact support.'
                    : 'Your account has been ' . $user->status . '. Please contact support.';
                
                if ($request->expectsJson() || $request->is('api/*')) {
                    // For API requests, return JSON response
                    return response()->json([
                        'success' => false,
                        'message' => $statusMessage,
                        'error_code' => 'account_inactive'
                    ], 403);
                }
                
                // For web requests, redirect to login with error message
                return redirect()->route('login')
                    ->with('error', $statusMessage);
            }
        }

        return $next($request);
    }
}
